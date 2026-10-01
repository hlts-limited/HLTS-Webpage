<?php
require __DIR__ . '/lib/app.php';

$jobs = db_all("SELECT * FROM jobs WHERE status = 'published' AND (closes_on IS NULL OR closes_on = '' OR closes_on >= ?) ORDER BY created_at DESC", [date('Y-m-d')]);
$selected = is_string($_GET['job'] ?? null) ? $_GET['job'] : '';
$jobTitles = array_column($jobs, 'title', 'slug');
$applyFor = $jobTitles[$selected] ?? 'General application (talent pool)';

page_start([
    'title' => 'Careers at HLTS Limited',
    'description' => 'Work with HLTS: open roles in education, technology and school operations, and our talent pool for school placements.',
    'form' => 'career',
]);

echo page_hero([
    'crumbs' => [['Company'], ['Careers']],
    'eyebrow' => 'Careers',
    'title' => 'Help schools and learners <span class="grad-text">do more with technology.</span>',
    'lead' => 'We hire educators, technologists and operations people who care about impact. Apply for an open role or join our talent pool for school placements.',
    'actions' => button('Open roles', '#roles', 'primary', 'arrow-down') . button('Join the talent pool', '#apply', 'ghost-light', 'person-plus'),
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

<section class="section section--alt" id="apply">
  <div class="container">
    <div class="form-page">
      <?= form_aside('Apply', 'Applying for: ' . $applyFor, 'Share a link to your CV or LinkedIn profile. We read every application.', [
          ['search', 'We review', 'Within two weeks, we check your experience against open and upcoming roles.'],
          ['camera-video', 'We talk', 'A short call, then an interview and a practical task.'],
          ['person-check', 'You join', 'Induction and training before you start.'],
      ]) ?>
      <div class="form-shell" data-reveal="zoom">
        <div class="form-shell__head"><h2>Your application</h2><p><?= h($applyFor) ?></p></div>
        <?= form_open('career') ?>
          <?= form_field('career', 'job', ['value' => $applyFor]) ?>
          <div class="form-body"><div class="form-grid"><?= form_fields('career', ['name', 'email', 'phone', 'cv_url', 'message', 'terms']) ?></div>
          <div class="form-nav"><?= submit_button('Send application', 'send') ?></div></div>
          <?= success_panel() ?>
        </form>
      </div>
    </div>
  </div>
</section>

<?php page_end(); ?>
