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

// Expose common session info to templates
$current_user = $_SESSION['user'] ?? null;
$user_email = $_SESSION['email'] ?? null;
$user_name = $_SESSION['name'] ?? 'Robert Fox'; // fallback to existing default

// Set page title
$page_title = 'Krist — My Orders';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title><?php echo htmlspecialchars($page_title, ENT_QUOTES); ?></title>
    <link rel="stylesheet" href="./../CSS/mystyle.css">
    <link rel="stylesheet" href="./../CSS/myorders.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<!-- NAVBAR -->
<?php require_once './includes/header.php'; ?>

<!-- MAIN PROFILE SECTION -->
<section class="profile-container container">
    <!-- Sidebar -->
    <aside class="profile-sidebar">
        <div class="profile-user">
            <img src="./../assets/Tareq/img.png" alt="User Photo">
            <div>
                <p>Hello 👋</p>
                <h4><?php echo htmlspecialchars($user_name, ENT_QUOTES); ?></h4>
            </div>
        </div>

        <nav class="profile-menu">
            <a href="./personal_information.php"><i class="fa-regular fa-user"></i> Personal Information</a>
            <a href="#" class="active"><i class="fa-solid fa-box"></i> My Orders</a>
            <a href="#"><i class="fa-regular fa-heart"></i> My Wishlists</a>
            <a href="#"><i class="fa-regular fa-address-card"></i> Manage Addresses</a>
            <a href="#"><i class="fa-regular fa-credit-card"></i> Saved Cards</a>
            <a href="#"><i class="fa-regular fa-bell"></i> Notifications</a>
            <a href="#"><i class="fa-solid fa-gear"></i> Settings</a>
        </nav>
    </aside>

    <!-- Orders Section -->
    <div class="profile-content">
        <h2>My Orders</h2>

        <div class="orders-header">
            <div class="search-box">
                <i class="fa fa-search"></i>
                <input type="text" placeholder="Search">
            </div>
            <button class="filter-btn"><i class="fa fa-sliders-h"></i> Filter</button>
        </div>

        <div class="order-list">

            <!-- Order 1 -->
            <div class="order-card">
                <img src="./../assets/Tareq/imga.png" alt="Product">
                <div class="order-details">
                    <h4>Girls Pink Moana Printed Dress</h4>
                    <p>Size: S<br>Qty: 1</p>
                    <span class="status delivered">Delivered</span>
                    <p class="note">Your product has been delivered</p>
                </div>
                <div class="order-price">$80.00</div>
                <div class="order-actions">
                    <button class="btn-outline">View Order</button>
                    <button class="btn-primary">Write a Review</button>
                </div>
            </div>

            <!-- Order 2 -->
            <div class="order-card">
                <img src="./../assets/Tareq/imgb.png" alt="Product">
                <div class="order-details">
                    <h4>Women Textured Handheld Bag</h4>
                    <p>Size: Regular<br>Qty: 1</p>
                    <span class="status inprocess">In Process</span>
                    <p class="note">Your product has been Inprocess</p>
                </div>
                <div class="order-price">$80.00</div>
                <div class="order-actions">
                    <button class="btn-outline">View Order</button>
                    <button class="btn-danger">Cancel Order</button>
                </div>
            </div>

            <!-- Order 3 -->
            <div class="order-card">
                <img src="./../assets/Tareq/imgc.png" alt="Product">
                <div class="order-details">
                    <h4>Tailored Cotton Casual Shirt</h4>
                    <p>Size: M<br>Qty: 1</p>
                    <span class="status inprocess">In Process</span>
                    <p class="note">Your product has been Inprocess</p>
                </div>
                <div class="order-price">$40.00</div>
                <div class="order-actions">
                    <button class="btn-outline">View Order</button>
                    <button class="btn-danger">Cancel Order</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FOOTER -->
<footer class="site-footer">
    <div class="container footer-grid">
        <div class="brand">Krist</div>
        <div class="footer-links">
            <a href="#">About Us</a>
            <a href="#">Privacy Policy</a>
            <a href="#">Contact</a>
        </div>
        <div class="copyright">©2025 Krist — All Rights reserved</div>
    </div>
</footer>
<script src="./../JS/script.js"></script>
</body>
</html>
