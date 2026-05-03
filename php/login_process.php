<?php
/**
 * login_process.php - ENHANCED WITH SECURITY
 * Phase 1 Implementation: Rate Limiting + Security Logging
 */

require_once __DIR__.'/config/security.php';
require_once __DIR__.'/config/db.php';

// Initialize security (CSRF, headers, session config)
initializeSecurity();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// Validate CSRF token
if (!validateCSRFToken($_POST['csrf'] ?? '')) {
    logSecurityEvent('CSRF_VALIDATION_FAILED', ['ip' => $_SERVER['REMOTE_ADDR']]);
    $_SESSION['auth_error'] = 'Invalid request. Please try again.';
    header('Location: login.php');
    exit;
}

// Get and validate input
$email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
$password = $_POST['password'] ?? '';

// Validate email format
if (!$email || !validateEmail($email)) {
    logSecurityEvent('LOGIN_INVALID_EMAIL', ['email' => $email]);
    $_SESSION['auth_error'] = 'Please enter a valid email address.';
    header('Location: login.php');
    exit;
}

// Validate password provided
if (!$password) {
    $_SESSION['auth_error'] = 'Password is required.';
    header('Location: login.php');
    exit;
}

// Rate limiting: 5 attempts per 15 minutes
$rate_limit_key = "login_attempts_{$email}";
if (!checkRateLimit($rate_limit_key, 5, 900)) {
    logSecurityEvent('LOGIN_RATE_LIMIT_EXCEEDED', ['email' => $email]);
    $_SESSION['auth_error'] = 'Too many login attempts. Please try again in 15 minutes.';
    header('Location: login.php');
    exit;
}

// Attempt authentication
try {
    $stmt = getDB()->prepare('SELECT id, name, email, password FROM users WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();
    
    // Verify password (timing-safe comparison)
    if (!$user || !password_verify($password, $user['password'])) {
        logSecurityEvent('LOGIN_FAILED', ['email' => $email]);
        $_SESSION['auth_error'] = 'Invalid email or password.';
        header('Location: login.php');
        exit;
    }
    
    // Successful login - reset rate limit
    resetRateLimit($rate_limit_key);
    
    // Regenerate session ID (prevent session fixation)
    regenerateSessionID();
    
    // Store user data in session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['login_time'] = time();
    
    // Log successful login
    logSecurityEvent('LOGIN_SUCCESS', ['user_id' => $user['id'], 'email' => $email]);
    
    // Redirect to dashboard
    header('Location: dashboard.php');
    exit;
    
} catch (PDOException $ex) {
    error_log('Database error during login: ' . $ex->getMessage());
    logSecurityEvent('LOGIN_DATABASE_ERROR', ['email' => $email]);
    $_SESSION['auth_error'] = 'Server error. Please try again later.';
    header('Location: login.php');
    exit;
}
