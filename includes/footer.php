<?php
    $siteName = $siteName ?? "Krist";
    $currentYear = $currentYear ?? date('Y');
    $companyEmail = $companyEmail ?? "info@krist.com";
    $companyPhone = $companyPhone ?? "+49 123 456 7890";
    $companyAddress = $companyAddress ?? "Gymnasiumstrasse 11, 85049 Ingolstadt";
    $isInPages = strpos($_SERVER['PHP_SELF'], '/pages/') !== false;
    $basePath = $isInPages ? './..' : '.';
?>

<footer class="site-footer">
    <div class="container footer-grid">
        <div class="brand"><?php echo $siteName; ?></div>
        <div class="footer-links">
            <a href="<?php echo $isInPages ? './about.php' : './pages/about.php'; ?>">About Us</a>
            <a href="#">Privacy Policy</a>
            <a href="#">Contact</a>
        </div>
        <div class="copyright">©<?php echo $currentYear; ?> <?php echo $siteName; ?> — All Rights reserved</div>
    </div>
</footer>
