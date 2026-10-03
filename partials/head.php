<?php
/**
 * Shared <head> and opening <body>. Called by page_start() in lib/ui.php,
 * which fills $GLOBALS['page'] with the title, description and page name.
 */

$meta = $GLOBALS['page'];
$canonical = absolute_url($meta['page'] === 'index' ? '' : $meta['page'] . '.html');
if (!empty($meta['canonical'])) {
    $canonical = absolute_url($meta['canonical']);
}
$stylesheets = [
    'css/tokens.css',
    'css/ui/base.css',
    'css/ui/components.css',
    'css/ui/forms.css',
    'css/ui/sections.css',
    'css/ui/pages.css',
    'css/ui/motion.css',
];
?>
<!DOCTYPE html>
<html lang="en-NG">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= h($meta['title']) ?></title>
    <meta name="description" content="<?= h($meta['description']) ?>">
    <meta name="theme-color" content="#110e2b">
<?php if ($meta['noindex']): ?>
    <meta name="robots" content="noindex">
<?php endif; ?>
    <link rel="canonical" href="<?= h($canonical) ?>">

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="HLTS Limited">
    <meta property="og:title" content="<?= h($meta['title']) ?>">
    <meta property="og:description" content="<?= h($meta['description']) ?>">
    <meta property="og:image" content="<?= h(absolute_url(img($meta['image']))) ?>">
    <meta property="og:url" content="<?= h($canonical) ?>">
    <meta name="twitter:card" content="summary_large_image">

    <!-- Hostinger's server replaces the policy header with its own, so the policy is repeated here; browsers enforce both. -->
    <meta http-equiv="Content-Security-Policy" content="<?= h(csp_policy(true)) ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Sora:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<?php foreach ($stylesheets as $stylesheet): ?>
    <link rel="stylesheet" href="<?= h(asset($stylesheet)) ?>">
<?php endforeach; ?>

    <script src="<?= h(asset('js/boot.js')) ?>"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous" defer></script>
    <script src="<?= h(asset('js/app.js')) ?>" defer></script>
    <script src="<?= h(asset('js/motion.js')) ?>" defer></script>
<?php foreach ($meta['scripts'] as $script): ?>
    <script src="<?= h(asset($script)) ?>" defer></script>
<?php endforeach; ?>

    <link rel="icon" href="/images/brand/logo-64.png" type="image/png">
    <link rel="apple-touch-icon" href="/images/brand/logo-192.png">

    <script type="application/ld+json"><?= json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'EducationalOrganization',
        'name' => 'HLTS Limited',
        'url' => config('site_url'),
        'logo' => absolute_url('images/logoh.png'),
        'telephone' => config('phone'),
        'email' => config('email_public'),
        'address' => ['@type' => 'PostalAddress', 'streetAddress' => '8 Assembly Close, Folagoro', 'addressLocality' => 'Somolu, Lagos', 'addressCountry' => 'NG'],
        'sameAs' => [
            'https://web.facebook.com/profile.php?id=61551105837140',
            'https://www.instagram.com/hltslimited/',
            'https://www.linkedin.com/company/high-level-tech-services-limited',
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) ?></script>
  </head>

  <body class="<?= h(trim('page-' . $meta['page'] . ' ' . $meta['body_class'])) ?>">
    <a class="skip-link" href="#main">Skip to content</a>
