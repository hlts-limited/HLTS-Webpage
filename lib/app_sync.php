<?php
/**
 * Sends every website form submission to the HLTS Operation Suite (the staff
 * app at hlts-hr.vercel.app), where the team works on it.
 *
 * Each submission is queued in the app_sync table first, so nothing is lost if
 * the app is briefly unreachable: it is retried with growing gaps, and the
 * admin area (Staff app sync) can resend by hand. Requests are signed with a
 * shared secret (config app_sync.secret) and the app ignores anything else.
 *
 * Job seekers' details are private: once their submission (and CV) has reached
 * the app, the website keeps no copy, so the app's 30-day deletion is the only
 * record that matters.
 */

/** Forms whose details leave the website once delivered (job seekers). */
const APP_SYNC_PRIVATE_FORMS = ['candidate', 'career'];
const APP_SYNC_MAX_ATTEMPTS = 50;

function app_sync_enabled(): bool
{
    return (string) config('app_sync.url') !== '' && trim((string) config('app_sync.secret')) !== '';
}

/** The record sent to the app for one submission. */
function app_payload(int $leadId, string $key, array $definition, array $data, string $submittedAt, ?array $consent, ?string $fileName): array
{
    $fields = [];
    foreach ($definition['fields'] as $name => $field) {
        if ($field['type'] === 'consent' || $field['type'] === 'file') {
            continue;
        }
        $value = $data[$name] ?? '';
        if (is_array($value)) {
            $value = implode(', ', array_map(fn ($v) => $field['options'][$v] ?? $v, $value));
        } elseif (isset($field['options'][$value])) {
            $value = $field['options'][$value];
        }
        if ($value === '' || $value === null) {
            continue;
        }
        $fields[] = ['key' => $name, 'label' => strip_tags($field['label']), 'value' => mb_substr((string) $value, 0, 6000)];
    }
    $map = $definition['lead'];
    $pick = fn (string $role) => isset($map[$role]) && ($data[$map[$role]] ?? '') !== '' ? (string) $data[$map[$role]] : null;
    $raw = $data;
    unset($raw['terms']);

    return [
        'v' => 1,
        'source' => 'lead:' . $leadId,
        'form' => $key,
        'submittedAt' => (new DateTimeImmutable($submittedAt, new DateTimeZone(config('timezone'))))->format(DATE_ATOM),
        'name' => $pick('name'),
        'email' => $pick('email'),
        'phone' => $pick('phone'),
        'whatsapp' => ($data['whatsapp'] ?? '') !== '' ? (string) $data['whatsapp'] : null,
        'organisation' => $pick('organisation'),
        'summary' => mb_substr((string) ($definition['summary'])($data), 0, 300),
        'topic' => ($data['topic'] ?? '') !== '' ? (string) $data['topic'] : null,
        'fields' => $fields,
        'data' => (object) $raw,
        'consent' => $consent,
        'file' => $fileName !== null ? ['name' => $fileName, 'field' => 'cv'] : null,
    ];
}

