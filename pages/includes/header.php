<?php
if (!isset($page_title)) $page_title = 'Krist — Webshop';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title><?php echo htmlspecialchars($page_title, ENT_QUOTES); ?></title>
  <link rel="stylesheet" href="./../CSS/mystyle.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<header class="navbar">
  <div class="nav-container">
    <div class="nav-left">
      <img src="./../assets/Group 56.png" alt="Krist Logo" class="logo">
    </div>
    <nav class="nav-center">
      <a href="./homePage.php">Home</a>
      <div class="dropdown">
        <a href="./list.php">Shop <span class="arrow">▾</span></a>
        <div class="dropdown-content">
          <a href="./list.php">Shoes</a>
          <a href="./list.php">Electronic</a>
        </div>
      </div>
      <a href="#">About us</a>
      <a href="#">Contact</a>
    </nav>
    <div class="nav-right">
      <button class="theme-toggle" id="themeToggle">🌙</button>
      <a class="icon search" href="#"><i class="fa fa-search"></i></a>
      <a class="icon"><i class="fa-regular fa-heart"></i></a>
      <a class="icon"><i class="fa fa-shopping-bag"></i></a>
      <a class="login-btn" href="./customer.php">Profile</a>
    </div>
  </div>
</header>
