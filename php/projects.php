<?php
/**
 * php/projects.php - ENHANCED WITH SECURITY
 * Phase 1 Implementation: CSRF + URL Validation + Security Logging
 */

require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../config/db.php';

// Initialize security
initializeSecurity();

header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// ===== PUBLIC: Fetch all projects =====
if ($method === 'GET' && $action === 'all') {
    try {
        $rows = getDB()
            ->query('SELECT * FROM projects ORDER BY sort_order ASC, id DESC')
            ->fetchAll();
        echo json_encode(['success' => true, 'projects' => $rows]);
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        logSecurityEvent('PROJECT_FETCH_ERROR');
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error.']);
    }
    exit;
}

// ===== ALL WRITE ACTIONS REQUIRE AUTHENTICATION =====
if (!isset($_SESSION['user_id'])) {
    logSecurityEvent('UNAUTHORIZED_PROJECT_ACCESS', ['action' => $action]);
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit;
}

// ===== CSRF VALIDATION FOR ALL POST REQUESTS =====
if ($method === 'POST') {
    if (!validateCSRFToken($_POST['csrf'] ?? '')) {
        logSecurityEvent('CSRF_VALIDATION_FAILED', ['action' => $action, 'user_id' => $_SESSION['user_id']]);
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        exit;
    }
}

// ===== VALIDATION HELPER =====
function validateProjectData(&$title, &$desc, &$tech, &$cat, &$emoji, &$gh, &$live) {
    $errors = [];
    
    // Sanitize inputs
    $title = sanitizeString(trim($title), 100);
    $desc = sanitizeString(trim($desc), 500);
    $tech = sanitizeString(trim($tech), 200);
    $emoji = trim($emoji);
    $gh = trim($gh);
    $live = trim($live);
    
    // Validate title
    if (strlen($title) < 3 || strlen($title) > 100) {
        $errors[] = 'Title must be 3-100 characters.';
    }
    
    // Validate description
    if (strlen($desc) < 10 || strlen($desc) > 500) {
        $errors[] = 'Description must be 10-500 characters.';
    }
    
    // Validate tech stack
    if (strlen($tech) < 3 || strlen($tech) > 200) {
        $errors[] = 'Tech stack must be 3-200 characters.';
    }
    
    // Validate category
    if (!in_array($cat, ['fullstack', 'frontend', 'backend'])) {
        $cat = 'fullstack';
    }
    
    // Validate emoji
    if (!empty($emoji) && !validateEmoji($emoji)) {
        $errors[] = 'Invalid emoji.';
    }
    
    // Validate URLs
    if (!empty($gh) && !validateURL($gh)) {
        $errors[] = 'Invalid GitHub URL.';
    }
    if (!empty($live) && !validateURL($live)) {
        $errors[] = 'Invalid Live URL.';
    }
    
    return $errors;
}

// ===== ADD PROJECT =====
if ($method === 'POST' && $action === 'add') {
    $title = $_POST['title'] ?? '';
    $desc = $_POST['description'] ?? '';
    $tech = $_POST['tech'] ?? '';
    $cat = $_POST['category'] ?? 'fullstack';
    $emoji = $_POST['emoji'] ?? '🚀';
    $gh = $_POST['github_url'] ?? '';
    $live = $_POST['live_url'] ?? '';
    $sort = (int)($_POST['sort_order'] ?? 0);
    
    // Validate all inputs
    $errors = validateProjectData($title, $desc, $tech, $cat, $emoji, $gh, $live);
    
    if (!empty($errors)) {
        logSecurityEvent('PROJECT_ADD_VALIDATION_FAILED', ['errors' => count($errors), 'user_id' => $_SESSION['user_id']]);
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
        exit;
    }
    
    try {
        $stmt = getDB()->prepare(
            'INSERT INTO projects (title, description, tech, category, emoji, github_url, live_url, sort_order)
             VALUES (:t, :d, :tc, :c, :e, :g, :l, :s)'
        );
        
        $stmt->execute([
            ':t' => $title,
            ':d' => $desc,
            ':tc' => $tech,
            ':c' => $cat,
            ':e' => $emoji,
            ':g' => !empty($gh) ? $gh : null,
            ':l' => !empty($live) ? $live : null,
            ':s' => $sort
        ]);
        
        logSecurityEvent('PROJECT_ADDED', ['title' => $title, 'user_id' => $_SESSION['user_id']]);
        echo json_encode(['success' => true, 'message' => 'Project added successfully.']);
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        logSecurityEvent('PROJECT_ADD_ERROR', ['user_id' => $_SESSION['user_id']]);
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error.']);
    }
    exit;
}

// ===== EDIT PROJECT =====
if ($method === 'POST' && $action === 'edit') {
    $id = (int)($_POST['id'] ?? 0);
    $title = $_POST['title'] ?? '';
    $desc = $_POST['description'] ?? '';
    $tech = $_POST['tech'] ?? '';
    $cat = $_POST['category'] ?? 'fullstack';
    $emoji = $_POST['emoji'] ?? '🚀';
    $gh = $_POST['github_url'] ?? '';
    $live = $_POST['live_url'] ?? '';
    $sort = (int)($_POST['sort_order'] ?? 0);
    
    // Validate ID
    if (!$id) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Invalid project ID.']);
        exit;
    }
    
    // Validate all inputs
    $errors = validateProjectData($title, $desc, $tech, $cat, $emoji, $gh, $live);
    
    if (!empty($errors)) {
        logSecurityEvent('PROJECT_EDIT_VALIDATION_FAILED', ['id' => $id, 'errors' => count($errors), 'user_id' => $_SESSION['user_id']]);
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
        exit;
    }
    
    try {
        $stmt = getDB()->prepare(
            'UPDATE projects SET title=:t, description=:d, tech=:tc, category=:c, emoji=:e,
             github_url=:g, live_url=:l, sort_order=:s WHERE id=:id'
        );
        
        $stmt->execute([
            ':t' => $title,
            ':d' => $desc,
            ':tc' => $tech,
            ':c' => $cat,
            ':e' => $emoji,
            ':g' => !empty($gh) ? $gh : null,
            ':l' => !empty($live) ? $live : null,
            ':s' => $sort,
            ':id' => $id
        ]);
        
        logSecurityEvent('PROJECT_UPDATED', ['id' => $id, 'title' => $title, 'user_id' => $_SESSION['user_id']]);
        echo json_encode(['success' => true, 'message' => 'Project updated successfully.']);
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        logSecurityEvent('PROJECT_EDIT_ERROR', ['id' => $id, 'user_id' => $_SESSION['user_id']]);
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error.']);
    }
    exit;
}

// ===== DELETE PROJECT =====
if ($method === 'POST' && $action === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    
    if (!$id) {
        http_response_code(422);
        echo json_encode(['success' => false, 'message' => 'Invalid project ID.']);
        exit;
    }
    
    try {
        $stmt = getDB()->prepare('DELETE FROM projects WHERE id = :id');
        $stmt->execute([':id' => $id]);
        
        logSecurityEvent('PROJECT_DELETED', ['id' => $id, 'user_id' => $_SESSION['user_id']]);
        echo json_encode(['success' => true, 'message' => 'Project deleted successfully.']);
    } catch (PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        logSecurityEvent('PROJECT_DELETE_ERROR', ['id' => $id, 'user_id' => $_SESSION['user_id']]);
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Server error.']);
    }
    exit;
}

// Unknown action
http_response_code(400);
echo json_encode(['success' => false, 'message' => 'Unknown action.']);
