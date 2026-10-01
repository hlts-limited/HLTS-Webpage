<?php
require __DIR__ . '/lib/app.php';

$student = require_student();
$notice = flash('portal_notice');
$error = '';

if (is_post()) {
    csrf_require();
    $action = $_POST['action'] ?? '';

    if ($action === 'logout') {
        auth_logout('student');
        redirect(page_url('portal'));
    }

    if ($action === 'password') {
        $current = (string) ($_POST['current'] ?? '');
        $new = (string) ($_POST['new'] ?? '');
        if (!password_verify($current, $student['password_hash'])) {
            $error = 'Your current password is not correct.';
        } elseif (strlen($new) < 8) {
            $error = 'Choose a new password with at least 8 characters.';
        } elseif ($new !== ($_POST['confirm'] ?? '')) {
            $error = 'The new passwords do not match.';
        } else {
            db_run('UPDATE students SET password_hash = ?, must_change_password = 0 WHERE id = ?', [password_hash($new, PASSWORD_DEFAULT), $student['id']]);
            flash('portal_notice', 'Password updated.');
            redirect('/student.php');
        }
    }
}

$course = course($student['course_slug']);
$materials = db_all("SELECT * FROM materials WHERE course_slug = ? AND status = 'published' ORDER BY sort_order, id", [$student['course_slug']]);
$payments = db_all('SELECT * FROM payments WHERE email = ? ORDER BY created_at DESC', [$student['email']]);
$certificates = db_all("SELECT * FROM certificates WHERE student_id = ? AND status = 'valid'", [$student['id']]);
$firstName = explode(' ', $student['name'])[0];

page_start([
    'title' => 'My learning – HLTS Student Portal',
    'noindex' => true,
    'page' => 'student',
]);

echo page_hero([
    'eyebrow' => 'Student portal · ' . $student['student_no'],
    'title' => 'Hello, <span class="grad-text">' . h($firstName) . '.</span>',
    'lead' => $course ? 'You are enrolled in ' . $course['title'] . '.' : 'Welcome to your HLTS student portal.',
    'actions' => '<form method="post" class="d-inline">' . csrf_field() . '<input type="hidden" name="action" value="logout"><button class="btn-hl btn-hl--ghost-light" type="submit"><span>Sign out</span> ' . icon('box-arrow-right') . '</button></form>',
]);
?>

<section class="section">
  <div class="container">
<?php if ($notice): ?>
    <div class="notice mb-4" role="status"><?= icon('check-circle') ?><p><?= h($notice) ?></p></div>
<?php endif; ?>
<?php if ($student['must_change_password']): ?>
    <div class="notice mb-4"><?= icon('shield-lock') ?><p>You are using a temporary password. Please <a href="#password">set your own password</a> below.</p></div>
<?php endif; ?>

    <div class="portal-grid">
      <div class="panel" data-reveal>
        <h2><?= icon('journal-text') ?> Course materials</h2>
<?php if ($materials): ?>
<?php foreach ($materials as $m): ?>
        <a class="material" href="<?= h($m['url']) ?>" target="_blank" rel="noopener">
          <?= icon(['video' => 'play-btn', 'document' => 'file-earmark-text', 'assignment' => 'pencil-square'][$m['kind']] ?? 'link-45deg') ?>
          <span><strong><?= h($m['title']) ?></strong><br><small class="muted"><?= h(ucfirst($m['kind'])) ?></small></span>
          <?= icon('arrow-up-right') ?>
        </a>
<?php endforeach; ?>
<?php else: ?>
        <?= empty_state('hourglass-split', 'Materials coming soon', 'Your instructor will add lessons and resources here as your course begins.') ?>
<?php endif; ?>
      </div>

      <div style="display:grid; gap:20px; align-content:start">
        <div class="panel" data-reveal>
          <h2><?= icon('person-badge') ?> My details</h2>
          <dl class="review-list">
            <div><dt>Student ID</dt><dd><?= h($student['student_no']) ?></dd></div>
            <div><dt>Course</dt><dd><?= h($course['title'] ?? '—') ?></dd></div>
            <div><dt>Email</dt><dd><?= h($student['email']) ?></dd></div>
            <div><dt>Phone</dt><dd><?= h($student['phone'] ?: '—') ?></dd></div>
          </dl>
        </div>

        <div class="panel" data-reveal>
          <h2><?= icon('receipt') ?> Payments</h2>
<?php if ($payments): ?>
          <div class="table-wrap"><table class="table-hl"><thead><tr><th>Date</th><th>Amount</th><th>Status</th></tr></thead><tbody>
<?php foreach ($payments as $p): ?>
            <tr><td><?= h(format_date($p['paid_at'] ?: $p['created_at'])) ?></td><td><?= naira((int) $p['amount_kobo']) ?></td><td><span class="chip<?= $p['status'] === 'paid' ? '' : ' chip--magenta' ?>"><?= h(ucfirst($p['status'])) ?></span></td></tr>
<?php endforeach; ?>
          </tbody></table></div>
<?php else: ?>
          <p class="muted mb-0">No online payments yet. Payments made at the office are recorded by our team.</p>
<?php endif; ?>
        </div>

        <div class="panel" data-reveal>
          <h2><?= icon('patch-check') ?> Certificate</h2>
<?php if ($certificates): foreach ($certificates as $c): ?>
          <a class="material" href="<?= h(page_url('verify-certificate', ['code' => $c['code']])) ?>"><?= icon('award') ?><span><strong><?= h($c['course']) ?></strong><br><small class="muted"><?= h($c['code']) ?></small></span><?= icon('arrow-right') ?></a>
<?php endforeach; else: ?>
          <p class="muted mb-0">Your certificate appears here when you complete your course.</p>
<?php endif; ?>
        </div>
      </div>
    </div>

    <div class="panel mt-4" id="password" data-reveal>
      <h2><?= icon('key') ?> Change password</h2>
<?php if ($error): ?>
      <div class="notice notice--error mb-3" role="alert"><?= icon('exclamation-circle') ?><p><?= h($error) ?></p></div>
<?php endif; ?>
      <form method="post" class="form-grid" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); align-items: end">
        <?= csrf_field() ?>
        <input type="hidden" name="action" value="password">
        <div class="field"><label class="field-label" for="current">Current password</label><input id="current" type="password" name="current" autocomplete="current-password" required></div>
        <div class="field"><label class="field-label" for="new">New password</label><input id="new" type="password" name="new" autocomplete="new-password" minlength="8" required></div>
        <div class="field"><label class="field-label" for="confirm">Confirm new password</label><input id="confirm" type="password" name="confirm" autocomplete="new-password" minlength="8" required></div>
        <div><button class="btn-hl btn-hl--primary" type="submit"><span>Update password</span></button></div>
      </form>
    </div>
  </div>
</section>

<?php page_end(); ?>
