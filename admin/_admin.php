<?php
/**
 * Shared setup for every admin page: sign-in check, layout and helpers.
 *
 *   require __DIR__ . '/_admin.php';
 *   $admin = require_admin();
 *   admin_start('Leads', 'leads');
 *   ...
 *   admin_end();
 */

require dirname(__DIR__) . '/lib/app.php';

header('X-Robots-Tag: noindex, nofollow');

const LEAD_STATUSES = ['new' => 'New', 'contacted' => 'Contacted', 'in-progress' => 'In progress', 'won' => 'Won', 'closed' => 'Closed'];

function lead_types(): array
{
    $types = [];
    foreach (form_definitions() as $key => $definition) {
        $types[$key] = $definition['title'];
    }
    return $types;
}

function admin_nav(): array
{
    // [label, icon, link, section]. A heading is printed where the section changes.
    return [
        'dashboard' => ['Dashboard', 'speedometer2', '/admin/', ''],
        'leads' => ['Form submissions', 'inbox', '/admin/leads.php', 'From the website'],
        'app-sync' => ['Staff app sync', 'arrow-repeat', '/admin/app-sync.php', 'From the website'],
        'students' => ['Students', 'person-badge', '/admin/students.php', 'Online Institution'],
        'payments' => ['Payments', 'credit-card', '/admin/payments.php', 'Online Institution'],
        'results' => ['Results', 'clipboard-data', '/admin/results.php', 'Online Institution'],
        'certificates' => ['Certificates', 'patch-check', '/admin/certificates.php', 'Online Institution'],
        'materials' => ['Course materials', 'journal-text', '/admin/content.php?type=materials', 'Online Institution'],
        'posts' => ['Blog', 'newspaper', '/admin/content.php?type=posts', 'Website content'],
        'events' => ['Events', 'calendar-event', '/admin/content.php?type=events', 'Website content'],
        'projects' => ['Portfolio', 'collection', '/admin/content.php?type=projects', 'Website content'],
        'jobs' => ['Jobs', 'briefcase', '/admin/content.php?type=jobs', 'Website content'],
        'admins' => ['Staff accounts', 'people', '/admin/admins.php', 'Access'],
    ];
}

/** Signed in AND past two-step sign-in: gets the full admin layout. Otherwise the plain sign-in card. */
function admin_full(): ?array
{
    $admin = auth_user('admin');
    return $admin && !empty($admin['totp_secret']) ? $admin : null;
}

