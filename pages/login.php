<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
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

  <form id="loginForm">
        <div class="form-group">
          <label for="email">Email Address</label>
          <input class="form-input" type="text" id="email" name="email" placeholder="robertfox@example.com" required />
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input class="form-input" type="password" id="password" name="password" placeholder="********" required />
        </div>

        <div class="options">
          <label><input type="checkbox" /> Remember Me</label>
          <a href="#">Forgot Password?</a>
        </div>

        <div class="auth-actions">
          <button type="submit" class="btn primary">Login</button>
        </div>

        <!-- prompt: guide users to registration if they don't have an account -->
        <p class="auth-prompt">Don't have an account? <a href="./registration.html">Create one</a></p>
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

