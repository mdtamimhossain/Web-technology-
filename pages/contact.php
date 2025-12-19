<?php
require_once './../includes/functions.php';

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $msg = $_POST['message'] ?? '';
    
    if ($name && $email && $subject && $msg) {
        $message = 'Thank you for your message! We will get back to you soon.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Krist</title>
    <link rel="stylesheet" href="./../CSS/mystyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .contact-page { padding: 40px 0; }
        .contact-page h1 { margin-bottom: 30px; }
        .contact-container { display: grid; grid-template-columns: 1fr 1fr; gap: 40px; }
        .contact-info { background: #f8f9fa; padding: 30px; border-radius: 10px; }
        .contact-info h2 { margin-bottom: 20px; color: #333; }
        .info-item { display: flex; align-items: flex-start; margin-bottom: 20px; }
        .info-item i { width: 40px; height: 40px; background: #000; color: #fff; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; }
        .info-item div h4 { margin: 0 0 5px 0; }
        .info-item div p { margin: 0; color: #666; }
        .contact-form { background: #fff; padding: 30px; border: 1px solid #eee; border-radius: 10px; }
        .contact-form h2 { margin-bottom: 20px; color: #333; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; }
        .form-group input, .form-group textarea { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 14px; }
        .form-group textarea { resize: vertical; min-height: 120px; }
        .btn-submit { background: #000; color: #fff; padding: 12px 30px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .btn-submit:hover { background: #333; }
        .success-message { background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        .map-section { margin-top: 40px; }
        .map-section iframe { width: 100%; height: 300px; border: 0; border-radius: 10px; }
        @media (max-width: 768px) { .contact-container { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
<?php include './../includes/navbar.php'; ?>

<div class="breadcrumb container">
    <a href="./../index.php">Home</a> / <span>Contact Us</span>
</div>

<section class="contact-page container">
    <h1><i class="fa fa-envelope"></i> Contact Us</h1>
    
    <div class="contact-container">
        <div class="contact-info">
            <h2>Get In Touch</h2>
            <p style="color: #666; margin-bottom: 30px;">Have questions? We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
            
            <div class="info-item">
                <i class="fa fa-map-marker-alt"></i>
                <div>
                    <h4>Address</h4>
                    <p>301 Baseline Rd, Highland, California 92346</p>
                </div>
            </div>
            
            <div class="info-item">
                <i class="fa fa-phone"></i>
                <div>
                    <h4>Phone</h4>
                    <p>707-927-0137</p>
                </div>
            </div>
            
            <div class="info-item">
                <i class="fa fa-envelope"></i>
                <div>
                    <h4>Email</h4>
                    <p>krist@example.com</p>
                </div>
            </div>
            
            <div class="info-item">
                <i class="fa fa-clock"></i>
                <div>
                    <h4>Business Hours</h4>
                    <p>Mon - Fri: 9:00 AM - 6:00 PM</p>
                </div>
            </div>
        </div>
        
        <div class="contact-form">
            <h2>Send Message</h2>
            
            <?php if ($message): ?>
            <div class="success-message">
                <i class="fa fa-check-circle"></i> <?php echo $message; ?>
            </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="name">Your Name</label>
                    <input type="text" id="name" name="name" placeholder="Enter your name" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Your Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required>
                </div>
                
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" placeholder="Enter subject" required>
                </div>
                
                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" placeholder="Write your message here..." required></textarea>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="fa fa-paper-plane"></i> Send Message
                </button>
            </form>
        </div>
    </div>
    
    <div class="map-section">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3305.7152203584424!2d-117.20857668478727!3d34.12861498059366!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80c353c8a0e9b9e5%3A0x4f6d7f1c7a8b9c0d!2s301%20Baseline%20Rd%2C%20Highland%2C%20CA%2092346!5e0!3m2!1sen!2sus!4v1600000000000!5m2!1sen!2sus" allowfullscreen="" loading="lazy"></iframe>
    </div>
</section>

<?php include './../includes/footer.php'; ?>
<script src="./../JS/script.js"></script>
<script src="./../JS/cart.js"></script>
</body>
</html>
