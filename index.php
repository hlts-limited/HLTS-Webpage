<?php
require __DIR__ . '/lib/app.php';

page_start([
    'title' => 'HLTS Limited – One partner for the whole learning journey',
    'page' => 'index',
]);

$nodes = [
    'schools' => ['Schools', 'building', 16, 17],
    'teachers' => ['Teachers', 'easel2', 84, 15],
    'students' => ['Students', 'mortarboard', 91, 58],
    'parents' => ['Parents', 'people', 74, 88],
    'community' => ['Community', 'globe2', 22, 87],
    'digital' => ['Digital', 'code-slash', 7, 54],
];
// Curved links from each node to the centre of the infinity mark (280, 260).
$links = [
    'schools' => 'M90 90 C 150 110, 210 170, 280 260',
    'teachers' => 'M470 80 C 420 120, 340 170, 280 260',
    'students' => 'M510 300 C 440 300, 360 280, 280 260',
    'parents' => 'M415 458 C 380 380, 330 320, 280 260',
    'community' => 'M125 452 C 170 380, 230 320, 280 260',
    'digital' => 'M40 280 C 120 280, 200 270, 280 260',
];

$audiences = [
    ['schools', 'I run a school', 'Staff, CBT, results, IT support and full school management.', page_url('services'), 'building', 'Explore school solutions'],
    ['students', 'I want to learn', 'Practical tech courses with guidance, projects and a certificate.', page_url('course'), 'mortarboard', 'Browse courses'],
    ['community', 'I want to join a community', 'Meet builders, learners and educators at TechMind Africa.', page_url('join-techmind'), 'globe2', 'Join TechMind'],
    ['digital', 'I need a digital product', 'Websites, apps, portals and design built around your goals.', page_url('digital-solutions'), 'code-slash', 'See what we build'],
];

$tracks = [
    'schools' => [
        'label' => 'Schools', 'icon' => 'building',
        'kicker' => 'HLTS@School', 'title' => 'Build a stronger school team.',
        'text' => 'Deploy vetted education professionals into your school and choose a support plan that fits your stage of growth.',
        'points' => ['Qualified ICT and subject staff', 'Three flexible engagement levels', 'Training and ongoing support'],
        'cta' => ['Register your school', page_url('school-form')],
    ],
    'operations' => [
        'label' => 'Operations', 'icon' => 'diagram-3',
        'kicker' => 'HLTS Operations', 'title' => 'Make every school process work smarter.',
        'text' => 'Bring CBT, result management, IT support and daily school operations into one reliable digital workflow.',
        'points' => ['Secure CBT exams', 'Results and report cards in hours', 'Responsive IT support'],
        'cta' => ['See school management', page_url('school-management')],
    ],
    'institution' => [
        'label' => 'Online Institution', 'icon' => 'laptop',
        'kicker' => 'HLTS Online Institution', 'title' => 'Turn ambition into practical skills.',
        'text' => 'Learn with structured courses, expert guidance and a digital environment built for the next generation of African talent.',
        'points' => ['Career-ready courses', 'Mentors and projects', 'Verifiable certificates'],
        'cta' => ['Browse courses', page_url('course')],
    ],
    'community' => [
        'label' => 'TechMind Africa', 'icon' => 'globe2',
        'kicker' => 'TechMind Africa', 'title' => 'Give technology a bigger purpose.',
        'text' => 'A free community turning curiosity into capability through meetups, collaboration and real opportunities to build.',
        'points' => ['Peer learning network', 'Community-led projects', 'Mentors and partners'],
        'cta' => ['Join the community', page_url('join-techmind')],
    ],
    'digital' => [
        'label' => 'Digital Solutions', 'icon' => 'code-slash',
        'kicker' => 'HLTS Digital', 'title' => 'Software built by people who know schools.',
        'text' => 'We design and build websites, apps, portals and brands for schools, businesses and organisations.',
        'points' => ['Websites and apps', 'Portals and dashboards', 'Brand and design'],
        'cta' => ['Request a quote', page_url('request-quote')],
    ],
];

$cbtQuestions = [
    ['q' => 'What is 15% of 240?', 'options' => ['24', '36', '32', '40'], 'answer' => 1],
    ['q' => 'Which part of a computer stores data permanently?', 'options' => ['RAM', 'CPU', 'Hard drive', 'Monitor'], 'answer' => 2],
    ['q' => 'Choose the correctly spelt word.', 'options' => ['Accomodate', 'Acommodate', 'Accommodate', 'Acomodate'], 'answer' => 2],
    ['q' => 'The chemical symbol for sodium is…', 'options' => ['S', 'Na', 'So', 'Sd'], 'answer' => 1],
];

