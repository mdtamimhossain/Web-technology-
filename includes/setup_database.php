<?php


// Database credentials
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'webShop';

echo "<h1>WebShop Database Setup</h1>";

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    // Create database if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p style='color:green;'>✓ Database '$dbname' created or already exists.</p>";
    
    $pdo->exec("USE `$dbname`");
    

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            phone VARCHAR(20),
            address TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB
    ");
    echo "<p style='color:green;'>✓ Table 'users' created successfully.</p>";
    
    // Create shopping_cart table for persistent permant and guest er jonne session-based carts
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS shopping_cart (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NULL,
            session_id VARCHAR(255) NULL,
            product_id VARCHAR(50) NOT NULL,
            product_name VARCHAR(255) NOT NULL,
            product_price DECIMAL(10,2) NOT NULL,
            quantity INT NOT NULL DEFAULT 1,
            size VARCHAR(10),
            color VARCHAR(50),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_session_id (session_id)
        ) ENGINE=InnoDB
    ");
    echo "<p style='color:green;'>✓ Table 'shopping_cart' created successfully.</p>";
    
    // Create orders table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS orders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            order_number VARCHAR(50) NOT NULL UNIQUE,
            subtotal DECIMAL(10,2) NOT NULL,
            tax_amount DECIMAL(10,2) NOT NULL,
            total_amount DECIMAL(10,2) NOT NULL,
            status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
            shipping_name VARCHAR(100),
            shipping_address TEXT,
            shipping_phone VARCHAR(20),
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user_id (user_id),
            INDEX idx_order_number (order_number),
            INDEX idx_status (status)
        ) ENGINE=InnoDB
    ");
    echo "<p style='color:green;'>✓ Table 'orders' created successfully.</p>";
    
    // Create order_items table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS order_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            order_id INT NOT NULL,
            product_id VARCHAR(50) NOT NULL,
            product_name VARCHAR(255) NOT NULL,
            product_price DECIMAL(10,2) NOT NULL,
            quantity INT NOT NULL,
            size VARCHAR(10),
            color VARCHAR(50),
            subtotal DECIMAL(10,2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
            INDEX idx_order_id (order_id)
        ) ENGINE=InnoDB
    ");
    echo "<p style='color:green;'>✓ Table 'order_items' created successfully.</p>";
    
    // Insert a test user (password: test123) default hisebe
    $testPassword = password_hash('test123', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO users (name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute(['Test User', 'test@example.com', $testPassword, '123-456-7890', '123 Test Street, Test City']);
    echo "<p style='color:blue;'>ℹ Test user created: email='test@example.com', password='test123'</p>";
    
    echo "<hr>";
    echo "<h2 style='color:green;'>✓ Database setup completed successfully!</h2>";
    echo "<p>You can now use the shopping cart functionality.</p>";
    echo "<p><a href='../index.php'>← Go to Homepage</a></p>";
    
} catch (PDOException $e) {
    echo "<p style='color:red;'>✗ Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Make sure XAMPP MySQL is running and the credentials are correct.</p>";
}
?>
