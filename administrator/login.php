<?php
require_once __DIR__ . '/../auth/admin_auth.php';

// If already logged in, go to dashboard
if (isAdminLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$debug = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Debug: Check what's being received
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch();
    
    if (!$admin) {
        $debug = "No admin found with username: " . htmlspecialchars($username);
    } else {
        $debug = "Admin found. Password verify: " . (password_verify($password, $admin['password']) ? "TRUE" : "FALSE");
    }
    
    if (adminLogin($username, $password)) {
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        body { font-family: Arial; background: #f5f5f5; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 350px; }
        h1 { text-align: center; color: #333; margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; color: #555; }
        input[type="text"], input[type="password"] { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        button:hover { background: #0056b3; }
        .error { background: #ffe6e6; color: #cc0000; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; }
        .hint { text-align: center; color: #666; font-size: 12px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h1>Admin Login</h1>
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($debug): ?>
            <div style="background:#e6f3ff; color:#0066cc; padding:10px; border-radius:5px; margin-bottom:20px; text-align:center; font-size:12px;"><?php echo $debug; ?></div>
        <?php endif; ?>
        <form method="POST" autocomplete="off">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" autocomplete="off" placeholder="admin" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" autocomplete="new-password" placeholder="admin123" required>
            </div>
            <button type="submit">Login</button>
        </form>
        <p class="hint">Default: admin / admin123</p>
    </div>
</body>
</html>
