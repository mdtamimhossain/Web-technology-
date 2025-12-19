/**
 * Shopping Cart JavaScript
 * Handles all cart operations via AJAX
 */

// Get API path from the global variable set by PHP
const API_PATH = window.apiPath || './api/';
const AUTH_PATH = window.authPath || './auth/';

/**
 * Show toast notification
 * @param {string} message - Message to display
 * @param {string} type - 'success', 'error', or 'info'
 */
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toastMessage');
    
    if (toast && toastMessage) {
        toastMessage.textContent = message;
        toast.className = `toast ${type}`;
        toast.classList.remove('hidden');
        
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 3000);
    }
}

/**
 * Add product to cart
 * @param {string} productId - Product ID
 * @param {number} quantity - Quantity to add
 * @param {string} size - Selected size
 * @param {string} color - Selected color
 */
async function addToCart(productId, quantity = 1, size = '', color = '') {
    try {
        const formData = new FormData();
        formData.append('action', 'add');
        formData.append('product_id', productId);
        formData.append('quantity', quantity);
        formData.append('size', size);
        formData.append('color', color);
        
        const response = await fetch(API_PATH + 'cart.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            updateCartBadge(result.cartCount);
            showToast('Product added to cart!', 'success');
        } else {
            showToast(result.message || 'Failed to add product', 'error');
        }
        
        return result;
    } catch (error) {
        console.error('Error adding to cart:', error);
        showToast('Error adding product to cart', 'error');
        return { success: false, message: error.message };
    }
}

/**
 * Update cart item quantity
 * @param {string} cartKey - Cart item key
 * @param {number} delta - Change in quantity (+1 or -1)
 */
async function updateQuantity(cartKey, delta) {
    const input = document.querySelector(`input[data-cart-key="${cartKey}"]`);
    if (!input) return;
    
    let newQuantity = parseInt(input.value) + delta;
    if (newQuantity < 1) newQuantity = 1;
    
    input.value = newQuantity;
    await setQuantity(cartKey, newQuantity);
}

/**
 * Set specific quantity for cart item
 * @param {string} cartKey - Cart item key
 * @param {number} quantity - New quantity
 */
async function setQuantity(cartKey, quantity) {
    quantity = parseInt(quantity);
    if (isNaN(quantity) || quantity < 1) {
        quantity = 1;
    }
    
    try {
        const formData = new FormData();
        formData.append('action', 'update');
        formData.append('cart_key', cartKey);
        formData.append('quantity', quantity);
        
        const response = await fetch(API_PATH + 'cart.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            updateCartDisplay(result.cartSummary);
            updateCartBadge(result.cartCount);
        } else {
            showToast(result.message || 'Failed to update cart', 'error');
        }
    } catch (error) {
        console.error('Error updating cart:', error);
        showToast('Error updating cart', 'error');
    }
}

/**
 * Remove item from cart
 * @param {string} cartKey - Cart item key
 */
async function removeItem(cartKey) {
    if (!confirm('Are you sure you want to remove this item?')) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('action', 'remove');
        formData.append('cart_key', cartKey);
        
        const response = await fetch(API_PATH + 'cart.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Remove item from DOM
            const itemElement = document.querySelector(`[data-cart-key="${cartKey}"]`);
            if (itemElement) {
                itemElement.remove();
            }
            
            updateCartDisplay(result.cartSummary);
            updateCartBadge(result.cartCount);
            showToast('Item removed from cart', 'success');
            
            // If cart is empty, reload page to show empty state
            if (result.cartCount === 0) {
                setTimeout(() => location.reload(), 500);
            }
        } else {
            showToast(result.message || 'Failed to remove item', 'error');
        }
    } catch (error) {
        console.error('Error removing item:', error);
        showToast('Error removing item', 'error');
    }
}

/**
 * Clear entire cart
 */
async function clearCart() {
    if (!confirm('Are you sure you want to clear your entire cart?')) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('action', 'clear');
        
        const response = await fetch(API_PATH + 'cart.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('Cart cleared', 'success');
            setTimeout(() => location.reload(), 500);
        } else {
            showToast(result.message || 'Failed to clear cart', 'error');
        }
    } catch (error) {
        console.error('Error clearing cart:', error);
        showToast('Error clearing cart', 'error');
    }
}

/**
 * Update cart badge in navbar
 * @param {number} count - New item count
 */
function updateCartBadge(count) {
    const badge = document.getElementById('cartBadge');
    const cartIcon = document.getElementById('cartIcon');
    
    if (badge) {
        badge.textContent = count;
        if (count > 0) {
            badge.classList.remove('hidden');
            if (cartIcon) {
                const icon = cartIcon.querySelector('i');
                if (icon) {
                    icon.classList.remove('cart-empty');
                    icon.classList.add('cart-filled');
                }
            }
        } else {
            badge.classList.add('hidden');
            if (cartIcon) {
                const icon = cartIcon.querySelector('i');
                if (icon) {
                    icon.classList.remove('cart-filled');
                    icon.classList.add('cart-empty');
                }
            }
        }
    }
}

