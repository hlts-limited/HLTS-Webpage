<?php
require __DIR__ . '/lib/app.php';

$code = strtoupper(trim(is_string($_GET['code'] ?? null) ? $_GET['code'] : ''));
$certificate = null;
$checked = false;
$limited = false;

if ($code !== '') {
    if (!rate_limit('cert:' . client_ip(), 30, 3600)) {
        $limited = true;
    } else {
        $checked = true;
        $certificate = db_one('SELECT * FROM certificates WHERE code = ?', [$code]);
    }
}

page_start([
    'title' => 'Verify an HLTS Certificate',
    'description' => 'Check that an HLTS Online Institution certificate is genuine by entering its certificate number.',
]);

echo page_hero([
    'crumbs' => [['Online Institution', page_url('online-institution')], ['Verify a certificate']],
    'eyebrow' => 'Certificate verification',
    'title' => 'Is this certificate <span class="grad-text">genuine?</span>',
    'lead' => 'Employers, schools and learners can confirm any HLTS certificate using the number printed on it.',
]);
?>

<section class="section">
  <div class="container">
    <div class="lookup">
      <form class="form-shell" method="get" action="/verify-certificate.php" data-reveal="zoom">
        <div class="field">
          <label class="field-label" for="code">Certificate number</label>
          <div class="input-wrap"><input id="code" name="code" value="<?= h($code) ?>" placeholder="HLTS-2026-ABC123" autocomplete="off" required></div>
        </div>
        <?= consent_checkbox('cert-terms', !empty($_GET['terms'])) ?>
        <div class="form-nav"><button class="btn-hl btn-hl--primary btn-hl--block" type="submit"><span>Verify certificate</span> <?= icon('patch-check') ?></button></div>
      </form>
    </div>

<?php if ($limited): ?>
    <div class="notice notice--error mt-5" style="max-width:560px;margin-inline:auto"><?= icon('hourglass') ?><p>Too many checks from your connection. Please wait an hour and try again.</p></div>
<?php elseif ($checked && $certificate): $valid = $certificate['status'] === 'valid'; ?>
    <div class="certificate mt-5">
      <span class="certificate__status<?= $valid ? '' : ' certificate__status--bad' ?>"><?= icon($valid ? 'patch-check-fill' : 'x-octagon') ?> <?= $valid ? 'Genuine certificate' : 'This certificate has been revoked' ?></span>
      <h2><?= h($certificate['holder_name']) ?></h2>
      <p class="muted">completed</p>
      <p class="h4"><?= h($certificate['course']) ?></p>
      <dl>
        <div><dt>Certificate number</dt><dd><?= h($certificate['code']) ?></dd></div>
        <div><dt>Issued</dt><dd><?= h(format_date($certificate['issued_on'])) ?></dd></div>
        <div><dt>Issued by</dt><dd>HLTS Online Institution</dd></div>
      </dl>
      <div class="actions mt-4 no-print" style="justify-content:center"><button class="btn-hl btn-hl--ghost" type="button" data-print><?= icon('printer') ?> <span>Print</span></button></div>
    </div>
<?php elseif ($checked): ?>
    <div class="certificate mt-5">
      <span class="certificate__status certificate__status--bad"><?= icon('question-octagon') ?> No match found</span>
      <h2>We could not find that number.</h2>
      <p>Check for typing mistakes, including the dashes. If you still cannot find it, <a href="<?= h(page_url('contact')) ?>">contact us</a> with a photo of the certificate.</p>
    </div>
<?php endif; ?>
  </div>
</section>

<?php page_end(); ?>
