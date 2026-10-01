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
    return [
        'dashboard' => ['Dashboard', 'speedometer2', '/admin/'],
        'leads' => ['Leads & enquiries', 'inbox', '/admin/leads.php'],
        'students' => ['Students', 'person-badge', '/admin/students.php'],
        'payments' => ['Payments', 'credit-card', '/admin/payments.php'],
        'results' => ['Results', 'clipboard-data', '/admin/results.php'],
        'certificates' => ['Certificates', 'patch-check', '/admin/certificates.php'],
        'posts' => ['Insights', 'newspaper', '/admin/content.php?type=posts'],
        'events' => ['Events', 'calendar-event', '/admin/content.php?type=events'],
        'projects' => ['Portfolio', 'collection', '/admin/content.php?type=projects'],
        'jobs' => ['Jobs', 'briefcase', '/admin/content.php?type=jobs'],
        'materials' => ['Course materials', 'journal-text', '/admin/content.php?type=materials'],
        'admins' => ['Staff accounts', 'people', '/admin/admins.php'],
    ];
}

function admin_start(string $title, string $active = ''): void
{
    $admin = auth_user('admin');
    $newLeads = $admin ? (int) db_value("SELECT COUNT(*) FROM leads WHERE status = 'new'") : 0;
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="robots" content="noindex, nofollow">
  <title><?= h($title) ?> – HLTS Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@600;700&display=swap" rel="stylesheet">
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
<?php foreach (admin_nav() as $key => [$label, $iconName, $href]): ?>
      <a href="<?= h($href) ?>" class="<?= $key === $active ? 'is-active' : '' ?>"<?= $key === $active ? ' aria-current="page"' : '' ?>><?= icon($iconName) ?> <span><?= h($label) ?></span><?php if ($key === 'leads' && $newLeads): ?><b class="badge-count"><?= $newLeads ?></b><?php endif; ?></a>
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
    if (auth_user('admin')) {
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
