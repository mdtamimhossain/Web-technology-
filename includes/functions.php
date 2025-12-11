<?php
/**
 * Product and Category Functions
 */

// Product data array
function getProductsData() {
    return [
        [
            'id' => 'shoe1',
            'name' => 'Running Sneaker Pro',
            'category' => 'shoes',
            'price' => 89.99,
            'oldPrice' => 120.00,
            'image' => 'shoe1.png',
            'description' => 'Premium running sneakers with cushioned sole for maximum comfort during long runs.',
            'rating' => 4.5,
            'colors' => ['black', 'white', 'blue'],
            'sizes' => ['38', '39', '40', '41', '42', '43', '44'],
            'inStock' => true
        ],
        [
            'id' => 'shoe2',
            'name' => 'Classic Leather Boot',
            'category' => 'shoes',
            'price' => 149.99,
            'oldPrice' => 180.00,
            'image' => 'shoe2.png',
            'description' => 'Elegant leather boots perfect for formal occasions and everyday wear.',
            'rating' => 4.8,
            'colors' => ['brown', 'black'],
            'sizes' => ['39', '40', '41', '42', '43'],
            'inStock' => true
        ],
        [
            'id' => 'shoe3',
            'name' => 'Sport Training Shoe',
            'category' => 'shoes',
            'price' => 75.00,
            'oldPrice' => 95.00,
            'image' => 'shoe3.png',
            'description' => 'Versatile training shoes designed for gym workouts and sports activities.',
            'rating' => 4.2,
            'colors' => ['red', 'black', 'white'],
            'sizes' => ['38', '39', '40', '41', '42'],
            'inStock' => true
        ],
        [
            'id' => 'elec1',
            'name' => 'Wireless Bluetooth Headphones',
            'category' => 'electronics',
            'price' => 129.99,
            'oldPrice' => 159.99,
            'image' => 'shoe1.png',
            'description' => 'High-quality wireless headphones with noise cancellation and 30-hour battery life.',
            'rating' => 4.7,
            'colors' => ['black', 'silver', 'rose gold'],
            'sizes' => [],
            'inStock' => true
        ],
        [
            'id' => 'elec2',
            'name' => 'Smart Watch Pro',
            'category' => 'electronics',
            'price' => 299.99,
            'oldPrice' => 349.99,
            'image' => 'shoe2.png',
            'description' => 'Advanced smartwatch with health monitoring, GPS, and smartphone connectivity.',
            'rating' => 4.6,
            'colors' => ['black', 'silver'],
            'sizes' => [],
            'inStock' => true
        ],
        [
            'id' => 'fashion1',
            'name' => 'Cotton Casual T-Shirt',
            'category' => 'fashion',
            'price' => 29.99,
            'oldPrice' => 39.99,
            'image' => 'shoe3.png',
            'description' => 'Comfortable 100% cotton t-shirt perfect for casual everyday wear.',
            'rating' => 4.3,
            'colors' => ['white', 'black', 'navy', 'gray'],
            'sizes' => ['S', 'M', 'L', 'XL', 'XXL'],
            'inStock' => true
        ],
        [
            'id' => 'fashion2',
            'name' => 'Denim Jacket Classic',
            'category' => 'fashion',
            'price' => 89.99,
            'oldPrice' => 110.00,
            'image' => 'shoe1.png',
            'description' => 'Timeless denim jacket that never goes out of style.',
            'rating' => 4.4,
            'colors' => ['blue', 'light blue', 'black'],
            'sizes' => ['S', 'M', 'L', 'XL'],
            'inStock' => true
        ],
        [
            'id' => 'fashion3',
            'name' => 'Summer Dress Floral',
            'category' => 'fashion',
            'price' => 59.99,
            'oldPrice' => 79.99,
            'image' => 'shoe2.png',
            'description' => 'Beautiful floral print dress perfect for summer occasions.',
            'rating' => 4.5,
            'colors' => ['pink', 'yellow', 'blue'],
            'sizes' => ['XS', 'S', 'M', 'L'],
            'inStock' => true
        ]
    ];
}

// Category data array
function getCategoriesData() {
    return [
        ['id' => 'shoes', 'name' => 'Shoes', 'description' => 'Footwear for all occasions'],
        ['id' => 'electronics', 'name' => 'Electronics', 'description' => 'Latest gadgets and devices'],
        ['id' => 'fashion', 'name' => 'Fashion', 'description' => 'Trendy clothing and accessories']
    ];
}

/**
 * Get all products
 */
function getAllProducts() {
    return getProductsData();
}

/**
 * Get product by ID
 */
function getProductById($id) {
    $products = getProductsData();
    foreach ($products as $product) {
        if ($product['id'] === $id) {
            return $product;
        }
    }
    return null;
}

/**
 * Get products by category
 */
function getProductsByCategory($category) {
    $products = getProductsData();
    $filtered = [];
    foreach ($products as $product) {
        if ($product['category'] === $category) {
            $filtered[] = $product;
        }
    }
    return $filtered;
}

/**
 * Get all categories
 */
function getAllCategories() {
    return getCategoriesData();
}

/**
 * Get category by ID
 */
function getCategoryById($id) {
    $categories = getCategoriesData();
    foreach ($categories as $category) {
        if ($category['id'] === $id) {
            return $category;
        }
    }
    return null;
}

/**
 * Generate HTML for star rating display
 * @param float $rating Rating value (0-5)
 * @return string HTML string with star icons
 */
function generateStarRating($rating) {
    $html = '';
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
    
    // Full stars
    for ($i = 0; $i < $fullStars; $i++) {
        $html .= '<i class="fa fa-star"></i>';
    }
    
    // Half star
    if ($halfStar) {
        $html .= '<i class="fa fa-star-half-alt"></i>';
    }
    
    // Empty stars
    for ($i = 0; $i < $emptyStars; $i++) {
        $html .= '<i class="fa-regular fa-star"></i>';
    }
    
    $html .= ' <span>(' . number_format($rating, 1) . ')</span>';
    
    return $html;
}

/**
 * Get related products (same category, excluding current product)
 * @param string $productId Current product ID to exclude
 * @param int $limit Maximum number of products to return
 * @return array Array of related products
 */
function getRelatedProducts($productId, $limit = 4) {
    $product = getProductById($productId);
    if (!$product) {
        return [];
    }
    
    $categoryProducts = getProductsByCategory($product['category']);
    $related = [];
    
    foreach ($categoryProducts as $p) {
        if ($p['id'] !== $productId) {
            $related[] = $p;
        }
        if (count($related) >= $limit) {
            break;
        }
    }
    
    // If not enough related products in same category, add from other categories
    if (count($related) < $limit) {
        $allProducts = getAllProducts();
        foreach ($allProducts as $p) {
            if ($p['id'] !== $productId && !in_array($p, $related)) {
                $related[] = $p;
            }
            if (count($related) >= $limit) {
                break;
            }
        }
    }
    
    return $related;
}

/**
 * Format price with currency symbol
 */
function formatPrice($price) {
    return '$' . number_format($price, 2);
}
?>
