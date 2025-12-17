<?php
require_once './../includes/functions.php';

$products = [];
$ids = isset($_GET['ids']) ? explode(',', $_GET['ids']) : [];

foreach ($ids as $id) {
    $product = getProductById(trim($id));
    if ($product) {
        $products[] = $product;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Krist — Compare Products</title>
    <link rel="stylesheet" href="./../CSS/mystyle.css">
    <link rel="stylesheet" href="./../CSS/compare.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include './../includes/navbar.php'; ?>

<section class="container">
    <h1>Product Comparison</h1>
    <?php if (empty($products)): ?>
        <p>No products selected for comparison. <a href="./list.php">Browse products</a></p>
    <?php else: ?>
        <!-- Comparison table here -->
    <?php endif; ?>
</section>

<?php include './../includes/footer.php'; ?>
</body>
</html>
