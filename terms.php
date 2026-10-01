<?php
require __DIR__ . '/lib/app.php';

page_start([
    'title' => 'Terms of Service – HLTS Limited',
    'description' => 'The terms for using HLTS services and how HLTS collects, stores and protects information.',
]);

echo page_hero([
    'crumbs' => [['Company'], ['Terms & Privacy']],
    'eyebrow' => 'Legal',
    'title' => 'Terms of Service <span class="grad-text">&amp; Privacy Policy</span>',
    'lead' => 'The terms for using HLTS services, and how we handle your information.',
    'actions' => button('Terms of Service', '#terms-of-service', 'ghost-light', 'arrow-down') . button('Privacy Policy', page_url('privacy'), 'ghost-light', 'shield-lock'),
]);
?>

<section class="section">
  <div class="container" style="max-width: 860px">
    <article class="prose" data-reveal>
      <h2 id="terms-of-service">Terms of Service</h2>
      <h3>1. Acceptable use</h3>
      <p>By using HLTS websites, portals and tools you agree to use them lawfully and responsibly. You must not transmit malicious code, send unsolicited material, or disrupt the normal operation of our services.</p>
      <h3>2. Your responsibilities</h3>
      <p>Keep your login details (passwords, usernames, PINs and tokens) confidential. Activity under your account is your responsibility. Make sure the information you give us is accurate and up to date.</p>
      <h3>3. Payments</h3>
      <p>Online payments are processed securely by Paystack. HLTS never sees or stores your full card details. Fees are as shown at the time of registration; contact us about refunds or changes.</p>
      <h3>4. Limitation of liability</h3>
      <p>We work to keep our platforms available and data safe, but we are not liable for indirect or consequential losses caused by outages, technical failures or loss of connectivity.</p>
      <h3>5. Account suspension</h3>
      <p>We may suspend or close accounts that break these terms, share proprietary content without permission, or fail to meet subscription obligations. Institutions are notified before any permanent closure.</p>

      <h2 id="privacy-policy">Privacy Policy</h2>
      <p>How we collect, use, protect and delete personal information is set out in full in our <a href="<?= h(page_url('privacy')) ?>">Privacy Policy</a>. By submitting any form on this website you confirm you have read it.</p>
      <p class="small muted">Last updated <?= date('F Y') ?>.</p>
    </article>
  </div>
</section>

<?php page_end(); ?>
