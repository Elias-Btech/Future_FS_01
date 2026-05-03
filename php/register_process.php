<?php
/**
 * register_process.php - ENHANCED WITH SECURITY
 * Phase 1 Implementation: Rate Limiting + Security Logging
 */

require_once __DIR__.'/config/security.php';
require_once __DIR__.'/config/db.php';

// Initialize security
initializeSecurity();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.php');
    exit;
}

// Validate CSRF token
if (!validateCSRFToken($_POST['csrf'] ?? '')) {
    logSecurityEvent('CSRF_VALIDATION_FAILED', ['action' => 'register']);
    $_SESSION['auth_error'] = 'Invalid request. Please try again.';
    header('Location: register.php');
    exit;
}

// Rate limiting: 3 registration attempts per hour per IP
$ip = $_SERVER['REMOTE_ADDR'];
$rate_limit_key = "register_attempts_{$ip}";
if (!checkRateLimit($rate_limit_key, 3, 3600)) {
    logSecurityEvent('REGISTER_RATE_LIMIT_EXCEEDED', ['ip' => $ip]);
    $_SESSION['auth_error'] = 'Too many registration attempts. Please try again later.';
    header('Location: register.php');
    exit;
}

// Get and sanitize input
$name = htmlspecialchars(strip_tags(trim($_POST['name'] ?? '')), ENT_QUOTES, 'UTF-8');
$email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
$pass = $_POST['password'] ?? '';
$conf = $_POST['confirm'] ?? '';

// Validation errors array
$errs = [];

// Validate name
if (strlen($name) < 2 || strlen($name) > 100) {
    $errs[] = 'Name must be 2–100 characters.';
}

// Validate email
if (!$email || !validateEmail($email)) {
    $errs[] = 'Please enter a valid email.';
}

// Validate password strength
if (strlen($pass) < 8) {
    $errs[] = 'Password must be at least 8 characters.';
}
if (!preg_match('/[A-Z]/', $pass)) {
    $errs[] = 'Password must contain an uppercase letter.';
}
if (!preg_match('/[0-9]/', $pass)) {
    $errs[] = 'Password must contain a number.';
}

// Validate password confirmation
if ($pass !== $conf) {
    $errs[] = 'Passwords do not match.';
}

// If validation errors, redirect
if (!empty($errs)) {
    logSecurityEvent('REGISTER_VALIDATION_FAILED', ['email' => $email, 'errors' => count($errs)]);
    $_SESSION['auth_error'] = implode(' ', $errs);
    header('Location: register.php');
    exit;
}

// Attempt registration
try {
    $db = getDB();
    
    // Check if email already exists
    $check = $db->prepare('SELECT id FROM users WHERE email = :e LIMIT 1');
    $check->execute([':e' => $email]);
    
    if ($check->fetch()) {
        logSecurityEvent('REGISTER_EMAIL_EXISTS', ['email' => $email]);
        $_SESSION['auth_error'] = 'An account with that email already exists.';
        header('Location: register.php');
        exit;
    }
    
    // Hash password with Bcrypt (cost=12)
    $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);
    
    // Insert new user
    $insert = $db->prepare('INSERT INTO users(name, email, password) VALUES(:n, :e, :p)');
    $insert->execute([':n' => $name, ':e' => $email, ':p' => $hash]);
    
    // Log successful registration
    logSecurityEvent('REGISTER_SUCCESS', ['email' => $email, 'ip' => $ip]);
    
    $_SESSION['auth_success'] = 'Account created! Please sign in.';
    header('Location: login.php');
    exit;
    
} catch (PDOException $ex) {
    error_log('Database error during registration: ' . $ex->getMessage());
    logSecurityEvent('REGISTER_DATABASE_ERROR', ['email' => $email]);
    $_SESSION['auth_error'] = 'Server error. Please try again later.';
    header('Location: register.php');
    exit;
}
