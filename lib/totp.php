<?php
/**
 * Two-step sign-in codes for the admin area (RFC 6238 TOTP: 6 digits, 30 seconds, SHA-1), the same
 * codes Google Authenticator, Microsoft Authenticator and 1Password show.
 *
 * Secrets are stored encrypted with the site's app_key, and each code works only once.
 */

const TOTP_PERIOD = 30;
const TOTP_DIGITS = 6;

function totp_new_secret(): string
{
    return base32_encode(random_bytes(20));
}

function base32_encode(string $bytes): string
{
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $bits = '';
    foreach (str_split($bytes) as $char) {
        $bits .= str_pad(decbin(ord($char)), 8, '0', STR_PAD_LEFT);
    }
    $out = '';
    foreach (str_split($bits, 5) as $chunk) {
        $out .= $alphabet[bindec(str_pad($chunk, 5, '0', STR_PAD_RIGHT))];
    }
    return $out;
}

function base32_decode(string $text): string
{
    $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $text = strtoupper(preg_replace('/[^A-Za-z2-7]/', '', $text) ?? '');
    $bits = '';
    foreach (str_split($text) as $char) {
        $bits .= str_pad(decbin(strpos($alphabet, $char)), 5, '0', STR_PAD_LEFT);
    }
    $out = '';
    foreach (str_split($bits, 8) as $byte) {
        if (strlen($byte) === 8) {
            $out .= chr(bindec($byte));
        }
    }
    return $out;
}

/** The code for one 30-second step. */
function totp_code(string $secret, int $step): string
{
    $hash = hash_hmac('sha1', pack('J', $step), base32_decode($secret), true);
    $offset = ord($hash[19]) & 0x0F;
    $value = ((ord($hash[$offset]) & 0x7F) << 24) | (ord($hash[$offset + 1]) << 16) | (ord($hash[$offset + 2]) << 8) | ord($hash[$offset + 3]);
    return str_pad((string) ($value % (10 ** TOTP_DIGITS)), TOTP_DIGITS, '0', STR_PAD_LEFT);
}

/**
 * The step a code matches (allowing one step of clock drift either way), or null. A step at or
 * before $lastUsed is refused, so a code can't be used twice.
 */
function totp_verify(string $secret, string $code, ?int $lastUsed = null, ?int $now = null): ?int
{
    $code = preg_replace('/\D/', '', $code) ?? '';
    if (strlen($code) !== TOTP_DIGITS) {
        return null;
    }
    $current = intdiv($now ?? time(), TOTP_PERIOD);
    foreach ([0, -1, 1] as $drift) {
        $step = $current + $drift;
        if ($lastUsed !== null && $step <= $lastUsed) {
            continue;
        }
        if (hash_equals(totp_code($secret, $step), $code)) {
            return $step;
        }
    }
    return null;
}

/** Link that opens the authenticator app on a phone with the account filled in. */
function totp_uri(string $secret, string $email): string
{
    return 'otpauth://totp/' . rawurlencode('HLTS website:' . $email) . '?secret=' . $secret . '&issuer=' . rawurlencode('HLTS website') . '&period=' . TOTP_PERIOD . '&digits=' . TOTP_DIGITS;
}

/** Encrypt a secret for the database (AES-256-GCM with a key derived from app_key). */
function secret_encrypt(string $plain): string
{
    $key = hash('sha256', 'hlts-totp|' . (string) config('app_key'), true);
    $iv = random_bytes(12);
    $tag = '';
    $cipher = openssl_encrypt($plain, 'aes-256-gcm', $key, OPENSSL_RAW_DATA, $iv, $tag);
    return 'v1:' . base64_encode($iv . $tag . $cipher);
}

function secret_decrypt(?string $stored): ?string
{
    if (!$stored || !str_starts_with($stored, 'v1:')) {
        return null;
    }
    $raw = base64_decode(substr($stored, 3), true);
    if ($raw === false || strlen($raw) < 29) {
        return null;
    }
    $key = hash('sha256', 'hlts-totp|' . (string) config('app_key'), true);
    $plain = openssl_decrypt(substr($raw, 28), 'aes-256-gcm', $key, OPENSSL_RAW_DATA, substr($raw, 0, 12), substr($raw, 12, 16));
    return $plain === false ? null : $plain;
}
