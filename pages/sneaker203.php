<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Page</title>
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

    <h1>Sneaker203</h1>
    <section>
        <img src="./../assets/sneaker203.png" alt="SneakerMax203" style="max-width:200px;"/>
        <p><strong>Price:</strong> €79.99</p>
        <p><strong>Description:</strong> Sneaker203 is a comfortable running shoe with cushioned sole and high durability.</p>
        <p><strong>Product ID:</strong> sneaker203</p>
    </section>
    <form action="#" method="post">
        <label for="qty">Quantity:</label>
        <input type="number" id="qty" name="quantity" value="1" min="1" />
        <div style="margin-top:8px;">
            <button type="submit">Add to cart</button>
            <button type="button" >View cart</button>
        </div>
    </form>

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