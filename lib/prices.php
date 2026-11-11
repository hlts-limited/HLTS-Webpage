<?php
/**
 * School prices come from the staff app, so they are changed in ONE place (the app's
 * lib/pricing.ts) and the website follows within the hour.
 *
 * The app's list is fetched with a signed request (the same shared secret as form delivery),
 * kept in storage/cache/app-prices.json, and refreshed by the cron job (bin/app-sync.php) and
 * whenever an admin opens "Staff app sync". Public pages only read the saved copy; they fetch
 * for themselves only when there is no recent copy at all. If the app can't be reached the last
 * good copy stays in use, and before the first successful fetch the amounts written in
 * lib/content.php are used.
 */

const APP_PRICES_REFRESH_SECONDS = 3600;       // cron / admin refresh at most hourly
const APP_PRICES_STALE_SECONDS = 6 * 3600;     // a public page fetches only when the copy is older than this
const APP_PRICES_RETRY_SECONDS = 600;

function app_prices_file(): string
{
    return STORAGE_DIR . '/cache/app-prices.json';
}

/** The saved copy: ['at' => time fetched, 'tried' => time last attempted, 'prices' => [...]] or null. */
function app_prices_cache(): ?array
{
    $file = app_prices_file();
    $cached = is_file($file) ? json_decode((string) file_get_contents($file), true) : null;
    return is_array($cached) ? $cached : null;
}

/**
 * The app's price list (amounts in kobo), or null to use the built-in amounts.
 * $maxAge: fetch again when the saved copy is older than this many seconds.
 */
function app_prices(int $maxAge = APP_PRICES_STALE_SECONDS): ?array
{
    static $memo = [];
    if (array_key_exists($maxAge, $memo)) {
        return $memo[$maxAge];
    }
    $cached = app_prices_cache();
    $prices = is_array($cached['prices'] ?? null) ? $cached['prices'] : null;
    $fresh = $cached && ($cached['at'] ?? 0) > time() - $maxAge;
    $triedRecently = $cached && ($cached['tried'] ?? 0) > time() - APP_PRICES_RETRY_SECONDS;
    if ($fresh || $triedRecently || !app_sync_enabled()) {
        return $memo[$maxAge] = $prices;
    }

    $fetched = app_prices_fetch();
    $save = ['at' => $fetched ? time() : ($cached['at'] ?? 0), 'tried' => time(), 'prices' => $fetched ?? $prices];
    $file = app_prices_file();
    if (!is_dir(dirname($file))) {
        @mkdir(dirname($file), 0755, true);
    }
    @file_put_contents($file, json_encode($save), LOCK_EX);
    return $memo[$maxAge] = $save['prices'];
}

/** Ask the app for its price list. Returns the checked list, or null if it couldn't be read. */
function app_prices_fetch(): ?array
{
    $base = app_base_url();
    if ($base === '') {
        return null;
    }
    $timestamp = (string) time();
    $signature = hash_hmac('sha256', $timestamp . '.prices.', trim((string) config('app_sync.secret')));
    $ch = curl_init($base . '/api/webhooks/website/prices');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_CONNECTTIMEOUT => 3,
        CURLOPT_TIMEOUT => 6,
        CURLOPT_HTTPHEADER => ['Accept: application/json', 'X-HLTS-Timestamp: ' . $timestamp, 'X-HLTS-Signature: ' . $signature],
    ]);
    $body = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);
    $json = is_string($body) ? json_decode($body, true) : null;
    if ($status !== 200 || !is_array($json) || empty($json['ok'])) {
        log_event('APP_PRICES_FAILED', 'HTTP ' . $status);
        return null;
    }
    $prices = app_prices_check($json);
    if ($prices === null) {
        log_event('APP_PRICES_INVALID');
    }
    return $prices;
}

/** Accept the app's list only if every amount is a sensible whole number; otherwise ignore all of it. */
function app_prices_check(array $json): ?array
{
    $money = fn ($v) => is_int($v) && $v > 0 && $v < 100000000000;
    $out = ['packages' => [], 'full_time' => [], 'part_time' => [], 'add_ons' => []];
    foreach (['basic', 'professional', 'premium'] as $key) {
        $p = $json['packages'][$key] ?? null;
        if (!is_array($p) || !$money($p['base'] ?? null) || !$money($p['per_pupil'] ?? null) || !$money($p['minimum'] ?? null)) {
            return null;
        }
        $out['packages'][$key] = ['base' => $p['base'], 'per_pupil' => $p['per_pupil'], 'minimum' => $p['minimum']];
    }
    foreach ((array) ($json['full_time'] ?? []) as $n => $row) {
        if (!ctype_digit((string) $n) || !is_array($row)) {
            return null;
        }
        foreach (['basic', 'professional', 'premium'] as $key) {
            if (!$money($row[$key] ?? null)) {
                return null;
            }
            $out['full_time'][(int) $n][$key] = $row[$key];
        }
    }
    foreach ((array) ($json['part_time'] ?? []) as $n => $price) {
        if (!ctype_digit((string) $n) || !$money($price)) {
            return null;
        }
        $out['part_time'][(int) $n] = $price;
    }
    if (!$out['full_time'] || !$out['part_time']) {
        return null;
    }
    foreach ((array) ($json['add_ons'] ?? []) as $a) {
        $name = is_array($a) && is_string($a['name'] ?? null) ? $a['name'] : null;
        $from = $a['from'] ?? null;
        $to = $a['to'] ?? null;
        if ($name === null || !(($from === null && $to === null) || ($money($from) && $money($to) && $to >= $from))) {
            return null;
        }
        $out['add_ons'][$name] = ['from' => $from, 'to' => $to];
    }
    ksort($out['full_time']);
    ksort($out['part_time']);
    return $out;
}

/** For the admin page: "from the staff app, updated 12 Nov, 3:05pm" or why not. */
function app_prices_status(): string
{
    $cached = app_prices_cache();
    if (!app_sync_enabled()) {
        return 'Using the prices written in the website (the staff app link is off).';
    }
    if (!$cached || empty($cached['prices'])) {
        return 'Using the prices written in the website; the staff app has not been reached yet.';
    }
    return 'Prices come from the staff app. Last updated ' . date('j M, g:ia', (int) $cached['at']) . '.';
}
