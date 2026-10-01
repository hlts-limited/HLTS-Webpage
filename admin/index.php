<?php
require __DIR__ . '/_admin.php';
$admin = require_admin();

$counts = [
    ['New leads', (int) db_value("SELECT COUNT(*) FROM leads WHERE status = 'new'"), 'inbox', '/admin/leads.php?status=new'],
    ['Leads this month', (int) db_value('SELECT COUNT(*) FROM leads WHERE created_at >= ?', [date('Y-m-01')]), 'graph-up-arrow', '/admin/leads.php'],
    ['Active students', (int) db_value("SELECT COUNT(*) FROM students WHERE status = 'active'"), 'person-badge', '/admin/students.php'],
    ['Paid this month', (int) db_value("SELECT COALESCE(SUM(amount_kobo), 0) FROM payments WHERE status = 'paid' AND paid_at >= ?", [date('Y-m-01')]), 'credit-card', '/admin/payments.php'],
];
$byType = db_all("SELECT type, COUNT(*) AS total, SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) AS fresh FROM leads GROUP BY type ORDER BY total DESC");
$latest = db_all('SELECT * FROM leads ORDER BY created_at DESC LIMIT 8');
$types = lead_types();

admin_start('Dashboard', 'dashboard');
?>
<p class="lead mb-4">Good <?= (int) date('H') < 12 ? 'morning' : ((int) date('H') < 17 ? 'afternoon' : 'evening') ?>, <?= h(explode(' ', $admin['name'])[0]) ?>.</p>

<div class="admin-stats">
<?php foreach ($counts as $i => [$label, $value, $iconName, $href]): ?>
  <a class="admin-stat" href="<?= h($href) ?>">
    <span class="icon-tile<?= $i === 0 ? ' icon-tile--solid' : '' ?>"><?= icon($iconName) ?></span>
    <strong><?= $i === 3 ? naira($value) : number_format($value) ?></strong>
    <span><?= h($label) ?></span>
  </a>
<?php endforeach; ?>
</div>

<div class="admin-grid">
  <section class="panel">
    <div class="panel__head"><h2>Latest enquiries</h2><a class="text-link" href="/admin/leads.php">All leads <?= icon('arrow-right') ?></a></div>
<?php if ($latest): ?>
    <div class="table-wrap"><table class="table-hl">
      <thead><tr><th>When</th><th>Type</th><th>Name</th><th>Summary</th><th>Status</th></tr></thead>
      <tbody>
<?php foreach ($latest as $lead): ?>
        <tr class="row-link" data-href="/admin/lead.php?id=<?= (int) $lead['id'] ?>">
          <td class="nowrap"><?= h(format_date($lead['created_at'], 'j M, g:ia')) ?></td>
          <td><?= h($types[$lead['type']] ?? $lead['type']) ?></td>
          <td><a href="/admin/lead.php?id=<?= (int) $lead['id'] ?>"><?= h($lead['name'] ?: $lead['email']) ?></a><br><small class="muted"><?= h($lead['organisation']) ?></small></td>
          <td><?= h($lead['summary']) ?></td>
          <td><?= status_chip($lead['status']) ?></td>
        </tr>
<?php endforeach; ?>
      </tbody>
    </table></div>
<?php else: ?>
    <?= empty_state('inbox', 'No enquiries yet', 'Form submissions from the website will appear here.') ?>
<?php endif; ?>
  </section>

  <section class="panel">
    <div class="panel__head"><h2>By form</h2></div>
<?php if ($byType): ?>
    <ul class="admin-bars">
<?php $max = max(array_column($byType, 'total')); foreach ($byType as $row): ?>
      <li>
        <a href="/admin/leads.php?type=<?= h($row['type']) ?>"><span><?= h($types[$row['type']] ?? $row['type']) ?></span><strong><?= (int) $row['total'] ?><?= $row['fresh'] ? ' <small class="chip chip--magenta">' . (int) $row['fresh'] . ' new</small>' : '' ?></strong></a>
        <span class="admin-bars__bar" style="--w: <?= round($row['total'] / $max * 100) ?>%"></span>
      </li>
<?php endforeach; ?>
    </ul>
<?php else: ?>
    <p class="muted">Nothing yet.</p>
<?php endif; ?>
    <div class="panel__head mt-4"><h2>Quick actions</h2></div>
    <div class="admin-quick">
      <a href="/admin/results.php"><?= icon('upload') ?> Upload results</a>
      <a href="/admin/certificates.php"><?= icon('patch-plus') ?> Issue certificate</a>
      <a href="/admin/content.php?type=posts&amp;action=new"><?= icon('pencil-square') ?> Write an article</a>
      <a href="/admin/content.php?type=events&amp;action=new"><?= icon('calendar-plus') ?> Add an event</a>
    </div>
<?php if (!paystack_enabled()): ?>
    <div class="notice mt-4"><?= icon('info-circle') ?><p>Online payments are off. Add Paystack keys to <code>config/config.local.php</code> to turn them on.</p></div>
<?php endif; ?>
  </section>
</div>
<?php admin_end(); ?>