$events = [];
try {
    $events = db_all("SELECT * FROM events WHERE status = 'published' AND starts_at >= ? ORDER BY starts_at LIMIT 3", [now()]);
} catch (Throwable $e) {
    // The home page still works if the database is unavailable.
}
?>

<section class="home-hero on-dark">
  <div class="page-hero__aurora" aria-hidden="true"></div>
  <div class="container home-hero__grid">
    <div class="home-hero__copy">
      <div data-reveal><?= eyebrow('Education · Technology · People') ?></div>
      <h1 class="home-hero__title" data-reveal data-reveal-delay="1">One partner for the whole <span class="grad-text">learning journey.</span></h1>
      <p class="lead" data-reveal data-reveal-delay="2">HLTS helps primary and secondary schools run better, teaches practical tech skills, grows a community of African builders, and creates the digital tools that connect them.</p>
      <div class="actions" data-reveal data-reveal-delay="3">
        <?= button('Find your path', '#paths', 'primary', 'arrow-down-right') ?>
        <?= button('Book a school demo', page_url('book-demo'), 'ghost-light', 'calendar-check') ?>
      </div>
      <ul class="hero-proof" data-reveal data-reveal-delay="4">
        <li><strong data-count="4">4</strong><span>partner schools</span></li>
        <li><strong data-count="1000" data-suffix="+">1,000+</strong><span>learners</span></li>
        <li><strong data-count="98" data-suffix="%">98%</strong><span>satisfaction</span></li>
      </ul>
    </div>

    <div class="hero-net" data-hero-net aria-hidden="true">
      <svg class="hero-net__svg" viewBox="0 0 560 520" focusable="false">
        <defs>
          <linearGradient id="net-grad" x1="0" x2="1" y1="0" y2="1">
            <stop offset="0" stop-color="var(--indigo-500)"/>
            <stop offset=".5" stop-color="var(--violet-500)"/>
            <stop offset="1" stop-color="var(--magenta-500)"/>
          </linearGradient>
          <radialGradient id="net-glow">
            <stop offset="0" stop-color="var(--violet-500)" stop-opacity=".55"/>
            <stop offset="1" stop-color="var(--violet-500)" stop-opacity="0"/>
          </radialGradient>
        </defs>
        <circle cx="280" cy="260" r="190" fill="url(#net-glow)" class="hero-net__glow"/>
        <circle cx="280" cy="260" r="150" class="hero-net__orbit"/>
        <circle cx="280" cy="260" r="225" class="hero-net__orbit hero-net__orbit--outer"/>
<?php foreach ($links as $key => $d): ?>
        <path class="hero-net__link" d="<?= $d ?>" pathLength="1"/>
<?php endforeach; ?>
        <g transform="translate(180 210)">
          <path class="hero-net__inf-track" d="<?= INFINITY_PATH ?>"/>
          <path class="hero-net__inf" d="<?= INFINITY_PATH ?>" stroke="url(#net-grad)" pathLength="1"/>
          <circle r="5" class="hero-net__spark">
            <animateMotion dur="5s" repeatCount="indefinite" path="<?= INFINITY_PATH ?>"/>
          </circle>
        </g>
<?php $i = 0; foreach ($links as $key => $d): ?>
        <circle r="3.5" class="hero-net__pulse">
          <animateMotion dur="<?= 2.6 + ($i % 3) * 0.5 ?>s" begin="<?= 1.6 + $i * 0.45 ?>s" repeatCount="indefinite" path="<?= $d ?>" keyPoints="0;1" keyTimes="0;1" calcMode="linear"/>
        </circle>
<?php $i++; endforeach; ?>
      </svg>
<?php $i = 0; foreach ($nodes as $key => [$label, $iconName, $x, $y]): ?>
      <span class="hero-net__node" data-node="<?= h($key) ?>" style="--x: <?= $x ?>%; --y: <?= $y ?>%; --d: <?= $i++ ?>">
        <span class="hero-net__icon"><?= icon($iconName) ?></span><?= h($label) ?>
      </span>
<?php endforeach; ?>
      <div class="hero-float hero-float--a"><?= icon('ui-checks-grid') ?><span><small>CBT mock exam</small><strong>Average 92%</strong></span></div>
      <div class="hero-float hero-float--b"><?= icon('clipboard-data') ?><span><small>First term results</small><strong>Published</strong></span></div>
    </div>
  </div>
  <a class="scroll-cue" href="#paths" aria-label="Scroll to choose your path"><span></span></a>
</section>

<section class="section" id="paths">
  <div class="container">
    <?= section_head('Start here', 'What brings you to <span class="grad-text">HLTS</span>?', 'Choose your path and we will take you straight to the right place.') ?>
    <div class="audience-grid" data-reveal-group>
