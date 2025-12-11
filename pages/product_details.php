<?php
// Debug: Uncomment these lines to see what's happening
// echo '<pre>GET: '; print_r($_GET); echo '</pre>';
// echo '<pre>REQUEST_URI: ' . $_SERVER['REQUEST_URI'] . '</pre>';

require_once './../includes/functions.php';

// Remove comparison mode logic - keep only single product
$product = null;
$error = null;

if (isset($_GET["pid"])) {
    if (empty($_GET["pid"])) {
        $error = "No value for the product ID parameter!";
    } else {
        $product = getProductById($_GET["pid"]);
        if (!$product) {
            $error = "Product not found with ID: " . htmlspecialchars($_GET["pid"]);
        }
    }
} else {
    $error = "Product ID parameter is missing!";
}

$relatedProducts = $product ? getRelatedProducts($product['id'], 4) : [];
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Krist — <?php echo $product ? htmlspecialchars($product['name']) : 'Product Details'; ?></title>
    <link rel="stylesheet" href="./../CSS/mystyle.css">
    <link rel="stylesheet" href="./../CSS/product_dtls.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include './../includes/navbar.php'; ?>

<?php if ($error): ?>
    <!-- ERROR MESSAGE -->
    <div class="container">
        <div class="error-message">
            <h2><i class="fa fa-exclamation-triangle"></i> Error</h2>
            <p><?php echo htmlspecialchars($error); ?></p>
            <p><a href="./list.php">← Back to Shop</a></p>
        </div>
    </div>
<?php else: ?>
    <!-- SINGLE PRODUCT VIEW -->
    <!-- BREADCRUMB -->
    <div class="breadcrumb container">
        Home / Shop / <?php echo htmlspecialchars($product['name']); ?>
    </div>

    <!-- PRODUCT DETAILS SECTION -->
    <section class="product-details container">
        <div class="product-main">
            <!-- Product Images -->
            <div class="product-images">
                <img src="./../assets/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="main-image">
                <div class="thumbnail-images">
                    <img src="./../assets/<?php echo htmlspecialchars($product['image']); ?>" alt="Thumbnail">
                    <img src="./../assets/thumb1.avif" alt="Thumbnail">
                    <img src="./../assets/thumb2.avif" alt="Thumbnail">
                    <img src="./../assets/thumb3.avif" alt="Thumbnail">
                </div>
            </div>

            <div class="product-info">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <div class="rating">
                    <?php echo generateStarRating($product['rating']); ?>
                </div>
                <p class="price">
                    <?php echo formatPrice($product['price']); ?>
                    <?php if ($product['oldPrice'] > $product['price']): ?>
                        <span class="old-price"><?php echo formatPrice($product['oldPrice']); ?></span>
                    <?php endif; ?>
                </p>

                <p class="short-description">
                    <?php echo htmlspecialchars($product['description']); ?>
                </p>

                <!-- Color Selection -->
                <?php if (!empty($product['colors'])): ?>
                <div class="options">
                    <label>Color:</label>
                    <div class="color-options">
                        <?php foreach ($product['colors'] as $color): ?>
                            <span class="color" style="background-color:<?php echo htmlspecialchars($color); ?>;"></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Size Selection -->
                <?php if (!empty($product['sizes'])): ?>
                <div class="options">
                    <label>Size:</label>
                    <div class="size-options">
                        <?php foreach ($product['sizes'] as $size): ?>
                            <button type="button" class="size-btn"><?php echo htmlspecialchars($size); ?></button>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Add to Cart -->
                <button class="btn-primary add-to-cart"><i class="fa fa-shopping-cart"></i> Add to Cart</button>
                
                <p style="margin-top: 15px; font-size: 13px; color: #666;">
                    Product ID: <?php echo htmlspecialchars($product['id']); ?> | 
                    Category: <?php echo htmlspecialchars(ucfirst($product['category'])); ?>
                </p>
            </div>
        </div>

        <!-- Product Tabs -->
        <div class="product-tabs">
            <button class="tab-link active" onclick="openTab(event, 'description')">Description</button>
            <button class="tab-link" onclick="openTab(event, 'additional')">Additional Information</button>
            <button class="tab-link" onclick="openTab(event, 'reviews')">Reviews</button>

            <div id="description" class="tab-content active">
                <p><?php echo htmlspecialchars($product['description']); ?></p>
            </div>
            <div id="additional" class="tab-content">
                <p><strong>Category:</strong> <?php echo htmlspecialchars(ucfirst($product['category'])); ?></p>
                <?php if (!empty($product['sizes'])): ?>
                    <p><strong>Available Sizes:</strong> <?php echo implode(', ', $product['sizes']); ?></p>
                <?php endif; ?>
                <?php if (!empty($product['colors'])): ?>
                    <p><strong>Available Colors:</strong> <?php echo implode(', ', array_map('ucfirst', $product['colors'])); ?></p>
                <?php endif; ?>
                <p><strong>In Stock:</strong> <?php echo $product['inStock'] ? 'Yes' : 'No'; ?></p>
            </div>
            <div id="reviews" class="tab-content">
                <p>No reviews yet. Be the first to write a review!</p>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($relatedProducts)): ?>
        <div class="related-products">
            <h3>Related Products</h3>
            <div class="product-grid">
                <?php foreach ($relatedProducts as $relatedProduct): ?>
                <a href="product_details.php?pid=<?php echo urlencode($relatedProduct['id']); ?>" class="product-link">
                    <div class="product-card">
                        <div class="product-img">
                            <img src="./../assets/<?php echo htmlspecialchars($relatedProduct['image']); ?>" alt="<?php echo htmlspecialchars($relatedProduct['name']); ?>" />
                            <button class="add-btn">Add to Cart</button>
                            <div class="icons">
                                <button><i class="fa-regular fa-eye"></i></button>
                                <button><i class="fa-regular fa-heart"></i></button>
                                <button><i class="fa-solid fa-code-compare"></i></button>
                            </div>
                        </div>
                        <div class="product-info">
                            <h4><?php echo htmlspecialchars($relatedProduct['name']); ?></h4>
                            <span class="price"><?php echo formatPrice($relatedProduct['price']); ?></span>
                            <?php if ($relatedProduct['oldPrice'] > $relatedProduct['price']): ?>
                                <span class="old-price"><?php echo formatPrice($relatedProduct['oldPrice']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </section>
<?php endif; ?>

<?php include './../includes/footer.php'; ?>
<script src="./../JS/script.js"></script>
</body>
</html>
