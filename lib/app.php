<?php
/**
 * Every PHP entry point starts here.
 *
 *   require __DIR__ . '/lib/app.php';
 *
 * Loads configuration, starts the session, sends security headers and pulls in
 * the shared helpers. Pages, form endpoints, the admin area and the student
 * portal all go through this file so they behave the same way.
 */

declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__));
define('STORAGE_DIR', APP_ROOT . '/storage');

require __DIR__ . '/config.php';
require __DIR__ . '/helpers.php';
require __DIR__ . '/db.php';
require __DIR__ . '/csrf.php';
require __DIR__ . '/ratelimit.php';
require __DIR__ . '/mailer.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/content.php';
require __DIR__ . '/forms.php';
require __DIR__ . '/paystack.php';
require __DIR__ . '/results.php';
require __DIR__ . '/ui.php';

date_default_timezone_set(config('timezone'));

if (config('debug')) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', STORAGE_DIR . '/logs/php-errors.log');
}

if (!is_dir(STORAGE_DIR . '/logs')) {
    @mkdir(STORAGE_DIR . '/logs', 0775, true);
}

if (PHP_SAPI !== 'cli') {
    session_name('hlts_session');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => is_https(),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
    send_security_headers();
}
