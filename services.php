<?php
require __DIR__ . '/lib/app.php';

page_start([
    'title' => 'School Solutions – HLTS Limited',
    'description' => 'Everything HLTS does for primary and secondary schools: CBT, result management, IT support, daily operations, staff deployment, labs and full school management.',
]);

echo page_hero([
    'crumbs' => [['For Schools']],
    'eyebrow' => 'HLTS for schools',
    'title' => 'Technology, people and support that help <span class="grad-text">schools run better.</span>',
    'lead' => 'From one service to full school management, HLTS gives primary and secondary schools the systems, staff and support to focus on teaching.',
    'actions' => button('Book a free demo', page_url('book-demo'), 'primary', 'calendar-check') . button('Register your school', page_url('school-form'), 'ghost-light', 'arrow-right'),
    'visual' => photo_frame('images/slide3.jpg', 'Students learning with technology in class', 'People, platforms and practical support', 'HLTS@School', true),
]);
?>

<section class="section" id="modules">
  <div class="container">
    <?= section_head('What we do', 'Pick one service or the whole set.', 'Every module works on its own and gets better together. We set it up, train your staff and stay on hand.') ?>
    <div class="grid grid--4" data-reveal-group>
<?php foreach (school_modules() as $key => $module): ?>
      <article class="card-hl" data-reveal>
        <span class="icon-tile"><?= icon($module['icon']) ?></span>
        <h3><?= h($module['title']) ?></h3>
        <p><?= h($module['text']) ?></p>
      </article>
<?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--alt">
  <div class="container">
    <div class="feature-rows">
      <div class="feature-row" data-reveal>
        <div class="feature-row__copy">
          <?= eyebrow('Full school management') ?>
          <h2>Let HLTS run your school's technology.</h2>
          <p class="lead">Choose the modules you need, or hand us the whole stack. One partner, one point of contact, one plan.</p>
          <?= button('See school management', page_url('school-management'), 'primary', 'arrow-right') ?>
        </div>
        <div class="feature-row__visual"><?= photo_frame('images/setup.jpeg', 'HLTS setting up school technology', 'Set up, trained and supported', 'School management') ?></div>
      </div>
      <div class="feature-row feature-row--flip" data-reveal>
        <div class="feature-row__copy">
          <?= eyebrow('CBT & assessments') ?>
          <h2>Secure computer-based exams, marked instantly.</h2>
          <p class="lead">Question banks, scheduling, secure logins and automatic marking for tests, mocks and exams.</p>
          <?= button('Explore CBT', page_url('cbt'), 'primary', 'arrow-right') ?>
        </div>
        <div class="feature-row__visual"><?= photo_frame('images/cbt.jpg', 'Students taking a computer-based test', 'From setup to results', 'CBT') ?></div>
      </div>
      <div class="feature-row" data-reveal>
        <div class="feature-row__copy">
          <?= eyebrow('Staff deployment') ?>
          <h2>Vetted staff, placed and supported.</h2>
          <p class="lead">ICT facilitators, subject teachers, lab technicians and exam officers, trained by HLTS and replaced quickly if needed.</p>
          <?= button('See staff deployment', page_url('staff-deployment'), 'primary', 'arrow-right') ?>
        </div>
        <div class="feature-row__visual"><?= photo_frame('images/2025meeting/team.jpg', 'HLTS team planning school support', 'People who understand schools', 'HLTS@School') ?></div>
      </div>
    </div>
  </div>
</section>

<section class="section section--night">
  <div class="container split">
    <div>
      <?= section_head('Why schools choose HLTS', 'A partner, not just a supplier.', 'We stay with you after setup: training staff, fixing problems and improving how things work each term.', 'left') ?>
      <div class="actions" data-reveal>
        <?= button('Book a demo', page_url('book-demo'), 'light', 'calendar-check') ?>
        <?= button('Partner school? Get IT support', page_url('it-support'), 'ghost-light', 'headset') ?>
      </div>
    </div>
    <div class="grid grid--2" data-reveal-group>
      <div class="card-hl card-hl--dark" data-reveal><span class="icon-tile"><?= icon('rocket-takeoff') ?></span><h3>Quick start</h3><p>Most schools are up and running within one to two weeks, with training included.</p></div>
      <div class="card-hl card-hl--dark" data-reveal><span class="icon-tile"><?= icon('sliders') ?></span><h3>Fits your school</h3><p>We adapt to your curriculum, calendar and the way your staff already work.</p></div>
      <div class="card-hl card-hl--dark" data-reveal><span class="icon-tile"><?= icon('headset') ?></span><h3>Real support</h3><p>Help by phone, WhatsApp, email and on-site visits for partner schools.</p></div>
      <div class="card-hl card-hl--dark" data-reveal><span class="icon-tile"><?= icon('shield-lock') ?></span><h3>Secure by design</h3><p>Role-based access, regular backups and data that is never sold.</p></div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <?= section_head('Proof', 'Schools that work with us.') ?>
    <div class="grid grid--3" data-reveal-group>
<?php foreach (case_studies() as $case): ?>
      <article class="case-card" data-reveal>
        <header><span class="chip"><?= icon('geo-alt') ?> <?= h($case['place']) ?></span><h3><?= h($case['school']) ?></h3></header>
        <p>“<?= h($case['quote']) ?>”</p>
        <dl>
<?php foreach ($case['stats'] as [$value, $label]): ?>
          <div><dt><?= h($label) ?></dt><dd><?= h($value) ?></dd></div>
<?php endforeach; ?>
        </dl>
      </article>
<?php endforeach; ?>
    </div>
  </div>
</section>

<?= cta_band('See HLTS working in your school.', 'Book a free demo at your school, online or at our office. We will show you the modules that matter to you.', ['Book a free demo', page_url('book-demo')], ['Talk to us', page_url('contact')]) ?>

<?php page_end(); ?>
