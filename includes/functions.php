<?php

function getProductsData() {
    $jsonPath = __DIR__ . '/../data/products.json';
    
    if (!file_exists($jsonPath)) {
        return [];
    }
    
    $jsonContent = file_get_contents($jsonPath);
    $data = json_decode($jsonContent, true);
    
    return $data['products'] ?? [];
}

function getCategoriesData() {
    return [
        ['id' => 'shoes', 'name' => 'Shoes', 'description' => 'Footwear for all occasions'],
        ['id' => 'electronics', 'name' => 'Electronics', 'description' => 'Latest gadgets and devices'],
        ['id' => 'electronic', 'name' => 'Electronics', 'description' => 'Latest gadgets and devices'],
        ['id' => 'fashion', 'name' => 'Fashion', 'description' => 'Trendy clothing and accessories']
    ];
}

function getAllProducts() {
    return getProductsData();
}

function getProductById($id) {
    $products = getProductsData();
    foreach ($products as $product) {
        if ($product['id'] === $id) {
            return $product;
        }
    }
    return null;
}

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

function getAllCategories() {
    return getCategoriesData();
}

function getCategoryById($id) {
    $categories = getCategoriesData();
    foreach ($categories as $category) {
        if ($category['id'] === $id) {
            return $category;
        }
    }
    return null;
}

function generateStarRating($rating) {
    $html = '';
    $fullStars = floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
    
    for ($i = 0; $i < $fullStars; $i++) {
        $html .= '<i class="fa fa-star"></i>';
    }
    
    if ($halfStar) {
        $html .= '<i class="fa fa-star-half-alt"></i>';
    }
    
    for ($i = 0; $i < $emptyStars; $i++) {
        $html .= '<i class="fa-regular fa-star"></i>';
    }
    
    $html .= ' <span>(' . number_format($rating, 1) . ')</span>';
    
    return $html;
}

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

function formatPrice($price) {
    return '$' . number_format($price, 2);
}
?>
