/**
 * Shopping Cart JavaScript
 * Cart er sob operation AJAX diye handle kore
 */

// PHP theke set kora API path ney
const API_PATH = window.apiPath || './api/';
const AUTH_PATH = window.authPath || './auth/';

// Toast notification dekhay
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

// Cart e product add kore
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

// Cart item er quantity update kore (delta diye)
async function updateQuantity(cartKey, delta) {
    const input = document.querySelector(`input[data-cart-key="${cartKey}"]`);
    if (!input) return;
    
    let newQuantity = parseInt(input.value) + delta;
    if (newQuantity < 1) newQuantity = 1;
    
    input.value = newQuantity;
    await setQuantity(cartKey, newQuantity);
}

// Cart item er specific quantity set kore
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

// Cart theke item remove kore
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
            // DOM theke item remove kore
            const itemElement = document.querySelector(`[data-cart-key="${cartKey}"]`);
            if (itemElement) {
                itemElement.remove();
            }
            
            updateCartDisplay(result.cartSummary);
            updateCartBadge(result.cartCount);
            showToast('Item removed from cart', 'success');
            
            // Cart empty hole page reload kore
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

// Pura cart clear kore
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

// Navbar e cart badge update kore
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

// Cart display update kore notun summary diye
function updateCartDisplay(summary) {
    if (!summary) return;
    
    // Individual item subtotals update kore
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
    
    // Summary totals update kore
    const updateElement = (id, value) => {
        const el = document.getElementById(id);
        if (el) el.textContent = value;
    };
    
    updateElement('itemCount', summary.itemCount);
    updateElement('subtotal', summary.subtotal.toFixed(2));
    updateElement('subtotalDisplay', summary.subtotal.toFixed(2));
    
    // Discount info update kore jodi thake
    if (summary.discount && summary.discount > 0) {
        const discountAmount = summary.subtotal * summary.discount / 100;
        updateElement('discountAmount', discountAmount.toFixed(2));
    }
    
    updateElement('taxAmount', summary.tax.toFixed(2));
    updateElement('totalAmount', summary.total.toFixed(2));
}

// Login modal dekhay
function showLoginModal() {
    const modal = document.getElementById('loginModal');
    if (modal) {
        modal.style.display = 'flex';
    }
}

// Login modal bondho kore
function closeLoginModal() {
    const modal = document.getElementById('loginModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// Order success modal dekhay
function showOrderSuccess(orderNumber, total, discountPercent = 0, subtotal = 0) {
    const modal = document.getElementById('orderSuccessModal');
    const orderNumEl = document.getElementById('orderNumber');
    const orderTotalEl = document.getElementById('orderTotal');
    const discountInfoEl = document.getElementById('orderDiscountInfo');
    const discountAmountEl = document.getElementById('orderDiscountAmount');
    const discountPercentEl = document.getElementById('orderDiscountPercent');
    
    if (modal) {
        if (orderNumEl) orderNumEl.textContent = orderNumber;
        if (orderTotalEl) orderTotalEl.textContent = '$' + parseFloat(total).toFixed(2);
        
        // Discount info dekhay jodi thake
        if (discountPercent > 0 && discountInfoEl) {
            const discountAmount = subtotal * discountPercent / 100;
            if (discountAmountEl) discountAmountEl.textContent = '$' + discountAmount.toFixed(2);
            if (discountPercentEl) discountPercentEl.textContent = discountPercent;
            discountInfoEl.classList.remove('hidden');
        } else if (discountInfoEl) {
            discountInfoEl.classList.add('hidden');
        }
        
        modal.style.display = 'flex';
    }
}

// Logout kore
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
    // Login form submit
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
    
    // Checkout form submit
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
                    // Simple discount - now returns percent as a number
                    showOrderSuccess(result.orderNumber, result.total, result.discount || 0, result.subtotal || 0);
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
    
    // Modal er baire click korle bondho hoy
    window.addEventListener('click', function(e) {
        const loginModal = document.getElementById('loginModal');
        if (e.target === loginModal) {
            closeLoginModal();
        }
    });
    
    // Escape key diye modal bondho kore
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLoginModal();
        }
    });
});

// Functions globally available kore
window.addToCart = addToCart;
window.updateQuantity = updateQuantity;
window.setQuantity = setQuantity;
window.removeItem = removeItem;
window.clearCart = clearCart;
window.showLoginModal = showLoginModal;
window.closeLoginModal = closeLoginModal;
window.logout = logout;
window.showToast = showToast;
