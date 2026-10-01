<?php
require __DIR__ . '/lib/app.php';

page_start([
    'title' => 'TechMind Africa – A community for builders | HLTS',
    'description' => 'TechMind Africa is the free HLTS community for learners, builders, educators and technology enthusiasts creating a more capable digital Africa.',
    'image' => 'images/2027images/TechMind Afica.jpeg',
]);

$events = db_all("SELECT * FROM events WHERE status = 'published' AND starts_at >= ? ORDER BY starts_at LIMIT 3", [now()]);
$moments = [
    ['images/2027images/WhatsApp Image 2026-09-26 at 3.21.18 PM.jpeg', 'TechMind Africa community gathering'],
    ['images/2027images/WhatsApp Image 2026-09-26 at 3.21.19 PM.jpeg', 'Members sharing ideas'],
    ['images/2027images/WhatsApp Image 2026-09-26 at 3.21.19 PM (1).jpeg', 'Participants at a TechMind event'],
    ['images/2027images/WhatsApp Image 2026-09-26 at 3.21.20 PM.jpeg', 'Learning together'],
    ['images/2027images/WhatsApp Image 2026-09-26 at 3.21.21 PM.jpeg', 'TechMind Africa members'],
];

$logo = '<figure class="photo-frame" style="aspect-ratio:1;max-width:420px;margin-inline:auto"><img src="' . h(img('images/2027images/TechMind Afica.jpeg')) . '" alt="TechMind Africa logo" fetchpriority="high"><figcaption><span>TechMind Africa</span><strong>Learn together. Build together.</strong></figcaption></figure>';

echo page_hero([
    'crumbs' => [['TechMind Africa']],
    'eyebrow' => 'TechMind Africa · free community',
    'title' => 'A community for people building <span class="grad-text">what Africa needs next.</span>',
    'lead' => 'Learners, educators, creators and technology enthusiasts learning openly, sharing ideas and turning curiosity into useful work.',
    'actions' => button('Join TechMind (free)', page_url('join-techmind'), 'primary', 'people') . button('See events', page_url('events'), 'ghost-light', 'calendar-event'),
    'visual' => $logo,
]);
?>

<section class="section">
  <div class="container split">
    <div>
      <?= section_head('Our vision', 'Technology should create access, confidence and opportunity.', '', 'left') ?>
      <div class="prose" data-reveal>
        <p>TechMind Africa exists to make the journey into technology feel less isolated: a space where a young learner can ask questions, an educator can exchange ideas, and a new builder can find the encouragement to begin.</p>
        <p>Our vision is a connected community of Africans who do not only consume technology, but understand it, shape it and use it to solve problems around them.</p>
      </div>
    </div>
    <div class="grid" data-reveal-group>
      <article class="card-hl" data-reveal><span class="icon-tile"><?= icon('lightbulb') ?></span><h3>Learn openly</h3><p>Space for questions, practical teaching and learning that welcomes beginners.</p></article>
      <article class="card-hl" data-reveal><span class="icon-tile"><?= icon('people') ?></span><h3>Build together</h3><p>Share ideas, collaborate on projects and learn from people at every stage.</p></article>
      <article class="card-hl" data-reveal><span class="icon-tile"><?= icon('rocket-takeoff') ?></span><h3>Create opportunity</h3><p>Turn skills into visible work, stronger networks and real possibilities.</p></article>
    </div>
  </div>
</section>

<section class="section section--alt">
  <div class="container">
    <?= section_head('Community in practice', 'Learning, ideas and people in the same room.') ?>
    <div class="gallery" data-reveal="fade">
<?php foreach ($moments as [$src, $alt]): ?>
      <figure><img src="<?= h(img($src)) ?>" alt="<?= h($alt) ?>" loading="lazy" decoding="async"><figcaption><?= h($alt) ?></figcaption></figure>
<?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="rail-head">
      <?= section_head('Upcoming', 'Events and meetups', '', 'left') ?>
      <div class="mb-4"><?= button('All events', page_url('events'), 'secondary', 'arrow-right') ?></div>
    </div>
<?php if ($events): ?>
    <div class="grid grid--3" data-reveal-group>
<?php foreach ($events as $event): require __DIR__ . '/partials/event-card.php'; endforeach; ?>
    </div>
<?php else: ?>
    <?= empty_state('calendar-event', 'New events are on the way', 'Join TechMind Africa and we will tell you as soon as the next meetup is scheduled.', button('Join to get updates', page_url('join-techmind'), 'primary', 'arrow-right')) ?>
<?php endif; ?>
  </div>
</section>

<section class="section section--night">
  <div class="container split">
    <div data-reveal><?= photo_frame('images/Israel.jpeg', 'Israel Akinola, TechMind Africa community lead', 'Community Lead, TechMind Africa', 'Israel Akinola') ?></div>
    <div>
      <?= section_head('Community lead', 'Meet Israel Akinola.', '', 'left') ?>
      <div class="prose" data-reveal>
        <p>Israel leads TechMind Africa, creating space for learners, builders, educators and technology enthusiasts to connect and grow together.</p>
        <p>Through the community, Israel is helping turn shared curiosity into practical learning, collaboration and opportunities to build.</p>
      </div>
      <div class="actions mt-4" data-reveal>
        <?= button('Become a mentor', page_url('join-techmind', ['join_as' => 'mentor']), 'light', 'person-heart') ?>
        <?= button('Partner with us', page_url('join-techmind', ['join_as' => 'partner']), 'ghost-light', 'building') ?>
      </div>
    </div>
  </div>
</section>

<?= cta_band('Bring your questions, ideas and first draft.', 'Joining is free. Meet people who are learning and building, just like you.', ['Join TechMind Africa', page_url('join-techmind')], ['Learn a skill', page_url('course')]) ?>

<?php page_end(); ?>
