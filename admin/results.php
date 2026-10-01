<?php
require __DIR__ . '/_admin.php';
require_admin();

// Example CSV so staff know the expected columns.
if (($_GET['template'] ?? '') === '1') {
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="hlts-results-template.csv"');
    echo "student_id,student_name,class,Mathematics,English Language,Basic Science,Computer Studies,position,remark\n";
    echo "ENG/2026/001,Adaeze Okafor,JSS 2A,86,74,68,91,2nd,Excellent work this term\n";
    echo "ENG/2026/002,Tunde Bello,JSS 2A,58,63,71,80,,\n";
    exit;
}

$slips = null;
$batchView = null;

if (is_post()) {
    csrf_require();
    $action = $_POST['action'] ?? '';

    if ($action === 'upload') {
        $title = trim((string) ($_POST['title'] ?? ''));
        $file = $_FILES['csv'] ?? null;
        if ($title === '' || !$file || $file['error'] !== UPLOAD_ERR_OK) {
            admin_error('Add a title and choose a CSV file.', '/admin/results.php');
        }
        if ($file['size'] > 2 * 1024 * 1024 || !preg_match('/\.csv$/i', (string) $file['name'])) {
            admin_error('Upload a .csv file under 2 MB. In Excel use File → Save As → CSV.', '/admin/results.php');
        }

        [$rows, $errors] = parse_results_csv($file['tmp_name']);
        if ($errors) {
            admin_error('Nothing was saved. Fix these rows and upload again: ' . implode(' ', array_slice($errors, 0, 8)) . (count($errors) > 8 ? ' …and ' . (count($errors) - 8) . ' more.' : ''), '/admin/results.php');
        }
        if (!$rows) {
            admin_error('No student rows found in the file.', '/admin/results.php');
        }

        $pdo = db();
        $pdo->beginTransaction();
        try {
            $batchId = db_insert('result_batches', [
                'title' => $title,
                'school' => trim((string) ($_POST['school'] ?? '')),
                'session_label' => trim((string) ($_POST['session_label'] ?? '')),
                'term' => trim((string) ($_POST['term'] ?? '')),
                'published' => empty($_POST['publish']) ? 0 : 1,
                'created_at' => now(),
            ]);
            $slips = [];
            foreach ($rows as $row) {
                $pin = generate_pin();
                db_insert('results', [
                    'batch_id' => $batchId,
                    'student_ref' => $row['student_ref'],
                    'student_name' => $row['student_name'],
                    'class_name' => $row['class_name'],
                    'pin_hash' => result_pin_hash($pin),
                    'data' => json_encode($row['data']),
                    'created_at' => now(),
                ]);
                $slips[] = $row + ['pin' => $pin];
            }
            $pdo->commit();
        } catch (Throwable $e) {
            $pdo->rollBack();
            error_log('Results upload failed: ' . $e->getMessage());
            admin_error('Upload failed. Nothing was saved.', '/admin/results.php');
        }
        $batchView = db_one('SELECT * FROM result_batches WHERE id = ?', [$batchId]);
        log_event('RESULTS_UPLOADED', "$batchId (" . count($slips) . ' students)');
    }

    if ($action === 'publish') {
        db_run('UPDATE result_batches SET published = 1 - published WHERE id = ?', [(int) $_POST['id']]);
        admin_notice('Batch visibility updated.', '/admin/results.php');
    }

    if ($action === 'delete') {
        $id = (int) $_POST['id'];
        db_run('DELETE FROM results WHERE batch_id = ?', [$id]);
        db_run('DELETE FROM result_batches WHERE id = ?', [$id]);
        admin_notice('Batch and its results deleted.', '/admin/results.php');
    }

    if ($action === 'repin') {
        $result = db_one('SELECT * FROM results WHERE id = ?', [(int) $_POST['id']]);
        if ($result) {
            $pin = generate_pin();
            db_run('UPDATE results SET pin_hash = ? WHERE id = ?', [result_pin_hash($pin), $result['id']]);
            $batchView = db_one('SELECT * FROM result_batches WHERE id = ?', [$result['batch_id']]);
            $slips = [['student_ref' => $result['student_ref'], 'student_name' => $result['student_name'], 'class_name' => $result['class_name'], 'pin' => $pin]];
        }
    }
}

$batches = db_all('SELECT b.*, (SELECT COUNT(*) FROM results r WHERE r.batch_id = b.id) AS students FROM result_batches b ORDER BY b.created_at DESC');
$viewId = (int) ($_GET['batch'] ?? 0);
$viewing = $viewId ? db_one('SELECT * FROM result_batches WHERE id = ?', [$viewId]) : null;
$viewRows = $viewing ? db_all('SELECT id, student_ref, student_name, class_name, data FROM results WHERE batch_id = ? ORDER BY class_name, student_name', [$viewId]) : [];

admin_start('Results', 'results');
?>

<?php if ($slips): ?>
<section class="panel mb-4 pin-slips-panel">
  <div class="panel__head no-print">
    <h2><?= icon('key') ?> PIN slips for <?= h($batchView['title'] ?? '') ?></h2>
    <button class="btn-hl btn-hl--primary" type="button" data-print><?= icon('printer') ?> <span>Print slips</span></button>
  </div>
  <div class="notice notice--error mb-3 no-print"><?= icon('exclamation-triangle') ?><p><strong>Print or save these now.</strong> PINs are stored securely and cannot be shown again. You can issue a new PIN for any student later.</p></div>
  <div class="pin-slips">
