<?php
require_once './../includes/functions.php';

// Get category filter from URL
$categoryFilter = null;
if (isset($_GET['category']) && !empty($_GET['category'])) {
    $categoryFilter = $_GET['category'];
}

// Get products based on filter
if ($categoryFilter) {
    $products = getProductsByCategory($categoryFilter);
    $categoryInfo = getCategoryById($categoryFilter);
    $pageTitle = $categoryInfo ? $categoryInfo['name'] : ucfirst($categoryFilter);
} else {
    $products = getAllProducts();
    $pageTitle = "All Products";
}

// Get all categories for filter display
$categories = getAllCategories();
$productCount = count($products);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Browse Product - <?php echo htmlspecialchars($pageTitle); ?></title>
    <link rel="stylesheet" href="./../CSS/mystyle.css">
    <link rel="stylesheet" href="./../CSS/browseProduct.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .category-list {
            list-style: none;
            padding: 0;
            margin: 10px 0;
        }
        .category-list li {
            margin: 8px 0;
        }
        .category-list a {
            text-decoration: none;
            color: #333;
            display: block;
            padding: 8px 12px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }
        .category-list a:hover,
        .category-list a.active {
            background-color: #f0f0f0;
            color: var(--primary);
        }
        .no-products {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        .no-products i {
            font-size: 48px;
            margin-bottom: 20px;
            color: #ccc;
        }
        .product-card-link {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>
<body>
<?php include './../includes/navbar.php'; ?>

<section class="shop container">
    <div class="shop-path">
        <p>Shop &gt; <span><?php echo htmlspecialchars($pageTitle); ?></span></p>
    </div>

    <div class="shop-layout">
        <!-- Left Sidebar -->
        <div class="filters">
            <div class="filter-section">
                <p>Product Categories <i class="fa-solid fa-chevron-down"></i></p>
                <ul class="category-list">
                    <li>
                        <a href="list.php" <?php echo !$categoryFilter ? 'class="active"' : ''; ?>>
                            All Products
                        </a>
                    </li>
                    <?php foreach ($categories as $category): ?>
                    <li>
                        <a href="list.php?category=<?php echo urlencode($category['id']); ?>" 
                           <?php echo $categoryFilter === $category['id'] ? 'class="active"' : ''; ?>>
                            <?php echo htmlspecialchars($category['name']); ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="filter-section">
                <h3>Filter by Price <i class="fa-solid fa-chevron-down"></i></h3>
            </div>
            <div class="filter-section">
                <h3>Filter by Color <i class="fa-solid fa-chevron-down"></i></h3>
            </div>
            <div class="filter-section">
                <h3>Filter by Size <i class="fa-solid fa-chevron-down"></i></h3>
            </div>
        </div>

        <!-- Right Content -->
        <div class="shop-content">
            <div class="shop-header">
                <p>Showing <?php echo $productCount; ?> result<?php echo $productCount !== 1 ? 's' : ''; ?></p>
                <select class="sort-select">
                    <option>Sort by latest</option>
                    <option>Price: Low to High</option>
                    <option>Price: High to Low</option>
                    <option>Popularity</option>
                </select>
            </div>
            
            <!-- Tax calculator widget -->
            <div class="tax-widget">
                <label>Price (without tax): <input id="priceWOTax" type="number" min="0" step="0.01" placeholder="0.00"></label>
                <div class="tax-results">
                    <div>Price w/o tax: <span id="showPriceWOTax">-</span></div>
                    <div>Price with 19% tax: <span id="showPriceWithTax">-</span></div>
                </div>
            </div>

            <!-- Product Grid -->
            <?php if (empty($products)): ?>
            <div class="no-products">
                <i class="fa fa-box-open"></i>
                <h3>No products found</h3>
                <p>Try selecting a different category or <a href="list.php">view all products</a>.</p>
            </div>
            <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                <a href="product_details.php?pid=<?php echo urlencode($product['id']); ?>" class="product-card-link">
                    <div class="product-card">
                        <div class="product-img">
                            <img src="./../assets/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" />
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
            <?php endif; ?>

            <!-- Pagination -->
            <div class="pagination">
                <button class="page-btn prev">←</button>
                <button class="page-btn active">1</button>
                <button class="page-btn">2</button>
                <button class="page-btn">3</button>
                <button class="page-btn">4</button>
                <button class="page-btn next">→</button>
            </div>
        </div>
    </div>
</section>

<!-- Toast Notification -->
<div id="toast" class="toast hidden">
    <span id="toastMessage"></span>
</div>

<?php include './../includes/footer.php'; ?>
<script src="./../JS/script.js"></script>
<script src="./../JS/cart.js"></script>
<script src="./../JS/collection.js"></script>
<script src="./../JS/priceTax.js"></script>
</body>
</html>
