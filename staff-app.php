<?php
/**
 * Unlisted page: how HLTS staff install the Operation Suite app. Not in the menu or sitemap,
 * and hidden from search engines; shared by link or QR code.
 */
require __DIR__ . '/lib/app.php';

const STAFF_APP_URL = 'https://hlts-hr.vercel.app/login';
const STAFF_APK = 'downloads/hlts-ops-1.0.1.apk';
const STAFF_APK_VERSION = '1.0.1';
$apk = is_file(__DIR__ . '/' . STAFF_APK);
$apkSize = $apk ? round(filesize(__DIR__ . '/' . STAFF_APK) / 1048576, 1) : 0;

page_start([
    'title' => 'HLTS Staff App',
    'description' => 'Install the HLTS Operation Suite on your phone or computer.',
    'noindex' => true,
]);

echo page_hero([
    'crumbs' => [['Staff app']],
    'eyebrow' => 'For HLTS staff',
    'title' => 'Get the <span class="grad-text">HLTS staff app</span>',
    'lead' => 'Clock in, request leave, file your weekly report and get your payslip from your phone. Sign in with the same email and password as always.',
]);
?>

<section class="section">
  <div class="container">
    <div class="grid grid--3" data-reveal-group>
      <article class="card-hl" data-reveal>
        <span class="icon-tile"><?= icon('android2') ?></span>
        <h3>Android</h3>
<?php if ($apk): ?>
        <p>Download the app and open the file to install it.</p>
        <p class="small"><strong>Already have HLTS Ops?</strong> Download this version (<?= h(STAFF_APK_VERSION) ?>) and tap <strong>Update</strong>. You need it to clock in, because it can use your phone’s location. You stay signed in.</p>
        <p><a class="btn-hl btn-hl--primary" href="/<?= h(STAFF_APK) ?>" download><?= icon('download') ?> <span>Download for Android, version <?= h(STAFF_APK_VERSION) ?> (<?= h((string) $apkSize) ?> MB)</span></a></p>
        <p class="small muted">The first time, your phone may ask you to allow installing apps from your browser. Tap <strong>Settings</strong>, turn on <strong>Allow from this source</strong>, then go back and tap <strong>Install</strong>.</p>
        <p class="small muted">Or, in Chrome: open the <a href="<?= h(STAFF_APP_URL) ?>">staff app</a>, tap the ⋮ menu, then <strong>Install app</strong>.</p>
<?php else: ?>
        <ol class="small">
          <li>Open the <a href="<?= h(STAFF_APP_URL) ?>">staff app</a> in <strong>Chrome</strong>.</li>
          <li>Sign in, then tap <strong>Install app</strong> at the top of the screen (or the ⋮ menu, then <strong>Install app</strong>).</li>
          <li>Open <strong>HLTS Ops</strong> from your home screen.</li>
        </ol>
<?php endif; ?>
      </article>

      <article class="card-hl" data-reveal>
        <span class="icon-tile"><?= icon('apple') ?></span>
        <h3>iPhone and iPad</h3>
        <ol class="small">
          <li>Open the <a href="<?= h(STAFF_APP_URL) ?>">staff app</a> in <strong>Safari</strong>.</li>
          <li>Tap the Share button <?= icon('box-arrow-up') ?> at the bottom of the screen.</li>
          <li>Scroll down and tap <strong>Add to Home Screen</strong>, then <strong>Add</strong>.</li>
          <li>Open <strong>HLTS Ops</strong> from your home screen.</li>
        </ol>
      </article>

      <article class="card-hl" data-reveal>
        <span class="icon-tile"><?= icon('laptop') ?></span>
        <h3>Computer</h3>
        <ol class="small">
          <li>Open the <a href="<?= h(STAFF_APP_URL) ?>">staff app</a> in <strong>Chrome</strong> or <strong>Edge</strong>.</li>
          <li>Sign in, then click <strong>Install app</strong> at the top of the page (or the install icon at the right of the address bar).</li>
          <li>It opens in its own window, like any program.</li>
        </ol>
      </article>
    </div>
  </div>
</section>

<section class="section section--alt">
  <div class="container split split--top">
    <div data-reveal>
      <h2 class="h3">On a computer? Scan to open this page on your phone.</h2>
      <p class="muted">Point your phone camera at the code, then follow the steps for your phone above.</p>
      <div class="notice mt-4"><?= icon('bell') ?><div><strong>Turn on phone notifications</strong><p>After installing, open <strong>Security</strong> in the app and tap <strong>Turn on for this device</strong> to get leave decisions, tasks, payslips and reminders.</p></div></div>
    </div>
    <div data-reveal style="text-align:center">
      <img src="/images/staff-app-qr.svg" alt="QR code linking to this page" width="220" height="220" style="background:#fff;padding:12px;border-radius:16px">
    </div>
  </div>
</section>

<?php page_end(); ?>
