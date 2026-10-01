<?php
require __DIR__ . '/lib/app.php';

page_start([
    'title' => 'CBT Exam Setup & Management – HLTS Limited',
    'description' => 'HLTS sets up and manages computer-based tests for schools: question banks, scheduling, secure student access, automatic marking and result reports.',
]);

$questions = [
    ['q' => 'Simplify: 3(x + 4) − 2x', 'options' => ['x + 12', '5x + 4', 'x + 4', '3x + 12'], 'answer' => 0],
    ['q' => 'Which organ pumps blood around the body?', 'options' => ['Lungs', 'Heart', 'Liver', 'Kidney'], 'answer' => 1],
    ['q' => 'Antonym of "scarce"', 'options' => ['Rare', 'Plentiful', 'Little', 'Thin'], 'answer' => 1],
    ['q' => 'A byte is made up of…', 'options' => ['4 bits', '16 bits', '8 bits', '2 bits'], 'answer' => 2],
];

$demo = '<div class="cbt-demo" data-cbt-demo data-questions="' . h(json_encode($questions)) . '" role="img" aria-label="Animated example of a CBT exam being taken and marked">'
    . '<div class="cbt-demo__top"><span class="cbt-demo__brand">' . icon('ui-checks-grid') . ' HLTS CBT · JSS 3 Test</span><span class="cbt-demo__timer">' . icon('stopwatch') . ' <span data-cbt-timer>30:00</span></span></div>'
    . '<div class="cbt-demo__progress"><span data-cbt-progress></span></div>'
    . '<div class="cbt-demo__body" aria-hidden="true"><p class="cbt-demo__num" data-cbt-num></p><p class="cbt-demo__q" data-cbt-question></p><ol class="cbt-demo__options" data-cbt-options></ol></div>'
    . '<div class="cbt-demo__dots" data-cbt-dots aria-hidden="true"></div>'
    . '<div class="cbt-demo__result" data-cbt-result hidden><svg class="tick-anim" viewBox="0 0 52 52" aria-hidden="true"><circle cx="26" cy="26" r="24"/><path d="M15 27 l7 7 l15 -16"/></svg><strong data-cbt-score>0%</strong><span>Marked instantly · Result saved</span></div>'
    . '</div>';

echo page_hero([
    'crumbs' => [['For Schools', page_url('services')], ['CBT & Assessments']],
    'eyebrow' => 'CBT & assessments',
    'title' => 'Make exam delivery <span class="grad-text">reliable, fast and fair.</span>',
    'lead' => 'HLTS sets up and runs computer-based tests for your school, from question banks and secure logins to instant marking and reports.',
    'actions' => button('Plan your CBT setup', page_url('book-demo', ['interests[]' => 'cbt']), 'primary', 'calendar-check') . button('How it works', '#workflow', 'ghost-light', 'arrow-down'),
    'visual' => $demo,
]);

$capabilities = [
    ['gear', 'Platform setup', 'Deployment and configuration tailored to your school, on your network or in the cloud.'],
    ['archive', 'Question banks', 'Upload, organise and reuse questions by subject, class and topic.'],
    ['calendar-event', 'Exam scheduling', 'Set timing, duration and access for different classes and arms.'],
    ['person-lock', 'Secure student access', 'Individual logins, shuffled questions and live monitoring protect integrity.'],
    ['check2-all', 'Automatic marking', 'Objective questions are marked instantly; teachers review written answers.'],
    ['bar-chart', 'Analytics & reports', 'Class and subject reports for teachers, leaders and parents.'],
    ['mortarboard', 'WAEC & JAMB practice', 'Students rehearse real exam conditions long before exam day.'],
    ['people', 'Training & support', 'Onboarding for staff and students, plus support on exam days.'],
];
?>

<section class="section">
  <div class="container">
    <?= section_head('What HLTS handles', 'Everything between "set the test" and "share the results".') ?>
    <div class="grid grid--4" data-reveal-group>
<?php foreach ($capabilities as [$iconName, $title, $text]): ?>
      <article class="card-hl" data-reveal><span class="icon-tile"><?= icon($iconName) ?></span><h3><?= h($title) ?></h3><p><?= h($text) ?></p></article>
<?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--night" id="workflow">
  <div class="container split split--top">
    <div>
      <?= section_head('A supported workflow', 'Prepare. Deliver. Review.', 'Three clear stages, with HLTS beside your team at each one.', 'left') ?>
      <div class="actions" data-reveal>
        <?= button('Book a CBT demo', page_url('book-demo', ['interests[]' => 'cbt']), 'light', 'calendar-check') ?>
      </div>
    </div>
    <ol class="path" data-path>
      <span class="path__progress" aria-hidden="true"></span>
      <li class="path__step"><span class="path__dot">1</span><div><h3>Prepare</h3><p>We configure the platform, load question banks and set schedules for each class.</p></div></li>
      <li class="path__step"><span class="path__dot">2</span><div><h3>Deliver</h3><p>Students sign in securely in the lab or on approved devices, with live monitoring.</p></div></li>
      <li class="path__step"><span class="path__dot">3</span><div><h3>Review</h3><p>Scores are ready instantly. Teachers review, then results flow into report cards.</p></div></li>
    </ol>
  </div>
</section>

<section class="section" id="system-integration">
  <div class="container split">
    <div data-reveal><?= photo_frame('images/setup.jpeg', 'Computer lab set up by HLTS', 'Labs designed, installed and maintained', 'Lab setup') ?></div>
    <div>
      <?= section_head('Labs & integrations', 'The infrastructure behind great exams.', 'We design and install computer labs and connect your school systems so information flows without retyping.', 'left') ?>
      <ul class="check-list" data-reveal>
        <li>Computer lab design, installation and staff training</li>
        <li>Payment gateways, SMS and email notifications</li>
        <li>Google Workspace and Microsoft Teams setup</li>
        <li>Secure API access for custom solutions</li>
        <li>Ongoing maintenance and technical support</li>
      </ul>
    </div>
  </div>
</section>

<?= cta_band('Ready for smoother exam days?', 'See CBT running in a demo at your school. Bring your exam officer.', ['Book a CBT demo', page_url('book-demo', ['interests[]' => 'cbt'])], ['Check a result', page_url('results'), 'search']) ?>

<?php page_end(); ?>
