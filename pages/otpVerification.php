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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter OTP</title>
    <link rel="stylesheet" href="./../CSS/mystyle.css">
    <link rel="stylesheet" href="./../CSS/otp-verification.css">
</head>
<body>
<div class="otp-wrapper">
    <div class="otp-left">
        <img src="./../assets/Tareq/img2.png" alt="Verification illustration">
    </div>

    <div class="otp-right">
        <a href="#" class="back-link">← Back</a>

        <div class="otp-form-container">
            <h2>Enter OTP</h2>
            <p>We have shared a code to your registered email address<br>
                <span class="otp-email">robertfox@example.com</span></p>

            <form>
                <div class="otp-inputs">
                    <input type="text" maxlength="1" required>
                    <input type="text" maxlength="1" required>
                    <input type="text" maxlength="1" required>
                    <input type="text" maxlength="1" required>
                    <input type="text" maxlength="1" required>
                </div>

                <button type="submit" class="btn-primary">Verify</button>
            </form>
        </div>
    </div>
</div>

<script>
    // Auto-focus OTP inputs
    const inputs = document.querySelectorAll(".otp-inputs input");
    inputs.forEach((input, index) => {
        input.addEventListener("input", () => {
            if (input.value && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });
        input.addEventListener("keydown", (e) => {
            if (e.key === "Backspace" && !input.value && index > 0) {
                inputs[index - 1].focus();
            }
        });
    });
</script>
</body>
</html>