function admin_start(string $title, string $active = ''): void
{
    $admin = admin_full();
    // The badge shows what needs a person: undelivered submissions when the staff app is connected, new leads otherwise.
    $badgeOn = app_sync_enabled() ? 'app-sync' : 'leads';
    $badge = !$admin ? 0 : (int) ($badgeOn === 'app-sync' ? db_value("SELECT COUNT(*) FROM app_sync WHERE status = 'failed'") : db_value("SELECT COUNT(*) FROM leads WHERE status = 'new'"));
    $section = '';
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <meta http-equiv="Content-Security-Policy" content="<?= h(csp_policy(true)) ?>">
  <title><?= h($title) ?> – HLTS Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&family=JetBrains+Mono:wght@100..800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?= h(asset('css/tokens.css')) ?>">
  <link rel="stylesheet" href="<?= h(asset('css/ui/base.css')) ?>">
  <link rel="stylesheet" href="<?= h(asset('css/ui/components.css')) ?>">
  <link rel="stylesheet" href="<?= h(asset('css/ui/forms.css')) ?>">
  <link rel="stylesheet" href="<?= h(asset('css/ui/pages.css')) ?>">
  <link rel="stylesheet" href="<?= h(asset('css/ui/admin.css')) ?>">
  <link rel="icon" href="/images/brand/logo-64.png">
  <script src="<?= h(asset('js/admin.js')) ?>" defer></script>
</head>
<body class="admin-body">
<?php if ($admin): ?>
  <aside class="admin-side" id="adminSide">
    <a class="brand" href="/admin/"><img src="/images/brand/logo-192.png" alt="" width="40" height="40"><span class="brand__text"><span class="brand__name">HLTS</span><span class="brand__tag">ADMIN</span></span></a>
    <nav aria-label="Admin">
<?php foreach (admin_nav() as $key => [$label, $iconName, $href, $group]): ?>
<?php if ($group !== $section): $section = $group; ?>
      <p class="admin-side__group"><?= h($group) ?></p>
<?php endif; ?>
      <a href="<?= h($href) ?>" class="<?= $key === $active ? 'is-active' : '' ?>"<?= $key === $active ? ' aria-current="page"' : '' ?>><?= icon($iconName) ?> <span><?= h($label) ?></span><?php if ($key === $badgeOn && $badge): ?><b class="badge-count"><?= $badge ?></b><?php endif; ?></a>
<?php endforeach; ?>
    </nav>
    <div class="admin-side__foot">
      <a href="/" target="_blank" rel="noopener"><?= icon('box-arrow-up-right') ?> <span>View website</span></a>
      <form method="post" action="/admin/logout.php"><?= csrf_field() ?><button type="submit"><?= icon('box-arrow-right') ?> <span>Sign out</span></button></form>
    </div>
  </aside>
  <div class="admin-main">
    <header class="admin-top">
      <button class="admin-menu" type="button" data-admin-menu aria-controls="adminSide" aria-expanded="false" aria-label="Menu"><?= icon('list') ?></button>
      <h1><?= h($title) ?></h1>
      <span class="admin-top__user"><?= icon('person-circle') ?> <?= h($admin['name']) ?></span>
    </header>
    <main class="admin-content">
<?php if ($msg = flash('admin_notice')): ?>
      <div class="notice mb-4" role="status"><?= icon('check-circle') ?><p><?= h($msg) ?></p></div>
<?php endif; ?>
<?php if ($msg = flash('admin_error')): ?>
      <div class="notice notice--error mb-4" role="alert"><?= icon('exclamation-circle') ?><p><?= h($msg) ?></p></div>
<?php endif; ?>
<?php else: ?>
  <div class="admin-auth">
<?php endif;
}

function admin_end(): void
{
    if (admin_full()) {
        echo '</main></div>';
    } else {
        echo '</div>';
    }
    echo '</body></html>';
}

function admin_notice(string $message, string $to): never
{
    flash('admin_notice', $message);
    redirect($to);
}

function admin_error(string $message, string $to): never
{
    flash('admin_error', $message);
    redirect($to);
}

function status_chip(string $status): string
{
    $class = [
        'new' => 'chip--magenta', 'pending' => 'chip--magenta', 'review' => 'chip--magenta',
        'contacted' => 'chip--violet', 'in-progress' => 'chip--violet', 'draft' => 'chip--line',
        'closed' => 'chip--line', 'failed' => 'chip--line', 'abandoned' => 'chip--line', 'revoked' => 'chip--line', 'inactive' => 'chip--line',
    ][$status] ?? '';
    return '<span class="chip ' . $class . '">' . h(ucwords(str_replace('-', ' ', $status))) . '</span>';
}

/** Every image under /images, for picking covers without file uploads. */
function image_choices(): array
{
    $choices = [];
    $root = APP_ROOT . '/images';
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if (preg_match('/\.(jpe?g|png|webp)$/i', $file->getFilename()) && $file->getSize() < 3 * 1024 * 1024) {
            $relative = 'images/' . str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1));
            $choices[$relative] = $relative;
        }
    }
    ksort($choices);
    return $choices;
}

const IMAGE_UPLOAD_MAX_BYTES = 10 * 1024 * 1024;
const IMAGE_MAX_SIDE = 2000;

/** The largest picture that can be uploaded: 10 MB, or less if the server's PHP limits are lower. */
function image_upload_limit(): int
{
    $bytes = static function (string $v): int {
        $n = (int) $v;
        return match (strtolower(substr(trim($v), -1))) { 'g' => $n << 30, 'm' => $n << 20, 'k' => $n << 10, default => $n };
    };
    $limits = array_filter([IMAGE_UPLOAD_MAX_BYTES, $bytes((string) ini_get('upload_max_filesize')), $bytes((string) ini_get('post_max_size'))]);
    return min($limits);
}

