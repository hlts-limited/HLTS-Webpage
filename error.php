<?php
/**
 * Friendly error pages. .htaccess points ErrorDocument 403/404/500 here.
 */

require __DIR__ . '/lib/app.php';

$code = (int) ($_GET['code'] ?? ($_SERVER['REDIRECT_STATUS'] ?? 404));
$messages = [
    403 => ['This page is private', 'You do not have access to this page.'],
    404 => ['We could not find that page', 'It may have moved, or the link may be wrong. Try one of these instead.'],
    500 => ['Something went wrong on our side', 'Please try again in a moment. If it keeps happening, contact us.'],
];
[$title, $text] = $messages[$code] ?? $messages[404];
http_response_code(isset($messages[$code]) ? $code : 404);

page_start(['title' => $title . ' – HLTS Limited', 'noindex' => true, 'page' => 'error']);
?>
<section class="message-page">
  <div class="container">
    <div class="message-card">
      <div style="max-width:220px;margin:0 auto 12px"><?= infinity_svg('error') ?></div>
      <p class="eyebrow" style="justify-content:center">Error <?= (int) $code ?></p>
      <h1 class="h2"><?= h($title) ?></h1>
      <p><?= h($text) ?></p>
      <div class="actions" style="justify-content:center">
        <?= button('Home', '/', 'primary', 'house') ?>
        <?= button('For schools', page_url('services'), 'ghost', 'building') ?>
        <?= button('Courses', page_url('course'), 'ghost', 'mortarboard') ?>
        <?= button('Contact', page_url('contact'), 'ghost', 'envelope') ?>
      </div>
    </div>
  </div>
</section>
<?php page_end(); ?>
