<?php
require __DIR__ . '/lib/app.php';

$result = null;
$batch = null;
$error = '';
$studentId = '';
$agreed = false;

if (is_post()) {
    csrf_require();
    $studentId = strtoupper(trim((string) ($_POST['student_id'] ?? '')));
    $pin = preg_replace('/\D/', '', (string) ($_POST['pin'] ?? '')) ?? '';
    $agreed = !empty($_POST['terms']);

    if (!rate_limit('results:' . client_ip(), 10, 3600)) {
        $error = 'Too many attempts from your connection. Please wait an hour and try again.';
        log_event('RESULTS_RATE_LIMITED');
    } elseif ($studentId === '' || strlen($pin) < 6) {
        $error = 'Enter the student ID and the PIN printed on the result slip.';
    } elseif (!$agreed) {
        $error = 'Tick the box to agree to the Privacy Policy before checking a result.';
    } else {
        $result = db_one(
            'SELECT r.* FROM results r JOIN result_batches b ON b.id = r.batch_id
             WHERE r.student_ref = ? AND r.pin_hash = ? AND b.published = 1
             ORDER BY r.id DESC LIMIT 1',
            [$studentId, result_pin_hash($pin)]
        );
        if ($result) {
            $batch = db_one('SELECT * FROM result_batches WHERE id = ?', [$result['batch_id']]);
        } else {
            $error = 'We could not find a result for that student ID and PIN. Check both and try again.';
            log_event('RESULTS_NOT_FOUND', $studentId);
        }
    }
}

page_start([
    'title' => 'Check School Results – HLTS',
    'description' => 'Parents and students at HLTS partner schools can check term results online with a student ID and PIN.',
    'noindex' => (bool) $result,
]);

echo page_hero([
    'crumbs' => [['For Schools', page_url('services')], ['Check results']],
    'eyebrow' => 'Result checker',
    'title' => 'Check a result <span class="grad-text">in seconds.</span>',
    'lead' => 'For parents and students at HLTS partner schools. Use the student ID and PIN given by the school.',
]);
?>

<section class="section">
  <div class="container">
<?php if (!$result): ?>
    <div class="lookup">
      <form class="form-shell" method="post" action="/results.php" data-reveal="zoom">
        <?= csrf_field() ?>
<?php if ($error): ?>
        <div class="notice notice--error mb-3" role="alert"><?= icon('exclamation-circle') ?><p><?= h($error) ?></p></div>
<?php endif; ?>
        <div class="form-grid" style="grid-template-columns: 1fr">
          <div class="field">
            <label class="field-label" for="student_id">Student ID</label>
            <div class="input-wrap"><input id="student_id" name="student_id" value="<?= h($studentId) ?>" autocomplete="off" required placeholder="e.g. ENG/2026/014"></div>
          </div>
          <div class="field">
            <label class="field-label" for="pin">Result PIN</label>
            <div class="input-wrap"><input id="pin" name="pin" inputmode="numeric" autocomplete="off" required placeholder="10-digit PIN"></div>
            <p class="field-help">Printed on the slip from your school. Keep it private.</p>
          </div>
          <?= consent_checkbox('results-terms', !empty($agreed), 'I am the student or their parent/guardian, and I agree to the HLTS') ?>
        </div>
        <div class="form-nav"><button class="btn-hl btn-hl--primary btn-hl--block btn-hl--lg" type="submit"><span>Check result</span> <?= icon('search') ?></button></div>
      </form>
      <p class="center small muted mt-4">Lost your PIN? Contact your school. Schools: <a href="<?= h(page_url('book-demo', ['interests[]' => 'results'])) ?>">get online results for your school</a>.</p>
    </div>
<?php else: $data = json_decode($result['data'], true); ?>
    <article class="result-slip">
      <header class="result-slip__head">
        <div class="result-slip__brand">
          <img src="/images/brand/logo-192.png" alt="">
          <div><strong><?= h($batch['school'] ?: 'HLTS partner school') ?></strong><span class="muted small"><?= h($batch['title']) ?></span></div>
        </div>
        <div class="actions no-print">
          <button class="btn-hl btn-hl--secondary btn-hl--sm" type="button" data-print><?= icon('printer') ?> <span>Print</span></button>
          <a class="btn-hl btn-hl--ghost btn-hl--sm" href="/results.php"><span>Check another</span></a>
        </div>
      </header>
      <dl class="result-slip__meta">
        <div><dt>Student</dt><dd><?= h($result['student_name']) ?></dd></div>
        <div><dt>Student ID</dt><dd><?= h($result['student_ref']) ?></dd></div>
        <div><dt>Class</dt><dd><?= h($result['class_name']) ?></dd></div>
        <div><dt>Term</dt><dd><?= h(trim($batch['term'] . ' ' . $batch['session_label'])) ?></dd></div>
        <div><dt>Average</dt><dd><?= h((string) $data['average']) ?>%</dd></div>
<?php if (!empty($data['position'])): ?>
        <div><dt>Position</dt><dd><?= h($data['position']) ?></dd></div>
<?php endif; ?>
      </dl>
      <div class="table-wrap">
        <table class="table-hl">
          <thead><tr><th>Subject</th><th>Score</th><th>Grade</th><th>Remark</th></tr></thead>
          <tbody>
<?php foreach ($data['subjects'] as $s): ?>
            <tr><td><?= h($s['subject']) ?></td><td><strong><?= h((string) $s['score']) ?></strong></td><td><span class="grade-pill<?= $s['score'] < 40 ? ' grade-pill--low' : '' ?>"><?= h($s['grade']) ?></span></td><td><?= h($s['remark']) ?></td></tr>
<?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <p class="mt-4 mb-0"><strong>Overall remark:</strong> <?= h($data['remark']) ?></p>
    </article>
<?php endif; ?>
  </div>
</section>

<?php page_end(); ?>
