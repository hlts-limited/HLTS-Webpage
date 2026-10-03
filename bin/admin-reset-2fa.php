<?php
/**
 * Emergency only: switch off two-step sign-in for one admin who lost their phone and has no other
 * admin to reset it. They set it up again at their next sign-in. Command line only:
 *
 *   php /home/USER/public_html/bin/admin-reset-2fa.php admin@example.com
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require dirname(__DIR__) . '/lib/app.php';

$email = strtolower(trim((string) ($argv[1] ?? '')));
if ($email === '') {
    fwrite(STDERR, "Usage: php bin/admin-reset-2fa.php admin@example.com\n");
    exit(1);
}
$changed = db_run('UPDATE admins SET totp_secret = NULL, totp_last_step = NULL WHERE email = ?', [$email]);
log_event('ADMIN_2FA_RESET_CLI', $email);
echo $changed ? "Two-step sign-in reset for $email.\n" : "No admin with that email.\n";
