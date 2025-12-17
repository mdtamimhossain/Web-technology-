<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Page</title>
    <link rel="stylesheet" href="./../CSS/mystyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include './../includes/navbar.php'; ?>

    <section class="container">
        <h1>Sneaker203</h1>
        <img src="./../assets/sneaker203.png" alt="SneakerMax203" style="max-width:200px;"/>
        <p><strong>Price:</strong> €79.99</p>
        <p><strong>Description:</strong> Sneaker203 is a comfortable running shoe with cushioned sole and high durability.</p>
        <p><strong>Product ID:</strong> sneaker203</p>
        
        <form action="#" method="post">
            <label for="qty">Quantity:</label>
            <input type="number" id="qty" name="quantity" value="1" min="1" />
            <div style="margin-top:8px;">
                <button type="submit">Add to cart</button>
                <button type="button">View cart</button>
            </div>
        </form>
    </section>

<?php include './../includes/footer.php'; ?>
<script src="./../JS/script.js"></script>
</body>
</html>