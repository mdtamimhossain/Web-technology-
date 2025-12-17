<?php
require_once './../includes/db.php';

// Check if already logged in
if (isLoggedIn()) {
    $redirect = $_GET['redirect'] ?? 'homePage.php';
    header('Location: ' . $redirect);
    exit;
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Email and password are required';
    } else {
        $pdo = getDBConnection();
        if ($pdo) {
            $stmt = $pdo->prepare("SELECT id, name, email, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                initSession();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                
                $redirect = $_GET['redirect'] ?? 'homePage.php';
                header('Location: ' . $redirect);
                exit;
            } else {
                $error = 'Invalid email or password';
            }
        } else {
            $error = 'Database connection failed';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Krist</title>
  <link rel="stylesheet" href="./../CSS/log.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <!-- Left side with image background -->
  <div class="left-section">
    <div class="logo">Krist</div>
  </div>

  <!-- Right side with login form -->
  <div class="right-section">
    <div class="login-box">
      <h1>Welcome Back</h1>
      <p>Please login to your account</p>

      <?php if ($error): ?>
      <div class="error-alert">
        <i class="fa fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
      </div>
      <?php endif; ?>
      
      <?php if ($success): ?>
      <div class="success-alert">
        <i class="fa fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
      </div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="form-group">
          <label for="email">Email Address</label>
          <input class="form-input" type="email" id="email" name="email" 
                 placeholder="Enter your email" 
                 value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required />
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input class="form-input" type="password" id="password" name="password" 
                 placeholder="Enter your password" required />
        </div>

        <div class="options">
          <label><input type="checkbox" name="remember" /> Remember Me</label>
          <a href="./fogot-password.php">Forgot Password?</a>
        </div>

        <div class="auth-actions">
          <button type="submit" class="btn primary">
            <i class="fa fa-sign-in-alt"></i> Login
          </button>
        </div>

        <p class="auth-prompt">Don't have an account? <a href="./registration.php">Create one</a></p>
      </form>

      <div class="back-home">
        <a href="./../index.php"><i class="fa fa-arrow-left"></i> Back to Home</a>
      </div>
      
      <div class="test-credentials">
        <p><strong>Test Account:</strong></p>
        <p>Email: test@example.com</p>
        <p>Password: test123</p>
      </div>
    </div>
  </div>
</body>
</html>

