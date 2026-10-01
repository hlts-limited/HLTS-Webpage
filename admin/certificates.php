<?php
require __DIR__ . '/_admin.php';
require_admin();

if (is_post()) {
    csrf_require();
    $action = $_POST['action'] ?? '';

    if ($action === 'issue') {
        $name = trim((string) ($_POST['holder_name'] ?? ''));
        $courseTitle = trim((string) ($_POST['course'] ?? ''));
        $issued = (string) ($_POST['issued_on'] ?? date('Y-m-d'));
        $studentId = (int) ($_POST['student_id'] ?? 0) ?: null;
        if ($name === '' || $courseTitle === '' || !DateTime::createFromFormat('Y-m-d', $issued)) {
            admin_error('Enter the holder name, course and issue date.', '/admin/certificates.php');
        }
        do {
            $code = 'HLTS-' . substr($issued, 0, 4) . '-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
        } while (db_value('SELECT COUNT(*) FROM certificates WHERE code = ?', [$code]));

        db_insert('certificates', ['code' => $code, 'holder_name' => $name, 'course' => $courseTitle, 'issued_on' => $issued, 'status' => 'valid', 'student_id' => $studentId, 'created_at' => now()]);
        admin_notice("Certificate $code issued. Print this number on the certificate.", '/admin/certificates.php');
    }

    if ($action === 'toggle') {
        $cert = db_one('SELECT * FROM certificates WHERE id = ?', [(int) ($_POST['id'] ?? 0)]);
        if ($cert) {
            db_run('UPDATE certificates SET status = ? WHERE id = ?', [$cert['status'] === 'valid' ? 'revoked' : 'valid', $cert['id']]);
            admin_notice('Certificate ' . ($cert['status'] === 'valid' ? 'revoked' : 'restored') . '.', '/admin/certificates.php');
        }
    }
}

$certificates = db_all('SELECT * FROM certificates ORDER BY created_at DESC LIMIT 300');
$students = db_all("SELECT id, name, student_no, course_slug FROM students WHERE status = 'active' ORDER BY name");

admin_start('Certificates', 'certificates');
?>
<div class="admin-grid">
  <section class="panel">
    <h2>Issued certificates</h2>
<?php if ($certificates): ?>
    <div class="table-wrap"><table class="table-hl">
      <thead><tr><th>Number</th><th>Holder</th><th>Course</th><th>Issued</th><th>Status</th><th></th></tr></thead>
      <tbody>
<?php foreach ($certificates as $c): ?>
        <tr>
          <td><a href="<?= h(page_url('verify-certificate', ['code' => $c['code']])) ?>" target="_blank" rel="noopener"><code><?= h($c['code']) ?></code></a></td>
          <td><?= h($c['holder_name']) ?></td>
          <td class="small"><?= h($c['course']) ?></td>
          <td class="nowrap small"><?= h(format_date($c['issued_on'])) ?></td>
          <td><?= status_chip($c['status']) ?></td>
          <td><form method="post" data-confirm="<?= $c['status'] === 'valid' ? 'Revoke this certificate? It will show as revoked when checked.' : 'Restore this certificate?' ?>"><?= csrf_field() ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= (int) $c['id'] ?>"><button class="icon-btn" title="<?= $c['status'] === 'valid' ? 'Revoke' : 'Restore' ?>" aria-label="<?= $c['status'] === 'valid' ? 'Revoke' : 'Restore' ?>"><?= icon($c['status'] === 'valid' ? 'x-octagon' : 'arrow-counterclockwise') ?></button></form></td>
        </tr>
<?php endforeach; ?>
      </tbody>
    </table></div>
<?php else: ?>
    <?= empty_state('patch-check', 'No certificates yet', 'Issue one when a learner completes a course. Anyone can then verify it online.') ?>
<?php endif; ?>
  </section>

  <section class="panel">
    <h2>Issue a certificate</h2>
    <form method="post" class="admin-form">
      <?= csrf_field() ?><input type="hidden" name="action" value="issue">
      <div class="field"><label class="field-label" for="c-student">Student (optional)</label><div class="select-wrap"><select id="c-student" name="student_id" data-fill-certificate><option value="">Not linked to a portal account</option><?php foreach ($students as $s): ?><option value="<?= (int) $s['id'] ?>" data-name="<?= h($s['name']) ?>" data-course="<?= h(course($s['course_slug'])['title'] ?? '') ?>"><?= h($s['name'] . ' · ' . $s['student_no']) ?></option><?php endforeach; ?></select><?= icon('chevron-down') ?></div></div>
      <div class="field"><label class="field-label" for="c-name">Name on certificate</label><input id="c-name" name="holder_name" required></div>
      <div class="field"><label class="field-label" for="c-course">Course</label><input id="c-course" name="course" list="course-titles" required><datalist id="course-titles"><?php foreach (courses() as $c): ?><option value="<?= h($c['title']) ?>"><?php endforeach; ?></datalist></div>
      <div class="field"><label class="field-label" for="c-date">Issue date</label><input id="c-date" type="date" name="issued_on" value="<?= date('Y-m-d') ?>" required></div>
      <button class="btn-hl btn-hl--primary" type="submit"><?= icon('patch-plus') ?> <span>Issue certificate</span></button>
    </form>
  </section>
</div>
<?php admin_end(); ?>
