<?php
/** Required once for every admin: link an authenticator app, then confirm with a code. */
require __DIR__ . '/_admin.php';
$me = require_admin(true);

if (!empty($me['totp_secret'])) {
    redirect('/admin/');
}

// The new secret lives in the session until it is confirmed, so a half-finished setup changes nothing.
if (empty($_SESSION['admin_2fa_setup']) || ($_SESSION['admin_2fa_setup']['id'] ?? 0) !== (int) $me['id']) {
    $_SESSION['admin_2fa_setup'] = ['id' => (int) $me['id'], 'secret' => totp_new_secret()];
}
$secret = $_SESSION['admin_2fa_setup']['secret'];

$error = '';
if (is_post()) {
    csrf_require();
    if (!rate_limit('admin-2fa-setup:' . $me['id'], 10, 900)) {
        $error = 'Too many attempts. Wait 15 minutes and try again.';
    } else {
        $step = totp_verify($secret, (string) ($_POST['code'] ?? ''));
        if ($step === null) {
            $error = 'That code didn’t match. Check the key was typed correctly, and use the current code.';
        } else {
            db_run('UPDATE admins SET totp_secret = ?, totp_last_step = ? WHERE id = ?', [secret_encrypt($secret), $step, $me['id']]);
            unset($_SESSION['admin_2fa_setup']);
            session_regenerate_id(true);
            log_event('ADMIN_2FA_ENABLED', $me['email']);
            admin_notice('Two-step sign-in is on. You’ll be asked for a code each time you sign in.', '/admin/');
        }
    }
}

admin_start('Set up two-step sign-in');
?>
<form class="admin-auth__card" method="post">
  <?= csrf_field() ?>
  <a class="brand mb-3" href="/"><img src="/images/brand/logo-192.png" alt="" width="52" height="52"><span class="brand__text"><span class="brand__name">HLTS</span><span class="brand__tag">ADMIN</span></span></a>
  <h1 class="h3">Set up two-step sign-in</h1>
  <p class="muted">The website admin now needs a code from your phone as well as your password. This takes about a minute, once.</p>
<?php if ($error): ?>
  <div class="notice notice--error mb-3" role="alert"><?= icon('exclamation-circle') ?><p><?= h($error) ?></p></div>
<?php endif; ?>
  <ol class="small mb-3" style="padding-left:1.1rem">
    <li>Install <strong>Google Authenticator</strong> or <strong>Microsoft Authenticator</strong> on your phone (or use the one you already have for the HLTS app).</li>
    <li>On this phone, <a href="<?= h(totp_uri($secret, $me['email'])) ?>">tap here to add the account</a>. On a computer, choose “Enter a setup key” in the app and type this key:</li>
  </ol>
  <p class="mb-3"><code style="font-size:1.05rem;letter-spacing:.08em;word-break:break-all"><?= h(trim(chunk_split($secret, 4, ' '))) ?></code></p>
  <p class="small muted mb-3">Account name: HLTS website. Type of key: time based.</p>
  <div class="field mb-4"><label class="field-label" for="code">3. Enter the 6-digit code it shows</label><input id="code" name="code" inputmode="numeric" autocomplete="one-time-code" pattern="[0-9 ]{6,7}" maxlength="7" required autofocus></div>
  <button class="btn-hl btn-hl--primary btn-hl--block btn-hl--lg" type="submit"><span>Turn on two-step sign-in</span> <?= icon('shield-check') ?></button>
</form>
<form method="post" action="/admin/logout.php" class="text-center mt-3"><?= csrf_field() ?><button class="btn-hl btn-hl--ghost btn-hl--sm" type="submit"><span>Sign out</span></button></form>
<?php admin_end(); ?>
