<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Try to include a project-wide config from the project root (if present)
$projectRoot = dirname(__DIR__);
if (file_exists($projectRoot . '/config.php')) {
    require_once $projectRoot . '/config.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Registration</title>
  <link rel="stylesheet" href="./../CSS/register.css" />
  <!-- reuse the login styles so registration matches the login page -->
  <link rel="stylesheet" href="./../CSS/log.css" />
</head>
<body>
  <div class="left-section">
    <div class="logo">Krist</div>
  </div>

  <div class="right-section">
    <div class="login-box">
      <h1>Create New Account</h1>
      <p>Please enter details</p>

  <form id="registrationForm" action="#" method="post">
        <div class="form-group">
          <label for="username">Username</label>
          <input class="form-input" type="text" id="username" name="username" placeholder="Enter your username" required />
        </div>

        <div class="form-group">
          <label for="email">Email</label>
          <input class="form-input" type="email" id="email" name="email" placeholder="Enter your email" required />
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input class="form-input" type="password" id="password" name="password" placeholder="Enter your password" required />
        </div>

        <div class="form-group">
          <label for="confirm-password">Confirm password</label>
          <input class="form-input" type="password" id="confirm-password" name="confirm_password" placeholder="Confirm your password" required />
        </div>

        <div class="auth-actions">
          <button type="submit" class="btn primary">Register</button>
        </div>

        <!-- prompt: guide users to login if they already have an account -->
        <p class="auth-prompt">Already have an account? <a href="./login.html">Sign in</a></p>
      </form>

      <!-- 👇 Back to home link added here -->
      <div class="back-home">
        <a href="./homePage.html">Back to Home</a>
      </div>
    </div>
  </div>
  <script src="./../JS/script.js"></script>
  <script src="./../JS/validation.js"></script>
</body>
</html>
