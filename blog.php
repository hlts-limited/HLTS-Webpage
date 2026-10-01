<?php
require __DIR__ . '/lib/app.php';

$posts = db_all("SELECT * FROM posts WHERE status = 'published' ORDER BY published_at DESC, id DESC");

page_start([
    'title' => 'Insights – Education and technology | HLTS',
    'description' => 'Practical ideas from HLTS on digital learning, CBT, school operations and technology skills.',
]);

echo page_hero([
    'crumbs' => [['Company'], ['Insights']],
    'eyebrow' => 'Insights',
    'title' => 'Ideas for schools, <span class="grad-text">learners and builders.</span>',
    'lead' => 'Practical advice on digital learning, assessments, school operations and technology skills.',
]);
?>

<section class="section">
  <div class="container">
<?php if ($posts): ?>
    <div class="grid grid--3" data-reveal-group>
<?php foreach ($posts as $post): ?>
      <a class="post-card" href="<?= h(page_url('post', ['p' => $post['slug']])) ?>" data-reveal>
        <div class="post-card__media"><?php if ($post['cover']): ?><img src="<?= h(img($post['cover'])) ?>" alt="" loading="lazy"><?php endif; ?></div>
        <div class="post-card__body">
          <div class="post-card__meta"><?php if ($post['category']): ?><span class="chip chip--violet"><?= h($post['category']) ?></span><?php endif; ?><span><?= h(format_date($post['published_at'])) ?></span></div>
          <h3><?= h($post['title']) ?></h3>
          <p><?= h($post['excerpt']) ?></p>
          <span class="text-link mt-auto">Read article <?= icon('arrow-right') ?></span>
        </div>
      </a>
<?php endforeach; ?>
    </div>
<?php else: ?>
    <?= empty_state('newspaper', 'First articles coming soon', 'Subscribe at the bottom of the page and we will email you when we publish.') ?>
<?php endif; ?>
  </div>
</section>

<?php page_end(); ?>