/** "4 MB", for messages. */
function megabytes(int $bytes): string
{
    return rtrim(rtrim(number_format($bytes / 1048576, 1), '0'), '.') . ' MB';
}

/**
 * Save a cover image uploaded from the admin's device into images/uploads/YYYY-MM/ under a random
 * name. Only real JPEG, PNG or WebP files are accepted (checked from the file's bytes, not its
 * name). Where the server has GD, the picture is re-saved at no more than 2000px on its longest
 * side, which also strips anything hidden inside it. Returns [path|null, error|null].
 */
function save_uploaded_image(?array $file): array
{
    $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($error === UPLOAD_ERR_NO_FILE) {
        return [null, null];
    }
    if (in_array($error, [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true) || (int) ($file['size'] ?? 0) > image_upload_limit()) {
        return [null, 'The image must be ' . megabytes(image_upload_limit()) . ' or smaller.'];
    }
    if ($error !== UPLOAD_ERR_OK || !is_uploaded_file((string) ($file['tmp_name'] ?? ''))) {
        return [null, 'The upload did not finish. Please try again.'];
    }
    $tmp = $file['tmp_name'];
    $info = @getimagesize($tmp);
    $types = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
    if (!$info || !isset($types[$info[2]]) || $info[0] < 1 || $info[1] < 1) {
        return [null, 'Upload a photo in JPG, PNG or WebP format.'];
    }
    $ext = $types[$info[2]];
    $dir = APP_ROOT . '/images/uploads/' . date('Y-m');
    if (!is_dir($dir) && !mkdir($dir, 0755, true)) {
        return [null, 'The image could not be saved on the server. Please try again.'];
    }
    $name = bin2hex(random_bytes(12)) . '.' . $ext;
    $dest = "$dir/$name";

    $saved = false;
    $loaders = ['jpg' => 'imagecreatefromjpeg', 'png' => 'imagecreatefrompng', 'webp' => 'imagecreatefromwebp'];
    if (function_exists($loaders[$ext]) && function_exists('imagecreatetruecolor')) {
        $src = @$loaders[$ext]($tmp);
        if ($src) {
            [$w, $h] = [$info[0], $info[1]];
            $scale = min(1, IMAGE_MAX_SIDE / max($w, $h));
            $nw = max(1, (int) round($w * $scale));
            $nh = max(1, (int) round($h * $scale));
            $out = imagecreatetruecolor($nw, $nh);
            if ($ext !== 'jpg') {
                imagealphablending($out, false);
                imagesavealpha($out, true);
            }
            imagecopyresampled($out, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
            $saved = match ($ext) {
                'jpg' => imagejpeg($out, $dest, 82),
                'png' => imagepng($out, $dest, 7),
                'webp' => imagewebp($out, $dest, 82),
            };
        }
    }
    if (!$saved) {
        // No GD on this server: keep the original, which must then be small enough to show.
        if ((int) $file['size'] > 3 * 1024 * 1024) {
            return [null, 'This server can’t shrink images. Upload a photo under 3 MB.'];
        }
        $saved = move_uploaded_file($tmp, $dest);
    }
    if (!$saved) {
        return [null, 'The image could not be saved on the server. Please try again.'];
    }
    @chmod($dest, 0644);
    return ['images/uploads/' . date('Y-m') . '/' . $name, null];
}

function next_student_no(): string
{
    $year = date('Y');
    $last = db_value('SELECT student_no FROM students WHERE student_no LIKE ? ORDER BY student_no DESC LIMIT 1', ["HLTS/$year/%"]);
    $number = $last ? ((int) substr((string) $last, -4)) + 1 : 1;
    return sprintf('HLTS/%s/%04d', $year, $number);
}

function safe_url(string $url): bool
{
    return (bool) preg_match('#^https?://#i', $url) && filter_var($url, FILTER_VALIDATE_URL);
}
