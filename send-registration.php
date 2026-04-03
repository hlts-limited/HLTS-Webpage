<?php
declare(strict_types=1);

session_start();

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');

error_reporting(0);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }

    $data = trim((string) $data);
    $data = stripslashes($data);

    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validatePhone($phone) {
    return preg_match('/^(\+234|0)[7-9][0-1]\d{8}$/', str_replace(' ', '', $phone)) === 1;
}

function logSecurityEvent($event, $details = '') {
    $logFile = sys_get_temp_dir() . '/hlts-security.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $message = "[$timestamp] [$ip] $event: $details\n";

    @file_put_contents($logFile, $message, FILE_APPEND);
}

function renderResponse($title, $message, $success) {
    $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    $accent = $success ? '#10B981' : '#EF4444';

    echo '<!DOCTYPE html>';
    echo '<html lang="en">';
    echo '<head>';
    echo '<meta charset="UTF-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
    echo '<title>' . $safeTitle . '</title>';
    echo '<style>';
    echo 'body{margin:0;font-family:Arial,sans-serif;background:#f3f6fb;color:#10223d;display:grid;place-items:center;min-height:100vh;padding:24px;}';
    echo '.card{max-width:640px;width:100%;background:#fff;border-radius:20px;padding:32px;box-shadow:0 18px 45px rgba(0,0,0,.12);border-top:6px solid ' . $accent . ';}';
    echo 'h1{margin:0 0 12px;font-size:2rem;color:#002060;}';
    echo 'p{line-height:1.6;font-size:1rem;margin:0 0 24px;}';
    echo '.actions{display:flex;gap:12px;flex-wrap:wrap;}';
    echo '.btn{display:inline-block;padding:12px 18px;border-radius:12px;text-decoration:none;font-weight:700;}';
    echo '.primary{background:#002060;color:#fff;}';
    echo '.secondary{background:#e9eef7;color:#002060;}';
    echo '</style>';
    echo '</head>';
    echo '<body>';
    echo '<main class="card">';
    echo '<h1>' . $safeTitle . '</h1>';
    echo '<p>' . $safeMessage . '</p>';
    echo '<div class="actions">';
    echo '<a class="btn primary" href="registration-form.html">Return to Registration</a>';
    echo '<a class="btn secondary" href="index.html">Go to Homepage</a>';
    echo '</div>';
    echo '</main>';
    echo '</body>';
    echo '</html>';
}

function checkRateLimit() {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $rateFile = sys_get_temp_dir() . '/hlts_rate_' . md5($ip);
    $attempts = [];

    if (file_exists($rateFile)) {
        $storedAttempts = json_decode((string) file_get_contents($rateFile), true);
        if (is_array($storedAttempts)) {
            $attempts = array_values(array_filter($storedAttempts, function ($time) {
                return $time > (time() - 60);
            }));
        }

        if (count($attempts) >= 5) {
            logSecurityEvent('RATE_LIMIT_EXCEEDED', 'IP: ' . $ip);
            http_response_code(429);
            renderResponse('Too Many Requests', 'Too many submissions were made from this browser. Please try again later.', false);
            exit;
        }
    }

    $attempts[] = time();
    @file_put_contents($rateFile, json_encode($attempts));
}

function validateCSRF() {
    $postedToken = $_POST['csrf_token'] ?? '';
    $cookieToken = $_COOKIE['hlts_csrf_token'] ?? '';
    $sessionToken = $_SESSION['csrf_token'] ?? '';
    $expectedToken = $sessionToken !== '' ? $sessionToken : $cookieToken;

    if ($postedToken === '' || $expectedToken === '' || !hash_equals($expectedToken, $postedToken)) {
        logSecurityEvent('CSRF_VALIDATION_FAILED', 'Token mismatch or missing token');
        http_response_code(403);
        renderResponse('Security Check Failed', 'The form security token was missing or invalid. Please refresh the registration page and try again.', false);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    renderResponse('Method Not Allowed', 'Please submit the registration form from the website.', false);
    exit;
}

checkRateLimit();
validateCSRF();

$name = sanitizeInput($_POST['fullName'] ?? '');
$email = trim((string) ($_POST['email'] ?? ''));
$phone = sanitizeInput($_POST['phone'] ?? '');
$program = sanitizeInput($_POST['program'] ?? '');
$message = sanitizeInput($_POST['message'] ?? '');

$programLabels = [
    'frontend' => 'Front-end Development',
    'backend' => 'Back-end Development',
    'fullstack' => 'Full-Stack Development',
    'data-analysis' => 'Data Analysis',
    'graphic-design' => 'Graphic Design',
    'video-editing' => 'Video Editing',
    'desktop-publishing' => 'Desktop Publishing',
    'visual-programming' => 'Visual Based Programming',
];

$errors = [];

if ($name === '') {
    $errors[] = 'Full name is required.';
}

if (!validateEmail($email)) {
    $errors[] = 'Please enter a valid email address.';
}

if (!validatePhone($phone)) {
    $errors[] = 'Please enter a valid Nigerian phone number.';
}

if (!array_key_exists($program, $programLabels)) {
    $errors[] = 'Please select a valid program.';
}

if (!isset($_POST['terms'])) {
    $errors[] = 'You must agree to the terms and privacy policy.';
}

if ($errors !== []) {
    http_response_code(400);
    renderResponse('Check the Form', implode(' ', $errors), false);
    exit;
}

$to = 'md@hltsltd.com';
$subject = 'New Registration from HLTS Website';
$body = "Name: {$name}\nEmail: {$email}\nPhone: {$phone}\nProgram: {$programLabels[$program]}\nMessage:\n{$message}";
$headers = [
    'From: no-reply@hltsltd.com',
    'Reply-To: ' . $email,
    'Content-Type: text/plain; charset=UTF-8',
];

$mailSent = mail($to, $subject, $body, implode("\r\n", $headers));

if ($mailSent) {
    renderResponse('Registration Sent', 'Thank you for registering. Your submission has been received successfully.', true);
    exit;
}

logSecurityEvent('MAIL_SEND_FAILED', 'Failed to send registration email');
http_response_code(500);
renderResponse('Submission Failed', 'Your form was received, but the email notification could not be sent. Please try again later.', false);
