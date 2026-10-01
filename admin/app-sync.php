<?php
/**
 * Delivery of form submissions to the HLTS staff app: what is waiting, what
 * failed and why, plus buttons to send now, retry and copy older submissions.
 */
require __DIR__ . '/_admin.php';
require_admin();

if (is_post()) {
    csrf_require();
    $action = $_POST['action'] ?? '';
    if (!app_sync_enabled()) {
        admin_error('Sending is switched off until the shared secret is set in config.local.php (app_sync.secret).', '/admin/app-sync.php');
    }
    if ($action === 'send') {
        $r = app_sync_run(25);
        admin_notice("Sent {$r['sent']}. Still waiting: {$r['waiting']}. Failed: {$r['failed']}.", '/admin/app-sync.php');
    }
    if ($action === 'retry') {
        $n = app_sync_retry_failed();
        $r = app_sync_run(25);
        admin_notice("$n failed submission(s) put back in the queue. Sent {$r['sent']} now.", '/admin/app-sync.php');
    }
    if ($action === 'backfill') {
        $n = app_sync_backfill();
        $r = app_sync_run(25);
        admin_notice("$n older submission(s) queued. Sent {$r['sent']} now; the rest go out automatically.", '/admin/app-sync.php');
    }
}

$counts = app_sync_counts();
$problems = db_all("SELECT s.*, l.type, l.created_at AS lead_at FROM app_sync s LEFT JOIN leads l ON l.id = s.lead_id WHERE s.status IN ('pending', 'failed') ORDER BY s.id DESC LIMIT 100");
$types = lead_types();

admin_start('Staff app sync', 'app-sync');
?>
<p class="muted">Every form submission is sent to the HLTS staff app (<?= h((string) config('app_sync.url')) ?>), where the team works on it. If the app can’t be reached, the website keeps trying with growing gaps. Job seekers’ details and CVs are removed from the website once they arrive in the app.</p>

<?php if (!app_sync_enabled()): ?>
<div class="notice notice--error mb-4" role="alert"><?= icon('exclamation-triangle') ?><p>Sending is <strong>off</strong>. Add the shared secret to <code>config/config.local.php</code> as <code>'app_sync' => ['secret' => '…']</code>, using the same value as <code>WEBSITE_WEBHOOK_SECRET</code> in Vercel.</p></div>
<?php endif; ?>

<div class="admin-stats mb-4">
<?php foreach ([['Sent', $counts['sent'], 'check2-circle'], ['Waiting', $counts['pending'], 'hourglass-split'], ['Failed', $counts['failed'], 'exclamation-triangle'], ['Older, never sent', $counts['never'], 'archive']] as $i => [$label, $value, $iconName]): ?>
  <div class="admin-stat">
    <span class="icon-tile<?= $i === 0 ? ' icon-tile--solid' : '' ?>"><?= icon($iconName) ?></span>
    <strong><?= number_format($value) ?></strong>
    <span><?= h($label) ?></span>
  </div>
<?php endforeach; ?>
</div>

<div class="d-flex flex-wrap gap-2 mb-4">
  <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="send"><button class="btn-hl btn-hl--primary" type="submit"><?= icon('send') ?> <span>Send waiting now</span></button></form>
<?php if ($counts['failed']): ?>
  <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="retry"><button class="btn-hl btn-hl--ghost" type="submit"><?= icon('arrow-repeat') ?> <span>Retry failed</span></button></form>
<?php endif; ?>
<?php if ($counts['never']): ?>
  <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="backfill"><button class="btn-hl btn-hl--ghost" type="submit"><?= icon('cloud-upload') ?> <span>Send <?= $counts['never'] ?> older submission(s) to the app</span></button></form>
<?php endif; ?>
</div>

<?php if ($problems): ?>
<div class="table-wrap">
  <table class="table-hl">
    <thead><tr><th>Submitted</th><th>Form</th><th>Status</th><th>Tries</th><th>Next try</th><th>Last problem</th></tr></thead>
    <tbody>
<?php foreach ($problems as $p): ?>
      <tr>
        <td><?= h(format_date($p['lead_at'] ?? $p['created_at'], 'j M Y, H:i')) ?></td>
        <td><?= h($types[$p['form']] ?? $p['form']) ?></td>
        <td><?= $p['status'] === 'failed' ? '<span class="chip chip--magenta">Failed</span>' : '<span class="chip">Waiting</span>' ?></td>
        <td><?= (int) $p['attempts'] ?></td>
        <td><?= $p['status'] === 'pending' ? h(format_date($p['next_attempt_at'], 'j M, H:i')) : '—' ?></td>
        <td class="small"><?= h($p['last_error'] ?: '—') ?></td>
      </tr>
<?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php else: ?>
<?= empty_state('check2-circle', 'Nothing waiting', 'Every submission has reached the staff app.') ?>
<?php endif; ?>

<?php admin_end(); ?>
