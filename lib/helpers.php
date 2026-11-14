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
    if (is_external_url($page)) {
        return $page;
    }
    $url = $page === 'index' ? '/' : '/' . $page . '.html';
    return $query ? $url . '?' . http_build_query($query) : $url;
}

function is_external_url(string $url): bool
{
    return (bool) preg_match('#^https?://#i', $url);
}

/** Extra attributes for menu links: external sites open in a new tab. */
function link_target(string $page): string
{
    return is_external_url($page) ? ' target="_blank" rel="noopener"' : '';
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

/** A path on this site to go back to, or $fallback. Rejects other sites ("//x", "/\\x"), schemes and control characters. */
function safe_local_path($path, string $fallback = '/'): string
{
    if (!is_string($path) || $path === '' || strlen($path) > 512) {
        return $fallback;
    }
    if ($path[0] !== '/' || str_starts_with($path, '//') || str_contains($path, '\\') || preg_match('/[\x00-\x1F\x7F]/', $path)) {
        return $fallback;
    }
    return $path;
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
 * "- " bullet lists, and inside them [words](https://address) for a link and
 * **words** for bold. Everything is escaped first, so admin-entered text can
 * never inject markup.
 *
 * Pictures: ![Caption](images/…) on a line of its own, with a blank line after it, fills the
 * width of the text. Written on the line directly above a paragraph (no blank line between),
 * the picture is smaller and the paragraph runs beside it.
 */
function render_text(string $text): string
{
    $html = '';
    $paragraph = [];
    $list = [];
    $quote = [];

    $flush = function () use (&$html, &$paragraph, &$list, &$quote) {
        if ($paragraph) {
            $html .= '<p>' . implode('<br>', array_map('render_inline', $paragraph)) . '</p>';
            $paragraph = [];
        }
        if ($list) {
            $html .= '<ul>' . implode('', array_map(fn ($item) => '<li>' . render_inline($item) . '</li>', $list)) . '</ul>';
            $list = [];
        }
        if ($quote) {
            $html .= '<blockquote class="prose-quote"><p>' . implode('<br>', array_map('render_inline', $quote)) . '</p></blockquote>';
            $quote = [];
        }
    };

    $lines = preg_split('/\R/', trim($text)) ?: [];
    for ($i = 0; $i < count($lines); $i++) {
        $trimmed = trim($lines[$i]);
        if ($trimmed === '') {
            $flush();
        } elseif (preg_match('/^!\[([^\]\n]{0,300})\]\(([^()\s]{1,300})\)$/u', $trimmed, $picture)) {
            $flush();
            // Text on the very next line runs beside the picture; a blank line means full width.
            $beside = [];
            while (isset($lines[$i + 1]) && !is_block_line(trim($lines[$i + 1]))) {
                $beside[] = trim($lines[++$i]);
            }
            $html .= $beside
                ? render_picture_beside($picture[1], $picture[2], $beside)
                : render_picture($picture[1], $picture[2]);
        } elseif (str_starts_with($trimmed, '### ')) {
            $flush();
            $html .= '<h3>' . render_inline(substr($trimmed, 4)) . '</h3>';
        } elseif (str_starts_with($trimmed, '## ')) {
            $flush();
            $html .= '<h2>' . render_inline(substr($trimmed, 3)) . '</h2>';
        } elseif (str_starts_with($trimmed, '- ')) {
            if ($paragraph || $quote) {
                $flush();
            }
            $list[] = substr($trimmed, 2);
        } elseif (str_starts_with($trimmed, '> ')) {
            if ($paragraph || $list) {
                $flush();
            }
            $quote[] = substr($trimmed, 2);
        } else {
            if ($list || $quote) {
                $flush();
            }
            $paragraph[] = $trimmed;
        }
    }
    $flush();

    return $html;
}

/**
 * A picture inside the text: ![Caption](images/…) on a line of its own. The caption is optional
 * and is also used as the description for people who can't see the picture. A picture that isn't
 * stored on this website is left as typed, so no post can pull an image from somewhere else.
 */
function render_picture(string $caption, string $src): string
{
    $path = post_image_path($src);
    if ($path === null) {
        return '<p>' . h('![' . $caption . '](' . $src . ')') . '</p>';
    }
    $caption = trim($caption);
    // The real size is written in, so the page keeps the right space while the picture loads.
    $size = @getimagesize(APP_ROOT . '/' . $path);
    $dimensions = $size && $size[0] > 0 ? ' width="' . (int) $size[0] . '" height="' . (int) $size[1] . '"' : '';
    $html = '<figure class="prose-figure"><img src="' . h(img($path)) . '"' . $dimensions . ' alt="' . h($caption) . '" loading="lazy" decoding="async">';
    if ($caption !== '') {
        $html .= '<figcaption>' . h($caption) . '</figcaption>';
    }
    return $html . '</figure>';
}

/** A line that starts something of its own, so it never runs beside a picture. */
function is_block_line(string $line): bool
{
    return $line === ''
        || str_starts_with($line, '## ')
        || str_starts_with($line, '### ')
        || str_starts_with($line, '- ')
        || str_starts_with($line, '> ')
        || (bool) preg_match('/^!\[/', $line);
}

/**
 * A smaller picture with a paragraph running beside it, as a news site shows a thumbnail next to
 * each item. Written as the picture line with the text on the line directly below it.
 */
function render_picture_beside(string $caption, string $src, array $lines): string
{
    $path = post_image_path($src);
    $text = '<div class="prose-beside__text"><p>' . implode('<br>', array_map('render_inline', $lines)) . '</p></div>';
    if ($path === null) {
        return '<p>' . h('![' . $caption . '](' . $src . ')') . '</p>' . str_replace(['<div class="prose-beside__text">', '</div>'], '', $text);
    }
    $caption = trim($caption);
    $size = @getimagesize(APP_ROOT . '/' . $path);
    $dimensions = $size && $size[0] > 0 ? ' width="' . (int) $size[0] . '" height="' . (int) $size[1] . '"' : '';
    $picture = '<figure class="prose-beside__picture"><img src="' . h(img($path)) . '"' . $dimensions . ' alt="' . h($caption) . '" loading="lazy" decoding="async">';
    if ($caption !== '') {
        $picture .= '<figcaption>' . h($caption) . '</figcaption>';
    }
    return '<div class="prose-beside">' . $picture . '</figure>' . $text . '</div>';
}

/** A picture stored on this website (images/…), or null. Nothing that could run as code. */
function post_image_path(string $src): ?string
{
    $src = trim(ltrim(trim($src), '/'));
    if (str_contains($src, '..') || str_contains($src, '\\')) {
        return null;
    }
    return preg_match('#^images/[\w.\- ]+(/[\w.\- ]+)*\.(jpe?g|png|webp|gif)$#i', $src) ? $src : null;
}

/**
 * One line of text with [words](address) links, **bold**, *italic* and ~smaller~, as safe HTML.
 * A link whose address isn't allowed (see link_href) is left exactly as typed, so the writer can
 * see it didn't work. Bold, italic and smaller also work inside the words of a link.
 */
function render_inline(string $text): string
{
    // Links are set aside first so that styles can wrap them: *~[words](/page)~* works.
    $links = [];
    $tokenised = preg_replace_callback('/\[([^\]\n]{1,300})\]\(([^()\s]{1,2000})\)/u', function ($m) use (&$links) {
        $href = link_href($m[2]);
        if ($href === null) {
            return $m[0]; // not an address we allow: left exactly as typed
        }
        $links[] = [$href, $m[1]];
        return "\x02" . (count($links) - 1) . "\x03";
    }, $text) ?? $text;

    $html = render_emphasis(h($tokenised));

    return preg_replace_callback('/\x02(\d+)\x03/', function ($m) use ($links) {
        [$href, $label] = $links[(int) $m[1]];
        $external = preg_match('#^https?://#i', $href) && strcasecmp((string) parse_url($href, PHP_URL_HOST), (string) parse_url((string) config('site_url'), PHP_URL_HOST)) !== 0;
        return '<a href="' . h($href) . '"' . ($external ? ' target="_blank" rel="noopener"' : '') . '>' . render_emphasis(h($label)) . '</a>';
    }, $html) ?? $html;
}

/**
 * **bold**, *italic* and ~smaller~ inside text that has already been made safe. Styles can be
 * put together — *~words~* is italic and smaller — by taking the outermost mark first and then
 * doing the same inside it. Marks with a space just inside them (as in "3 * 4 * 5") are ordinary
 * writing and are left alone.
 */
function render_emphasis(string $safe): string
{
    $marks = [
        ['/\*\*([^*\n]{1,500}?)\*\*/u', 'strong'],
        ['/\*([^*\n]{1,500}?)\*/u', 'em'],
        ['/~([^~\n]{1,500}?)~/u', 'small'],
    ];
    $best = null;
    foreach ($marks as [$pattern, $tag]) {
        $offset = 0;
        while (preg_match($pattern, $safe, $m, PREG_OFFSET_CAPTURE, $offset)) {
            if ($m[1][0] !== '' && trim($m[1][0]) === $m[1][0]) {
                if ($best === null || $m[0][1] < $best[0]) {
                    $best = [$m[0][1], strlen($m[0][0]), $m[1][0], $tag];
                }
                break;
            }
            $offset = $m[0][1] + 1;
        }
    }
    if ($best === null) {
        return $safe;
    }
    [$at, $length, $inner, $tag] = $best;
    return substr($safe, 0, $at) . "<$tag>" . render_emphasis($inner) . "</$tag>" . render_emphasis(substr($safe, $at + $length));
}

/**
 * The address a written link may point to, or null. Allowed: full web addresses (https:// or
 * http://), email (mailto:) and phone (tel:) links, and this site's own pages ("/pricing.html",
 * "#section"). Anything else, such as "javascript:", is refused.
 */
function link_href(string $url): ?string
{
    $url = trim($url);
    if (preg_match('#^https?://#i', $url)) {
        return filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
    }
    if (preg_match('/^mailto:[^\s@<>"]+@[^\s@<>"]+\.[^\s@<>"]+$/i', $url) || preg_match('/^tel:\+?[0-9][0-9 ()-]{2,20}$/i', $url)) {
        return $url;
    }
    // "/page" but not "//other-site" or "/\other-site", which browsers treat as another website.
    if (preg_match('#^/(?![/\\\\])[^\s\\\\]*$#', $url) || preg_match('/^#[\w-]+$/', $url)) {
        return $url;
    }
    return null;
}

/** The site's Content-Security-Policy (sent as a header and repeated in a <meta> tag on every page). */
function csp_policy(bool $forMeta = false): string
{
    $directives = [
        "default-src 'self'",
        // Only the exact Bootstrap files the site uses, not everything on the jsDelivr CDN.
        "script-src 'self' https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js",
        "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/ https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/ https://fonts.googleapis.com",
        "font-src 'self' https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/ https://fonts.gstatic.com",
        "img-src 'self' data: https:",
        "connect-src 'self'",
        "frame-src https://www.google.com https://www.youtube-nocookie.com",
        "object-src 'none'",
        "base-uri 'self'",
        "form-action 'self' https://checkout.paystack.com",
    ];
    // frame-ancestors only works as a header; framing is also refused by X-Frame-Options.
    if (!$forMeta) {
        $directives[] = "frame-ancestors 'self'";
    }
    return implode('; ', $directives);
}

function send_security_headers(): void
{
    $csp = csp_policy();

    header('Content-Security-Policy: ' . $csp);
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    if (is_https()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}
