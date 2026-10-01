<?php
/**
 * Local preview only. Run from the project folder:
 *
 *   php -S localhost:8000 dev-router.php
 *
 * PHP's built-in server ignores .htaccess, so this mirrors its rules:
 * /about.html serves about.php, private folders are blocked, and missing
 * pages get the site's 404 page. Blocked on the live server by .htaccess.
 */

$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');

if ($path === '/') {
    $path = '/index.html';
}

$serve = function (string $script, ?int $status = null) {
    $_SERVER['SCRIPT_NAME'] = '/' . $script;
    $_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/' . $script;
    if ($status) {
        $_GET['code'] = $status;
    }
    require __DIR__ . '/' . $script;
    return true;
};

// Private folders and legacy files that .htaccess blocks on the live site.
if (preg_match('#^/(partials|lib|config|storage|bin)(/|$)#', $path)
    || preg_match('#^/admin/_#', $path)
    || preg_match('#^/(admin_dashboard|security-dashboard|portal_interface)\.html$#', $path)
    || preg_match('#/\.#', $path)) {
    http_response_code(403);
    return $serve('error.php', 403);
}

if ($path === '/sitemap.xml') {
    return $serve('sitemap.php');
}

if (preg_match('#^/([\w-]+)\.html$#', $path, $match) && is_file(__DIR__ . '/' . $match[1] . '.php')) {
    return $serve($match[1] . '.php');
}

if ($path === '/admin' || $path === '/admin/') {
    return $serve('admin/index.php');
}

if (!is_file(__DIR__ . $path)) {
    return $serve('error.php', 404);
}

// Real files (images, CSS, JS, PHP endpoints) are served as normal.
return false;
