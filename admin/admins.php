<?php
require __DIR__ . '/_admin.php';
$me = require_admin();

if (is_post()) {
    csrf_require();
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            admin_error('Enter a name and a valid email.', '/admin/admins.php');
        }
        if (db_value('SELECT COUNT(*) FROM admins WHERE email = ?', [$email])) {
            admin_error('That email already has an account.', '/admin/admins.php');
        }
        $password = generate_password(14);
        db_insert('admins', ['email' => $email, 'name' => $name, 'password_hash' => password_hash($password, PASSWORD_DEFAULT), 'created_at' => now()]);
        log_event('ADMIN_CREATED', $email . ' by ' . $me['email']);
        flash('new_admin', ['email' => $email, 'password' => $password]);
        admin_notice("Account created for $name.", '/admin/admins.php');
    }

    if ($action === 'password') {
        $new = (string) ($_POST['new'] ?? '');
        if (!password_verify((string) ($_POST['current'] ?? ''), $me['password_hash'])) {
            admin_error('Your current password is not correct.', '/admin/admins.php');
        }
        if (strlen($new) < 12 || $new !== ($_POST['confirm'] ?? '')) {
            admin_error('New passwords must match and be at least 12 characters.', '/admin/admins.php');
        }
        db_run('UPDATE admins SET password_hash = ? WHERE id = ?', [password_hash($new, PASSWORD_DEFAULT), $me['id']]);
        admin_notice('Your password has been changed.', '/admin/admins.php');
    }

    if ($action === 'reset2fa') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id === (int) $me['id']) {
            admin_error('Ask another admin to reset your two-step sign-in.', '/admin/admins.php');
        }
        db_run('UPDATE admins SET totp_secret = NULL, totp_last_step = NULL WHERE id = ?', [$id]);
        log_event('ADMIN_2FA_RESET', $id . ' by ' . $me['email']);
        admin_notice('Two-step sign-in reset. They set it up again at their next sign-in.', '/admin/admins.php');
    }

    if ($action === 'remove') {
        $id = (int) ($_POST['id'] ?? 0);
        if ($id === (int) $me['id']) {
            admin_error('You cannot remove your own account.', '/admin/admins.php');
        }
        db_run('DELETE FROM admins WHERE id = ?', [$id]);
        log_event('ADMIN_REMOVED', $id . ' by ' . $me['email']);
        admin_notice('Account removed.', '/admin/admins.php');
    }
}

$admins = db_all('SELECT * FROM admins ORDER BY name');
$newAdmin = flash('new_admin');

admin_start('Staff accounts', 'admins');
?>
<?php if ($newAdmin): ?>
<div class="notice mb-4"><?= icon('key') ?><p><strong>Share privately</strong> (shown once): <?= h($newAdmin['email']) ?> · temporary password <code><?= h($newAdmin['password']) ?></code>. Ask them to change it after signing in.</p></div>
<?php endif; ?>

<div class="admin-grid">
  <section class="panel">
    <h2>People who can sign in</h2>
    <div class="table-wrap"><table class="table-hl">
      <thead><tr><th>Name</th><th>Email</th><th>Two-step</th><th>Last sign-in</th><th></th></tr></thead>
      <tbody>
<?php foreach ($admins as $a): ?>
        <tr>
          <td><strong><?= h($a['name']) ?></strong><?= (int) $a['id'] === (int) $me['id'] ? ' <span class="chip">You</span>' : '' ?></td>
          <td><?= h($a['email']) ?></td>
          <td class="small"><?= !empty($a['totp_secret']) ? 'On' : 'Not set up' ?></td>
          <td class="small"><?= h($a['last_login'] ? format_date($a['last_login'], 'j M Y, g:ia') : 'Never') ?></td>
          <td><?php if ((int) $a['id'] !== (int) $me['id']): ?><?php if (!empty($a['totp_secret'])): ?><form method="post" class="d-inline" data-confirm="Reset <?= h($a['name']) ?>'s two-step sign-in? They set it up again with a new phone at their next sign-in."><?= csrf_field() ?><input type="hidden" name="action" value="reset2fa"><input type="hidden" name="id" value="<?= (int) $a['id'] ?>"><button class="icon-btn" aria-label="Reset two-step sign-in" title="Reset two-step sign-in"><?= icon('phone') ?></button></form><?php endif; ?><form method="post" data-confirm="Remove <?= h($a['name']) ?>'s access?"><?= csrf_field() ?><input type="hidden" name="action" value="remove"><input type="hidden" name="id" value="<?= (int) $a['id'] ?>"><button class="icon-btn" aria-label="Remove" title="Remove"><?= icon('person-x') ?></button></form><?php endif; ?></td>
        </tr>
<?php endforeach; ?>
      </tbody>
    </table></div>
  </section>

  <div class="admin-stack">
    <section class="panel">
      <h2>Add a staff member</h2>
      <form method="post" class="admin-form">
        <?= csrf_field() ?><input type="hidden" name="action" value="create">
        <div class="field"><label class="field-label" for="a-name">Name</label><input id="a-name" name="name" required></div>
        <div class="field"><label class="field-label" for="a-email">Email</label><input id="a-email" type="email" name="email" required></div>
        <button class="btn-hl btn-hl--primary" type="submit"><?= icon('person-plus') ?> <span>Create account</span></button>
      </form>
    </section>
    <section class="panel">
      <h2>Change my password</h2>
      <form method="post" class="admin-form">
        <?= csrf_field() ?><input type="hidden" name="action" value="password">
        <div class="field"><label class="field-label" for="cur">Current password</label><input id="cur" type="password" name="current" autocomplete="current-password" required></div>
        <div class="field"><label class="field-label" for="new">New password (12+ characters)</label><input id="new" type="password" name="new" autocomplete="new-password" minlength="12" required></div>
        <div class="field"><label class="field-label" for="conf">Confirm new password</label><input id="conf" type="password" name="confirm" autocomplete="new-password" minlength="12" required></div>
        <button class="btn-hl btn-hl--primary" type="submit"><span>Update password</span></button>
      </form>
    </section>
  </div>
</div>
<?php admin_end(); ?>
