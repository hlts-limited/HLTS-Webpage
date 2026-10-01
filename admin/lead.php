<?php
require __DIR__ . '/_admin.php';
require_admin();

$id = (int) ($_GET['id'] ?? 0);
$lead = db_one('SELECT * FROM leads WHERE id = ?', [$id]);
if (!$lead) {
    admin_error('That lead no longer exists.', '/admin/leads.php');
}

$payload = json_decode($lead['payload'], true) ?: [];
$definition = form_definition($lead['type']);
$details = $definition ? describe_submission($definition, $payload) : $payload;
$student = $lead['type'] === 'student' ? db_one('SELECT * FROM students WHERE lead_id = ? OR email = ?', [$lead['id'], $lead['email']]) : null;
$payments = $lead['email'] ? db_all('SELECT * FROM payments WHERE email = ? ORDER BY created_at DESC', [$lead['email']]) : [];

if (is_post()) {
    csrf_require();
    $action = $_POST['action'] ?? '';

    if ($action === 'update') {
        $status = isset(LEAD_STATUSES[$_POST['status'] ?? '']) ? $_POST['status'] : $lead['status'];
        db_run('UPDATE leads SET status = ?, notes = ?, updated_at = ? WHERE id = ?', [$status, trim((string) ($_POST['notes'] ?? '')), now(), $id]);
        admin_notice('Lead updated.', "/admin/lead.php?id=$id");
    }

    if ($action === 'enrol' && $lead['type'] === 'student' && !$student) {
        $password = generate_password();
        $studentNo = next_student_no();
        db_insert('students', [
            'student_no' => $studentNo,
            'name' => $lead['name'],
            'email' => $lead['email'],
            'phone' => $lead['phone'],
            'course_slug' => (string) ($payload['course'] ?? ''),
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'must_change_password' => 1,
            'status' => 'active',
            'lead_id' => $id,
            'created_at' => now(),
        ]);
        db_run('UPDATE leads SET status = ?, updated_at = ? WHERE id = ?', ['won', now(), $id]);

        $sent = !empty($_POST['email_login']) && send_mail(
            $lead['email'],
            'Your HLTS student portal login',
            "Hello {$lead['name']},\n\nWelcome to HLTS Online Institution! Your student portal is ready.\n\nStudent ID: $studentNo\nTemporary password: $password\nSign in: " . absolute_url('portal.html') . "\n\nPlease change your password after signing in.\n\nHLTS Online Institution"
        );
        flash('new_password', ['student_no' => $studentNo, 'password' => $password, 'emailed' => $sent]);
        admin_notice("Student account $studentNo created.", "/admin/lead.php?id=$id");
    }

    if ($action === 'delete') {
        db_run('DELETE FROM leads WHERE id = ?', [$id]);
        log_event('LEAD_DELETED', (string) $id);
        admin_notice('Lead deleted.', '/admin/leads.php');
    }
}

$newPassword = flash('new_password');
admin_start(($definition['title'] ?? 'Lead') . ' #' . $id, 'leads');
?>
<a class="text-link mb-3 d-inline-flex" href="/admin/leads.php"><?= icon('arrow-left') ?> All leads</a>

<?php if ($newPassword): ?>
<div class="notice mb-4"><?= icon('key') ?><div><p><strong>Share these login details with the student</strong> (shown once):<br>Student ID <code><?= h($newPassword['student_no']) ?></code> · Temporary password <code><?= h($newPassword['password']) ?></code></p><p class="small"><?= $newPassword['emailed'] ? 'Also emailed to the student.' : 'Not emailed.' ?></p></div></div>
<?php endif; ?>

<div class="admin-grid">
  <section class="panel">
    <div class="panel__head"><h2><?= h($lead['name'] ?: $lead['email']) ?></h2><?= status_chip($lead['status']) ?></div>
    <p class="muted small mb-3">Received <?= h(format_date($lead['created_at'], 'l j F Y, g:ia')) ?> · IP <?= h($lead['ip']) ?></p>
    <div class="actions mb-4">
