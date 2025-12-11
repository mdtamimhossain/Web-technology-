<?php
    // Default values if not set in the calling page
    $siteName = $siteName ?? "Krist";
    $currentPage = $currentPage ?? "home";
    
    // Determine if we're in the pages directory or root
    $inPages = strpos($_SERVER['PHP_SELF'], '/pages/') !== false;
    $basePath = $inPages ? './..' : '.';
    $pagesPath = $inPages ? './' : './pages/';
?>

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
            <a class="icon search" href="#"><i class="fa fa-search"></i></a>
            <a class="icon" href="#"><i class="fa-regular fa-heart"></i></a>
            <a class="icon" href="<?php echo $pagesPath; ?>shoppingCart.php"><i class="fa fa-shopping-bag"></i></a>
            <a class="login-btn" href="<?php echo $pagesPath; ?>login.php">Login</a>
        </div>
    </div>
</header>
