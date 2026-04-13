// assets/js/main.js
// Common JavaScript functions for MandiGateway

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function() {
                alert.remove();
            }, 500);
        }, 5000);
    });
});

// Add to cart with AJAX (optional enhancement)
function addToCart(productId, button) {
    fetch(`cart.php?add=${productId}`, {
        method: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.text())
    .then(() => {
        if(button) {
            button.innerHTML = '<i class="fas fa-check"></i> Added';
            button.classList.remove('btn-primary');
            button.classList.add('btn-success');
            setTimeout(() => {
                button.innerHTML = '<i class="fas fa-cart-plus"></i> Add to Cart';
                button.classList.remove('btn-success');
                button.classList.add('btn-primary');
            }, 2000);
        }
        // Update cart count if exists
        let cartBadge = document.querySelector('.cart-count');
        if(cartBadge) {
            let current = parseInt(cartBadge.innerText) || 0;
            cartBadge.innerText = current + 1;
        }
    });
}

// Product image zoom (for product detail page)
function initImageZoom() {
    const mainImg = document.querySelector('.product-main-img');
    if(mainImg) {
        mainImg.addEventListener('mousemove', function(e) {
            const zoom = document.querySelector('.image-zoom');
            if(!zoom) return;
            const x = e.offsetX / this.offsetWidth * 100;
            const y = e.offsetY / this.offsetHeight * 100;
            zoom.style.backgroundPosition = `${x}% ${y}%`;
        });
    }
}

// WhatsApp order quick button
function sendWhatsAppOrder(phone, productName, price) {
    const text = `I want to buy ${productName} for Rs. ${price}`;
    window.open(`https://wa.me/${phone}?text=${encodeURIComponent(text)}`, '_blank');
}

// Confirm delete actions
function confirmDelete(url) {
    if(confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
        window.location.href = url;
    }
    return false;
}

// Initialize tooltips (Bootstrap 5)
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
tooltipTriggerList.forEach(function (tooltipTriggerEl) {
    new bootstrap.Tooltip(tooltipTriggerEl);
});