<?php foreach ($slips as $slip): ?>
    <div class="pin-slip">
      <img src="/images/brand/logo-64.png" alt="" width="32" height="32">
      <strong><?= h($slip['student_name']) ?></strong>
      <span><?= h($slip['class_name']) ?> · <?= h($batchView['title'] ?? '') ?></span>
      <dl><div><dt>Student ID</dt><dd><?= h($slip['student_ref']) ?></dd></div><div><dt>PIN</dt><dd><?= h(chunk_split($slip['pin'], 5, ' ')) ?></dd></div></dl>
      <small>Check at <?= h(absolute_url('results.html')) ?></small>
    </div>
<?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<?php if ($viewing): ?>
<section class="panel mb-4 no-print">
  <div class="panel__head"><h2><?= h($viewing['title']) ?> · <?= count($viewRows) ?> students</h2><a class="text-link" href="/admin/results.php">Close <?= icon('x') ?></a></div>
  <div class="table-wrap"><table class="table-hl">
    <thead><tr><th>Student</th><th>Class</th><th>Average</th><th>Subjects</th><th></th></tr></thead>
    <tbody>
<?php foreach ($viewRows as $r): $d = json_decode($r['data'], true); ?>
      <tr>
        <td><strong><?= h($r['student_name']) ?></strong><br><small class="muted"><?= h($r['student_ref']) ?></small></td>
        <td><?= h($r['class_name']) ?></td>
        <td><?= h((string) $d['average']) ?>%</td>
        <td class="small"><?= h(implode(', ', array_map(fn ($s) => $s['subject'] . ' ' . $s['score'], $d['subjects']))) ?></td>
        <td><form method="post" data-confirm="Issue a new PIN? The old PIN will stop working."><?= csrf_field() ?><input type="hidden" name="action" value="repin"><input type="hidden" name="id" value="<?= (int) $r['id'] ?>"><button class="btn-hl btn-hl--ghost btn-hl--sm" type="submit"><?= icon('key') ?> <span>New PIN</span></button></form></td>
      </tr>
<?php endforeach; ?>
    </tbody>
  </table></div>
</section>
<?php endif; ?>

<div class="admin-grid no-print">
  <section class="panel">
    <h2>Result batches</h2>
<?php if ($batches): ?>
    <div class="table-wrap"><table class="table-hl">
      <thead><tr><th>Batch</th><th>Students</th><th>Visible to parents</th><th></th></tr></thead>
      <tbody>
<?php foreach ($batches as $b): ?>
        <tr>
          <td><a href="?batch=<?= (int) $b['id'] ?>"><strong><?= h($b['title']) ?></strong></a><br><small class="muted"><?= h(trim($b['school'] . ' · ' . $b['term'] . ' ' . $b['session_label'], ' ·')) ?> · <?= h(format_date($b['created_at'])) ?></small></td>
          <td><?= (int) $b['students'] ?></td>
          <td><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="publish"><input type="hidden" name="id" value="<?= (int) $b['id'] ?>"><button class="chip <?= $b['published'] ? '' : 'chip--line' ?>" type="submit"><?= icon($b['published'] ? 'eye' : 'eye-slash') ?> <?= $b['published'] ? 'Published' : 'Hidden' ?></button></form></td>
          <td><form method="post" data-confirm="Delete this batch and all its results? Parents will no longer be able to check them."><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $b['id'] ?>"><button class="icon-btn" aria-label="Delete batch" title="Delete batch"><?= icon('trash') ?></button></form></td>
        </tr>
<?php endforeach; ?>
      </tbody>
    </table></div>
<?php else: ?>
    <?= empty_state('clipboard-data', 'No results uploaded yet', 'Upload a CSV of scores to let parents check results online.') ?>
<?php endif; ?>
  </section>

  <section class="panel">
    <h2>Upload results</h2>
    <p class="small">One row per student: <code>student_id</code>, <code>student_name</code>, <code>class</code>, then one column per subject with the total score (0–100). Optional: <code>position</code>, <code>remark</code>. <a href="?template=1">Download a template</a>.</p>
    <form method="post" enctype="multipart/form-data" class="admin-form">
      <?= csrf_field() ?><input type="hidden" name="action" value="upload">
      <div class="field"><label class="field-label" for="r-title">Batch title</label><input id="r-title" name="title" placeholder="JSS 2 – First Term 2026/2027" required></div>
      <div class="field"><label class="field-label" for="r-school">School</label><input id="r-school" name="school" placeholder="Engreg School"></div>
      <div class="grid grid--2" style="gap:12px">
        <div class="field"><label class="field-label" for="r-term">Term</label><input id="r-term" name="term" placeholder="First Term"></div>
        <div class="field"><label class="field-label" for="r-session">Session</label><input id="r-session" name="session_label" placeholder="2026/2027"></div>
      </div>
      <div class="field"><label class="field-label" for="r-csv">CSV file</label><input id="r-csv" type="file" name="csv" accept=".csv,text/csv" required></div>
      <label class="consent"><input type="checkbox" name="publish" value="1" checked><span class="consent__box" aria-hidden="true"><?= icon('check-lg') ?></span><span>Make visible to parents straight away</span></label>
      <button class="btn-hl btn-hl--primary" type="submit"><?= icon('upload') ?> <span>Upload and create PINs</span></button>
    </form>
  </section>
</div>
<?php admin_end(); ?>
