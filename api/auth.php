<?php
/**
 * Authentication API Endpoint
 * Handles login, logout, and registration
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/db.php';

// Get the action from POST
$action = $_POST['action'] ?? $_GET['action'] ?? '';

$response = ['success' => false, 'message' => 'Invalid action'];

switch ($action) {
    case 'login':
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        
        if (empty($email) || empty($password)) {
            $response = ['success' => false, 'message' => 'Email and password are required'];
            break;
        }
        
        $pdo = getDBConnection();
        if (!$pdo) {
            $response = ['success' => false, 'message' => 'Database connection failed'];
            break;
        }
        
        $stmt = $pdo->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            initSession();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            
            $response = [
                'success' => true,
                'message' => 'Login successful',
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'email' => $user['email']
                ]
            ];
        } else {
            $response = ['success' => false, 'message' => 'Invalid email or password'];
        }
        break;
        
    case 'register':
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');
        
        if (empty($name) || empty($email) || empty($password)) {
            $response = ['success' => false, 'message' => 'Name, email, and password are required'];
            break;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response = ['success' => false, 'message' => 'Invalid email format'];
            break;
        }
        
        if (strlen($password) < 6) {
            $response = ['success' => false, 'message' => 'Password must be at least 6 characters'];
            break;
        }
        
        $pdo = getDBConnection();
        if (!$pdo) {
            $response = ['success' => false, 'message' => 'Database connection failed'];
            break;
        }
        
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $response = ['success' => false, 'message' => 'Email already registered'];
            break;
        }
        
        // Create new user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)");
        
        try {
            $stmt->execute([$name, $email, $hashedPassword, $phone, $address]);
            $userId = $pdo->lastInsertId();
            
            // Auto login after registration
            initSession();
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            
            $response = [
                'success' => true,
                'message' => 'Registration successful',
                'user' => [
                    'id' => $userId,
                    'name' => $name,
                    'email' => $email
                ]
            ];
        } catch (PDOException $e) {
            $response = ['success' => false, 'message' => 'Registration failed. Please try again.'];
        }
        break;
        
    case 'logout':
        initSession();
        session_destroy();
        $response = ['success' => true, 'message' => 'Logged out successfully'];
        break;
        
    case 'check':
        $response = [
            'success' => true,
            'isLoggedIn' => isLoggedIn(),
            'user' => isLoggedIn() ? [
                'id' => $_SESSION['user_id'],
                'name' => $_SESSION['user_name'] ?? '',
                'email' => $_SESSION['user_email'] ?? ''
            ] : null
        ];
        break;
        
    default:
        $response = ['success' => false, 'message' => 'Unknown action'];
}

echo json_encode($response);
?>
