<?php
/**
 * Resend form submissions the staff app hasn't received yet.
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
echo json_encode($result), PHP_EOL;
