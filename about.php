<?php
require __DIR__ . '/lib/app.php';

page_start([
    'title' => 'About HLTS Limited – Our mission, values and team',
    'description' => 'HLTS Limited is a Lagos EdTech company transforming lives through education, technology and sustainable development.',
]);

$gallery = [
    ['images/2025meeting/team.jpg', 'Strategic planning session'],
    ['images/2025meeting/team2.jpg', 'Team collaboration'],
    ['images/2025meeting/CEO.jpg', 'CEO Christopher Oyeh sharing the roadmap'],
    ['images/2025meeting/Supervisor.jpeg', 'Team coordination'],
    ['images/2025meeting/DepSuper.jpg', 'Colleagues sharing ideas'],
    ['images/2025meeting/hlts.jpg', 'The HLTS team'],
];

echo page_hero([
    'crumbs' => [['Company'], ['About']],
    'eyebrow' => 'Our story',
    'title' => 'Transforming lives through <span class="grad-text">education and technology.</span>',
    'lead' => 'HLTS Limited is a Lagos-based technology company making technology practical and accessible for schools, learners and communities across Africa.',
    'actions' => button('Meet the team', '#team', 'primary', 'arrow-down') . button('Work with us', page_url('careers'), 'ghost-light', 'briefcase'),
    'visual' => photo_frame('images/2027images/WhatsApp Image 2026-09-26 at 3.21.18 PM.jpeg', 'HLTS members at a recent event', 'Education and technology, built together', 'HLTS in action', true),
]);
?>

<section class="section">
  <div class="container split split--top">
    <div>
      <?= section_head('Who we are', 'A catalyst for a more digital Africa.', '', 'left') ?>
      <div class="prose" data-reveal>
        <p>HLTS Limited is a forward-thinking technology company focused on using innovation, digital tools and modern IT to solve real problems and drive sustainable development across Africa.</p>
        <p>We empower individuals, schools, businesses and institutions through education technology, school operations, digital skills training, software development and technology integration.</p>
        <p>We aim to bridge the digital divide by making technology more accessible and practical for everyday use.</p>
      </div>
    </div>
    <div class="grid grid--2" data-reveal-group>
      <article class="card-hl" data-reveal><span class="icon-tile icon-tile--solid"><?= icon('eye') ?></span><h3>Our vision</h3><p>To be the leading catalyst for innovation and empowerment in Nigeria, fostering skilled professionals and sustainable communities through education and technology.</p></article>
      <article class="card-hl" data-reveal><span class="icon-tile icon-tile--solid"><?= icon('bullseye') ?></span><h3>Our mission</h3><p>To deliver transformative learning experiences, practical solutions and expert guidance that equip people and organisations to thrive in a fast-changing world.</p></article>
    </div>
  </div>
</section>

