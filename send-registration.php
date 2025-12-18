<?php
// ============================================
// HLTS REGISTRATION FORM HANDLER - SECURE
// ============================================

// Start session for CSRF token
session_start();

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');

// Error handling - don't expose sensitive info
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/php-errors.log');

// ============================================
// SECURITY FUNCTIONS
// ============================================

function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validatePhone($phone) {
    // Nigerian phone number format
    return preg_match('/^(\+234|0)[7-9][0-1]\d{8}$/', str_replace(' ', '', $phone));
}

function logSecurityEvent($event, $details = '') {
    $logFile = '/var/log/hlts-security.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $message = "[$timestamp] [$ip] $event: $details\n";
    @file_put_contents($logFile, $message, FILE_APPEND);
}

// ============================================
// RATE LIMITING
// ============================================

function checkRateLimit() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $rateFile = sys_get_temp_dir() . '/hlts_rate_' . md5($ip);
    
    if (file_exists($rateFile)) {
        $attempts = json_decode(file_get_contents($rateFile), true);
        $attempts = array_filter($attempts, function($time) {
            return $time > (time() - 60); // 1 minute window
        });
        
        if (count($attempts) >= 5) {
            logSecurityEvent('RATE_LIMIT_EXCEEDED', "IP: $ip");
            http_response_code(429);
            die(json_encode(['error' => 'Too many requests. Please try again later.']));
        }
    } else {
        $attempts = [];
    }
    
    $attempts[] = time();
    file_put_contents($rateFile, json_encode($attempts));
}

// ============================================
// CSRF PROTECTION
// ============================================

function validateCSRF() {
    if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
        logSecurityEvent('CSRF_VALIDATION_FAILED', 'Missing token');
        http_response_code(403);
        die(json_encode(['error' => 'Security validation failed']));
    }
    
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        logSecurityEvent('CSRF_VALIDATION_FAILED', 'Token mismatch');
        http_response_code(403);
        die(json_encode(['error' => 'Security validation failed']));
    }
}

// ============================================
// MAIN PROCESSING
// ============================================

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name     = htmlspecialchars($_POST['fullName']);
  $email    = htmlspecialchars($_POST['email']);
  $phone    = htmlspecialchars($_POST['phone']);
  $program  = htmlspecialchars($_POST['program']);
  $message  = htmlspecialchars($_POST['message']);

  $to       = "md@hltsltd.com";
  $subject  = "New Registration from HLTS Website";
  $body     = "Name: $name\nEmail: $email\nPhone: $phone\nProgram: $program\nMessage:\n$message";
  $headers  = "From: no-reply@hltsltd.com";

  if (mail($to, $subject, $body, $headers)) {
    echo "Thank you for registering!";
  } else {
    echo "Sorry, something went wrong.";
  }
}
?>
