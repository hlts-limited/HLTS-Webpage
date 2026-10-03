<?php
/**
 * Sign-in for HLTS staff (admin area) and students (portal).
 *
 * Passwords are stored with password_hash(). Sign-in attempts are rate
 * limited, the session id changes on sign-in, and idle sessions expire.
 */

const SESSION_IDLE_SECONDS = 7200;

function auth_attempt(string $kind, string $identifier, string $password): ?array
{
    $identifier = trim($identifier);
    $limitKey = "login:$kind:" . client_ip();

    if (!rate_limit($limitKey, 8, 900)) {
        log_event('LOGIN_RATE_LIMITED', "$kind $identifier");
        throw new RuntimeException('Too many sign-in attempts. Wait 15 minutes and try again.');
    }

    if ($kind === 'admin') {
        $user = db_one('SELECT * FROM admins WHERE email = ?', [strtolower($identifier)]);
    } else {
        $user = db_one('SELECT * FROM students WHERE (student_no = ? OR email = ?) AND status = ?', [strtoupper($identifier), strtolower($identifier), 'active']);
    }

    // Verify against a dummy hash when the user doesn't exist, so timing doesn't reveal valid accounts.
    $hash = $user['password_hash'] ?? password_hash(random_bytes(8), PASSWORD_DEFAULT);
    if (!password_verify($password, $hash) || !$user) {
        log_event('LOGIN_FAILED', "$kind $identifier");
        return null;
    }

    rate_limit_clear($limitKey);
    session_regenerate_id(true);
    // Admins with two-step sign-in: the password is only the first step.
    if ($kind === 'admin' && !empty($user['totp_secret'])) {
        $_SESSION['admin_2fa_pending'] = ['id' => (int) $user['id'], 'at' => time()];
        return $user + ['pending_2fa' => true];
    }
    $_SESSION["auth_$kind"] = ['id' => (int) $user['id'], 'seen' => time()];
    db_run("UPDATE " . ($kind === 'admin' ? 'admins' : 'students') . ' SET last_login = ? WHERE id = ?', [now(), $user['id']]);

    return $user;
}

/** Second step of admin sign-in: the 6-digit code. Returns the admin, or null with $error set. */
function auth_admin_code(string $code, ?string &$error = null): ?array
{
    $pending = $_SESSION['admin_2fa_pending'] ?? null;
    if (!$pending || time() - (int) $pending['at'] > 600) {
        unset($_SESSION['admin_2fa_pending']);
        $error = 'Your sign-in timed out. Enter your email and password again.';
        return null;
    }
    $limitKey = 'login:admin-2fa:' . $pending['id'];
    if (!rate_limit($limitKey, 5, 900)) {
        log_event('LOGIN_2FA_RATE_LIMITED', (string) $pending['id']);
        $error = 'Too many wrong codes. Wait 15 minutes and try again.';
        return null;
    }
    $user = db_one('SELECT * FROM admins WHERE id = ?', [$pending['id']]);
    $secret = $user ? secret_decrypt($user['totp_secret']) : null;
    $step = $secret ? totp_verify($secret, $code, isset($user['totp_last_step']) ? (int) $user['totp_last_step'] : null) : null;
    if ($step === null) {
        log_event('LOGIN_2FA_FAILED', (string) $pending['id']);
        $error = 'That code didn’t match. Codes change every 30 seconds: use the current one.';
        return null;
    }
    rate_limit_clear($limitKey);
    unset($_SESSION['admin_2fa_pending']);
    session_regenerate_id(true);
    $_SESSION['auth_admin'] = ['id' => (int) $user['id'], 'seen' => time()];
    db_run('UPDATE admins SET totp_last_step = ?, last_login = ? WHERE id = ?', [$step, now(), $user['id']]);
    return $user;
}

function auth_user(string $kind): ?array
{
    $session = $_SESSION["auth_$kind"] ?? null;
    if (!$session) {
        return null;
    }
    if (time() - (int) $session['seen'] > SESSION_IDLE_SECONDS) {
        unset($_SESSION["auth_$kind"]);
        return null;
    }
    $_SESSION["auth_$kind"]['seen'] = time();

    static $cache = [];
    if (!isset($cache[$kind])) {
        $table = $kind === 'admin' ? 'admins' : 'students';
        $cache[$kind] = db_one("SELECT * FROM $table WHERE id = ?", [$session['id']]);
    }
    return $cache[$kind];
}

function auth_logout(string $kind): void
{
    unset($_SESSION["auth_$kind"]);
    session_regenerate_id(true);
}

function require_admin(bool $allowWithout2fa = false): array
{
    $user = auth_user('admin');
    if (!$user) {
        redirect('/admin/login.php');
    }
    // Every admin must have two-step sign-in; until they do, the only page they can use is its setup.
    if (!$allowWithout2fa && empty($user['totp_secret'])) {
        redirect('/admin/two-factor-setup.php');
    }
    return $user;
}

function require_student(): array
{
    $user = auth_user('student');
    if (!$user || $user['status'] !== 'active') {
        redirect(page_url('portal'));
    }
    return $user;
}

function generate_password(int $length = 10): string
{
    $alphabet = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $alphabet[random_int(0, strlen($alphabet) - 1)];
    }
    return $password;
}
