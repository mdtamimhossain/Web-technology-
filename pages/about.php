<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>About Us - Krist</title>
    <link rel="stylesheet" href="./../CSS/mystyle.css">
    <link rel="stylesheet" href="./../CSS/about.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php
    $companyName = "Krist";
    $companyPhone = "+49 123 456 7890";
    $companyEmail = "info@krist.com";
    $companyAddress = "Gymnasiumstrasse 11,85049 Ingolstadt, Germany";
    $currentYear = date('Y');
    
    include './../includes/navbar.php';
?>

<!-- HERO SECTION -->
<section class="about-hero">
    <div class="about-container">
        <h1>About Krist</h1>
        <p>Discover the story behind your favorite online shopping destination</p>
    </div>
</section>

<!-- MAIN CONTENT -->
<div class="about-container">
    <!-- Our Story -->
    <section class="about-section">
        <h2>Our Story</h2>
        <div class="about-grid">
            <div class="about-text">
                <h3>Building Trust Since Day One</h3>
                <p>Krist was founded with a simple mission: to make quality shopping accessible to everyone. We started as a small team passionate about delivering excellent products and service.</p>
                <p>Today, we're proud to serve thousands of satisfied customers worldwide, offering a curated selection of shoes, electronics, and fashion items at competitive prices.</p>
            </div>
            <img src="./../assets/about-story.png" alt="Our Story" class="about-img" onerror="this.style.display='none'">
        </div>
    </section>

    <!-- Mission & Vision -->
    <section class="about-section">
        <h2>Our Mission & Vision</h2>
        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-bullseye"></i>
                </div>
                <h3>Our Mission</h3>
                <p>To provide exceptional online shopping experiences with quality products, reliable delivery, and outstanding customer service that exceeds expectations.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-star"></i>
                </div>
                <h3>Our Vision</h3>
                <p>To become the most trusted online retailer, known for innovation, sustainability, and customer-centric solutions in the global marketplace.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-heart"></i>
                </div>
                <h3>Our Values</h3>
                <p>Integrity, quality, and customer satisfaction drive everything we do. We believe in building long-term relationships with our customers.</p>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="about-section">
        <h2>Why Choose Krist?</h2>
        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-truck"></i>
                </div>
                <h3>Fast Delivery</h3>
                <p>We guarantee quick and reliable shipping to get your products to you promptly.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-shield"></i>
                </div>
                <h3>Secure Shopping</h3>
                <p>Your data is protected with industry-leading security measures and encryption.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-undo"></i>
                </div>
                <h3>Easy Returns</h3>
                <p>Hassle-free return policy ensures you shop with confidence and peace of mind.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-headset"></i>
                </div>
                <h3>24/7 Support</h3>
                <p>Our dedicated customer service team is always ready to help with any questions.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-tag"></i>
                </div>
                <h3>Best Prices</h3>
                <p>We offer competitive pricing and regular promotions to give you the best value.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fa-solid fa-check-circle"></i>
                </div>
                <h3>Quality Assured</h3>
                <p>All products go through rigorous quality checks before reaching your hands.</p>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="stats">
        <div class="stats-grid">
            <div class="stat">
                <div class="stat-number">50K+</div>
                <div class="stat-label">Happy Customers</div>
            </div>
            <div class="stat">
                <div class="stat-number">10K+</div>
                <div class="stat-label">Products</div>
            </div>
            <div class="stat">
                <div class="stat-number">100+</div>
                <div class="stat-label">Countries</div>
            </div>
            <div class="stat">
                <div class="stat-number">24/7</div>
                <div class="stat-label">Support</div>
            </div>
        </div>
    </section>

    <!-- Contact Information -->
    <section class="about-section">
        <div class="contact-card">
            <h3>Get In Touch With Us</h3>
            <div class="contact-info">
                <div class="contact-item">
                    <i class="fa fa-phone"></i>
                    <div class="contact-item-content">
                        <h4>Phone</h4>
                        <p><?php echo $companyPhone; ?></p>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fa fa-envelope"></i>
                    <div class="contact-item-content">
                        <h4>Email</h4>
                        <p><?php echo $companyEmail; ?></p>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="fa fa-map-marker"></i>
                    <div class="contact-item-content">
                        <h4>Address</h4>
                        <p><?php echo $companyAddress; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include './../includes/footer.php'; ?>
<script src="./../JS/script.js"></script>
</body>
</html>