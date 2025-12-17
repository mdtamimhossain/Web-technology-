<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Krist — Personal Information</title>
    <link rel="stylesheet" href="./../CSS/mystyle.css">
    <link rel="stylesheet" href="./../CSS/myorders.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include './../includes/navbar.php'; ?>

<!-- MAIN PROFILE SECTION -->
<section class="profile-container container">
    <!-- Sidebar -->
    <aside class="profile-sidebar">
        <div class="profile-user">
            <img src="./../assets/Tareq/img.png" alt="User Photo">
            <div>
                <p>Hello 👋</p>
                <h4>Robert Fox</h4>
            </div>
        </div>

        <nav class="profile-menu">
            <a href="./personal_information.php" class="active"><i class="fa-regular fa-user"></i> Personal Information</a>
            <a href="./customer.php"><i class="fa-solid fa-box"></i> My Orders</a>
            <a href="#"><i class="fa-regular fa-heart"></i> My Wishlists</a>
            <a href="#"><i class="fa-regular fa-address-card"></i> Manage Addresses</a>
            <a href="#"><i class="fa-regular fa-credit-card"></i> Saved Cards</a>
            <a href="#"><i class="fa-regular fa-bell"></i> Notifications</a>
            <a href="#"><i class="fa-solid fa-gear"></i> Settings</a>
        </nav>
    </aside>

    <!-- Personal Info Section -->
    <div class="profile-content">
        <h2>Personal Information</h2>

        <div class="personal-card">
            <div class="personal-header">
                <div class="avatar">
                    <img src="./../assets/Tareq/img.png" alt="User Photo">
                </div>
                <div class="personal-head-info">
                    <h3>Robert Fox</h3>
                    <p class="muted">Member since 2022</p>
                </div>
                <button class="btn-outline edit-avatar" type="button"><i class="fa fa-pen"></i> Edit</button>
            </div>

            <form class="personal-form" id="personalForm">
                <div>
                    <label for="firstName">First name</label>
                    <input id="firstName" name="firstName" type="text" value="Robert" required />
                </div>

                <div>
                    <label for="lastName">Last name</label>
                    <input id="lastName" name="lastName" type="text" value="Fox" required />
                </div>

                <div>
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="robertfox@example.com" required />
                </div>

                <div>
                    <label for="phone">Phone</label>
                    <input id="phone" name="phone" type="tel" value="+1 555 123 4567" />
                </div>

                <div class="full-width">
                    <label for="address">Address</label>
                    <textarea id="address" name="address">123 Main Street, Springfield</textarea>
                </div>

                <div>
                    <label for="city">City</label>
                    <input id="city" name="city" type="text" value="Springfield" />
                </div>

                <div>
                    <label for="country">Country</label>
                    <input id="country" name="country" type="text" value="USA" />
                </div>

                <div class="full-width personal-actions">
                    <button type="submit" class="btn-primary">Save Changes</button>
                    <button type="button" class="btn-outline" id="cancelBtn">Cancel</button>
                </div>
            </form>
        </div>

    </div>
</section>

<?php include './../includes/footer.php'; ?>
<script src="./../JS/script.js"></script>
<script src="./../JS/validation.js"></script>
<script src="./../JS/profile.js"></script>
</body>
</html>
