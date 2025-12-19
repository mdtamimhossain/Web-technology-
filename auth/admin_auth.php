<?php
/**
 * Admin Authentication Functions
 */

require_once __DIR__ . '/../database/db.php';

/**
 * Initialize session if not already started
 */
function initAdminSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Check if admin is logged in
 */
function isAdminLoggedIn() {
    initAdminSession();
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Get current admin ID
 */
function getCurrentAdminId() {
    initAdminSession();
    return isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : null;
}

/**
 * Get current admin data
 */
function getCurrentAdmin() {
    if (!isAdminLoggedIn()) {
        return null;
    }
    
    $pdo = getDBConnection();
    if (!$pdo) return null;
    
    $stmt = $pdo->prepare("SELECT id, username, email, name FROM admins WHERE id = ?");
    $stmt->execute([getCurrentAdminId()]);
    return $stmt->fetch();
}

/**
 * Login admin
 */
function loginAdmin($username, $password) {
    $pdo = getDBConnection();
    if (!$pdo) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    $stmt = $pdo->prepare("SELECT id, username, email, password, name FROM admins WHERE username = ? OR email = ?");
    $stmt->execute([$username, $username]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($password, $admin['password'])) {
        initAdminSession();
        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_username'] = $admin['username'];
        $_SESSION['admin_name'] = $admin['name'];
        
        return ['success' => true, 'message' => 'Login successful', 'admin' => $admin];
    }
    
    return ['success' => false, 'message' => 'Invalid username or password'];
}

/**
 * Logout admin
 */
function logoutAdmin() {
    initAdminSession();
    
    unset($_SESSION['admin_id']);
    unset($_SESSION['admin_username']);
    unset($_SESSION['admin_name']);
    
    // If no other session data, destroy session
    if (empty($_SESSION)) {
        session_destroy();
    }
}

/**
 * Require admin login - redirect if not logged in
 */
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}
?>
