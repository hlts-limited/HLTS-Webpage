<?php
require __DIR__ . '/lib/app.php';

$jobs = db_all("SELECT * FROM jobs WHERE status = 'published' AND (closes_on IS NULL OR closes_on = '' OR closes_on >= ?) ORDER BY created_at DESC", [date('Y-m-d')]);
$selected = is_string($_GET['job'] ?? null) ? $_GET['job'] : '';
$jobTitles = array_column($jobs, 'title', 'slug');
// A vacancy application only shows when someone picks an open role; everyone else joins the talent pool.
$applyFor = $jobTitles[$selected] ?? null;

page_start([
    'title' => 'Careers at HLTS Limited',
    'description' => 'Work with HLTS: open roles in education, technology and school operations, and our talent pool for school placements.',
    'form' => $applyFor ? 'career' : 'candidate',
]);

echo page_hero([
    'crumbs' => [['Company'], ['Careers']],
    'eyebrow' => 'Careers',
    'title' => 'Help schools and learners <span class="grad-text">do more with technology.</span>',
    'lead' => 'We hire educators, technologists and operations people who care about impact. Apply for an open role or join our talent pool for school placements.',
    'actions' => button('Open roles', '#roles', 'primary', 'arrow-down') . button('Join the talent pool', '#talent-pool', 'ghost-light', 'person-plus'),
]);
?>

<section class="section" id="roles">
  <div class="container">
    <?= section_head('Open roles', 'Current opportunities', '', 'left') ?>
<?php if ($jobs): ?>
    <div class="list-stack" data-reveal-group>
<?php foreach ($jobs as $job): ?>
      <article class="list-card" data-reveal>
        <div>
          <h3><?= h($job['title']) ?></h3>
          <p><?= h($job['summary']) ?></p>
          <div class="list-card__meta">
            <span class="chip"><?= icon('briefcase') ?> <?= h($job['job_type']) ?></span>
            <span class="chip chip--line"><?= icon('geo-alt') ?> <?= h($job['location']) ?></span>
<?php if ($job['closes_on']): ?>
            <span class="chip chip--magenta"><?= icon('calendar-x') ?> Closes <?= h(format_date($job['closes_on'])) ?></span>
<?php endif; ?>
          </div>
        </div>
        <?= button('Apply', page_url('careers', ['job' => $job['slug']]) . '#apply', 'primary', 'arrow-right') ?>
<?php if ($job['body']): ?>
        <details><summary>Role details</summary><div class="prose small mt-2"><?= render_text($job['body']) ?></div></details>
<?php endif; ?>
      </article>
<?php endforeach; ?>
    </div>
<?php else: ?>
    <?= empty_state('briefcase', 'No open roles right now', 'We regularly place ICT facilitators, teachers and technicians in partner schools. Join the talent pool below and we will contact you when something fits.') ?>
<?php endif; ?>
  </div>
</section>

<?php if ($applyFor): ?>
<section class="section section--alt" id="apply">
  <div class="container">
    <div class="form-page">
      <?= form_aside('Apply', 'Applying for: ' . $applyFor, 'Upload your CV and tell us why you fit. We read every application.', [
          ['search', 'We review', 'Within two weeks, we check your experience against the role.'],
          ['whatsapp', 'We talk', 'Shortlisted candidates hear from us on WhatsApp, then a short call and an interview.'],
          ['person-check', 'You join', 'Induction and training before you start.'],
      ]) ?>
      <div class="form-shell" data-reveal="zoom">
        <div class="form-shell__head"><h2>Your application</h2><p><?= h($applyFor) ?></p></div>
        <?= form_open('career') ?>
          <?= form_field('career', 'job', ['value' => $applyFor]) ?>
          <div class="form-body"><div class="form-grid"><?= form_fields('career', ['name', 'email', 'phone', 'whatsapp', 'cv', 'cv_url', 'message', 'terms']) ?></div>
          <div class="form-nav"><?= submit_button('Send application', 'send') ?></div></div>
          <?= success_panel() ?>
        </form>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<section class="section<?= $applyFor ? '' : ' section--alt' ?>" id="talent-pool">
  <div class="container">
    <div class="form-page">
      <?= form_aside('Talent pool', 'Looking for a job with HLTS?', 'Register once with your CV. When a school placement or role fits you, we contact you first.', [
          ['person-vcard', 'Tell us about you', 'The roles you want, your experience and where you live.'],
          ['file-earmark-text', 'Upload your CV', 'PDF or Word, up to 4 MB. Only our HR team can see it.'],
          ['whatsapp', 'Hear from us', 'We reach shortlisted candidates on WhatsApp.'],
      ]) ?>
      <div class="form-shell" data-reveal="zoom">
        <div class="form-shell__head"><h2>Join the talent pool</h2><p>Takes about three minutes.</p></div>
        <?= stepped_form('candidate', [
            ['title' => 'Role', 'heading' => 'What work are you looking for?', 'fields' => ['roles', 'qualification', 'experience', 'skills']],
            ['title' => 'Location', 'heading' => 'Where can you work?', 'fields' => ['state', 'area', 'relocate', 'start', 'salary']],
            ['title' => 'Contact', 'heading' => 'How can we reach you?', 'fields' => ['name', 'email', 'phone', 'whatsapp']],
            ['title' => 'CV', 'heading' => 'Your CV', 'fields' => ['cv', 'about', 'terms']],
        ], 'Join the talent pool') ?>
      </div>
    </div>
  </div>
</section>

<?php page_end(); ?>
