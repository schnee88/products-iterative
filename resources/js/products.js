// resources/js/products.js

class ProductManager {
    constructor() {
        console.log('ProductManager constructor called');
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        this.initEventListeners();
    }

    initEventListeners() {
        console.log('Initializing event listeners');
        
        // Remove any existing listeners first to prevent duplicates
        document.removeEventListener('click', this.clickHandler);
        
        // Create a bound method for the event handler
        this.clickHandler = this.handleClick.bind(this);
        document.addEventListener('click', this.clickHandler);
    }

    handleClick(e) {
        console.log('Click event detected', e.target);
        
        if (e.target.matches('.btn-increase') || e.target.closest('.btn-increase')) {
            e.preventDefault();
            const button = e.target.matches('.btn-increase') ? e.target : e.target.closest('.btn-increase');
            console.log('Increase button clicked for product:', button.dataset.productId);
            this.handleQuantityChange(button, 'increase');
        }

        if (e.target.matches('.btn-decrease') || e.target.closest('.btn-decrease')) {
            e.preventDefault();
            const button = e.target.matches('.btn-decrease') ? e.target : e.target.closest('.btn-decrease');
            console.log('Decrease button clicked for product:', button.dataset.productId);
            this.handleQuantityChange(button, 'decrease');
        }
    }

    async handleQuantityChange(button, action) {
        console.log(`Handling ${action} for product:`, button.dataset.productId);
        
        const productId = button.dataset.productId;
        const quantityElement = document.querySelector(`.quantity-${productId}`);
        const statusElement = document.querySelector(`.status-${productId}`);
        
        // Disable all buttons for this product during request
        const buttons = document.querySelectorAll(`[data-product-id="${productId}"]`);
        buttons.forEach(btn => {
            btn.disabled = true;
            btn.style.opacity = '0.6';
        });
        
        const originalText = button.innerHTML;
        button.innerHTML = '...';

        try {
            const response = await fetch(`/products/${productId}/${action}-quantity`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.csrfToken,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });

            const data = await response.json();
            console.log('Server response:', data);

            if (data.success) {
                // Update quantity display
                if (quantityElement) {
                    quantityElement.textContent = data.quantity;
                }
                
                // Update status if provided
                if (statusElement && data.status) {
                    statusElement.textContent = this.formatStatus(data.status);
                }
                
                // Show success message
                this.showFlashMessage(data.message, 'success');
            } else {
                // Show error message
                this.showFlashMessage(data.message, 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            this.showFlashMessage('An error occurred. Please try again.', 'error');
        } finally {
            // Re-enable buttons
            buttons.forEach(btn => {
                btn.disabled = false;
                btn.style.opacity = '1';
                btn.innerHTML = action === 'increase' ? '+' : '-';
            });
        }
    }

    formatStatus(status) {
        return status.split('_').map(word => 
            word.charAt(0).toUpperCase() + word.slice(1)
        ).join(' ');
    }

    showFlashMessage(message, type) {
        // Remove existing flash messages
        const existingFlash = document.querySelector('.flash-message');
        if (existingFlash) {
            existingFlash.remove();
        }

        // Create new flash message
        const flashDiv = document.createElement('div');
        flashDiv.className = `flash-message flash-${type}`;
        flashDiv.textContent = message;

        document.body.appendChild(flashDiv);

        // Auto remove after 3 seconds
        setTimeout(() => {
            if (flashDiv.parentNode) {
                flashDiv.remove();
            }
        }, 3000);
    }
}

// Make sure we only initialize once
let productManagerInitialized = false;

function initializeProductManager() {
    if (!productManagerInitialized) {
        console.log('Initializing ProductManager for the first time');
        new ProductManager();
        productManagerInitialized = true;
    } else {
        console.log('ProductManager already initialized, skipping...');
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', initializeProductManager);

// Export for potential module usage
export default ProductManager;