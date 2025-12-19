<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once './includes/functions.php';

$siteName = "Krist";
$currentYear = date('Y');
$companyEmail = "krist@example.com";
$companyPhone = "707-927-0137";
$companyAddress = "301 Baseline Rd, Highland, California 92346";

// Get bestseller products (limit to 8)
$allProducts = getAllProducts();
$bestsellers = array_slice($allProducts, 0, 8);

// Get categories for display
$categories = getAllCategories();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Krist — Webshop</title>
    <link rel="stylesheet" href="./CSS/mystyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include './includes/navbar.php'; ?>

<!-- HERO -->
<section class="hero container">
    <div class="hero-container">
        <div class="hero-text">
            <p class="hero-tagline">Classic Exclusive</p>
            <h1>Women's Collection</h1>
            <p class="hero-sub">UPTO 40% OFF</p>
            <a href="./pages/list.php" class="btn">Shop Now →</a>
        </div>

        <div class="hero-image">
            <img src="./assets/hero2.png" alt="Women's Collection">
        </div>
    </div>
</section>

<!-- Shop by category -->
<section class="container categories">
    <div class="categories-header">
        <h2>Shop by Categories</h2>
        <div class="cat-nav">
            <button class="cat-btn p"><i class="fa-solid fa-arrow-left"></i></button>
            <button class="cat-btn next"><i class="fa-solid fa-arrow-right"></i></button>
        </div>
    </div>

    <div class="category-row">
        <?php 
        // Category images - you can customize these
        $categoryImages = [
            'shoes' => 'category1.png',
            'electronics' => 'category2.png',
            'fashion' => 'category1.png'
        ];
        
        foreach ($categories as $category): 
            $image = isset($categoryImages[$category['id']]) ? $categoryImages[$category['id']] : 'category1.png';
        ?>
        <a href="./pages/list.php?category=<?php echo urlencode($category['id']); ?>" class="category-link">
            <div class="category-card">
                <img src="./assets/<?php echo htmlspecialchars($image); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>">
                <div class="cat-label"><?php echo htmlspecialchars($category['name']); ?></div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- BESTSELLERS -->
<section class="bestseller container">
    <h2>Our Bestseller</h2>

    <div class="product-grid">
        <?php foreach ($bestsellers as $product): ?>
        <a href="./pages/product_details.php?pid=<?php echo urlencode($product['id']); ?>" class="product-link">
            <div class="product-card">
                <div class="product-img">
                    <img src="./assets/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" />
                    <button class="add-btn" onclick="event.preventDefault(); event.stopPropagation(); addToCart('<?php echo htmlspecialchars($product['id']); ?>');">Add to Cart</button>
                    <div class="icons">
                        <button onclick="event.preventDefault(); event.stopPropagation();"><i class="fa-regular fa-eye"></i></button>
                        <button onclick="event.preventDefault(); event.stopPropagation();"><i class="fa-regular fa-heart"></i></button>
                        <button onclick="event.preventDefault(); event.stopPropagation();"><i class="fa-solid fa-code-compare"></i></button>
                    </div>
                </div>
                <div class="product-info">
                    <h4><?php echo htmlspecialchars($product['name']); ?></h4>
                    <p><?php echo htmlspecialchars(ucfirst($product['category'])); ?></p>
                    <span class="price"><?php echo formatPrice($product['price']); ?></span>
                    <?php if ($product['oldPrice'] > $product['price']): ?>
                        <span class="old-price"><?php echo formatPrice($product['oldPrice']); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</section>

<!-- Toast Notification -->
<div id="toast" class="toast hidden">
    <span id="toastMessage"></span>
</div>

<?php include './includes/footer.php'; ?>
<script src="./JS/script.js"></script>
<script src="./JS/cart.js"></script>
</body>
</html>