<section class="section section--night">
  <div class="container">
    <?= section_head('What we stand for', 'Values that guide every project.') ?>
    <div class="grid grid--4" data-reveal-group>
      <article class="card-hl card-hl--dark" data-reveal><span class="icon-tile"><?= icon('award') ?></span><h3>Excellence</h3><p>The highest standards in everything we do.</p></article>
      <article class="card-hl card-hl--dark" data-reveal><span class="icon-tile"><?= icon('lightning-charge') ?></span><h3>Innovation</h3><p>Creative, forward-thinking solutions.</p></article>
      <article class="card-hl card-hl--dark" data-reveal><span class="icon-tile"><?= icon('people') ?></span><h3>Empowerment</h3><p>Helping people reach their full potential.</p></article>
      <article class="card-hl card-hl--dark" data-reveal><span class="icon-tile"><?= icon('tree') ?></span><h3>Sustainability</h3><p>Lasting, positive impact for communities.</p></article>
    </div>
    <div class="actions mt-4" style="justify-content:center" data-reveal>
      <span class="chip chip--dark"><?= icon('chat-heart') ?> Honest communication</span>
      <span class="chip chip--dark"><?= icon('clipboard-check') ?> Accountability</span>
      <span class="chip chip--dark"><?= icon('shield-check') ?> Ethical standards</span>
      <span class="chip chip--dark"><?= icon('patch-check') ?> Quality assurance</span>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <?= section_head('What we do', 'Five connected parts of one mission.') ?>
    <div class="grid grid--3" data-reveal-group>
      <a class="card-hl" href="<?= h(page_url('services')) ?>" data-reveal><span class="icon-tile"><?= icon('building') ?></span><h3>EdTech for schools</h3><p>Digital learning, school systems and curriculum integration for primary and secondary schools.</p><span class="card-hl__foot text-link">School solutions <?= icon('arrow-right') ?></span></a>
      <a class="card-hl" href="<?= h(page_url('school-management')) ?>" data-reveal><span class="icon-tile"><?= icon('diagram-3') ?></span><h3>School operations</h3><p>CBT, results, IT support, daily operations, staff deployment and full management.</p><span class="card-hl__foot text-link">School management <?= icon('arrow-right') ?></span></a>
      <a class="card-hl" href="<?= h(page_url('online-institution')) ?>" data-reveal><span class="icon-tile"><?= icon('mortarboard') ?></span><h3>Online Institution</h3><p>Structured, practical training in programming, web, data, design and digital tools.</p><span class="card-hl__foot text-link">Courses <?= icon('arrow-right') ?></span></a>
      <a class="card-hl" href="<?= h(page_url('community')) ?>" data-reveal><span class="icon-tile"><?= icon('globe2') ?></span><h3>TechMind Africa</h3><p>A free community of learners, builders and educators growing together.</p><span class="card-hl__foot text-link">The community <?= icon('arrow-right') ?></span></a>
      <a class="card-hl" href="<?= h(page_url('digital-solutions')) ?>" data-reveal><span class="icon-tile"><?= icon('code-slash') ?></span><h3>Digital solutions</h3><p>Websites, apps, portals and brands for schools and organisations.</p><span class="card-hl__foot text-link">What we build <?= icon('arrow-right') ?></span></a>
      <a class="card-hl" href="<?= h(page_url('careers')) ?>" data-reveal><span class="icon-tile"><?= icon('briefcase') ?></span><h3>Careers</h3><p>Join the team, or our talent pool for school placements.</p><span class="card-hl__foot text-link">Open roles <?= icon('arrow-right') ?></span></a>
    </div>
  </div>
</section>

<section class="section section--alt" id="team">
  <div class="container">
    <?= section_head('Leadership', 'Meet the team.', 'The people driving HLTS\'s mission to transform education through technology.') ?>
    <div class="grid grid--4" data-reveal-group>
<?php foreach (team() as $member): ?>
      <figure class="team-card" tabindex="0" data-reveal>
        <img src="<?= h(img($member['image'])) ?>" alt="<?= h($member['name']) ?>" loading="lazy" decoding="async">
        <figcaption class="team-card__info"><h3><?= h($member['name']) ?></h3><span class="team-card__role"><?= h($member['role']) ?></span><p class="team-card__bio"><?= h($member['bio']) ?></p></figcaption>
      </figure>
<?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section" id="gallery-section">
  <div class="container">
    <?= section_head('Gallery', 'Moments from the HLTS team.') ?>
    <div class="gallery" data-reveal="fade">
<?php foreach ($gallery as [$src, $caption]): ?>
      <figure><img src="<?= h(img($src)) ?>" alt="<?= h($caption) ?>" loading="lazy" decoding="async"><figcaption><?= h($caption) ?></figcaption></figure>
<?php endforeach; ?>
    </div>
  </div>
</section>

<?= cta_band('Ready to be part of our journey?', 'Whether you run a school, want to learn, or need a digital partner, we would love to hear from you.', ['Talk to HLTS', page_url('contact')], ['Browse courses', page_url('course')]) ?>

<?php page_end(); ?>
