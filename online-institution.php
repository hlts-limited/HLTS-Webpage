<?php
require __DIR__ . '/lib/app.php';

page_start([
    'title' => 'HLTS Online Institution – Practical tech skills',
    'description' => 'Learn practical technology skills with HLTS Online Institution: structured courses, guided support, real projects and verifiable certificates.',
]);

echo page_hero([
    'crumbs' => [['Online Institution']],
    'eyebrow' => 'HLTS Online Institution',
    'title' => 'Practical skills for the future <span class="grad-text">you are building.</span>',
    'lead' => 'Learn technology with structure, support and a clear path from beginner to confident builder.',
    'actions' => button('Browse courses', page_url('course'), 'primary', 'arrow-right') . button('Register to learn', page_url('registration-form'), 'ghost-light', 'person-plus'),
    'visual' => photo_frame('images/achildcoding.jpeg', 'A young learner coding on a laptop', 'Skills that move with you', 'Learn by building', true),
]);
?>

<section class="section">
  <div class="container">
    <?= section_head('Why learn with us', 'Learning that leads somewhere.') ?>
    <div class="grid grid--3" data-reveal-group>
      <article class="card-hl" data-reveal><span class="icon-tile"><?= icon('compass') ?></span><h3>Clear direction</h3><p>Practical courses designed around skills you can use in school, work and real projects.</p></article>
      <article class="card-hl" data-reveal><span class="icon-tile"><?= icon('person-workspace') ?></span><h3>Guided support</h3><p>Structured instruction, expert guidance and a community that keeps you moving.</p></article>
      <article class="card-hl" data-reveal><span class="icon-tile"><?= icon('graph-up-arrow') ?></span><h3>Visible progress</h3><p>Follow your course, materials and payments in the student portal.</p></article>
      <article class="card-hl" data-reveal><span class="icon-tile"><?= icon('kanban') ?></span><h3>Real projects</h3><p>Build things you can show: websites, dashboards, designs and videos.</p></article>
      <article class="card-hl" data-reveal><span class="icon-tile"><?= icon('patch-check') ?></span><h3>Verifiable certificates</h3><p>Every certificate has a unique number anyone can check online.</p></article>
      <article class="card-hl" data-reveal><span class="icon-tile"><?= icon('globe2') ?></span><h3>A community behind you</h3><p>Learners join TechMind Africa for meetups, mentors and opportunities.</p></article>
    </div>
  </div>
</section>

<section class="section section--alt">
  <div class="container">
    <?= section_head('Popular courses', 'Start with one of these.') ?>
    <div class="grid grid--3" data-reveal-group>
<?php foreach (array_slice(courses(), 0, 3, true) as $slug => $course): ?>
      <?= course_card($slug, $course) ?>
<?php endforeach; ?>
    </div>
    <div class="center mt-5" data-reveal><?= button('See all courses', page_url('course'), 'secondary', 'arrow-right') ?></div>
  </div>
</section>

<section class="section section--night" id="how-to-register">
  <div class="container split split--top">
    <div>
      <?= section_head('How to register', 'Start in three simple steps.', 'From choosing a course to joining the HLTS learning community.', 'left') ?>
      <div class="actions" data-reveal>
        <?= button('Go to registration', page_url('registration-form'), 'light', 'arrow-right') ?>
        <?= button('Verify a certificate', page_url('verify-certificate'), 'ghost-light', 'patch-check') ?>
      </div>
    </div>
    <ol class="path" data-path>
      <span class="path__progress" aria-hidden="true"></span>
      <li class="path__step"><span class="path__dot">1</span><div><h3>Choose your course</h3><p>Review the courses and pick the path that fits your goals.</p></div></li>
      <li class="path__step"><span class="path__dot">2</span><div><h3>Register and pay</h3><p>Share your details, choose a payment plan and pay online or later.</p></div></li>
      <li class="path__step"><span class="path__dot">3</span><div><h3>Start learning</h3><p>Our team confirms your place and sets up your student portal.</p></div></li>
    </ol>
  </div>
</section>

<?= cta_band('Your next skill is one form away.', 'Register in two minutes. We will call you within one working day.', ['Register now', page_url('registration-form')], ['Student portal', page_url('portal'), 'person-badge']) ?>

<?php page_end(); ?>
