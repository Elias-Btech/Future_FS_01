<?php
/**
 * php/contact.php - ENHANCED WITH SECURITY & EMAIL NOTIFICATIONS
 * Phase 1 Implementation: Honeypot + Rate Limiting + Security Logging
 * Phase 4 Enhancement: Email notifications
 */

require_once __DIR__.'/../config/security.php';
require_once __DIR__.'/../config/db.php';
require_once __DIR__.'/../config/email.php';

// Initialize security
initializeSecurity();

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(json_encode(['success' => false, 'message' => 'Method not allowed.']));
}

header('Content-Type: application/json; charset=utf-8');

// Honeypot validation (spam protection)
if (!validateHoneypot('website')) {
    // Silently fail honeypot (don't reveal it's a honeypot)
    echo json_encode(['success' => true, 'message' => 'Message received.']);
    exit;
}

// Rate limiting: 5 messages per hour per IP
$ip = $_SERVER['REMOTE_ADDR'];
$rate_limit_key = "contact_messages_{$ip}";
if (!checkRateLimit($rate_limit_key, 5, 3600)) {
    logSecurityEvent('CONTACT_RATE_LIMIT_EXCEEDED', ['ip' => $ip]);
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Too many messages. Please try again later.']);
    exit;
}

// Get and sanitize input
$name = sanitizeString(trim($_POST['name'] ?? ''), 100);
$email = trim(filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL));
$msg = sanitizeString(trim($_POST['message'] ?? ''), 5000);

// Validation errors
$errs = [];

// Validate name
if (strlen($name) < 2 || strlen($name) > 100) {
    $errs[] = 'Name must be 2–100 characters.';
}

// Validate email
if (!$email || !validateEmail($email)) {
    $errs[] = 'Please provide a valid email.';
}

// Validate message
if (strlen($msg) < 10 || strlen($msg) > 5000) {
    $errs[] = 'Message must be 10–5000 characters.';
}

// If validation errors
if (!empty($errs)) {
    logSecurityEvent('CONTACT_VALIDATION_FAILED', ['email' => $email, 'errors' => count($errs)]);
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => implode(' ', $errs)]);
    exit;
}

// Save message to database
try {
    $stmt = getDB()->prepare(
        'INSERT INTO contact_messages(name, email, message, ip_address) 
         VALUES(:n, :e, :m, :i)'
    );
    
    $stmt->execute([
        ':n' => $name,
        ':e' => $email,
        ':m' => $msg,
        ':i' => $ip
    ]);
    
    // Log successful message
    logSecurityEvent('CONTACT_MESSAGE_RECEIVED', ['email' => $email, 'ip' => $ip]);
    
    // Try to send email notifications (don't fail if email isn't configured)
    try {
        // Admin notification
        sendContactNotification([
            'name' => $name,
            'email' => $email,
            'message' => $msg
        ]);
        
        // User confirmation
        sendContactConfirmation($email, $name);
    } catch (Exception $e) {
        // Email failed but message was saved - that's okay
        error_log('Email notification failed: ' . $e->getMessage());
    }
    
    // Redirect to success page instead of JSON response
    header('Location: ../contact-success.php?name=' . urlencode($name));
    exit;
    
} catch (PDOException $ex) {
    error_log('Database error in contact form: ' . $ex->getMessage());
    logSecurityEvent('CONTACT_DATABASE_ERROR', ['email' => $email]);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Server error. Please try again.']);
    exit;
}

