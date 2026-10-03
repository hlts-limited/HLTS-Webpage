<?php
require __DIR__ . '/_admin.php';

if (auth_user('admin')) {
    redirect('/admin/');
}
if ((int) db_value('SELECT COUNT(*) FROM admins') === 0) {
    redirect('/admin/setup.php');
}

$error = '';
$email = '';
if (is_post()) {
    csrf_require();
    $email = trim((string) ($_POST['email'] ?? ''));
    try {
        $user = auth_attempt('admin', $email, (string) ($_POST['password'] ?? ''));
        if ($user) {
            redirect(!empty($user['pending_2fa']) ? '/admin/two-factor.php' : '/admin/');
        }
        $error = 'Email or password is incorrect.';
    } catch (RuntimeException $e) {
        $error = $e->getMessage();
    }
}

admin_start('Sign in');
?>
<form class="admin-auth__card" method="post">
  <?= csrf_field() ?>
  <a class="brand mb-3" href="/"><img src="/images/brand/logo-192.png" alt="" width="52" height="52"><span class="brand__text"><span class="brand__name">HLTS</span><span class="brand__tag">ADMIN</span></span></a>
  <h1 class="h3">Staff sign in</h1>
  <p class="muted">Leads, students, results and website content.</p>
<?php if ($error): ?>
  <div class="notice notice--error mb-3" role="alert"><?= icon('exclamation-circle') ?><p><?= h($error) ?></p></div>
<?php endif; ?>
  <div class="field mb-3"><label class="field-label" for="email">Email</label><input id="email" type="email" name="email" value="<?= h($email) ?>" autocomplete="username" required autofocus></div>
  <div class="field mb-4"><label class="field-label" for="password">Password</label><input id="password" type="password" name="password" autocomplete="current-password" required></div>
  <button class="btn-hl btn-hl--primary btn-hl--block btn-hl--lg" type="submit"><span>Sign in</span> <?= icon('box-arrow-in-right') ?></button>
</form>
<?php admin_end(); ?>
