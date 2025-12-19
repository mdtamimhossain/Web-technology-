<?php
// Database Setup - Simple Version
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "webShop";

echo "<h1>Database Setup</h1>";

try {
    // Connect to MySQL
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    $pdo->exec("USE $dbname");
    echo "<p>Database created!</p>";
    
    // Users table
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        phone VARCHAR(20),
        address TEXT,
        is_blocked TINYINT(1) DEFAULT 0,
        blocked_reason TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p>Users table created!</p>";
    
    // Admins table
    $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p>Admins table created!</p>";
    
    // Orders table
    $pdo->exec("CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        order_number VARCHAR(50) NOT NULL,
        subtotal DECIMAL(10,2) NOT NULL,
        discount_percent INT DEFAULT 0,
        discount_amount DECIMAL(10,2) DEFAULT 0,
        tax_amount DECIMAL(10,2) NOT NULL,
        total_amount DECIMAL(10,2) NOT NULL,
        status VARCHAR(20) DEFAULT 'pending',
        shipping_name VARCHAR(100),
        shipping_address TEXT,
        shipping_phone VARCHAR(20),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p>Orders table created!</p>";
    
    // Order items table
    $pdo->exec("CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id VARCHAR(50) NOT NULL,
        product_name VARCHAR(255) NOT NULL,
        product_price DECIMAL(10,2) NOT NULL,
        quantity INT NOT NULL,
        size VARCHAR(10),
        color VARCHAR(50)
    )");
    echo "<p>Order items table created!</p>";
    
    // Add test user (password: test123)
    $password = password_hash("test123", PASSWORD_DEFAULT);
    $pdo->exec("INSERT IGNORE INTO users (name, email, password) VALUES ('Test User', 'test@example.com', '$password')");
    echo "<p>Test user added: test@example.com / test123</p>";
    
    // Add admin (password: admin123)
    $adminPass = password_hash("admin123", PASSWORD_DEFAULT);
    $pdo->exec("INSERT IGNORE INTO admins (username, password, name) VALUES ('admin', '$adminPass', 'Admin')");
    echo "<p>Admin added: admin / admin123</p>";
    
    echo "<h2>Setup Complete!</h2>";
    echo "<p><a href='../index.php'>Go to Homepage</a> | <a href='../administrator/login.php'>Go to Admin</a></p>";
    
} catch(PDOException $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}
?>