<?php foreach ($audiences as $i => [$key, $title, $text, $href, $iconName, $cta]): ?>
      <a class="audience-card" href="<?= h($href) ?>" data-audience="<?= h($key) ?>" data-reveal>
        <span class="audience-card__num">0<?= $i + 1 ?></span>
        <span class="icon-tile icon-tile--solid"><?= icon($iconName) ?></span>
        <h3><?= h($title) ?></h3>
        <p><?= h($text) ?></p>
        <span class="text-link"><?= h($cta) ?> <?= icon('arrow-right') ?></span>
      </a>
<?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section--tight partners" aria-label="Schools that work with HLTS">
  <div class="container">
    <p class="partners__label" data-reveal>Trusted by schools across Lagos</p>
  </div>
  <div class="marquee" data-reveal="fade">
    <div class="marquee__track">
<?php for ($copy = 0; $copy < 2; $copy++): ?>
<?php foreach (array_merge(partner_schools(), partner_schools()) as $school): ?>
      <span class="marquee__item"<?= $copy ? ' aria-hidden="true"' : '' ?>><?= icon('mortarboard-fill') ?> <?= h($school) ?></span>
<?php endforeach; ?>
<?php endfor; ?>
    </div>
  </div>
</section>

<section class="section section--alt" id="ecosystem">
  <div class="container">
    <?= section_head('The HLTS ecosystem', 'Five ways we move education forward.', 'Everything connects: the schools we support, the learners we train, the community we grow and the tools we build.') ?>
    <div class="eco" data-reveal>
      <div class="eco__tabs" role="tablist" aria-label="HLTS business lines" data-tabs>
<?php $first = true; foreach ($tracks as $key => $track): ?>
        <button class="eco__tab" role="tab" id="tab-<?= h($key) ?>" aria-controls="panel-<?= h($key) ?>" aria-selected="<?= $first ? 'true' : 'false' ?>" tabindex="<?= $first ? '0' : '-1' ?>">
          <?= icon($track['icon']) ?><span><?= h($track['label']) ?></span>
        </button>
<?php $first = false; endforeach; ?>
      </div>
<?php $first = true; foreach ($tracks as $key => $track): ?>
      <div class="eco__panel" role="tabpanel" id="panel-<?= h($key) ?>" aria-labelledby="tab-<?= h($key) ?>"<?= $first ? '' : ' hidden' ?> tabindex="0">
        <div class="eco__copy">
          <span class="chip"><?= h($track['kicker']) ?></span>
          <h3><?= h($track['title']) ?></h3>
          <p><?= h($track['text']) ?></p>
          <?= button($track['cta'][0], $track['cta'][1], 'primary', 'arrow-right') ?>
        </div>
        <ol class="eco__points">
<?php foreach ($track['points'] as $n => $point): ?>
          <li style="--i: <?= $n ?>"><span>0<?= $n + 1 ?></span><?= h($point) ?></li>
<?php endforeach; ?>
        </ol>
        <span class="eco__mark" aria-hidden="true"><?= icon($track['icon']) ?></span>
      </div>
<?php $first = false; endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--night showcase">
  <div class="container split">
    <div>
      <?= section_head('See it working', 'Exams that run themselves. Results parents can check tonight.', 'HLTS sets up computer-based tests, marks them automatically and publishes results online, so teachers get their weekends back.', 'left') ?>
      <ul class="check-list" data-reveal>
        <li>Question banks by subject and class, with timed, secure access</li>
        <li>Automatic marking and instant score reports</li>
        <li>Report cards and online result checking with PINs</li>
      </ul>
      <div class="actions mt-4" data-reveal>
        <?= button('Explore CBT', page_url('cbt'), 'light', 'arrow-right') ?>
        <?= button('Check a result', page_url('results'), 'ghost-light', 'search') ?>
      </div>
    </div>

    <div class="cbt-demo" data-cbt-demo data-questions="<?= h(json_encode($cbtQuestions)) ?>" data-reveal="zoom" aria-label="Animated example of an HLTS CBT exam" role="img">
      <div class="cbt-demo__top">
        <span class="cbt-demo__brand"><?= icon('ui-checks-grid') ?> HLTS CBT · SS2 Mock</span>
        <span class="cbt-demo__timer"><?= icon('stopwatch') ?> <span data-cbt-timer>30:00</span></span>
      </div>
      <div class="cbt-demo__progress"><span data-cbt-progress></span></div>
      <div class="cbt-demo__body" aria-hidden="true">
        <p class="cbt-demo__num" data-cbt-num>Question 1 of 4</p>
        <p class="cbt-demo__q" data-cbt-question></p>
        <ol class="cbt-demo__options" data-cbt-options></ol>
      </div>
      <div class="cbt-demo__dots" data-cbt-dots aria-hidden="true"></div>
      <div class="cbt-demo__result" data-cbt-result hidden>
        <svg class="tick-anim" viewBox="0 0 52 52" aria-hidden="true"><circle cx="26" cy="26" r="24"/><path d="M15 27 l7 7 l15 -16"/></svg>
        <strong data-cbt-score>0%</strong>
        <span>Marked instantly · Result saved</span>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container split split--wide-right split--top">
    <div>
      <?= section_head('How we work', 'From challenge to progress in three steps.', '', 'left') ?>
      <ol class="path" data-path>
        <span class="path__progress" aria-hidden="true"></span>
        <li class="path__step"><span class="path__dot">1</span><div><h3>Tell us what you need</h3><p>Start with your school, learning or technology challenge. A call, a visit or a form is enough.</p></div></li>
        <li class="path__step"><span class="path__dot">2</span><div><h3>Get the right solution</h3><p>We match you with the people, platform or support that fits, and agree a clear plan.</p></div></li>
        <li class="path__step"><span class="path__dot">3</span><div><h3>Grow with HLTS</h3><p>Training, support and regular reviews keep things improving as your needs change.</p></div></li>
      </ol>
    </div>
    <div>
      <div class="stats" data-reveal-group>
