<?php
/**
 * Authentication Functions
 * User login, logout ar session handle korar jonno
 */

require_once __DIR__ . '/../database/db.php';

// Session start kore jodi age start na hoy
function initSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

// User logged in kina check kore
function isLoggedIn() {
    initSession();
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Current user er ID return kore
function getCurrentUserId() {
    initSession();
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

// Current user er data return kore
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $pdo = getDBConnection();
    if (!$pdo) return null;
    
    $stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE id = ?");
    $stmt->execute([getCurrentUserId()]);
    return $stmt->fetch();
}

// Full name theke last name ber kore
function getLastName($fullName) {
    $parts = explode(' ', trim($fullName));
    return count($parts) > 1 ? end($parts) : $fullName;
}

// Current user er last name return kore
function getCurrentUserLastName() {
    initSession();
    if (isset($_SESSION['user_name'])) {
        return getLastName($_SESSION['user_name']);
    }
    return '';
}

// Email ar password diye login kore
function loginUser($email, $password) {
    $pdo = getDBConnection();
    if (!$pdo) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    $stmt = $pdo->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        initSession();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        
        return ['success' => true, 'message' => 'Login successful', 'user' => $user];
    }
    
    return ['success' => false, 'message' => 'Invalid email or password'];
}

// Notun user register kore
function registerUser($name, $email, $password) {
    $pdo = getDBConnection();
    if (!$pdo) {
        return ['success' => false, 'message' => 'Database connection failed'];
    }
    
    // Email already ache kina check kore
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Email already registered'];
    }
    
    // Password hash kore ar user insert kore
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    
    try {
        $stmt->execute([$name, $email, $hashedPassword]);
        $userId = $pdo->lastInsertId();
        
        // Registration er por auto login kore
        initSession();
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;
        
        return ['success' => true, 'message' => 'Registration successful'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => 'Registration failed'];
    }
}

// User ke logout kore
function logoutUser() {
    initSession();
    $_SESSION = [];
    
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    session_destroy();
}

// Session valid kina check kore
function validateSession() {
    initSession();
    
    if (!isLoggedIn()) {
        return false;
    }
    
    // Database e user ache kina verify kore
    $pdo = getDBConnection();
    if (!$pdo) return false;
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = ?");
    $stmt->execute([getCurrentUserId()]);
    
    return $stmt->fetch() !== false;
}
?>
