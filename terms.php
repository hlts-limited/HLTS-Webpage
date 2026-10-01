<?php
require __DIR__ . '/lib/app.php';

page_start([
    'title' => 'Terms of Service & Privacy Policy – HLTS Limited',
    'description' => 'The terms for using HLTS services and how HLTS collects, stores and protects information.',
]);

echo page_hero([
    'crumbs' => [['Company'], ['Terms & Privacy']],
    'eyebrow' => 'Legal',
    'title' => 'Terms of Service <span class="grad-text">&amp; Privacy Policy</span>',
    'lead' => 'The terms for using HLTS services, and how we handle your information.',
    'actions' => button('Terms of Service', '#terms-of-service', 'ghost-light', 'arrow-down') . button('Privacy Policy', '#privacy-policy', 'ghost-light', 'arrow-down'),
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
      <h3>1. What we collect</h3>
      <p>Information you give us through forms (such as name, email, phone and school), and, for partner schools, student names, academic results, attendance and parent or guardian contact details. We also collect basic, anonymous usage information to improve the website.</p>
      <h3>2. How we use it</h3>
      <p>To respond to enquiries, deliver the services you ask for, manage courses and results, process payments and send updates you have agreed to receive.</p>
      <h3>3. How we store and protect it</h3>
      <p>Data is stored securely with role-based access, encrypted connections, regular backups and modern security practices. Result PINs are stored in a form that cannot be read back.</p>
      <h3>4. Sharing</h3>
      <p>We never sell, rent or trade personal data or student records. We share information only with service providers who help us run our services (for example, payment and email providers), and only as needed.</p>
      <h3>5. Your rights and contact</h3>
      <p>You can ask to see, correct or delete your information. Email <a href="mailto:<?= h(config('email_public')) ?>"><?= h(config('email_public')) ?></a> or write to us at <?= h(config('address')) ?>.</p>
      <p class="small muted">Last updated <?= date('F Y') ?>.</p>
    </article>
  </div>
</section>

<?php page_end(); ?>
