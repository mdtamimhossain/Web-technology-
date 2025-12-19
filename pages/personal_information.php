<?php
require_once './../auth/auth.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: login.php?redirect=personal_information.php');
    exit;
}

// Get current user data
$user = getCurrentUser();
if (!$user) {
    header('Location: login.php');
    exit;
}

// Get additional user details from database
$pdo = getDBConnection();
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([getCurrentUserId()]);
$userDetails = $stmt->fetch();

// Parse name into first and last name
$nameParts = explode(' ', $userDetails['name'], 2);
$firstName = $nameParts[0] ?? '';
$lastName = $nameParts[1] ?? '';

// Format member since date
$memberSince = date('Y', strtotime($userDetails['created_at']));

// Handle form submission
$successMessage = '';
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newFirstName = trim($_POST['firstName'] ?? '');
    $newLastName = trim($_POST['lastName'] ?? '');
    $newEmail = trim($_POST['email'] ?? '');
    $newPhone = trim($_POST['phone'] ?? '');
    $newAddress = trim($_POST['address'] ?? '');
    
    if (empty($newFirstName) || empty($newEmail)) {
        $errorMessage = 'First name and email are required';
    } else {
        $fullName = $newFirstName . ($newLastName ? ' ' . $newLastName : '');
        
        // Check if email is taken by another user
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$newEmail, getCurrentUserId()]);
        if ($stmt->fetch()) {
            $errorMessage = 'Email is already taken by another user';
        } else {
            // Update user details
            $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, address = ? WHERE id = ?");
            if ($stmt->execute([$fullName, $newEmail, $newPhone, $newAddress, getCurrentUserId()])) {
                // Update session
                $_SESSION['user_name'] = $fullName;
                $_SESSION['user_email'] = $newEmail;
                
                $successMessage = 'Profile updated successfully!';
                
                // Refresh user data
                $firstName = $newFirstName;
                $lastName = $newLastName;
                $userDetails['email'] = $newEmail;
                $userDetails['phone'] = $newPhone;
                $userDetails['address'] = $newAddress;
            } else {
                $errorMessage = 'Failed to update profile';
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Krist — Personal Information</title>
    <link rel="stylesheet" href="./../CSS/mystyle.css">
    <link rel="stylesheet" href="./../CSS/myorders.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include './../includes/navbar.php'; ?>

<!-- MAIN PROFILE SECTION -->
<section class="profile-container container">
    <!-- Sidebar -->
    <aside class="profile-sidebar">
        <div class="profile-user">
            <img src="./../assets/Tareq/img.png" alt="User Photo">
            <div>
                <p>Hello 👋</p>
                <h4><?php echo htmlspecialchars($firstName); ?></h4>
            </div>
        </div>

        <nav class="profile-menu">
            <a href="./personal_information.php" class="active"><i class="fa-regular fa-user"></i> Personal Information</a>
            <a href="./myorders.php"><i class="fa-solid fa-box"></i> My Orders</a>
            <a href="#"><i class="fa-regular fa-heart"></i> My Wishlists</a>
            <a href="#"><i class="fa-regular fa-address-card"></i> Manage Addresses</a>
            <a href="#"><i class="fa-regular fa-credit-card"></i> Saved Cards</a>
            <a href="#"><i class="fa-regular fa-bell"></i> Notifications</a>
            <a href="#"><i class="fa-solid fa-gear"></i> Settings</a>
        </nav>
    </aside>

    <!-- Personal Info Section -->
    <div class="profile-content">
        <h2>Personal Information</h2>

        <?php if ($successMessage): ?>
        <div class="alert alert-success">
            <i class="fa fa-check-circle"></i> <?php echo htmlspecialchars($successMessage); ?>
        </div>
        <?php endif; ?>
        
        <?php if ($errorMessage): ?>
        <div class="alert alert-error">
            <i class="fa fa-exclamation-circle"></i> <?php echo htmlspecialchars($errorMessage); ?>
        </div>
        <?php endif; ?>

        <div class="personal-card">
            <div class="personal-header">
                <div class="avatar">
                    <img src="./../assets/Tareq/img.png" alt="User Photo">
                </div>
                <div class="personal-head-info">
                    <h3><?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></h3>
                    <p class="muted">Member since <?php echo $memberSince; ?></p>
                </div>
                <button class="btn-outline edit-avatar" type="button"><i class="fa fa-pen"></i> Edit</button>
            </div>

            <form class="personal-form" id="personalForm" method="POST" action="">
                <div>
                    <label for="firstName">First name</label>
                    <input id="firstName" name="firstName" type="text" value="<?php echo htmlspecialchars($firstName); ?>" required />
                </div>

                <div>
                    <label for="lastName">Last name</label>
                    <input id="lastName" name="lastName" type="text" value="<?php echo htmlspecialchars($lastName); ?>" />
                </div>

                <div>
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="<?php echo htmlspecialchars($userDetails['email']); ?>" required />
                </div>

                <div>
                    <label for="phone">Phone</label>
                    <input id="phone" name="phone" type="tel" value="<?php echo htmlspecialchars($userDetails['phone'] ?? ''); ?>" />
                </div>

                <div class="full-width">
                    <label for="address">Address</label>
                    <textarea id="address" name="address"><?php echo htmlspecialchars($userDetails['address'] ?? ''); ?></textarea>
                </div>

                <div class="full-width personal-actions">
                    <button type="submit" class="btn-primary">Save Changes</button>
                    <button type="button" class="btn-outline" id="cancelBtn" onclick="window.location.reload();">Cancel</button>
                </div>
            </form>
        </div>

    </div>
</section>

<?php include './../includes/footer.php'; ?>
<script src="./../JS/script.js"></script>
<script src="./../JS/cart.js"></script>
<script src="./../JS/validation.js"></script>
<script src="./../JS/profile.js"></script>
</body>
</html>
