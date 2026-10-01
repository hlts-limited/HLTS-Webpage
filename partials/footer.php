<?php
/**
 * Site footer, newsletter signup and floating WhatsApp button.
 */

$groups = nav_groups();
$footerColumns = ['schools', 'learn', 'build', 'company'];
?>
    <footer class="site-footer on-dark">
      <div class="container">
        <div class="site-footer__grid">
          <div class="site-footer__about">
            <a class="brand" href="/" aria-label="HLTS Limited home">
              <img src="/images/brand/logo-192.png" alt="" width="46" height="46" loading="lazy">
              <span class="brand__text"><span class="brand__name">HLTS</span><span class="brand__tag">TECHNOLOGY</span></span>
            </a>
            <p class="mt-3">Education, technology and people working together for schools, learners and communities across Africa.</p>
            <ul class="small">
              <li><a href="tel:<?= h(config('phone')) ?>"><?= icon('telephone') ?> <?= h(config('phone_display')) ?></a></li>
              <li><a href="mailto:<?= h(config('email_public')) ?>"><?= icon('envelope') ?> <?= h(config('email_public')) ?></a></li>
              <li><?= icon('geo-alt') ?> <?= h(config('address')) ?></li>
            </ul>
            <div class="socials">
              <a href="https://web.facebook.com/profile.php?id=61551105837140" aria-label="HLTS on Facebook" target="_blank" rel="noopener"><?= icon('facebook') ?></a>
              <a href="https://www.instagram.com/hltslimited/" aria-label="HLTS on Instagram" target="_blank" rel="noopener"><?= icon('instagram') ?></a>
              <a href="https://www.linkedin.com/company/high-level-tech-services-limited" aria-label="HLTS on LinkedIn" target="_blank" rel="noopener"><?= icon('linkedin') ?></a>
              <a href="https://wa.me/<?= h(config('whatsapp')) ?>" aria-label="Chat with HLTS on WhatsApp" target="_blank" rel="noopener"><?= icon('whatsapp') ?></a>
            </div>
          </div>
<?php foreach ($footerColumns as $key): ?>
          <div>
            <h2><?= h($groups[$key]['label']) ?></h2>
            <ul>
<?php foreach ($groups[$key]['items'] as [$page, $label]): ?>
              <li><a href="<?= h(page_url($page)) ?>"<?= link_target($page) ?>><?= h($label) ?></a></li>
<?php endforeach; ?>
<?php if ($key === 'build'): ?>
              <li><a href="<?= h(page_url('community')) ?>">TechMind Africa</a></li>
              <li><a href="<?= h(page_url('events')) ?>">Events</a></li>
<?php endif; ?>
            </ul>
          </div>
<?php endforeach; ?>
        </div>

        <div class="footer-news">
          <div>
            <h2>Get school and learning updates</h2>
            <p>One short email when we have something useful to share. No spam.</p>
          </div>
          <?= form_open('newsletter') ?>
            <div class="inline-form">
              <label class="visually-hidden" for="newsletter-email">Email address</label>
              <input type="email" id="newsletter-email" name="email" placeholder="Your email address" autocomplete="email" required>
              <button class="btn-hl btn-hl--primary" type="submit" data-submit><span class="btn-hl__label">Subscribe</span><span class="btn-hl__spinner" aria-hidden="true"></span></button>
            </div>
            <?= success_panel() ?>
          </form>
        </div>

        <div class="site-footer__bottom">
          <span>&copy; <?= date('Y') ?> HLTS Limited. All rights reserved.</span>
          <span><a href="<?= h(page_url('terms')) ?>">Terms &amp; Privacy</a> · <a href="<?= h(page_url('faq')) ?>">Help</a> · <a href="/admin/">Staff sign-in</a></span>
        </div>
      </div>
      <div class="site-footer__mark" aria-hidden="true"><?= infinity_svg('footer') ?></div>
    </footer>

    <a class="whatsapp-float" href="https://wa.me/<?= h(config('whatsapp')) ?>" target="_blank" rel="noopener" aria-label="Chat with HLTS on WhatsApp"><?= icon('whatsapp') ?></a>
  </body>
</html>
