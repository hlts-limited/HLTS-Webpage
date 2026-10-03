<?php
require __DIR__ . '/lib/app.php';

page_start([
    'title' => 'Staff Deployment for Schools – HLTS Limited',
    'description' => 'HLTS places vetted ICT facilitators, subject teachers, lab technicians and exam officers in primary and secondary schools, with training and fast replacement.',
]);

echo page_hero([
    'crumbs' => [['For Schools', page_url('services')], ['Staff Deployment']],
    'eyebrow' => 'Staff deployment',
    'title' => 'The right people in your school, <span class="grad-text">ready from day one.</span>',
    'lead' => 'HLTS recruits, vets, trains and supports education professionals, then places them in your school. If someone leaves, we replace them quickly.',
    'actions' => button('Request staff', page_url('school-form', ['modules[]' => 'staff']), 'primary', 'person-plus') . button('Join our talent pool', page_url('careers'), 'ghost-light', 'briefcase'),
    'visual' => photo_frame('images/2025meeting/team2.jpg', 'HLTS staff working together', 'Vetted, trained and supported', 'HLTS@School people', true),
]);
?>

<section class="section">
  <div class="container">
    <?= section_head('Roles we place', 'Specialists your school can rely on.') ?>
    <div class="grid grid--4" data-reveal-group>
<?php foreach (staff_roles() as $role): ?>
      <article class="card-hl" data-reveal><span class="icon-tile"><?= icon($role['icon']) ?></span><h3><?= h($role['title']) ?></h3><p><?= h($role['text']) ?></p></article>
<?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--alt">
  <div class="container split split--top">
    <div>
      <?= section_head('Our process', 'How we choose and support every placement.', 'Schools tell us the hardest part of hiring is trust. This is how we earn it.', 'left') ?>
      <div class="notice" data-reveal>
        <?= icon('arrow-repeat') ?>
        <div><strong>Replacement promise</strong><p>If a placed staff member leaves or is not the right fit, we arrange a replacement as quickly as possible so classes keep running.</p></div>
      </div>
    </div>
    <ol class="path" data-path>
      <span class="path__progress" aria-hidden="true"></span>
      <li class="path__step"><span class="path__dot">1</span><div><h3>Understand the role</h3><p>We agree the subjects, level, schedule and what success looks like for your school.</p></div></li>
      <li class="path__step"><span class="path__dot">2</span><div><h3>Recruit and vet</h3><p>Qualification and reference checks, interviews and a practical teaching or technical assessment.</p></div></li>
      <li class="path__step"><span class="path__dot">3</span><div><h3>Train</h3><p>HLTS induction covering our tools, child safeguarding and your school's way of working.</p></div></li>
      <li class="path__step"><span class="path__dot">4</span><div><h3>Place and support</h3><p>A supervisor checks in regularly, and you can raise concerns with HLTS at any time.</p></div></li>
    </ol>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid grid--3" data-reveal-group>
      <div class="card-hl" data-reveal><span class="icon-tile"><?= icon('shield-check') ?></span><h3>Vetted professionals</h3><p>Every placement is checked, interviewed and assessed before they meet your students.</p></div>
      <div class="card-hl" data-reveal><span class="icon-tile"><?= icon('journal-check') ?></span><h3>Ongoing development</h3><p>Our staff keep learning through HLTS training, so their skills stay current.</p></div>
      <div class="card-hl" data-reveal><span class="icon-tile"><?= icon('receipt') ?></span><h3>One simple invoice</h3><p>HLTS handles payroll and administration; you get one clear invoice each term.</p></div>
    </div>
  </div>
</section>

<section class="section section--alt" id="teacher-pricing">
  <div class="container">
    <?= section_head('Teacher pricing', 'Pay by the teacher, per term.', 'Choose full-time teachers for every school day, or part-time teachers 2 days a week. Your quote shows the exact price for the number you need.') ?>
    <div class="grid grid--3" data-reveal-group>
      <a class="card-hl" href="<?= h(page_url('pricing', ['basis' => 'fulltime']) . '#quote') ?>" data-reveal>
        <span class="icon-tile"><?= icon('person-workspace') ?></span>
        <h3>Full-time teachers</h3>
        <p>Monday to Friday, on the Basic, Professional or Premium package.</p>
        <span class="card-hl__foot text-link">From <?= h(naira(min(array_map('min', full_time_prices())))) ?> per term <?= icon('arrow-right') ?></span>
      </a>
      <a class="card-hl" href="<?= h(page_url('pricing', ['basis' => 'parttime']) . '#quote') ?>" data-reveal>
        <span class="icon-tile"><?= icon('calendar-week') ?></span>
        <h3>Part-time teachers</h3>
        <p>2 days a week, with a defined workload and timetable.</p>
        <span class="card-hl__foot text-link">From <?= h(naira(min(part_time_prices()))) ?> per term <?= icon('arrow-right') ?></span>
      </a>
      <a class="card-hl" href="<?= h(page_url('pricing') . '#packages') ?>" data-reveal>
        <span class="icon-tile"><?= icon('people') ?></span>
        <h3>Or pay per pupil</h3>
        <p>A base fee plus a fee for each participating pupil, with a minimum per term.</p>
        <span class="card-hl__foot text-link">See packages <?= icon('arrow-right') ?></span>
      </a>
    </div>
  </div>
</section>

<?= cta_band('Need staff for next term?', 'Tell us the roles you need and when. We will propose candidates and a plan.', ['Request staff', page_url('school-form', ['modules[]' => 'staff'])], ['Talk to us', page_url('contact')]) ?>

<?php page_end(); ?>
