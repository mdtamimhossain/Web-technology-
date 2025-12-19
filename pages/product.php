<?php
/**
 * Alternative product page that supports both single product view
 * and product comparison with pid1 and pid2 parameters
 */
require_once './../includes/functions.php';

$product1 = null;
$product2 = null;
$error = null;
$compareMode = false;

// Check for pid1 or pid parameter
$pid1 = null;
if (isset($_GET["pid1"])) {
    $pid1 = $_GET["pid1"];
} elseif (isset($_GET["pid"])) {
    $pid1 = $_GET["pid"];
}

// Validate first product
if ($pid1 !== null) {
    if (empty($pid1)) {
        $error = "No value for the product ID parameter!";
    } else {
        $product1 = getProductById($pid1);
        if (!$product1) {
            $error = "Product not found with ID: " . htmlspecialchars($pid1);
        }
    }
} else {
    $error = "Product ID parameter is missing!";
}

// Check for second product (pid2)
if (isset($_GET["pid2"])) {
    if (!empty($_GET["pid2"])) {
        $product2 = getProductById($_GET["pid2"]);
        if ($product2) {
            $compareMode = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Krist — <?php echo $product1 ? htmlspecialchars($product1['name']) : 'Product'; ?></title>
    <link rel="stylesheet" href="./../CSS/mystyle.css">
    <link rel="stylesheet" href="./../CSS/product_dtls.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .error-box {
            background: #fff3f3;
            border: 1px solid #ffcdd2;
            border-radius: 8px;
            padding: 30px;
            margin: 40px auto;
            max-width: 500px;
            text-align: center;
        }
        .error-box i {
            font-size: 48px;
            color: #e53935;
            margin-bottom: 15px;
        }
        .error-box h2 {
            color: #c62828;
            margin-bottom: 10px;
        }
        .error-box p {
            color: #666;
            margin-bottom: 20px;
        }
        .error-box a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
        }
        .comparison-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            margin-top: 20px;
        }
        .comparison-product {
            border: 1px solid #eee;
            border-radius: 12px;
            padding: 20px;
            background: #fff;
        }
        .comparison-product img {
            width: 100%;
            max-height: 300px;
            object-fit: contain;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .comparison-product h3 {
            margin-bottom: 10px;
        }
        .comparison-product .price {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
        }
        .comparison-product .old-price {
            text-decoration: line-through;
            color: #999;
            font-size: 16px;
            margin-left: 10px;
        }
        .comparison-product .description {
            color: #666;
            margin: 15px 0;
            line-height: 1.6;
        }
        .comparison-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .single-product {
            max-width: 800px;
            margin: 0 auto;
        }
        .single-product img {
            max-width: 400px;
            width: 100%;
            border-radius: 12px;
        }
        .product-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: start;
        }
        @media (max-width: 768px) {
            .comparison-grid,
            .product-layout {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<?php include './../includes/navbar.php'; ?>

<section class="container" style="padding: 40px 20px;">
    <?php if ($error): ?>
        <div class="error-box">
            <i class="fa fa-exclamation-circle"></i>
            <h2>Oops!</h2>
            <p><?php echo htmlspecialchars($error); ?></p>
            <a href="list.php"><i class="fa fa-arrow-left"></i> Back to Shop</a>
        </div>
    <?php elseif ($compareMode && $product1 && $product2): ?>
        <!-- COMPARISON MODE -->
        <div class="comparison-header">
            <h1>Product Comparison</h1>
            <p>Compare these two products side by side</p>
        </div>
        
        <div class="comparison-grid">
            <div class="comparison-product">
                <img src="./../assets/<?php echo htmlspecialchars($product1['image']); ?>" alt="<?php echo htmlspecialchars($product1['name']); ?>">
                <h3><?php echo htmlspecialchars($product1['name']); ?></h3>
                <div class="rating"><?php echo generateStarRating($product1['rating']); ?></div>
                <p class="price">
                    <?php echo formatPrice($product1['price']); ?>
                    <?php if ($product1['oldPrice'] > $product1['price']): ?>
                        <span class="old-price"><?php echo formatPrice($product1['oldPrice']); ?></span>
                    <?php endif; ?>
                </p>
                <p class="description"><?php echo htmlspecialchars($product1['description']); ?></p>
                <p><strong>Category:</strong> <?php echo htmlspecialchars(ucfirst($product1['category'])); ?></p>
                <?php if (!empty($product1['sizes'])): ?>
                    <p><strong>Sizes:</strong> <?php echo implode(', ', $product1['sizes']); ?></p>
                <?php endif; ?>
                <?php if (!empty($product1['colors'])): ?>
                    <p><strong>Colors:</strong> <?php echo implode(', ', array_map('ucfirst', $product1['colors'])); ?></p>
                <?php endif; ?>
                <button class="btn-primary" style="width: 100%; margin-top: 15px;">
                    <i class="fa fa-shopping-cart"></i> Add to Cart
                </button>
            </div>
            
            <div class="comparison-product">
                <img src="./../assets/<?php echo htmlspecialchars($product2['image']); ?>" alt="<?php echo htmlspecialchars($product2['name']); ?>">
                <h3><?php echo htmlspecialchars($product2['name']); ?></h3>
                <div class="rating"><?php echo generateStarRating($product2['rating']); ?></div>
                <p class="price">
                    <?php echo formatPrice($product2['price']); ?>
                    <?php if ($product2['oldPrice'] > $product2['price']): ?>
                        <span class="old-price"><?php echo formatPrice($product2['oldPrice']); ?></span>
                    <?php endif; ?>
                </p>
                <p class="description"><?php echo htmlspecialchars($product2['description']); ?></p>
                <p><strong>Category:</strong> <?php echo htmlspecialchars(ucfirst($product2['category'])); ?></p>
                <?php if (!empty($product2['sizes'])): ?>
                    <p><strong>Sizes:</strong> <?php echo implode(', ', $product2['sizes']); ?></p>
                <?php endif; ?>
                <?php if (!empty($product2['colors'])): ?>
                    <p><strong>Colors:</strong> <?php echo implode(', ', array_map('ucfirst', $product2['colors'])); ?></p>
                <?php endif; ?>
                <button class="btn-primary" style="width: 100%; margin-top: 15px;">
                    <i class="fa fa-shopping-cart"></i> Add to Cart
                </button>
            </div>
        </div>
    <?php elseif ($product1): ?>
        <!-- SINGLE PRODUCT VIEW -->
        <div class="breadcrumb">
            <a href="list.php">Shop</a> / <?php echo htmlspecialchars($product1['name']); ?>
        </div>
        
        <div class="single-product">
            <div class="product-layout">
                <div>
                    <img src="./../assets/<?php echo htmlspecialchars($product1['image']); ?>" alt="<?php echo htmlspecialchars($product1['name']); ?>">
                </div>
                <div>
                    <h1><?php echo htmlspecialchars($product1['name']); ?></h1>
                    <div class="rating" style="margin: 10px 0;"><?php echo generateStarRating($product1['rating']); ?></div>
                    <p class="price" style="font-size: 28px; font-weight: 700; color: var(--primary);">
                        <?php echo formatPrice($product1['price']); ?>
                        <?php if ($product1['oldPrice'] > $product1['price']): ?>
                            <span class="old-price" style="text-decoration: line-through; color: #999; font-size: 18px;"><?php echo formatPrice($product1['oldPrice']); ?></span>
                        <?php endif; ?>
                    </p>
                    <p style="color: #666; margin: 20px 0; line-height: 1.7;"><?php echo htmlspecialchars($product1['description']); ?></p>
                    
                    <p><strong>Product ID:</strong> <?php echo htmlspecialchars($product1['id']); ?></p>
                    <p><strong>Category:</strong> <?php echo htmlspecialchars(ucfirst($product1['category'])); ?></p>
                    
                    <?php if (!empty($product1['sizes'])): ?>
                        <p style="margin-top: 15px;"><strong>Sizes:</strong></p>
                        <div style="display: flex; gap: 8px; margin-top: 5px;">
                            <?php foreach ($product1['sizes'] as $size): ?>
                                <button style="padding: 8px 16px; border: 1px solid #ddd; background: #fff; border-radius: 4px; cursor: pointer;"><?php echo htmlspecialchars($size); ?></button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($product1['colors'])): ?>
                        <p style="margin-top: 15px;"><strong>Colors:</strong></p>
                        <div style="display: flex; gap: 8px; margin-top: 5px;">
                            <?php foreach ($product1['colors'] as $color): ?>
                                <span style="width: 30px; height: 30px; border-radius: 50%; background-color: <?php echo htmlspecialchars($color); ?>; border: 2px solid #ddd; display: inline-block;"></span>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <button class="btn-primary" style="margin-top: 25px; padding: 15px 30px;">
                        <i class="fa fa-shopping-cart"></i> Add to Cart
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>

<?php include './../includes/footer.php'; ?>
<script src="./../JS/script.js"></script>
<script src="./../JS/cart.js"></script>
</body>
</html>
