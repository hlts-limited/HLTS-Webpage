<?php
require __DIR__ . '/lib/app.php';

page_start([
    'title' => 'Tech Courses – HLTS Online Institution',
    'description' => 'Practical courses in front-end, back-end and full-stack web development, data analysis, graphic design, video editing, desktop publishing and block-based programming.',
]);

$tracks = array_unique(array_map(fn ($c) => $c['track'], courses()));

echo page_hero([
    'crumbs' => [['Online Institution', page_url('online-institution')], ['Courses']],
    'eyebrow' => 'Course catalogue',
    'title' => 'Choose a skill. <span class="grad-text">Build what comes next.</span>',
    'lead' => 'Practical technology courses with guidance, projects and a verifiable certificate. Learn online or in person in Lagos.',
    'actions' => button('Register now', page_url('registration-form'), 'primary', 'person-plus') . button('Talk to an adviser', 'https://wa.me/' . config('whatsapp'), 'ghost-light', 'whatsapp'),
]);
?>

<section class="section">
  <div class="container">
    <div class="filter-chips" data-filter-group="course-grid" role="group" aria-label="Filter courses">
      <button type="button" data-filter="all" aria-pressed="true">All courses</button>
<?php foreach ($tracks as $track): ?>
      <button type="button" data-filter="<?= h(slugify($track)) ?>" aria-pressed="false"><?= h($track) ?></button>
<?php endforeach; ?>
    </div>
    <div class="grid grid--3" id="course-grid" data-reveal-group>
<?php foreach (courses() as $slug => $course): ?>
      <?= course_card($slug, $course) ?>
<?php endforeach; ?>
    </div>
    <p class="center small muted mt-4" data-reveal>A session is 8 months, made up of two 4-month semesters. Pay once for the session, per semester (2 payments) or monthly (8 payments). Ask us about group discounts and school partnerships.</p>
  </div>
</section>

<?= cta_band('Not sure which course fits?', 'Tell us your goals and an adviser will recommend a path. It takes five minutes on WhatsApp.', ['Chat with an adviser', 'https://wa.me/' . config('whatsapp')], ['Register now', page_url('registration-form')]) ?>

<?php page_end(); ?>
