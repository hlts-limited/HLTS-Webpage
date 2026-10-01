<?php
/**
 * First-run setup: creates the first staff account.
 * Only works while there are no admin accounts at all.
 */

require __DIR__ . '/_admin.php';

if ((int) db_value('SELECT COUNT(*) FROM admins') > 0) {
    redirect('/admin/login.php');
}

// The setup link needs the secret from config.local.php ("setup_token"), so
// nobody else can claim the first account between upload and setup.
$expectedToken = (string) config('setup_token', '');
$token = (string) ($_POST['token'] ?? ($_GET['token'] ?? ''));
if ($expectedToken === '' || !hash_equals($expectedToken, $token)) {
    http_response_code(403);
    admin_start('Setup locked');
    echo '<div class="admin-auth__card"><h1 class="h3">Setup is locked</h1><p>Add a <code>setup_token</code> to <code>config/config.local.php</code>, then open <code>/admin/setup.php?token=YOUR_TOKEN</code>.</p></div>';
    admin_end();
    exit;
}

$errors = [];
$name = '';
$email = '';

if (is_post()) {
    csrf_require();
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = strtolower(trim((string) ($_POST['email'] ?? '')));
    $password = (string) ($_POST['password'] ?? '');

    if ($name === '') $errors[] = 'Enter your name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email.';
    if (strlen($password) < 12) $errors[] = 'Use a password with at least 12 characters.';
    if ($password !== ($_POST['confirm'] ?? '')) $errors[] = 'Passwords do not match.';

    if (!$errors) {
        db_insert('admins', ['email' => $email, 'name' => $name, 'password_hash' => password_hash($password, PASSWORD_DEFAULT), 'created_at' => now()]);
        log_event('ADMIN_SETUP', $email);
        auth_attempt('admin', $email, $password);
        admin_notice('Welcome! Your admin account is ready.', '/admin/');
    }
}

admin_start('Set up admin');
?>
<form class="admin-auth__card" method="post">
  <?= csrf_field() ?>
  <input type="hidden" name="token" value="<?= h($token) ?>">
  <a class="brand mb-3" href="/"><img src="/images/brand/logo-192.png" alt="" width="52" height="52"><span class="brand__text"><span class="brand__name">HLTS</span><span class="brand__tag">ADMIN</span></span></a>
  <h1 class="h3">Create the first staff account</h1>
  <p class="muted">This page only works once. After this, new staff are added from Staff accounts.</p>
<?php if ($errors): ?>
  <div class="notice notice--error mb-3" role="alert"><?= icon('exclamation-circle') ?><p><?= h(implode(' ', $errors)) ?></p></div>
<?php endif; ?>
  <div class="field mb-3"><label class="field-label" for="name">Your name</label><input id="name" name="name" value="<?= h($name) ?>" required></div>
  <div class="field mb-3"><label class="field-label" for="email">Email</label><input id="email" type="email" name="email" value="<?= h($email) ?>" autocomplete="username" required></div>
  <div class="field mb-3"><label class="field-label" for="password">Password (12+ characters)</label><input id="password" type="password" name="password" autocomplete="new-password" minlength="12" required></div>
  <div class="field mb-4"><label class="field-label" for="confirm">Confirm password</label><input id="confirm" type="password" name="confirm" autocomplete="new-password" minlength="12" required></div>
  <button class="btn-hl btn-hl--primary btn-hl--block btn-hl--lg" type="submit"><span>Create account</span></button>
</form>
<?php admin_end(); ?>
