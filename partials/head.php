<?php
/**
 * Shared <head> and opening <body> for every public page.
 *
 * Set these before including:
 *   $pageTitle        text for <title> and social cards
 *   $pageDescription  meta description for search results and social cards
 *   $bodyClass        optional class on <body>
 */

if (!function_exists('h')) {
    function h($value) {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

$siteUrl = 'https://hltsltd.com';
$currentPage = basename($_SERVER['SCRIPT_NAME'] ?? 'index.php', '.php');
$pageTitle = $pageTitle ?? 'HLTS Limited - Your Partner in Education';
$pageDescription = $pageDescription ?? 'HLTS Limited is a Lagos-based EdTech company supporting primary and secondary schools with technology, operations, staff and digital learning.';
$bodyClass = $bodyClass ?? '';
$canonicalUrl = $siteUrl . '/' . ($currentPage === 'index' ? '' : $currentPage . '.html');

$stylesheets = [
    'css/tokens.css',
    'css/base.css',
    'css/components.css',
    'css/pages.css',
    'css/dashboard-student.css',
    'css/pages-extra.css',
    'css/design-layer.css',
    'css/dashboard-admin.css',
    'css/responsive.css',
];
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= h($pageTitle) ?></title>
    <meta name="description" content="<?= h($pageDescription) ?>">
    <meta name="author" content="HLTS Limited">
    <link rel="canonical" href="<?= h($canonicalUrl) ?>">

    <!-- Social sharing -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="HLTS Limited">
    <meta property="og:title" content="<?= h($pageTitle) ?>">
    <meta property="og:description" content="<?= h($pageDescription) ?>">
    <meta property="og:image" content="<?= h($siteUrl) ?>/images/logoh.png">
    <meta property="og:url" content="<?= h($canonicalUrl) ?>">
    <meta name="twitter:card" content="summary_large_image">

    <!-- Preconnect for performance -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preconnect" href="https://unpkg.com">

    <!-- Fonts: Poppins for headings, Inter for body text -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css">

    <!-- Site styles, in cascade order -->
<?php foreach ($stylesheets as $stylesheet): ?>
    <link rel="stylesheet" href="<?= h($stylesheet) ?>">
<?php endforeach; ?>

    <link rel="icon" href="images/logoh.png" type="image/png">
  </head>

  <body<?= $bodyClass !== '' ? ' class="' . h($bodyClass) . '"' : '' ?>>
