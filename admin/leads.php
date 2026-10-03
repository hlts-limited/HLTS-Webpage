<?php
require __DIR__ . '/_admin.php';
require_admin();

$types = lead_types();
$type = isset($types[$_GET['type'] ?? '']) ? $_GET['type'] : '';
$status = isset(LEAD_STATUSES[$_GET['status'] ?? '']) ? $_GET['status'] : '';
$q = trim((string) ($_GET['q'] ?? ''));

$where = [];
$params = [];
if ($type) { $where[] = 'type = ?'; $params[] = $type; }
if ($status) { $where[] = 'status = ?'; $params[] = $status; }
if ($q !== '') {
    $where[] = '(name LIKE ? OR email LIKE ? OR phone LIKE ? OR organisation LIKE ? OR summary LIKE ?)';
    array_push($params, ...array_fill(0, 5, '%' . $q . '%'));
}
$sql = 'SELECT * FROM leads' . ($where ? ' WHERE ' . implode(' AND ', $where) : '') . ' ORDER BY created_at DESC';

// CSV export of the current filter.
if (($_GET['export'] ?? '') === 'csv') {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="hlts-leads-' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF");
    fputcsv($out, ['Date', 'Type', 'Status', 'Name', 'Email', 'Phone', 'Organisation', 'Summary', 'Details', 'Notes']);
    foreach (db_all($sql, $params) as $lead) {
        $definition = form_definition($lead['type']);
        $details = $definition ? describe_submission($definition, json_decode($lead['payload'], true) ?: []) : [];
        $detailText = implode('; ', array_map(fn ($k, $v) => "$k: $v", array_keys($details), $details));
        // Prefix cells that spreadsheet apps would treat as formulas.
        $row = array_map(fn ($v) => preg_match('/^[\s]*[=+\-@\t\r]/', (string) $v) ? "'" . $v : $v, [
            $lead['created_at'], $types[$lead['type']] ?? $lead['type'], $lead['status'], $lead['name'], $lead['email'], $lead['phone'], $lead['organisation'], $lead['summary'], $detailText, $lead['notes'],
        ]);
        fputcsv($out, $row);
    }
    fclose($out);
    exit;
}

$leads = db_all($sql . ' LIMIT 500', $params);

admin_start('Leads & enquiries', 'leads');
?>
<form class="admin-filters" method="get">
  <div class="field"><label class="field-label" for="q">Search</label><input id="q" name="q" value="<?= h($q) ?>" placeholder="Name, email, phone, school"></div>
  <div class="field"><label class="field-label" for="type">Form</label><div class="select-wrap"><select id="type" name="type"><option value="">All forms</option><?php foreach ($types as $k => $label): ?><option value="<?= h($k) ?>"<?= $type === $k ? ' selected' : '' ?>><?= h($label) ?></option><?php endforeach; ?></select><?= icon('chevron-down') ?></div></div>
  <div class="field"><label class="field-label" for="status">Status</label><div class="select-wrap"><select id="status" name="status"><option value="">Any status</option><?php foreach (LEAD_STATUSES as $k => $label): ?><option value="<?= h($k) ?>"<?= $status === $k ? ' selected' : '' ?>><?= h($label) ?></option><?php endforeach; ?></select><?= icon('chevron-down') ?></div></div>
  <div class="admin-filters__actions">
    <button class="btn-hl btn-hl--primary" type="submit"><?= icon('funnel') ?> <span>Filter</span></button>
    <a class="btn-hl btn-hl--ghost" href="?<?= h(http_build_query(['type' => $type, 'status' => $status, 'q' => $q, 'export' => 'csv'])) ?>"><?= icon('download') ?> <span>Export CSV</span></a>
  </div>
</form>

<?php if ($leads): ?>
<div class="table-wrap">
  <table class="table-hl">
    <thead><tr><th>When</th><th>Form</th><th>Name</th><th>Contact</th><th>Summary</th><th>Status</th></tr></thead>
    <tbody>
<?php foreach ($leads as $lead): ?>
      <tr class="row-link" data-href="/admin/lead.php?id=<?= (int) $lead['id'] ?>">
        <td class="nowrap"><?= h(format_date($lead['created_at'], 'j M Y, g:ia')) ?></td>
        <td><?= h($types[$lead['type']] ?? $lead['type']) ?></td>
        <td><a href="/admin/lead.php?id=<?= (int) $lead['id'] ?>"><strong><?= h($lead['name'] ?: '—') ?></strong></a><br><small class="muted"><?= h($lead['organisation']) ?></small></td>
        <td><small><?= h($lead['email']) ?><br><?= h($lead['phone']) ?></small></td>
        <td><?= h($lead['summary']) ?></td>
        <td><?= status_chip($lead['status']) ?></td>
      </tr>
<?php endforeach; ?>
    </tbody>
  </table>
</div>
<p class="small muted mt-3">Showing <?= count($leads) ?> <?= count($leads) === 500 ? '(most recent 500; use filters or export)' : '' ?></p>
<?php else: ?>
<?= empty_state('inbox', 'No leads match', 'Try a different filter, or wait for the next enquiry from the website.') ?>
<?php endif; ?>
<?php admin_end(); ?>
