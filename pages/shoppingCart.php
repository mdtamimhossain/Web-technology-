<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="./../CSS/mystyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<header class="navbar">
    <div class="nav-container">
        <div class="nav-left">
            <img src="./../assets/Group 56.png" alt="Krist Logo" class="logo">
        </div>
        <nav class="nav-center">
            <a href="./homePage.php">Home</a>
            <div class="dropdown">
                <a href="./list.php">Shop <span class="arrow">▾</span></a>
                <div class="dropdown-content">
                    <a href="#">Shoes</a>
                    <a href="#">Electronic</a>
                </div>
            </div>
            <a href="#">About us</a>
            <a href="#">Contact</a>
        </nav>
        <div class="nav-right">
            <a class="icon search" href="#"><i class="fa fa-search"></i></a>
            <a class="icon"><i class="fa-regular fa-heart"></i></a>
            <a class="icon"><i class="fa fa-shopping-bag"></i></a>
            <a class="login-btn" href="./login.php">Login</a>
        </div>
    </div>
</header>

<section class="container">
    <h2>Shopping Cart</h2>
    <p>We will implement this later</p>
</section>

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