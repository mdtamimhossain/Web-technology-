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
    <link rel="stylesheet" href="./../CSS/browseProduct.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* Override to ensure filters display correctly */
        .filters {
            display: block !important;
        }
        .filter-section {
            display: block !important;
            width: 100% !important;
        }
        .filter-header {
            display: flex !important;
            width: 100% !important;
        }
        .filter-content {
            display: block !important;
            width: 100% !important;
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
        .filter-apply-btn {
            width: 100%;
            padding: 10px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            transition: opacity 0.2s;
        }
        .filter-apply-btn:hover {
            opacity: 0.9;
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
            <!-- Product Categories Filter -->
            <div class="filter-section open">
                <div class="filter-header">
                    <p>Product Categories</p>
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
                <div class="filter-content">
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
            </div>
            
            <!-- Filter by Price -->
            <div class="filter-section">
                <div class="filter-header">
                    <h3>Filter by Price</h3>
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
                <div class="filter-content">
                    <div class="price-filter">
                        <div class="price-inputs">
                            <input type="number" placeholder="Min" id="minPrice">
                            <input type="number" placeholder="Max" id="maxPrice">
                        </div>
                        <button class="filter-apply-btn" onclick="applyPriceFilter()">Apply</button>
                    </div>
                </div>
            </div>
            
            <!-- Filter by Color -->
            <div class="filter-section">
                <div class="filter-header">
                    <h3>Filter by Color</h3>
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
                <div class="filter-content">
                    <div class="color-options">
                        <span class="color-option" style="background-color: #000000;" title="Black"></span>
                        <span class="color-option" style="background-color: #ffffff; border: 1px solid #ddd;" title="White"></span>
                        <span class="color-option" style="background-color: #ff0000;" title="Red"></span>
                        <span class="color-option" style="background-color: #0000ff;" title="Blue"></span>
                        <span class="color-option" style="background-color: #00ff00;" title="Green"></span>
                        <span class="color-option" style="background-color: #ffff00;" title="Yellow"></span>
                        <span class="color-option" style="background-color: #ff6600;" title="Orange"></span>
                        <span class="color-option" style="background-color: #808080;" title="Gray"></span>
                    </div>
                </div>
            </div>
            
            <!-- Filter by Size -->
            <div class="filter-section">
                <div class="filter-header">
                    <h3>Filter by Size</h3>
                    <i class="fa-solid fa-chevron-down"></i>
                </div>
                <div class="filter-content">
                    <div class="size-options">
                        <span class="size-option">XS</span>
                        <span class="size-option">S</span>
                        <span class="size-option">M</span>
                        <span class="size-option">L</span>
                        <span class="size-option">XL</span>
                        <span class="size-option">XXL</span>
                    </div>
                </div>
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
<script>
// Filter toggle functionality
document.querySelectorAll('.filter-header').forEach(header => {
    header.addEventListener('click', function() {
        const section = this.parentElement;
        section.classList.toggle('open');
    });
});

// Color option selection
document.querySelectorAll('.color-option').forEach(option => {
    option.addEventListener('click', function() {
        this.classList.toggle('active');
    });
});

// Size option selection
document.querySelectorAll('.size-option').forEach(option => {
    option.addEventListener('click', function() {
        this.classList.toggle('active');
    });
});

// Price filter (placeholder function)
function applyPriceFilter() {
    const min = document.getElementById('minPrice').value;
    const max = document.getElementById('maxPrice').value;
    // Add filter logic here
    console.log('Price filter:', min, '-', max);
}
</script>
</body>
</html>
