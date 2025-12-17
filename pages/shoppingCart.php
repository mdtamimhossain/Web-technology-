<?php
require_once './../includes/cart_functions.php';

$cartSummary = getCartSummary();
$isUserLoggedIn = isLoggedIn();
$currentUser = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Krist</title>
    <link rel="stylesheet" href="./../CSS/mystyle.css">
    <link rel="stylesheet" href="./../CSS/shoppingCart.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<?php include './../includes/navbar.php'; ?>

<!-- Breadcrumb -->
<div class="breadcrumb container">
    <a href="./../index.php">Home</a> / <span>Shopping Cart</span>
</div>

<section class="cart-page container">
    <h1><i class="fa fa-shopping-cart"></i> Shopping Cart</h1>
    
    <?php if (empty($cartSummary['items'])): ?>
    <!-- Empty Cart State -->
    <div class="empty-cart">
        <div class="empty-cart-icon">
            <i class="fa fa-shopping-bag"></i>
        </div>
        <h2>Your cart is empty</h2>
        <p>Looks like you haven't added any items to your cart yet.</p>
        <a href="./list.php" class="btn-primary">
            <i class="fa fa-arrow-left"></i> Continue Shopping
        </a>
    </div>
    <?php else: ?>
    
    <div class="cart-layout">
        <!-- Cart Items Section -->
        <div class="cart-items-section">
            <div class="cart-header">
                <span class="col-product">Product</span>
                <span class="col-price">Price</span>
                <span class="col-quantity">Quantity</span>
                <span class="col-subtotal">Subtotal</span>
                <span class="col-action">Action</span>
            </div>
            
            <div class="cart-items" id="cartItems">
                <?php foreach ($cartSummary['items'] as $cartKey => $item): ?>
                <div class="cart-item" data-cart-key="<?php echo htmlspecialchars($cartKey); ?>">
                    <div class="col-product">
                        <img src="./../assets/<?php echo htmlspecialchars($item['image']); ?>" 
                             alt="<?php echo htmlspecialchars($item['name']); ?>" 
                             class="item-image">
                        <div class="item-details">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <?php if (!empty($item['size']) || !empty($item['color'])): ?>
                            <p class="item-variants">
                                <?php if (!empty($item['size'])): ?>
                                    <span>Size: <?php echo htmlspecialchars($item['size']); ?></span>
                                <?php endif; ?>
                                <?php if (!empty($item['color'])): ?>
                                    <span>Color: <?php echo htmlspecialchars($item['color']); ?></span>
                                <?php endif; ?>
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="col-price">
                        $<?php echo number_format($item['price'], 2); ?>
                    </div>
                    
                    <div class="col-quantity">
                        <div class="quantity-control">
                            <button class="qty-btn minus" onclick="updateQuantity('<?php echo $cartKey; ?>', -1)">
                                <i class="fa fa-minus"></i>
                            </button>
                            <input type="number" 
                                   class="qty-input" 
                                   value="<?php echo $item['quantity']; ?>" 
                                   min="1" 
                                   data-cart-key="<?php echo htmlspecialchars($cartKey); ?>"
                                   onchange="setQuantity('<?php echo $cartKey; ?>', this.value)">
                            <button class="qty-btn plus" onclick="updateQuantity('<?php echo $cartKey; ?>', 1)">
                                <i class="fa fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="col-subtotal">
                        $<span class="item-subtotal"><?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    </div>
                    
                    <div class="col-action">
                        <button class="remove-btn" onclick="removeItem('<?php echo $cartKey; ?>')" title="Remove item">
                            <i class="fa fa-trash"></i>
                        </button>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div class="cart-actions">
                <a href="./list.php" class="btn-secondary">
                    <i class="fa fa-arrow-left"></i> Continue Shopping
                </a>
                <button class="btn-outline" onclick="clearCart()">
                    <i class="fa fa-trash"></i> Clear Cart
                </button>
            </div>
        </div>
        
        <!-- Cart Summary Section -->
        <div class="cart-summary-section">
            <div class="cart-summary">
                <h2>Order Summary</h2>
                
                <div class="summary-row">
                    <span>Items (<span id="itemCount"><?php echo $cartSummary['itemCount']; ?></span>)</span>
                    <span>$<span id="subtotal"><?php echo number_format($cartSummary['subtotal'], 2); ?></span></span>
                </div>
                
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>$<span id="subtotalDisplay"><?php echo number_format($cartSummary['subtotal'], 2); ?></span></span>
                </div>
                
                <div class="summary-row tax-row">
                    <span>Tax (<?php echo $cartSummary['taxRate']; ?>%)</span>
                    <span>$<span id="taxAmount"><?php echo number_format($cartSummary['tax'], 2); ?></span></span>
                </div>
                
                <div class="summary-row total-row">
                    <span>Total</span>
                    <span class="total-amount">$<span id="totalAmount"><?php echo number_format($cartSummary['total'], 2); ?></span></span>
                </div>
                
                <?php if ($isUserLoggedIn): ?>
                <!-- Checkout Form for Logged-in Users -->
                <div class="checkout-section">
                    <h3>Shipping Information</h3>
                    <form id="checkoutForm">
                        <div class="form-group">
                            <label for="shippingName">Full Name</label>
                            <input type="text" id="shippingName" name="shipping_name" 
                                   value="<?php echo htmlspecialchars($currentUser['name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="shippingAddress">Address</label>
                            <textarea id="shippingAddress" name="shipping_address" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="shippingPhone">Phone</label>
                            <input type="tel" id="shippingPhone" name="shipping_phone" required>
                        </div>
                        <div class="form-group">
                            <label for="notes">Order Notes (Optional)</label>
                            <textarea id="notes" name="notes" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn-checkout">
                            <i class="fa fa-lock"></i> Place Order
                        </button>
                    </form>
                </div>
                <?php else: ?>
                <!-- Login Required Message -->
                <div class="login-required">
                    <div class="login-message">
                        <i class="fa fa-user-lock"></i>
                        <p>Please login to complete your order</p>
                    </div>
                    <button class="btn-checkout" onclick="showLoginModal()">
                        <i class="fa fa-sign-in-alt"></i> Login to Checkout
                    </button>
                    <p class="register-link">
                        Don't have an account? <a href="./registration.php">Register here</a>
                    </p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</section>