<?php foreach (site_stats() as $stat): ?>
        <div class="stat" data-reveal><span class="stat__value" data-count="<?= (int) $stat['value'] ?>" data-suffix="<?= h($stat['suffix']) ?>"><?= number_format($stat['value']) . h($stat['suffix']) ?></span><span class="stat__label"><?= h($stat['label']) ?></span></div>
<?php endforeach; ?>
      </div>
      <div class="promise-grid" data-reveal-group>
        <div class="promise" data-reveal><?= icon('graph-up-arrow') ?><div><strong>Practical growth</strong><p>Systems that improve outcomes without adding complexity.</p></div></div>
        <div class="promise" data-reveal><?= icon('layers') ?><div><strong>Integrated support</strong><p>People, platforms and processes that work together.</p></div></div>
        <div class="promise" data-reveal><?= icon('people') ?><div><strong>Human-first delivery</strong><p>Designed around educators, students and families.</p></div></div>
        <div class="promise" data-reveal><?= icon('shield-check') ?><div><strong>Trackable results</strong><p>Secure workflows and progress you can measure.</p></div></div>
      </div>
    </div>
  </div>
</section>

<section class="section section--alt">
  <div class="container">
    <div data-rail>
      <div class="rail-head">
        <?= section_head('Success stories', 'Trusted by schools building better outcomes.', '', 'left') ?>
        <div class="rail-controls">
          <button class="rail-btn" type="button" data-rail-prev aria-label="Previous story"><?= icon('arrow-left') ?></button>
          <button class="rail-btn" type="button" data-rail-next aria-label="Next story"><?= icon('arrow-right') ?></button>
        </div>
      </div>
      <div class="quote-rail" data-reveal="fade" tabindex="0" aria-label="Testimonials">
<?php foreach (testimonials() as $t): $initials = implode('', array_map(fn ($w) => $w[0], array_slice(explode(' ', $t['name']), 0, 2))); ?>
        <figure class="quote-card">
          <span class="quote-card__mark" aria-hidden="true"><?= icon('quote') ?></span>
          <blockquote><?= h($t['quote']) ?></blockquote>
          <footer><span class="avatar" aria-hidden="true"><?= h($initials) ?></span><figcaption><strong><?= h($t['name']) ?></strong><small><?= h($t['role']) ?></small></figcaption></footer>
        </figure>
<?php endforeach; ?>
      </div>
    </div>

    <div class="grid grid--3 mt-5" data-reveal-group>
<?php foreach (case_studies() as $case): ?>
      <article class="case-card" data-reveal>
        <header><span class="chip chip--violet"><?= icon('geo-alt') ?> <?= h($case['place']) ?></span><h3><?= h($case['school']) ?></h3></header>
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

<?php if ($events): ?>
<section class="section">
  <div class="container">
    <?= section_head('TechMind Africa', 'Upcoming events', '', 'left') ?>
    <div class="grid grid--3" data-reveal-group>
<?php foreach ($events as $event): ?>
      <?php require __DIR__ . '/partials/event-card.php'; ?>
<?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?= cta_band('Ready to move your school or career forward?', 'Book a free demo for your school, or start a course this term. We reply within one working day.', ['Book a school demo', page_url('book-demo')], ['Browse courses', page_url('course')]) ?>

<?php page_end(); ?>
