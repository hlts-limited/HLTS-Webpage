<?php
require __DIR__ . '/lib/app.php';

$slug = is_string($_GET['p'] ?? null) ? $_GET['p'] : '';
$post = db_one("SELECT * FROM posts WHERE slug = ? AND status = 'published'", [$slug]);

if (!$post) {
    http_response_code(404);
    render_message_page('Article not found', 'This article may have moved or is not published yet.', false, page_url('blog'), 'See all insights');
    exit;
}

$more = db_all("SELECT * FROM posts WHERE status = 'published' AND id <> ? ORDER BY published_at DESC LIMIT 3", [$post['id']]);
$words = str_word_count(strip_tags($post['body']));

page_start([
    'title' => $post['title'] . ' – HLTS Insights',
    'description' => $post['excerpt'],
    'canonical' => 'post.html?p=' . $post['slug'],
    'image' => $post['cover'] ?: 'images/logoh.png',
    'page' => 'post',
]);

echo page_hero([
    'crumbs' => [['Insights', page_url('blog')], [$post['title']]],
    'eyebrow' => $post['category'] ?: 'Insights',
    'title' => h($post['title']),
    'lead' => $post['excerpt'],
    'note' => icon('calendar3') . ' ' . h(format_date($post['published_at'])) . ' &nbsp;·&nbsp; ' . icon('clock') . ' ' . max(1, (int) round($words / 200)) . ' min read',
]);
?>

<section class="section">
  <div class="container" style="max-width: 900px">
<?php if ($post['cover']): ?>
    <figure class="post-cover" data-reveal="zoom"><img src="<?= h(img($post['cover'])) ?>" alt=""></figure>
<?php endif; ?>
    <article class="prose" data-reveal><?= render_text($post['body']) ?></article>
    <div class="actions mt-5">
      <?= button('Share on WhatsApp', 'https://wa.me/?text=' . rawurlencode($post['title'] . ' ' . absolute_url('post.html?p=' . $post['slug'])), 'secondary', 'whatsapp') ?>
      <?= button('Share on LinkedIn', 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode(absolute_url('post.html?p=' . $post['slug'])), 'ghost', 'linkedin') ?>
    </div>
  </div>
</section>

<?php if ($more): ?>
<section class="section section--alt">
  <div class="container">
    <?= section_head('Keep reading', 'More insights') ?>
    <div class="grid grid--3" data-reveal-group>
<?php foreach ($more as $item): ?>
      <a class="post-card" href="<?= h(page_url('post', ['p' => $item['slug']])) ?>" data-reveal>
        <div class="post-card__media"><?php if ($item['cover']): ?><img src="<?= h(img($item['cover'])) ?>" alt="" loading="lazy"><?php endif; ?></div>
        <div class="post-card__body"><h3><?= h($item['title']) ?></h3><p><?= h($item['excerpt']) ?></p></div>
      </a>
<?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php page_end(); ?>
