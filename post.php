<?php
require __DIR__ . '/lib/app.php';

$slug = is_string($_GET['p'] ?? null) ? $_GET['p'] : '';
$post = db_one("SELECT * FROM posts WHERE slug = ? AND status = 'published'", [$slug]);

if (!$post) {
    http_response_code(404);
    render_message_page('Post not found', 'This post may have moved or is not published yet.', false, page_url('blog'), 'See the blog');
    exit;
}

$more = db_all("SELECT * FROM posts WHERE status = 'published' AND id <> ? ORDER BY published_at DESC LIMIT 3", [$post['id']]);
$words = str_word_count(strip_tags($post['body']));

page_start([
    'title' => $post['title'] . ' – HLTS Blog',
    'description' => $post['excerpt'],
    'canonical' => 'post.html?p=' . $post['slug'],
    'image' => $post['cover'] ?: 'images/logoh.png',
    'page' => 'post',
    'body_class' => 'blog-dark',
]);

echo page_hero([
    'crumbs' => [['Blog', page_url('blog')], [$post['title']]],
    'eyebrow' => $post['category'] ?: 'Blog',
    'title' => h($post['title']),
    'lead' => $post['excerpt'],
    'note' => icon('calendar3') . ' ' . h(format_date($post['published_at'])) . ' &nbsp;·&nbsp; ' . icon('clock') . ' ' . max(1, (int) round($words / 200)) . ' min read',
]);
?>

<section class="section">
  <div class="container" style="max-width: 900px">
<?php if ($video = youtube_id((string) ($post['video_url'] ?? ''))): ?>
    <div class="post-video" data-reveal="zoom"><?= youtube_player($video, $post['title']) ?></div>
<?php elseif ($post['cover']): ?>
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
<section class="section blog-section">
  <div class="container">
    <h2 class="blog-section__title">More from the blog</h2>
    <div class="blog-grid" data-reveal-group>
<?php foreach ($more as $item): ?>
      <?= blog_card($item) ?>
<?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php page_end(); ?>
