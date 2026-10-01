<?php
/**
 * Receives every public form on the site.
 *
 * With JavaScript the form posts here with fetch() and gets JSON back, so the
 * visitor stays on the page. Without JavaScript it posts normally and gets a
 * full results page (or is sent back to the form with their answers kept).
 */

require __DIR__ . '/lib/app.php';

// A CV larger than the server allows empties the whole post; say so instead of "unknown form".
if (is_post() && empty($_POST) && (int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    $message = 'Your file is too large. Please upload a CV of 4 MB or less.';
    wants_json() ? json_response(['ok' => false, 'message' => $message], 413) : render_message_page('File too large', $message, false);
    exit;
}

if (!is_post()) {
    http_response_code(405);
    render_message_page('Use the form', 'Please submit this from one of the forms on the website.', false);
    exit;
}

$key = is_string($_POST['form'] ?? null) ? $_POST['form'] : '';
$definition = form_definition($key);
$returnTo = is_string($_POST['return_to'] ?? null) && str_starts_with($_POST['return_to'], '/') && !str_starts_with($_POST['return_to'], '//')
    ? $_POST['return_to']
    : '/';

if (!$definition) {
    http_response_code(400);
    render_message_page('Unknown form', 'We could not tell which form this came from. Please try again from the website.', false);
    exit;
}

csrf_require();

$fail = function (string $message, array $errors = [], int $status = 422) use ($key, $returnTo) {
    if (wants_json()) {
        json_response(['ok' => false, 'message' => $message, 'errors' => $errors], $status);
    }
    flash('form_state', ['form' => $key, 'old' => $_POST, 'errors' => $errors, 'message' => $message]);
    redirect($returnTo . (str_contains($returnTo, '#') ? '' : '#form'));
};

// Bots fill in the hidden "website" field. Pretend it worked and stop.
if (!empty($_POST['website'])) {
    log_event('HONEYPOT', $key);
    wants_json() ? json_response(['ok' => true, 'message' => $definition['success']]) : render_message_page('Thank you', $definition['success'], true);
    exit;
}

if (!rate_limit("form:$key:" . client_ip(), 6, 600)) {
    log_event('FORM_RATE_LIMITED', $key);
    $fail('Too many submissions from your connection. Please wait a few minutes and try again.', [], 429);
}

[$data, $errors] = validate_form($definition, $_POST, $_FILES);

// Course-specific rules.
if ($key === 'student' && !isset($errors['course'])) {
    $hasFees = course($data['course'])['fees'] ?? [];
    if ($hasFees && $data['plan'] === '') {
        $errors['plan'] = 'Choose a payment plan.';
    }
    if ($data['age_group'] === 'under-13' || $data['age_group'] === '13-17') {
        if ($data['guardian'] === '') {
            $errors['guardian'] = 'Add a parent or guardian name for learners under 18.';
        }
    }
}

if ($errors) {
    $fail('Please check the highlighted fields.', $errors);
}

// An uploaded CV is kept privately until it has been sent to the staff app.
$upload = null;
foreach ($definition['fields'] as $name => $field) {
    if ($field['type'] === 'file' && is_array($data[$name] ?? null)) {
        $upload = store_upload($data[$name]);
        if (!$upload) {
            log_event('UPLOAD_SAVE_FAILED', $key);
            $fail('We could not save your CV just now. Please try again.', [$name => 'Upload failed. Please try again.'], 500);
        }
        $data[$name] = $upload['name'];
    }
}

// Proof of consent: when, and to which version of the privacy policy.
$consent = !empty($data['terms']) ? ['at' => date(DATE_ATOM), 'version' => PRIVACY_VERSION] : null;

$map = $definition['lead'];
$details = describe_submission($definition, $data);

try {
    $leadId = db_insert('leads', [
        'type' => $key,
        'name' => (string) ($data[$map['name'] ?? ''] ?? ''),
        'email' => (string) ($data[$map['email'] ?? ''] ?? ''),
        'phone' => (string) ($data[$map['phone'] ?? ''] ?? ''),
        'organisation' => (string) ($data[$map['organisation'] ?? ''] ?? ''),
        'summary' => mb_substr(($definition['summary'])($data), 0, 250),
        'payload' => json_encode($data + ['_consent' => $consent], JSON_UNESCAPED_UNICODE),
        'status' => 'new',
        'ip' => client_ip(),
        'created_at' => now(),
        'updated_at' => now(),
    ]);
} catch (Throwable $e) {
    error_log('Lead save failed: ' . $e->getMessage());
    $leadId = null;
}

// Hand it to the staff app (sent after the visitor has their answer; see the end of this file).
if ($leadId) {
    try {
        app_sync_queue($leadId, $key, app_payload($leadId, $key, $definition, $data, now(), $consent, $upload['name'] ?? null), $upload);
    } catch (Throwable $e) {
        error_log('App sync queue failed: ' . $e->getMessage());
    }
}

// Email the team. Job seekers' details stay out of email: they are only in the staff app.
$body = '';
if (!empty($definition['private'])) {
    $body .= "A new " . strtolower($definition['title']) . " has arrived" . (isset($data['job']) ? ' for: ' . $data['job'] : '') . ".\n\n"
        . "For privacy, the details and CV are not included in this email. Open Recruitment in the HLTS staff app to see them:\n"
        . "https://hlts-hr.vercel.app/recruitment\n";
} else {
    foreach ($details as $label => $value) {
        $body .= $label . ': ' . $value . "\n";
    }
}
$body .= "\nReceived: " . now() . ($leadId && empty($definition['private']) ? "\nView in admin: " . absolute_url("admin/lead.php?id=$leadId") : '');
$notifyTo = config("notify_to_by_form.$key") ?: config('notify_to');
$emailOk = send_mail((string) $notifyTo, $definition['subject'] . (empty($definition['private']) && isset($data['name']) && $data['name'] !== '' ? ': ' . $data['name'] : ''), $body, empty($definition['private']) ? ($data['email'] ?? null) : null);

if (!$leadId && !$emailOk) {
    log_event('SUBMISSION_LOST', $key);
    $fail('We could not save your details just now. Please try again, or contact us on WhatsApp.', [], 500);
}

// Confirmation to the visitor.
if (($definition['confirm'] ?? true) && config('mail.send_confirmations') && !empty($data['email'])) {
    $name = $data['name'] ?? '';
    $copy = '';
    foreach ($details as $label => $value) {
        $copy .= "- $label: $value\n";
    }
    send_mail(
        $data['email'],
        'We received your ' . strtolower($definition['title']) . ' – HLTS Limited',
        "Hello" . ($name ? " $name" : '') . ",\n\n" . $definition['success'] . "\n\nHere is what you sent:\n$copy\nIf anything is wrong, just reply to this email.\n\nHLTS Limited\n" . config('phone_display') . ' · ' . config('email_public')
    );
}

// Optional online payment straight after course registration.
$redirect = null;
if ($key === 'student' && !empty($_POST['pay_now']) && paystack_enabled() && course_fee($data['course'], $data['plan']) !== null) {
    try {
        $redirect = paystack_start($data, $data['course'], $data['plan'], $leadId);
    } catch (Throwable $e) {
        log_event('PAYSTACK_INIT_FAILED', $e->getMessage());
    }
}

// Deliver to the staff app once the visitor's response has gone out, so they never wait for it.
register_shutdown_function(function () use ($leadId) {
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }
    // Send the response now: PHP-FPM and LiteSpeed (Hostinger) each have their own way.
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    } elseif (function_exists('litespeed_finish_request')) {
        litespeed_finish_request();
    }
    ignore_user_abort(true);
    try {
        app_sync_run(5, $leadId);
    } catch (Throwable $e) {
        error_log('App sync failed: ' . $e->getMessage());
    }
});

if (wants_json()) {
    json_response(['ok' => true, 'message' => $definition['success'], 'redirect' => $redirect]);
}
if ($redirect) {
    redirect($redirect);
}
render_message_page('Thank you', $definition['success'], true, $returnTo, 'Back to the page');
