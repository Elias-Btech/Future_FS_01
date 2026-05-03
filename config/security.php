<?php
/**
 * config/security.php
 * Enhanced security functions for production-ready portfolio
 * Includes: CSRF, rate limiting, session management, security headers
 */

// ===== CSRF TOKEN MANAGEMENT =====
function generateCSRFToken() {
    if (!isset($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function validateCSRFToken($token) {
    return hash_equals($_SESSION['csrf'] ?? '', $token ?? '');
}

// ===== SESSION SECURITY =====
function initializeSecureSession() {
    // Only configure session if not already started
    if (session_status() === PHP_SESSION_NONE) {
        // Session configuration BEFORE session_start()
        ini_set('session.use_strict_mode', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_samesite', 'Strict');
        
        // HTTPS only in production
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            ini_set('session.cookie_secure', 1);
        }
        
        session_start();
    }
}

function enforceSessionTimeout($timeout = 1800) {
    if (isset($_SESSION['last_activity']) && 
        (time() - $_SESSION['last_activity']) > $timeout) {
        session_destroy();
        return false;
    }
    $_SESSION['last_activity'] = time();
    return true;
}

function regenerateSessionID() {
    session_regenerate_id(true);
}

// ===== RATE LIMITING =====
function checkRateLimit($key, $max_attempts = 5, $window = 900) {
    // Using APCu for in-memory caching (requires APCu extension)
    // Fallback to file-based if APCu not available
    
    if (extension_loaded('apcu')) {
        $attempts = apcu_fetch($key) ?? 0;
        
        if ($attempts >= $max_attempts) {
            return false;
        }
        
        apcu_store($key, $attempts + 1, $window);
        return true;
    } else {
        // Fallback: file-based rate limiting
        $cache_dir = sys_get_temp_dir() . '/portfolio_cache';
        if (!is_dir($cache_dir)) {
            mkdir($cache_dir, 0755, true);
        }
        
        $cache_file = $cache_dir . '/' . md5($key) . '.txt';
        $now = time();
        
        if (file_exists($cache_file)) {
            $data = json_decode(file_get_contents($cache_file), true);
            
            if ($now - $data['timestamp'] < $window) {
                if ($data['attempts'] >= $max_attempts) {
                    return false;
                }
                $data['attempts']++;
            } else {
                $data = ['attempts' => 1, 'timestamp' => $now];
            }
        } else {
            $data = ['attempts' => 1, 'timestamp' => $now];
        }
        
        file_put_contents($cache_file, json_encode($data));
        return true;
    }
}

function resetRateLimit($key) {
    if (extension_loaded('apcu')) {
        apcu_delete($key);
    } else {
        $cache_dir = sys_get_temp_dir() . '/portfolio_cache';
        $cache_file = $cache_dir . '/' . md5($key) . '.txt';
        if (file_exists($cache_file)) {
            unlink($cache_file);
        }
    }
}

// ===== INPUT VALIDATION =====
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validateURL($url) {
    if (empty($url)) return true; // Optional field
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

function sanitizeString($string, $max_length = 255) {
    $string = trim($string);
    $string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    return substr($string, 0, $max_length);
}

function validatePassword($password) {
    $errors = [];
    
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = 'Password must contain an uppercase letter.';
    }
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must contain a number.';
    }
    
    return $errors;
}

function validateEmoji($emoji) {
    // Validate emoji character
    return preg_match('/^[\x{1F300}-\x{1F9FF}]$/u', $emoji) === 1;
}

// ===== SECURITY HEADERS =====
function setSecurityHeaders() {
    // Prevent MIME type sniffing
    header('X-Content-Type-Options: nosniff');
    
    // Prevent clickjacking
    header('X-Frame-Options: SAMEORIGIN');
    
    // Enable XSS protection
    header('X-XSS-Protection: 1; mode=block');
    
    // Referrer policy
    header('Referrer-Policy: strict-origin-when-cross-origin');
    
    // Permissions policy
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    
    // Content Security Policy (adjust as needed)
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://fonts.googleapis.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self'");
}

// ===== LOGGING =====
function logSecurityEvent($event_type, $details = []) {
    $log_file = __DIR__ . '/../logs/security.log';
    
    // Create logs directory if it doesn't exist
    if (!is_dir(dirname($log_file))) {
        mkdir(dirname($log_file), 0755, true);
    }
    
    $log_entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'event_type' => $event_type,
        'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'details' => $details
    ];
    
    file_put_contents($log_file, json_encode($log_entry) . PHP_EOL, FILE_APPEND);
}

// ===== HONEYPOT VALIDATION =====
function validateHoneypot($field_name = 'website') {
    return empty($_POST[$field_name] ?? '');
}

// ===== IP BLOCKING =====
function isIPBlocked($ip = null) {
    $ip = $ip ?? $_SERVER['REMOTE_ADDR'];
    $blocked_ips_file = __DIR__ . '/../config/blocked_ips.txt';
    
    if (!file_exists($blocked_ips_file)) {
        return false;
    }
    
    $blocked_ips = file($blocked_ips_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return in_array($ip, $blocked_ips);
}

function blockIP($ip) {
    $blocked_ips_file = __DIR__ . '/../config/blocked_ips.txt';
    file_put_contents($blocked_ips_file, $ip . PHP_EOL, FILE_APPEND);
    logSecurityEvent('IP_BLOCKED', ['ip' => $ip]);
}

// ===== INITIALIZATION =====
// Call this at the start of every PHP file that needs security
function initializeSecurity() {
    initializeSecureSession();
    setSecurityHeaders();
    generateCSRFToken();
    
    // Check if IP is blocked
    if (isIPBlocked()) {
        http_response_code(403);
        die('Access denied.');
    }
}

?>
