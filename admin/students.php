<?php
require __DIR__ . '/_admin.php';
require_admin();

if (is_post()) {
    csrf_require();
    $action = $_POST['action'] ?? '';
    $id = (int) ($_POST['id'] ?? 0);

    if ($action === 'create') {
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $course = (string) ($_POST['course'] ?? '');
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || !course($course)) {
            admin_error('Enter a name, a valid email and a course.', '/admin/students.php');
        }
        if (db_value('SELECT COUNT(*) FROM students WHERE email = ?', [$email])) {
            admin_error('A student with that email already exists.', '/admin/students.php');
        }
        $password = generate_password();
        $studentNo = next_student_no();
        db_insert('students', [
            'student_no' => $studentNo, 'name' => $name, 'email' => $email,
            'phone' => normalise_phone((string) ($_POST['phone'] ?? '')), 'course_slug' => $course,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT), 'must_change_password' => 1,
            'status' => 'active', 'created_at' => now(),
        ]);
        flash('new_password', ['student_no' => $studentNo, 'password' => $password]);
        admin_notice("Student $studentNo created.", '/admin/students.php');
    }

    $student = db_one('SELECT * FROM students WHERE id = ?', [$id]);
    if ($student && $action === 'reset') {
        $password = generate_password();
        db_run('UPDATE students SET password_hash = ?, must_change_password = 1 WHERE id = ?', [password_hash($password, PASSWORD_DEFAULT), $id]);
        flash('new_password', ['student_no' => $student['student_no'], 'password' => $password]);
        admin_notice('Password reset.', '/admin/students.php');
    }
    if ($student && $action === 'toggle') {
        db_run('UPDATE students SET status = ? WHERE id = ?', [$student['status'] === 'active' ? 'inactive' : 'active', $id]);
        admin_notice('Student status updated.', '/admin/students.php');
    }
    if ($student && $action === 'course' && course((string) ($_POST['course'] ?? ''))) {
        db_run('UPDATE students SET course_slug = ? WHERE id = ?', [$_POST['course'], $id]);
        admin_notice('Course updated.', '/admin/students.php');
    }
}

$q = trim((string) ($_GET['q'] ?? ''));
$students = $q === ''
    ? db_all('SELECT * FROM students ORDER BY created_at DESC LIMIT 300')
    : db_all('SELECT * FROM students WHERE student_no LIKE ? OR name LIKE ? OR email LIKE ? ORDER BY created_at DESC', array_fill(0, 3, "%$q%"));
$newPassword = flash('new_password');

admin_start('Students', 'students');
?>
<?php if ($newPassword): ?>
<div class="notice mb-4"><?= icon('key') ?><p><strong>Share with the student</strong> (shown once): Student ID <code><?= h($newPassword['student_no']) ?></code> · Temporary password <code><?= h($newPassword['password']) ?></code></p></div>
<?php endif; ?>

<div class="admin-grid">
  <section class="panel">
    <form class="admin-filters mb-3" method="get" style="grid-template-columns: 1fr auto">
      <div class="field"><label class="visually-hidden" for="q">Search</label><input id="q" name="q" value="<?= h($q) ?>" placeholder="Search by ID, name or email"></div>
      <div class="admin-filters__actions"><button class="btn-hl btn-hl--primary" type="submit"><?= icon('search') ?> <span>Search</span></button></div>
    </form>
<?php if ($students): ?>
    <div class="table-wrap"><table class="table-hl">
      <thead><tr><th>Student</th><th>Course</th><th>Last sign-in</th><th>Status</th><th></th></tr></thead>
      <tbody>
<?php foreach ($students as $s): ?>
        <tr>
          <td><strong><?= h($s['name']) ?></strong><br><small class="muted"><?= h($s['student_no']) ?> · <?= h($s['email']) ?></small></td>
          <td>
            <form method="post" class="inline-select"><?= csrf_field() ?><input type="hidden" name="action" value="course"><input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
              <select name="course" aria-label="Course for <?= h($s['name']) ?>" data-autosubmit><?php foreach (courses() as $slug => $c): ?><option value="<?= h($slug) ?>"<?= $s['course_slug'] === $slug ? ' selected' : '' ?>><?= h($c['title']) ?></option><?php endforeach; ?></select>
            </form>
          </td>
          <td class="nowrap small"><?= h($s['last_login'] ? format_date($s['last_login'], 'j M, g:ia') : 'Never') ?></td>
          <td><?= status_chip($s['status']) ?></td>
          <td class="nowrap">
            <form method="post" class="d-inline" data-confirm="Reset this student's password?"><?= csrf_field() ?><input type="hidden" name="action" value="reset"><input type="hidden" name="id" value="<?= (int) $s['id'] ?>"><button class="icon-btn" title="Reset password" aria-label="Reset password"><?= icon('key') ?></button></form>
            <form method="post" class="d-inline"><?= csrf_field() ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= (int) $s['id'] ?>"><button class="icon-btn" title="<?= $s['status'] === 'active' ? 'Deactivate' : 'Activate' ?>" aria-label="<?= $s['status'] === 'active' ? 'Deactivate' : 'Activate' ?>"><?= icon($s['status'] === 'active' ? 'pause-circle' : 'play-circle') ?></button></form>
          </td>
        </tr>
<?php endforeach; ?>
      </tbody>
    </table></div>
<?php else: ?>
    <?= empty_state('person-badge', 'No students yet', 'Enrol learners from their registration lead, or add one here.') ?>
<?php endif; ?>
  </section>

  <section class="panel">
    <h2>Add a student</h2>
    <p class="small muted">Usually you enrol from the registration lead. Use this for walk-in registrations.</p>
    <form method="post" class="admin-form">
      <?= csrf_field() ?><input type="hidden" name="action" value="create">
      <div class="field"><label class="field-label" for="s-name">Full name</label><input id="s-name" name="name" required></div>
      <div class="field"><label class="field-label" for="s-email">Email</label><input id="s-email" type="email" name="email" required></div>
      <div class="field"><label class="field-label" for="s-phone">Phone</label><input id="s-phone" name="phone"></div>
      <div class="field"><label class="field-label" for="s-course">Course</label><div class="select-wrap"><select id="s-course" name="course" required><?php foreach (courses() as $slug => $c): ?><option value="<?= h($slug) ?>"><?= h($c['title']) ?></option><?php endforeach; ?></select><?= icon('chevron-down') ?></div></div>
      <button class="btn-hl btn-hl--primary" type="submit"><?= icon('person-plus') ?> <span>Create student</span></button>
    </form>
  </section>
</div>
<?php admin_end(); ?>
