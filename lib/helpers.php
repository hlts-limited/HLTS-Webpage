<?php
/**
 * Small helpers used everywhere: escaping, URLs, redirects, flash messages
 * and request details.
 */

function h($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function is_https(): bool
{
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        return true;
    }
    return ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function wants_json(): bool
{
    return str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
}

function client_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

/**
 * Link to a public page by its short name: page_url('about') -> "/about.html".
 * Pages keep their .html addresses; .htaccess serves the matching .php file.
 * Links are root-relative so they work from /admin/ and /portal pages too.
 */
function page_url(string $page, array $query = []): string
{
    $url = $page === 'index' ? '/' : '/' . $page . '.html';
    return $query ? $url . '?' . http_build_query($query) : $url;
}

function absolute_url(string $path = ''): string
{
    return rtrim((string) config('site_url'), '/') . '/' . ltrim($path, '/');
}

/** Stylesheet or script path with a version so browsers fetch changes. */
function asset(string $path): string
{
    $path = '/' . ltrim($path, '/');
    $file = APP_ROOT . $path;
    $version = is_file($file) ? (string) filemtime($file) : '1';
    return $path . '?v=' . $version;
}

function redirect(string $to, int $status = 303): never
{
    header('Location: ' . $to, true, $status);
    exit;
}

function json_response(array $data, int $status = 200): never
{
    http_response_code($status);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function flash(string $key, $value = null)
{
    if ($value !== null) {
        $_SESSION['flash'][$key] = $value;
        return null;
    }
    $stored = $_SESSION['flash'][$key] ?? null;
    unset($_SESSION['flash'][$key]);
    return $stored;
}

function now(): string
{
    return date('Y-m-d H:i:s');
}

function format_date(?string $value, string $format = 'j M Y'): string
{
    if (!$value) {
        return '';
    }
    $time = strtotime($value);
    return $time ? date($format, $time) : '';
}

function naira(int $kobo): string
{
    return '₦' . number_format($kobo / 100);
}

function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    return trim($text, '-') ?: 'item';
}

function log_event(string $event, string $details = ''): void
{
    $line = sprintf("[%s] [%s] %s: %s\n", now(), client_ip(), $event, $details);
    @file_put_contents(STORAGE_DIR . '/logs/events.log', $line, FILE_APPEND | LOCK_EX);
}

/**
 * Turn simple text into safe HTML. Supports paragraphs, "## " headings and
 * "- " bullet lists. Everything is escaped first, so admin-entered text can
 * never inject markup.
 */
function render_text(string $text): string
{
    $html = '';
    $paragraph = [];
    $list = [];

    $flush = function () use (&$html, &$paragraph, &$list) {
        if ($paragraph) {
            $html .= '<p>' . implode('<br>', array_map('h', $paragraph)) . '</p>';
            $paragraph = [];
        }
        if ($list) {
            $html .= '<ul>' . implode('', array_map(fn ($item) => '<li>' . h($item) . '</li>', $list)) . '</ul>';
            $list = [];
        }
    };

    foreach (preg_split('/\R/', trim($text)) ?: [] as $line) {
        $trimmed = trim($line);
        if ($trimmed === '') {
            $flush();
        } elseif (str_starts_with($trimmed, '### ')) {
            $flush();
            $html .= '<h3>' . h(substr($trimmed, 4)) . '</h3>';
        } elseif (str_starts_with($trimmed, '## ')) {
            $flush();
            $html .= '<h2>' . h(substr($trimmed, 3)) . '</h2>';
        } elseif (str_starts_with($trimmed, '- ')) {
            if ($paragraph) {
                $flush();
            }
            $list[] = substr($trimmed, 2);
        } else {
            if ($list) {
                $flush();
            }
            $paragraph[] = $trimmed;
        }
    }
    $flush();

    return $html;
}

function send_security_headers(): void
{
    $csp = implode('; ', [
        "default-src 'self'",
        "script-src 'self' https://cdn.jsdelivr.net",
        "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com",
        "font-src 'self' https://cdn.jsdelivr.net https://fonts.gstatic.com",
        "img-src 'self' data: https:",
        "connect-src 'self'",
        "frame-src https://www.google.com",
        "object-src 'none'",
        "base-uri 'self'",
        "form-action 'self' https://checkout.paystack.com",
        "frame-ancestors 'self'",
    ]);

    header('Content-Security-Policy: ' . $csp);
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    if (is_https()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}
