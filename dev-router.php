<?php
/**
 * Local preview only. Run from the project folder:
 *
 *   php -S localhost:8000 dev-router.php
 *
 * PHP's built-in server ignores .htaccess, so this does the same job:
 * /about.html serves about.php. Blocked on the live server by .htaccess.
 */

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($path === '/') {
    $path = '/index.html';
}

if (strpos($path, '/partials/') === 0) {
    http_response_code(403);
    return true;
}

if (preg_match('#^/([\w-]+)\.html$#', $path, $match) && is_file(__DIR__ . '/' . $match[1] . '.php')) {
    $_SERVER['SCRIPT_NAME'] = '/' . $match[1] . '.php';
    require __DIR__ . '/' . $match[1] . '.php';
    return true;
}

// Anything else (images, CSS, JS, send-registration.php) is served as normal.
return false;
