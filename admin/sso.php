<?php
/**
 * "Sign in with the HLTS staff app": one sign-in for people who use both.
 *
 *   1. /admin/sso.php            puts a random value in this browser's session and sends the
 *                                person to the staff app.
 *   2. The staff app             checks they are signed in there with two-step sign-in on, and
 *                                sends them back with a signed pass that lasts one minute.
 *   3. /admin/sso.php?pass=…     checks the signature, the time and the random value, then signs
 *                                them in IF their email is already listed under Staff accounts.
 *
 * The staff app never decides who is a website admin: it only vouches for who the person is.
 * The pass is signed with the shared secret (config app_sync.secret) and can be used once.
 */
require __DIR__ . '/_admin.php';

if (auth_user('admin')) {
    redirect('/admin/');
}
if (!app_sync_enabled() || app_base_url() === '') {
    admin_error('Signing in with the staff app isn’t switched on yet. Use your email and password.', '/admin/login.php');
}

$pass = $_GET['pass'] ?? null;

// Step 1: start.
if (!is_string($pass)) {
    $state = bin2hex(random_bytes(16));
    $_SESSION['admin_sso'] = ['state' => $state, 'at' => time()];
    redirect(app_base_url() . '/website-sso?state=' . $state);
}

// Step 3: come back with a pass.
$fail = function (string $why, string $message): never {
    log_event('SSO_REFUSED', $why);
    admin_error($message, '/admin/login.php');
};
$limitKey = 'login:admin-sso:' . client_ip();
if (!rate_limit($limitKey, 10, 900)) {
    $fail('rate limited', 'Too many sign-in attempts. Wait 15 minutes and try again.');
}

$expected = $_SESSION['admin_sso'] ?? null;
unset($_SESSION['admin_sso']); // one try per start
$claims = sso_read_pass($pass, trim((string) config('app_sync.secret')));
if ($claims === null) {
    $fail('bad signature or format', 'That sign-in didn’t work. Please try again.');
}
if (!$expected || time() - (int) $expected['at'] > 600 || !hash_equals((string) $expected['state'], $claims['state'])) {
    $fail('state mismatch', 'That sign-in was started in a different browser or has expired. Please try again.');
}

$user = db_one('SELECT * FROM admins WHERE email = ?', [$claims['email']]);
if (!$user) {
    $fail('not an admin: ' . $claims['email'], 'Your staff app email isn’t listed as a website admin. Ask an existing admin to add you under Staff accounts, using the same email.');
}

rate_limit_clear($limitKey);
unset($_SESSION['admin_2fa_pending']);
session_regenerate_id(true);
$_SESSION['auth_admin'] = ['id' => (int) $user['id'], 'seen' => time()];
db_run('UPDATE admins SET last_login = ? WHERE id = ?', [now(), $user['id']]);
log_event('ADMIN_SSO_LOGIN', (string) $user['id']);
redirect('/admin/');
