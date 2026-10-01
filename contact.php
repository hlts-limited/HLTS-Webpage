<?php
require __DIR__ . '/lib/app.php';

page_start([
    'title' => 'Contact HLTS Limited – Lagos, Nigeria',
    'description' => 'Contact HLTS Limited in Somolu, Lagos by phone, WhatsApp, email or the contact form.',
    'form' => 'contact',
]);

echo page_hero([
    'crumbs' => [['Company'], ['Contact']],
    'eyebrow' => 'Talk to HLTS',
    'title' => 'Let\'s talk about <span class="grad-text">what you need.</span>',
    'lead' => 'For school support, courses, digital projects or partnerships, our team in Lagos is ready to help.',
]);
?>

<section class="section" id="form">
  <div class="container">
    <div class="form-page">
      <aside class="form-page__aside" data-reveal>
        <?= eyebrow('Direct contact') ?>
        <h2>Reach us the way that suits you.</h2>
        <div class="contact-strip">
          <a href="tel:<?= h(config('phone')) ?>"><?= icon('telephone') ?> <?= h(config('phone_display')) ?> <?= icon('arrow-up-right') ?></a>
          <a href="https://wa.me/<?= h(config('whatsapp')) ?>" target="_blank" rel="noopener"><?= icon('whatsapp') ?> WhatsApp <?= icon('arrow-up-right') ?></a>
          <a href="mailto:<?= h(config('email_public')) ?>"><?= icon('envelope') ?> <?= h(config('email_public')) ?> <?= icon('arrow-up-right') ?></a>
          <a href="https://www.google.com/maps/search/?api=1&amp;query=8+Assembly+Close%2C+Folagoro%2C+Somolu%2C+Lagos%2C+Nigeria" target="_blank" rel="noopener"><?= icon('geo-alt') ?> 8 Assembly Close, Folagoro, Somolu <?= icon('arrow-up-right') ?></a>
        </div>
        <p class="small muted mt-4"><?= icon('clock') ?> <?= h(config('office_hours')) ?></p>
        <div class="map-frame mt-3">
          <iframe title="Map showing the HLTS office in Folagoro, Somolu, Lagos" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3963.9572092385833!2d3.37573667586253!3d6.5270888231198425!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x103b8d0074f29cff%3A0xbb486cf6bcfc9b1c!2sAssembly%20close%2C%20fola%20agoro!5e0!3m2!1sen!2sng!4v1757783462436!5m2!1sen!2sng" loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
        </div>
      </aside>
      <div class="form-shell" data-reveal="zoom">
        <div class="form-shell__head"><h2>Send a message</h2><p>We usually reply within one working day.</p></div>
        <?= simple_form('contact', ['topic', 'name', 'email', 'phone', 'message'], 'Send message') ?>
        <div class="grid grid--2 mt-4">
          <a class="card-hl" href="<?= h(page_url('book-demo')) ?>"><h3><?= icon('calendar-check') ?> Book a school demo</h3><p class="small">See HLTS in action at your school.</p></a>
          <a class="card-hl" href="<?= h(page_url('request-quote')) ?>"><h3><?= icon('chat-square-quote') ?> Request a quote</h3><p class="small">For websites, apps and portals.</p></a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php page_end(); ?>