/**
 * Update cart display with new summary
 * @param {object} summary - Cart summary object
 */
function updateCartDisplay(summary) {
    if (!summary) return;
    
    // Update individual item subtotals
    Object.keys(summary.items || {}).forEach(cartKey => {
        const item = summary.items[cartKey];
        const itemRow = document.querySelector(`[data-cart-key="${cartKey}"]`);
        if (itemRow) {
            const subtotalSpan = itemRow.querySelector('.item-subtotal');
            if (subtotalSpan) {
                subtotalSpan.textContent = (item.price * item.quantity).toFixed(2);
            }
        }
    });
    
    // Update summary totals
    const updateElement = (id, value) => {
        const el = document.getElementById(id);
        if (el) el.textContent = value;
    };
    
    updateElement('itemCount', summary.itemCount);
    updateElement('subtotal', summary.subtotal.toFixed(2));
    updateElement('subtotalDisplay', summary.subtotal.toFixed(2));
    updateElement('taxAmount', summary.tax.toFixed(2));
    updateElement('totalAmount', summary.total.toFixed(2));
}

/**
 * Show login modal
 */
function showLoginModal() {
    const modal = document.getElementById('loginModal');
    if (modal) {
        modal.style.display = 'flex';
    }
}

/**
 * Close login modal
 */
function closeLoginModal() {
    const modal = document.getElementById('loginModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

/**
 * Show order success modal
 * @param {string} orderNumber - Order number
 * @param {number} total - Order total
 */
function showOrderSuccess(orderNumber, total) {
    const modal = document.getElementById('orderSuccessModal');
    const orderNumEl = document.getElementById('orderNumber');
    const orderTotalEl = document.getElementById('orderTotal');
    
    if (modal) {
        if (orderNumEl) orderNumEl.textContent = orderNumber;
        if (orderTotalEl) orderTotalEl.textContent = '$' + parseFloat(total).toFixed(2);
        modal.style.display = 'flex';
    }
}

/**
 * Logout function
 */
async function logout() {
    try {
        const formData = new FormData();
        formData.append('action', 'logout');
        
        const response = await fetch(AUTH_PATH + 'auth_api.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            showToast('Logged out successfully', 'success');
            setTimeout(() => location.reload(), 500);
        }
    } catch (error) {
        console.error('Error logging out:', error);
    }
}

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    // Login form submission
    const loginForm = document.getElementById('loginForm');
    if (loginForm) {
        loginForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const email = document.getElementById('loginEmail').value;
            const password = document.getElementById('loginPassword').value;
            const errorDiv = document.getElementById('loginError');
            
            try {
                const formData = new FormData();
                formData.append('action', 'login');
                formData.append('email', email);
                formData.append('password', password);
                
                const response = await fetch(AUTH_PATH + 'auth_api.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showToast('Login successful!', 'success');
                    closeLoginModal();
                    setTimeout(() => location.reload(), 500);
                } else {
                    if (errorDiv) {
                        errorDiv.textContent = result.message;
                        errorDiv.classList.remove('hidden');
                    }
                }
            } catch (error) {
                console.error('Error logging in:', error);
                if (errorDiv) {
                    errorDiv.textContent = 'Login failed. Please try again.';
                    errorDiv.classList.remove('hidden');
                }
            }
        });
    }
    
    // Checkout form submission
    const checkoutForm = document.getElementById('checkoutForm');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(checkoutForm);
            formData.append('action', 'checkout');
            
            try {
                const response = await fetch(API_PATH + 'cart.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showOrderSuccess(result.orderNumber, result.total);
                    updateCartBadge(0);
                } else {
                    if (result.requireLogin) {
                        showLoginModal();
                    } else {
                        showToast(result.message || 'Checkout failed', 'error');
                    }
                }
            } catch (error) {
                console.error('Error during checkout:', error);
                showToast('Checkout failed. Please try again.', 'error');
            }
        });
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        const loginModal = document.getElementById('loginModal');
        if (e.target === loginModal) {
            closeLoginModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLoginModal();
        }
    });
});

// Make functions available globally
window.addToCart = addToCart;
window.updateQuantity = updateQuantity;
window.setQuantity = setQuantity;
window.removeItem = removeItem;
window.clearCart = clearCart;
window.showLoginModal = showLoginModal;
window.closeLoginModal = closeLoginModal;
window.logout = logout;
window.showToast = showToast;
