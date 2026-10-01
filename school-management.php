<?php
require __DIR__ . '/lib/app.php';

page_start([
    'title' => 'Full School Management – HLTS Limited',
    'description' => 'Choose the modules your school needs: CBT, result management, IT support, daily operations, staff deployment, labs and curriculum, or let HLTS manage it all.',
]);

$subjects = [['Mathematics', 86, 'A'], ['English Language', 74, 'A'], ['Basic Science', 68, 'B'], ['Computer Studies', 91, 'A'], ['Civic Education', 57, 'C']];

$sheet = '<div class="result-sheet" data-result-sheet role="img" aria-label="Example report card filling in automatically">'
    . '<span class="result-sheet__stamp">' . icon('check2-circle') . ' Published to parents</span>'
    . '<div class="result-sheet__head"><div><strong>First Term Report</strong><small>JSS 2 · Adaeze O. · 2026/2027</small></div></div>'
    . '<table><thead><tr><th>Subject</th><th>Score</th><th>Grade</th></tr></thead><tbody>';
foreach ($subjects as [$subject, $score, $grade]) {
    $sheet .= '<tr><td>' . h($subject) . '</td><td data-score="' . $score . '">0</td><td><span data-grade>' . h($grade) . '</span></td></tr>';
}
$sheet .= '</tbody></table><div class="result-sheet__chart" aria-hidden="true">';
foreach ($subjects as [, $score]) {
    $sheet .= '<span data-bar="' . $score . '"></span>';
}
$sheet .= '</div></div>';

echo page_hero([
    'crumbs' => [['For Schools', page_url('services')], ['School Management']],
    'eyebrow' => 'Full school management',
    'title' => 'Run your whole school <span class="grad-text">from one plan.</span>',
    'lead' => 'Pick the modules you need today and add more as you grow. HLTS sets everything up, trains your staff and supports you every term.',
    'actions' => button('Book a demo', page_url('book-demo'), 'primary', 'calendar-check') . button('Compare packages', '#packages', 'ghost-light', 'arrow-down'),
    'visual' => $sheet,
]);
?>

<section class="section">
  <div class="container">
    <?= section_head('Modules', 'Everything a modern school runs on.', 'Each module works on its own. Together they share one set of student records, so nothing is typed twice.') ?>
    <div class="grid grid--4" data-reveal-group>
<?php foreach (school_modules() as $key => $module): ?>
      <a class="card-hl" href="<?= h(page_url('book-demo', ['interests[]' => $key])) ?>" data-reveal>
        <span class="icon-tile"><?= icon($module['icon']) ?></span>
        <h3><?= h($module['title']) ?></h3>
        <p><?= h($module['text']) ?></p>
        <span class="card-hl__foot text-link">See it in a demo <?= icon('arrow-right') ?></span>
      </a>
<?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--alt" id="packages">
  <div class="container">
    <?= section_head('Packages', 'Three ways to work with HLTS.', 'Pricing depends on school size and the modules you choose. Book a demo and we will send a clear quote within two working days.') ?>
    <div class="packages" data-reveal-group>
<?php foreach (school_packages() as $package): $featured = !empty($package['featured']); ?>
      <article class="package<?= $featured ? ' package--featured' : '' ?>" data-reveal>
<?php if ($featured): ?>
        <span class="package__badge">Most popular</span>
<?php endif; ?>
        <h3><?= h($package['name']) ?></h3>
        <p class="package__for"><?= h($package['for']) ?></p>
        <ul class="check-list">
<?php foreach ($package['items'] as $item): ?>
          <li><?= h($item) ?></li>
<?php endforeach; ?>
        </ul>
        <?= button('Get a quote', page_url('book-demo'), $featured ? 'light' : 'secondary', 'arrow-right') ?>
      </article>
<?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="container split split--top">
    <div>
      <?= section_head('Onboarding', 'Live in one to two weeks.', 'A clear, guided start so your staff feel confident from day one.', 'left') ?>
      <div class="actions" data-reveal><?= button('Start with a demo', page_url('book-demo'), 'primary', 'calendar-check') ?></div>
    </div>
    <ol class="path" data-path>
      <span class="path__progress" aria-hidden="true"></span>
      <li class="path__step"><span class="path__dot">1</span><div><h3>Discovery visit</h3><p>We meet your leadership team, look at how things run today and agree priorities.</p></div></li>
      <li class="path__step"><span class="path__dot">2</span><div><h3>Setup and data</h3><p>We configure your modules and move existing student and staff records across.</p></div></li>
      <li class="path__step"><span class="path__dot">3</span><div><h3>Staff training</h3><p>Hands-on sessions for administrators and teachers, on site or online.</p></div></li>
      <li class="path__step"><span class="path__dot">4</span><div><h3>Go live and review</h3><p>We stay close during the first weeks, then review results with you each term.</p></div></li>
    </ol>
  </div>
</section>

<?= cta_band('Not sure which modules you need?', 'Tell us about your school and we will recommend a starting point. No obligation.', ['Book a free demo', page_url('book-demo')], ['Register your school', page_url('school-form')]) ?>

<?php page_end(); ?>
