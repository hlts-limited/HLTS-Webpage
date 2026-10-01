<?php
/** XML sitemap for search engines. Served at /sitemap.xml by .htaccess. */

require __DIR__ . '/lib/app.php';

header('Content-Type: application/xml; charset=UTF-8');

$pages = ['index', 'services', 'school-management', 'cbt', 'staff-deployment', 'school-form', 'book-demo', 'results',
    'online-institution', 'course', 'registration-form', 'verify-certificate',
    'digital-solutions', 'portfolio', 'request-quote',
    'community', 'events', 'join-techmind',
    'about', 'blog', 'careers', 'faq', 'contact', 'terms', 'privacy'];

$urls = array_map(fn ($p) => absolute_url($p === 'index' ? '' : "$p.html"), $pages);
foreach (array_keys(courses()) as $slug) {
    $urls[] = absolute_url('course-detail.html?c=' . $slug);
}
try {
    foreach (db_all("SELECT slug FROM posts WHERE status = 'published'") as $post) {
        $urls[] = absolute_url('post.html?p=' . $post['slug']);
    }
} catch (Throwable $e) {
    // Sitemap still lists the main pages without the database.
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $url) {
    echo '  <url><loc>' . h($url) . "</loc></url>\n";
}
echo '</urlset>';
