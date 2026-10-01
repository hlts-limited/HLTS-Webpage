<?php
require __DIR__ . '/lib/app.php';

page_start([
    'title' => 'Digital Solutions – Websites, Apps & Portals | HLTS Limited',
    'description' => 'HLTS designs and builds websites, web and mobile apps, portals, dashboards and brands for schools, businesses and organisations.',
]);

$devices = '<div class="devices" data-tilt aria-hidden="true">'
    . '<div class="device-laptop"><div class="device-screen"><div class="mock-ui"><div class="mock-ui__bar"><i></i><i></i><i></i></div>'
    . '<div class="mock-ui__body"><div class="mock-ui__side"><span></span><span></span><span></span><span></span><span></span></div>'
    . '<div class="mock-ui__main"><div class="mock-ui__tiles"><span></span><span></span><span></span></div>'
    . '<div class="mock-ui__chart"><span style="height:40%"></span><span style="height:65%"></span><span style="height:50%"></span><span style="height:85%"></span><span style="height:60%"></span><span style="height:95%"></span><span style="height:75%"></span></div>'
    . '<div class="mock-ui__rows"><span></span><span style="width:80%"></span><span style="width:60%"></span></div></div></div></div></div></div>'
    . '<div class="device-phone"><div class="device-screen"><div class="mock-phone"><span></span><span></span><span></span><span></span></div></div></div>'
    . '</div>';

echo page_hero([
    'crumbs' => [['Digital Solutions']],
    'eyebrow' => 'HLTS Digital',
    'title' => 'Software built by people <span class="grad-text">who understand schools.</span>',
    'lead' => 'We design and build websites, apps, portals and brands for schools, businesses and organisations, then keep them running.',
    'actions' => button('Request a quote', page_url('request-quote'), 'primary', 'chat-square-quote') . button('See our work', page_url('portfolio'), 'ghost-light', 'collection'),
    'visual' => $devices,
]);
?>

<section class="section">
  <div class="container">
    <?= section_head('What we build', 'From a first website to a full platform.') ?>
    <div class="grid grid--3" data-reveal-group>
<?php foreach (digital_services() as $service): ?>
      <article class="card-hl" data-reveal><span class="icon-tile"><?= icon($service['icon']) ?></span><h3><?= h($service['title']) ?></h3><p><?= h($service['text']) ?></p></article>
<?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--night">
  <div class="container split split--top">
    <div>
      <?= section_head('How we work', 'Clear steps, no surprises.', 'You always know what is being built, when, and what it costs.', 'left') ?>
      <div class="actions" data-reveal><?= button('Start a project', page_url('request-quote'), 'light', 'arrow-right') ?></div>
    </div>
    <ol class="path" data-path>
      <span class="path__progress" aria-hidden="true"></span>
      <li class="path__step"><span class="path__dot">1</span><div><h3>Discover</h3><p>We learn about your users, goals and constraints, then agree scope and budget.</p></div></li>
      <li class="path__step"><span class="path__dot">2</span><div><h3>Design</h3><p>Wireframes and visual designs you can click through before anything is built.</p></div></li>
      <li class="path__step"><span class="path__dot">3</span><div><h3>Build & test</h3><p>Regular check-ins and a staging site so you can review progress.</p></div></li>
      <li class="path__step"><span class="path__dot">4</span><div><h3>Launch & care</h3><p>We launch, train your team, and offer hosting, updates and support.</p></div></li>
    </ol>
  </div>
</section>

<section class="section">
  <div class="container split">
    <div>
      <?= section_head('Why HLTS', 'Education experience, built in.', 'Because we run school operations ourselves, we design for real teachers, parents and administrators, not just a brief.', 'left') ?>
      <ul class="check-list" data-reveal>
        <li>Mobile-first, fast on Nigerian networks</li>
        <li>Secure by default, with backups and access control</li>
        <li>Payments, SMS and email integrations</li>
        <li>Training and handover so your team owns it</li>
      </ul>
      <div class="actions mt-4" data-reveal><?= button('See the portfolio', page_url('portfolio'), 'secondary', 'collection') ?></div>
    </div>
    <div data-reveal><?= photo_frame('images/mockup.jpeg', 'Website mockups designed by HLTS', 'Designed and built in Lagos', 'HLTS Digital') ?></div>
  </div>
</section>

<?= cta_band('Have a project in mind?', 'Tell us what you need and we will reply with questions or a proposal within two working days.', ['Request a quote', page_url('request-quote')], ['WhatsApp us', 'https://wa.me/' . config('whatsapp'), 'whatsapp']) ?>

<?php page_end(); ?>
