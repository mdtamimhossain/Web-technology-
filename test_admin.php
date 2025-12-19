<?php
require_once __DIR__ . "/database/db.php";

$pdo = getDBConnection();
$username = "admin";
$password = "admin123";

$stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
$stmt->execute([$username]);
$admin = $stmt->fetch();

echo "Admin found: " . ($admin ? "YES" : "NO") . "\n";
if ($admin) {
    echo "Stored hash: " . $admin["password"] . "\n";
    echo "Password verify: " . (password_verify($password, $admin["password"]) ? "TRUE" : "FALSE") . "\n";
}
