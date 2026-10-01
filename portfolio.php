<?php
require __DIR__ . '/lib/app.php';

$projects = db_all("SELECT * FROM projects WHERE status = 'published' ORDER BY sort_order, created_at DESC");
$categories = array_values(array_unique(array_filter(array_column($projects, 'category'))));

page_start([
    'title' => 'Portfolio – HLTS Digital Solutions',
    'description' => 'Websites, portals, apps and design projects delivered by HLTS for schools and organisations.',
]);

echo page_hero([
    'crumbs' => [['Digital Solutions', page_url('digital-solutions')], ['Portfolio']],
    'eyebrow' => 'Portfolio',
    'title' => 'Work we are <span class="grad-text">proud of.</span>',
    'lead' => 'A selection of websites, portals, apps and designs we have delivered.',
    'actions' => button('Start your project', page_url('request-quote'), 'primary', 'arrow-right'),
]);
?>

<section class="section">
  <div class="container">
<?php if ($projects): ?>
<?php if (count($categories) > 1): ?>
    <div class="filter-chips" data-filter-group="project-grid" role="group" aria-label="Filter projects">
      <button type="button" data-filter="all" aria-pressed="true">All</button>
<?php foreach ($categories as $category): ?>
      <button type="button" data-filter="<?= h(slugify($category)) ?>" aria-pressed="false"><?= h($category) ?></button>
<?php endforeach; ?>
    </div>
<?php endif; ?>
    <div class="grid grid--3" id="project-grid" data-reveal-group>
<?php foreach ($projects as $project): ?>
      <article class="post-card" data-category="<?= h(slugify($project['category'])) ?>" data-reveal>
        <div class="post-card__media"><?php if ($project['cover']): ?><img src="<?= h(img($project['cover'])) ?>" alt="" loading="lazy"><?php endif; ?></div>
        <div class="post-card__body">
          <div class="post-card__meta"><span class="chip chip--violet"><?= h($project['category']) ?></span><span><?= h($project['client']) ?></span></div>
          <h3><?= h($project['title']) ?></h3>
          <p><?= h($project['summary']) ?></p>
<?php if ($project['results']): ?>
          <p class="small"><strong class="grad-text"><?= icon('graph-up-arrow') ?> <?= h($project['results']) ?></strong></p>
<?php endif; ?>
<?php if ($project['body']): ?>
          <details><summary class="text-link">Read the story</summary><div class="prose small mt-2"><?= render_text($project['body']) ?></div></details>
<?php endif; ?>
<?php if ($project['url']): ?>
          <a class="text-link mt-2" href="<?= h($project['url']) ?>" target="_blank" rel="noopener">Visit project <?= icon('arrow-up-right') ?></a>
<?php endif; ?>
        </div>
      </article>
<?php endforeach; ?>
    </div>
<?php else: ?>
    <?= empty_state('collection', 'Our portfolio is being updated', 'We are adding recent projects. Ask us for examples relevant to your project and we will share them directly.', button('Ask for examples', page_url('request-quote'), 'primary', 'arrow-right')) ?>
<?php endif; ?>
  </div>
</section>

<?= cta_band('Want something like this?', 'Tell us about your idea. We will help you shape it, then build it.', ['Request a quote', page_url('request-quote')]) ?>

<?php page_end(); ?>
