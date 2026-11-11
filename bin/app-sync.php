<?php
/**
 * Resend form submissions and course payments the staff app hasn't received yet,
 * and refresh the website's copy of the school price list from the app.
 * Run from cPanel > Cron Jobs every 15 minutes:
 *
 *   php /home/USER/public_html/bin/app-sync.php
 *
 * Command line only; the web server refuses this folder.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__) . '/lib/app.php';

$result = app_sync_run(50);
$result['prices'] = app_prices(APP_PRICES_REFRESH_SECONDS) ? 'from app' : 'built-in';
$result['errors_emailed'] = error_digest();
echo json_encode($result), PHP_EOL;

/**
 * Once a day, email any new lines in the website's error log to the address that receives
 * enquiries, so a broken page is noticed without anyone opening the server. Returns whether an
 * email went out.
 */
function error_digest(): bool
{
    $log = STORAGE_DIR . '/logs/php-errors.log';
    $markFile = STORAGE_DIR . '/cache/error-digest.json';
    $mark = is_file($markFile) ? (json_decode((string) file_get_contents($markFile), true) ?: []) : [];
    $size = is_file($log) ? (int) filesize($log) : 0;
    $offset = (int) ($mark['offset'] ?? 0);
    if ($size < $offset) {
        $offset = 0; // the log was cleared or rotated
    }
    if ($size <= $offset || ($mark['day'] ?? '') === date('Y-m-d')) {
        return false;
    }
    // At most the last 15,000 characters of what is new.
    $new = (string) file_get_contents($log, false, null, max($offset, $size - 15000));
    $lines = substr_count($new, "\n");
    $sent = send_mail(
        (string) config('notify_to'),
        'HLTS website: ' . $lines . ' new error line(s)',
        "The website logged new errors since the last report. Most are harmless, but a repeated one usually means a page or form is failing for visitors. Forward this to your developer.\n\n" . $new
    );
    if ($sent) {
        if (!is_dir(dirname($markFile))) {
            @mkdir(dirname($markFile), 0755, true);
        }
        @file_put_contents($markFile, json_encode(['offset' => $size, 'day' => date('Y-m-d')]), LOCK_EX);
    }
    return $sent;
}
