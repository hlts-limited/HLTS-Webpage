<?php
/** Second step of admin sign-in: the 6-digit code from the authenticator app. */
require __DIR__ . '/_admin.php';

if (admin_full()) {
    redirect('/admin/');
}
if (empty($_SESSION['admin_2fa_pending'])) {
    redirect('/admin/login.php');
}

$error = '';
if (is_post()) {
    csrf_require();
    if (auth_admin_code((string) ($_POST['code'] ?? ''), $error)) {
        redirect('/admin/');
    }
}

admin_start('Two-step sign-in');
?>
<form class="admin-auth__card" method="post">
  <?= csrf_field() ?>
  <a class="brand mb-3" href="/"><img src="/images/brand/logo-192.png" alt="" width="52" height="52"><span class="brand__text"><span class="brand__name">HLTS</span><span class="brand__tag">ADMIN</span></span></a>
  <h1 class="h3">Enter your code</h1>
  <p class="muted">Open your authenticator app and type the 6-digit code for “HLTS website”.</p>
<?php if ($error): ?>
  <div class="notice notice--error mb-3" role="alert"><?= icon('exclamation-circle') ?><p><?= h($error) ?></p></div>
<?php endif; ?>
  <div class="field mb-4"><label class="field-label" for="code">6-digit code</label><input id="code" name="code" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9 ]{6,7}" maxlength="7" required autofocus></div>
  <button class="btn-hl btn-hl--primary btn-hl--block btn-hl--lg" type="submit"><span>Sign in</span> <?= icon('shield-check') ?></button>
  <p class="small muted mt-3">Lost your phone? Ask another admin to reset your two-step sign-in.</p>
</form>
<?php admin_end(); ?>
