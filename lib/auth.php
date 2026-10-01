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
    $_SESSION["auth_$kind"] = ['id' => (int) $user['id'], 'seen' => time()];
    db_run("UPDATE " . ($kind === 'admin' ? 'admins' : 'students') . ' SET last_login = ? WHERE id = ?', [now(), $user['id']]);

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

function require_admin(): array
{
    $user = auth_user('admin');
    if (!$user) {
        redirect('/admin/login.php');
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
