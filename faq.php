<?php
require __DIR__ . '/lib/app.php';

page_start([
    'title' => 'FAQs – HLTS Limited',
    'description' => 'Answers to common questions about HLTS school services, courses, payments, certificates, results and TechMind Africa.',
]);

$search = '<div class="search-box" style="flex:1">' . icon('search') . '<label class="visually-hidden" for="faq-search">Search questions</label><input id="faq-search" type="search" placeholder="Search, e.g. results, payment, certificate" data-faq-search autocomplete="off"></div>';

echo page_hero([
    'crumbs' => [['Company'], ['FAQs']],
    'eyebrow' => 'Help centre',
    'title' => 'Answers for <span class="grad-text">your next step.</span>',
    'lead' => 'Quick answers about HLTS, our school services, courses and community.',
    'actions' => $search,
]);
?>

<section class="section">
  <div class="container" style="max-width: 860px">
<?php foreach (faqs() as $group => $items): ?>
    <div class="faq-group" data-reveal>
      <h2><?= h($group) ?></h2>
<?php foreach ($items as $i => [$question, $answer]): ?>
      <details class="faq-item">
        <summary><?= h($question) ?></summary>
        <div class="faq-item__body"><p><?= $answer /* trusted, written in lib/content.php */ ?></p></div>
      </details>
<?php endforeach; ?>
    </div>
<?php endforeach; ?>
    <div data-faq-empty hidden><?= empty_state('search', 'No matching questions', 'Try another word, or ask us directly.', button('Contact us', page_url('contact'), 'primary', 'arrow-right')) ?></div>
  </div>
</section>

<?= cta_band('Still have a question?', 'Our team usually replies within one working day.', ['Contact us', page_url('contact')], ['WhatsApp', 'https://wa.me/' . config('whatsapp'), 'whatsapp']) ?>

<?php page_end(); ?>
