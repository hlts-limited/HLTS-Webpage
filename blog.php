<?php
require __DIR__ . '/lib/app.php';

$categories = array_column(db_all("SELECT DISTINCT category FROM posts WHERE status = 'published' AND category IS NOT NULL AND category <> '' ORDER BY category"), 'category');
$category = is_string($_GET['category'] ?? null) && in_array($_GET['category'], $categories, true) ? $_GET['category'] : '';
$posts = $category !== ''
    ? db_all("SELECT * FROM posts WHERE status = 'published' AND category = ? ORDER BY published_at DESC, id DESC", [$category])
    : db_all("SELECT * FROM posts WHERE status = 'published' ORDER BY published_at DESC, id DESC");
$featured = $posts ? array_shift($posts) : null;
$videos = $category === '' ? youtube_videos(6) : [];

page_start([
    'title' => 'Blog – Technology news, ideas and know-how | HLTS',
    'description' => 'The HLTS blog: technology news and practical ideas on software, security, digital skills, education and the work we do across Africa.',
    'body_class' => 'blog-dark',
]);
?>

<section class="blog-hero">
  <!-- The video loads from js/app.js, and not at all on slow connections, data saver or reduced motion. -->
  <video class="blog-hero__video" data-blog-video="/media/blog-hero.mp4" muted loop playsinline preload="none" aria-hidden="true"></video>
  <div class="blog-hero__shade" aria-hidden="true"></div>
  <div class="container blog-hero__copy">
    <?= breadcrumbs([['Blog']]) ?>
    <p class="blog-label" data-reveal>// HLTS Blog</p>
    <h1 data-reveal data-reveal-delay="1">Technology news, ideas and know-how.</h1>
    <p class="blog-hero__lead" data-reveal data-reveal-delay="2">What is happening in tech, and what it means for schools, businesses and learners across Africa. Written by the HLTS team.</p>
<?php if ($categories): ?>
    <nav class="blog-filters" aria-label="Blog categories" data-reveal data-reveal-delay="3">
      <a href="<?= h(page_url('blog')) ?>"<?= $category === '' ? ' class="is-active" aria-current="page"' : '' ?>>All</a>
<?php foreach ($categories as $c): ?>
      <a href="<?= h(page_url('blog', ['category' => $c])) ?>"<?= $category === $c ? ' class="is-active" aria-current="page"' : '' ?>><?= h($c) ?></a>
<?php endforeach; ?>
    </nav>
<?php endif; ?>
  </div>
  <a class="blog-hero__credit" href="https://www.vecteezy.com/free-videos/news" target="_blank" rel="noopener">Video: Vecteezy</a>
</section>

<section class="section blog-section">
  <div class="container">
<?php if ($featured): ?>
    <?= blog_card($featured, true) ?>
<?php if ($posts): ?>
    <h2 class="blog-section__title"><?= $category !== '' ? h($category) : 'Latest posts' ?></h2>
    <div class="blog-grid" data-reveal-group>
<?php foreach ($posts as $post): ?>
      <?= blog_card($post) ?>
<?php endforeach; ?>
    </div>
<?php endif; ?>
<?php else: ?>
    <?= empty_state('newspaper', 'First posts coming soon', 'Subscribe at the bottom of the page and we will email you when we publish.') ?>
<?php endif; ?>
<?php if ($category === ''): ?>
    <?= videos_section($videos, 'Latest videos', true) ?>
<?php endif; ?>
  </div>
</section>

<?php page_end(); ?>