/** Queue a saved submission for delivery. $file is ['path' => relative to storage, 'name', 'mime']. */
function app_sync_queue(int $leadId, string $form, array $payload, ?array $file = null): void
{
    db_insert('app_sync', [
        'lead_id' => $leadId,
        'form' => $form,
        'payload' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'file_path' => $file['path'] ?? '',
        'file_name' => $file['name'] ?? '',
        'file_mime' => $file['mime'] ?? '',
        'status' => 'pending',
        'attempts' => 0,
        'last_error' => '',
        'next_attempt_at' => now(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
}

/**
 * Deliver queued submissions that are due. $firstLeadId is tried first (the one
 * just submitted). Returns counts of what happened.
 */
function app_sync_run(int $limit = 10, ?int $firstLeadId = null): array
{
    $done = ['sent' => 0, 'failed' => 0, 'waiting' => 0];
    if (!app_sync_enabled()) {
        return $done + ['disabled' => true];
    }
    $rows = db_all(
        'SELECT * FROM app_sync WHERE status = ? AND next_attempt_at <= ? ORDER BY CASE WHEN lead_id = ? THEN 0 ELSE 1 END, id LIMIT ' . max(1, min(100, $limit)),
        ['pending', now(), $firstLeadId ?? 0]
    );
    foreach ($rows as $row) {
        $done[app_sync_send($row)]++;
    }
    return $done;
}

/** Send one queued row. Returns 'sent', 'failed' (needs a person) or 'waiting' (will retry). */
function app_sync_send(array $row): string
{
    $payload = (string) $row['payload'];
    $path = $row['file_path'] !== '' ? STORAGE_DIR . '/' . $row['file_path'] : null;
    if ($path !== null && !is_file($path)) {
        $path = null; // the file is gone; send the details without it
    }
    $timestamp = (string) time();
    $signature = hash_hmac('sha256', $timestamp . '.' . $payload . '.' . ($path ? hash_file('sha256', $path) : ''), trim((string) config('app_sync.secret')));

    $fields = ['payload' => $payload];
    if ($path) {
        $fields['file'] = new CURLFile($path, $row['file_mime'] ?: 'application/octet-stream', $row['file_name'] ?: basename($path));
    }
    $ch = curl_init((string) config('app_sync.url'));
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $fields,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 6,
        CURLOPT_TIMEOUT => 25,
        CURLOPT_HTTPHEADER => ['Accept: application/json', 'X-HLTS-Timestamp: ' . $timestamp, 'X-HLTS-Signature: ' . $signature],
    ]);
    $body = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    $json = is_string($body) ? json_decode($body, true) : null;

    $attempts = (int) $row['attempts'] + 1;
    if ($status === 200 && is_array($json) && !empty($json['ok'])) {
        db_update('app_sync', (int) $row['id'], ['status' => 'sent', 'attempts' => $attempts, 'last_error' => '', 'payload' => '', 'file_path' => '', 'file_name' => '', 'sent_at' => now(), 'updated_at' => now()]);
        if ($path) {
            @unlink($path);
        }
        if (in_array($row['form'], APP_SYNC_PRIVATE_FORMS, true)) {
            app_sync_redact((int) $row['lead_id']);
        }
        return 'sent';
    }

    $message = $error !== '' ? $error : ('HTTP ' . $status . (is_array($json) && isset($json['error']) ? ': ' . $json['error'] : ''));
    // 4xx means the request itself is wrong (bad secret, invalid data): retrying won't help.
    $permanent = $status >= 400 && $status < 500 && !in_array($status, [408, 429], true);
    $failed = $permanent || $attempts >= APP_SYNC_MAX_ATTEMPTS;
    // Wait 2, 4, 8 … minutes between tries, at most 6 hours.
    $wait = min(360, 2 ** min($attempts, 9));
    db_update('app_sync', (int) $row['id'], [
        'status' => $failed ? 'failed' : 'pending',
        'attempts' => $attempts,
        'last_error' => mb_substr($message, 0, 250),
        'next_attempt_at' => date('Y-m-d H:i:s', time() + $wait * 60),
        'updated_at' => now(),
    ]);
    log_event('APP_SYNC_' . ($failed ? 'FAILED' : 'RETRY'), $row['form'] . ' lead ' . $row['lead_id'] . ': ' . $message);
    return $failed ? 'failed' : 'waiting';
}

/** Remove a job seeker's details from the website once the app has them. */
function app_sync_redact(int $leadId): void
{
    db_update('leads', $leadId, [
        'name' => '(in the staff app)',
        'email' => '',
        'phone' => '',
        'summary' => 'Sent to the HLTS staff app (Recruitment). Details are kept there only.',
        'payload' => '{}',
        'ip' => '',
        'updated_at' => now(),
    ]);
}

/** Put failed deliveries back in the queue (e.g. after fixing the secret). */
function app_sync_retry_failed(): int
{
    return db_run("UPDATE app_sync SET status = 'pending', attempts = 0, next_attempt_at = ? WHERE status = 'failed'", [now()]);
}

/** Queue every older submission that was never sent to the app. Returns how many were queued. */
function app_sync_backfill(): int
{
    $leads = db_all('SELECT * FROM leads WHERE id NOT IN (SELECT lead_id FROM app_sync) ORDER BY id');
    $count = 0;
    foreach ($leads as $lead) {
        $definition = form_definition($lead['type']);
        $data = json_decode((string) $lead['payload'], true);
        if (!$definition || !is_array($data) || !empty($data['redacted'])) {
            continue;
        }
        $consent = $data['_consent'] ?? (!empty($data['terms']) ? ['at' => (new DateTimeImmutable($lead['created_at'], new DateTimeZone(config('timezone'))))->format(DATE_ATOM), 'version' => 'before-2026-10'] : null);
        unset($data['_consent']);
        app_sync_queue((int) $lead['id'], $lead['type'], app_payload((int) $lead['id'], $lead['type'], $definition, $data, $lead['created_at'], $consent, null));
        $count++;
    }
    return $count;
}

/** Counts for the admin page. */
function app_sync_counts(): array
{
    $counts = ['pending' => 0, 'sent' => 0, 'failed' => 0];
    foreach (db_all('SELECT status, COUNT(*) AS n FROM app_sync GROUP BY status') as $r) {
        $counts[$r['status']] = (int) $r['n'];
    }
    $counts['never'] = (int) db_value('SELECT COUNT(*) FROM leads WHERE id NOT IN (SELECT lead_id FROM app_sync)');
    return $counts;
}

/** Keep an uploaded CV privately (storage/ is never served) until it has been sent. */
function store_upload(array $file): ?array
{
    $dir = STORAGE_DIR . '/uploads';
    if (!is_dir($dir) && !@mkdir($dir, 0770, true)) {
        return null;
    }
    if (!is_file($dir . '/.htaccess')) {
        @file_put_contents($dir . '/.htaccess', "Require all denied\n");
    }
    $name = bin2hex(random_bytes(16)) . '.' . $file['ext'];
    $moved = is_uploaded_file($file['tmp']) ? move_uploaded_file($file['tmp'], "$dir/$name") : @rename($file['tmp'], "$dir/$name");
    return $moved ? ['path' => "uploads/$name", 'name' => $file['name'], 'mime' => $file['mime']] : null;
}