<?php if ($lead['phone']): ?>
      <a class="btn-hl btn-hl--secondary btn-hl--sm" href="tel:<?= h($lead['phone']) ?>"><?= icon('telephone') ?> <span>Call</span></a>
      <a class="btn-hl btn-hl--secondary btn-hl--sm" href="https://wa.me/<?= h(ltrim(preg_replace('/^0/', '234', $lead['phone']) ?? '', '+')) ?>" target="_blank" rel="noopener"><?= icon('whatsapp') ?> <span>WhatsApp</span></a>
<?php endif; ?>
<?php if ($lead['email']): ?>
      <a class="btn-hl btn-hl--secondary btn-hl--sm" href="mailto:<?= h($lead['email']) ?>"><?= icon('envelope') ?> <span>Email</span></a>
<?php endif; ?>
    </div>
    <dl class="review-list">
<?php foreach ($details as $label => $value): ?>
      <div><dt><?= h($label) ?></dt><dd><?= nl2br(h(is_array($value) ? implode(', ', $value) : (string) $value)) ?></dd></div>
<?php endforeach; ?>
    </dl>
  </section>

  <div class="admin-stack">
    <section class="panel">
      <h2>Follow-up</h2>
      <form method="post">
        <?= csrf_field() ?><input type="hidden" name="action" value="update">
        <div class="field mb-3"><label class="field-label" for="status">Status</label><div class="select-wrap"><select id="status" name="status"><?php foreach (LEAD_STATUSES as $k => $label): ?><option value="<?= h($k) ?>"<?= $lead['status'] === $k ? ' selected' : '' ?>><?= h($label) ?></option><?php endforeach; ?></select><?= icon('chevron-down') ?></div></div>
        <div class="field mb-3"><label class="field-label" for="notes">Notes</label><textarea id="notes" name="notes" rows="5" placeholder="Calls, next steps, quotes sent…"><?= h($lead['notes']) ?></textarea></div>
        <button class="btn-hl btn-hl--primary" type="submit"><span>Save</span></button>
      </form>
    </section>

<?php if ($lead['type'] === 'student'): ?>
    <section class="panel">
      <h2>Student account</h2>
<?php if ($student): ?>
      <p>Enrolled as <a href="/admin/students.php?q=<?= h(urlencode($student['student_no'])) ?>"><strong><?= h($student['student_no']) ?></strong></a>.</p>
<?php else: ?>
      <p class="small">Create a portal account once the learner is confirmed. A temporary password is generated.</p>
      <form method="post">
        <?= csrf_field() ?><input type="hidden" name="action" value="enrol">
        <label class="consent mb-3"><input type="checkbox" name="email_login" value="1" checked><span class="consent__box" aria-hidden="true"><?= icon('check-lg') ?></span><span>Email the login details to <?= h($lead['email']) ?></span></label>
        <button class="btn-hl btn-hl--primary" type="submit"><?= icon('person-plus') ?> <span>Enrol as student</span></button>
      </form>
<?php endif; ?>
    </section>
<?php endif; ?>

<?php if ($payments): ?>
    <section class="panel">
      <h2>Payments</h2>
<?php foreach ($payments as $p): ?>
      <p class="mb-1"><?= naira((int) $p['amount_kobo']) ?> · <?= status_chip($p['status']) ?> <small class="muted"><?= h($p['reference']) ?></small></p>
<?php endforeach; ?>
    </section>
<?php endif; ?>

    <form method="post" data-confirm="Delete this lead permanently?">
      <?= csrf_field() ?><input type="hidden" name="action" value="delete">
      <button class="btn-hl btn-hl--ghost btn-hl--sm" type="submit"><?= icon('trash') ?> <span>Delete lead</span></button>
    </form>
  </div>
</div>
<?php admin_end(); ?>
