<?php
require_once './../auth/auth.php';

// Check if already logged in
if (isLoggedIn()) {
    $redirect = $_GET['redirect'] ?? './../index.php';
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
        $result = loginUser($email, $password);
        if ($result['success']) {
            $redirect = $_GET['redirect'] ?? './../index.php';
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="./../CSS/log.css" />
</head>
<body>
  <!-- Left side with image background -->
  <div class="left-section">
    <div class="logo">Krist</div>
  </div>

  <!-- Right side with login form -->
  <div class="right-section">
    <div class="login-box">
      <h1>Welcome</h1>
      <p>Please login here</p>

      <?php if ($error): ?>
      <div class="error-alert">
        <?php echo htmlspecialchars($error); ?>
      </div>
      <?php endif; ?>

      <form method="POST" action="" id="loginForm">
        <div class="form-group">
          <label for="email">Email Address</label>
          <input class="form-input" type="text" id="email" name="email" 
                 placeholder="robertfox@example.com" 
                 value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required />
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input class="form-input" type="password" id="password" name="password" 
                 placeholder="********" required />
        </div>

        <div class="options">
          <label><input type="checkbox" name="remember" /> Remember Me</label>
          <a href="./fogot-password.php">Forgot Password?</a>
        </div>

        <div class="auth-actions">
          <button type="submit" class="btn primary">Login</button>
        </div>

        <p class="auth-prompt">Don't have an account? <a href="./registration.php">Create one</a></p>
      </form>

      <div class="back-home">
        <a href="./../index.php">Back to Home</a>
      </div>
    </div>
  </div>
  <script src="./../JS/script.js"></script>
  <script src="./../JS/validation.js"></script>
</body>
</html>

