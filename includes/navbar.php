<?php
    require_once __DIR__ . '/cart_functions.php';
    require_once __DIR__ . '/../auth/auth.php';
    
    // Initialize session first
    initSession();
    
    $siteName = $siteName ?? "Krist";
    $currentPage = $currentPage ?? "home";
    
    $cartCount = getCartItemCount();
    $isUserLoggedIn = isLoggedIn();
    $userName = $isUserLoggedIn ? getCurrentUserLastName() : '';

    $inPages = strpos($_SERVER['PHP_SELF'], '/pages/') !== false;
    $basePath = $inPages ? './..' : '.';
    $pagesPath = $inPages ? './' : './pages/';
    $apiPath = $inPages ? './../api/' : './api/';
    $authPath = $inPages ? './../auth/' : './auth/';
?>

<!-- Store API path for JavaScript - Must be before cart.js loads -->
<script>
    window.apiPath = '<?php echo $apiPath; ?>';
    window.authPath = '<?php echo $authPath; ?>';
    window.pagesPath = '<?php echo $pagesPath; ?>';
    window.isLoggedIn = <?php echo $isUserLoggedIn ? 'true' : 'false'; ?>;
</script>

<header class="navbar">
    <div class="nav-container">
        <!-- logo -->
        <div class="nav-left">
            <a href="<?php echo $basePath; ?>/index.php">
                <img src="<?php echo $basePath; ?>/assets/Group 56.png" alt="<?php echo $siteName; ?> Logo" class="logo">
            </a>
        </div>

        <!-- center menu -->
        <nav class="nav-center">
            <a href="<?php echo $basePath; ?>/index.php">Home</a>
            <div class="dropdown">
                <a href="<?php echo $pagesPath; ?>list.php">Shop <span class="arrow">▾</span></a>
                <div class="dropdown-content">
                    <a href="<?php echo $pagesPath; ?>list.php">All Products</a>
                    <a href="<?php echo $pagesPath; ?>list.php?category=shoes">Shoes</a>
                    <a href="<?php echo $pagesPath; ?>list.php?category=fashion">Fashion</a>
                    <a href="<?php echo $pagesPath; ?>list.php?category=electronic">Electronics</a>
                </div>
            </div>
            <a href="<?php echo $pagesPath; ?>about.php">About us</a>
            <a href="<?php echo $pagesPath; ?>contact.php">Contact</a>
        </nav>

        <!-- right icons and login -->
        <div class="nav-right">
            <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">🌙</button>
            <a class="icon search" href="#"><i class="fa fa-search"></i></a>
            <a class="icon" href="#"><i class="fa-regular fa-heart"></i></a>
            
            <!-- Shopping Cart Icon with Badge -->
            <a class="icon cart-icon" href="<?php echo $pagesPath; ?>shoppingCart.php" id="cartIcon">
                <?php if ($cartCount > 0): ?>
                    <i class="fa fa-shopping-bag cart-filled"></i>
                    <span class="cart-badge" id="cartBadge"><?php echo $cartCount; ?></span>
                <?php else: ?>
                    <i class="fa fa-shopping-bag cart-empty"></i>
                    <span class="cart-badge hidden" id="cartBadge">0</span>
                <?php endif; ?>
            </a>
            
            <!-- Login/User Section -->
            <?php if ($isUserLoggedIn): ?>
                <div class="dropdown user-dropdown">
                    <a href="<?php echo $pagesPath; ?>personal_information.php" class="login-btn user-btn">
                        <i class="fa fa-user"></i> <?php echo htmlspecialchars($userName); ?> <span class="arrow"></span>
                    </a>
                    <div class="dropdown-content user-dropdown-content">
                        <a href="<?php echo $pagesPath; ?>personal_information.php"><i class="fa fa-user"></i> My Profile</a>
                        <a href="<?php echo $pagesPath; ?>myorders.php"><i class="fa fa-box"></i> My Orders</a>
                        <a href="#" onclick="logout(); return false;"><i class="fa fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a class="login-btn" href="<?php echo $pagesPath; ?>login.php">Login</a>
            <?php endif; ?>
        </div>
    </div>
</header>