<!-- Login Modal -->
<div id="loginModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeLoginModal()">&times;</span>
        <h2><i class="fa fa-sign-in-alt"></i> Login</h2>
        <form id="loginForm">
            <div class="form-group">
                <label for="loginEmail">Email Address</label>
                <input type="email" id="loginEmail" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="loginPassword">Password</label>
                <input type="password" id="loginPassword" name="password" placeholder="Enter your password" required>
            </div>
            <div id="loginError" class="error-message hidden"></div>
            <button type="submit" class="btn-primary btn-full">
                <i class="fa fa-sign-in-alt"></i> Login
            </button>
        </form>
        <p class="modal-register-link">
            Don't have an account? <a href="./registration.php">Register here</a>
        </p>
    </div>
</div>

<!-- Order Success Modal -->
<div id="orderSuccessModal" class="modal">
    <div class="modal-content success-modal">
        <div class="success-icon">
            <i class="fa fa-check-circle"></i>
        </div>
        <h2>Order Placed Successfully!</h2>
        <p>Your order number is: <strong id="orderNumber"></strong></p>
        <p>Total: <strong id="orderTotal"></strong></p>
        <div class="modal-actions">
            <a href="./myorders.php" class="btn-primary">View My Orders</a>
            <a href="./list.php" class="btn-secondary">Continue Shopping</a>
        </div>
    </div>
</div>

<!-- Toast Notification -->
<div id="toast" class="toast hidden">
    <span id="toastMessage"></span>
</div>

<?php include './../includes/footer.php'; ?>

<script src="./../JS/script.js"></script>
<script src="./../JS/cart.js"></script>
</body>
</html>