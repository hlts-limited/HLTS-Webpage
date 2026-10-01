<?php
require __DIR__ . '/_admin.php';
require_admin();

if (is_post()) {
    csrf_require();
    $action = $_POST['action'] ?? '';

    // Record a payment made at the office or by bank transfer outside Paystack.
    if ($action === 'record') {
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $amount = (int) round(((float) ($_POST['amount'] ?? 0)) * 100);
        $course = (string) ($_POST['course'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $amount <= 0 || !course($course)) {
            admin_error('Enter a valid email, amount and course.', '/admin/payments.php');
        }
        db_insert('payments', [
            'reference' => 'OFFLINE-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(3))),
            'email' => $email,
            'name' => trim((string) ($_POST['name'] ?? '')),
            'course_slug' => $course,
            'plan' => (string) ($_POST['plan'] ?? ''),
            'amount_kobo' => $amount,
            'status' => 'paid',
            'channel' => (string) ($_POST['channel'] ?? 'office'),
            'paid_at' => now(),
            'created_at' => now(),
        ]);
        admin_notice('Payment recorded.', '/admin/payments.php');
    }

    if ($action === 'verify' && paystack_enabled()) {
        try {
            $payment = paystack_verify((string) ($_POST['reference'] ?? ''));
            admin_notice('Checked with Paystack: ' . ($payment['status'] ?? 'unknown') . '.', '/admin/payments.php');
        } catch (Throwable $e) {
            admin_error('Could not reach Paystack: ' . $e->getMessage(), '/admin/payments.php');
        }
    }
}

$status = in_array($_GET['status'] ?? '', ['paid', 'pending', 'failed', 'review', 'abandoned'], true) ? $_GET['status'] : '';
$payments = $status
    ? db_all('SELECT * FROM payments WHERE status = ? ORDER BY created_at DESC LIMIT 300', [$status])
    : db_all('SELECT * FROM payments ORDER BY created_at DESC LIMIT 300');
$totalPaid = (int) db_value("SELECT COALESCE(SUM(amount_kobo), 0) FROM payments WHERE status = 'paid'");

admin_start('Payments', 'payments');
?>
<?php if (!paystack_enabled()): ?>
<div class="notice mb-4"><?= icon('info-circle') ?><p>Paystack is not connected, so learners cannot pay online yet. Add <code>paystack.public_key</code> and <code>paystack.secret_key</code> to <code>config/config.local.php</code>, and set the webhook URL to <code><?= h(absolute_url('paystack-webhook.php')) ?></code>.</p></div>
<?php endif; ?>

<div class="admin-grid">
  <section class="panel">
    <div class="panel__head">
      <h2>All payments · <?= naira($totalPaid) ?> received</h2>
      <div class="segmented segmented--links">
<?php foreach (['' => 'All', 'paid' => 'Paid', 'pending' => 'Pending', 'review' => 'Needs review', 'failed' => 'Failed'] as $k => $label): ?>
        <a class="<?= $status === $k ? 'is-active' : '' ?>" href="?status=<?= h($k) ?>"><?= h($label) ?></a>
<?php endforeach; ?>
      </div>
    </div>
<?php if ($payments): ?>
    <div class="table-wrap"><table class="table-hl">
      <thead><tr><th>Date</th><th>Payer</th><th>Course</th><th>Amount</th><th>Status</th><th>Reference</th></tr></thead>
      <tbody>
<?php foreach ($payments as $p): ?>
        <tr>
          <td class="nowrap small"><?= h(format_date($p['paid_at'] ?: $p['created_at'], 'j M Y, g:ia')) ?></td>
          <td><strong><?= h($p['name'] ?: '—') ?></strong><br><small class="muted"><?= h($p['email']) ?></small></td>
          <td class="small"><?= h(course($p['course_slug'])['title'] ?? $p['course_slug']) ?><br><span class="muted"><?= h(fee_plans()[$p['plan']] ?? $p['plan']) ?></span></td>
          <td><strong><?= naira((int) $p['amount_kobo']) ?></strong><br><small class="muted"><?= h($p['channel']) ?></small></td>
          <td><?= status_chip($p['status']) ?></td>
          <td class="small"><code><?= h($p['reference']) ?></code>
<?php if ($p['status'] !== 'paid' && paystack_enabled() && str_starts_with($p['reference'], 'HLTS-')): ?>
            <form method="post" class="d-inline"><?= csrf_field() ?><input type="hidden" name="action" value="verify"><input type="hidden" name="reference" value="<?= h($p['reference']) ?>"><button class="icon-btn" title="Check with Paystack" aria-label="Check with Paystack"><?= icon('arrow-repeat') ?></button></form>
<?php endif; ?>
          </td>
        </tr>
<?php endforeach; ?>
      </tbody>
    </table></div>
<?php else: ?>
    <?= empty_state('credit-card', 'No payments yet', 'Online and recorded payments will appear here.') ?>
<?php endif; ?>
  </section>

  <section class="panel">
    <h2>Record an offline payment</h2>
    <p class="small muted">For cash or transfers received outside Paystack.</p>
    <form method="post" class="admin-form">
      <?= csrf_field() ?><input type="hidden" name="action" value="record">
      <div class="field"><label class="field-label" for="p-name">Payer name</label><input id="p-name" name="name" required></div>
      <div class="field"><label class="field-label" for="p-email">Payer email</label><input id="p-email" type="email" name="email" required></div>
      <div class="field"><label class="field-label" for="p-course">Course</label><div class="select-wrap"><select id="p-course" name="course"><?php foreach (courses() as $slug => $c): ?><option value="<?= h($slug) ?>"><?= h($c['title']) ?></option><?php endforeach; ?></select><?= icon('chevron-down') ?></div></div>
      <div class="field"><label class="field-label" for="p-plan">Plan</label><div class="select-wrap"><select id="p-plan" name="plan"><?php foreach (fee_plans() as $k => $label): ?><option value="<?= h($k) ?>"><?= h($label) ?></option><?php endforeach; ?></select><?= icon('chevron-down') ?></div></div>
      <div class="field"><label class="field-label" for="p-amount">Amount (₦)</label><input id="p-amount" name="amount" type="number" min="1" step="1" required></div>
      <div class="field"><label class="field-label" for="p-channel">Method</label><div class="select-wrap"><select id="p-channel" name="channel"><option value="office">Cash at office</option><option value="transfer">Bank transfer</option><option value="pos">POS</option></select><?= icon('chevron-down') ?></div></div>
      <button class="btn-hl btn-hl--primary" type="submit"><span>Record payment</span></button>
    </form>
  </section>
</div>
<?php admin_end(); ?>
