// Admin Dashboard Enhancements
function refreshStats() {
    // Add loading animation to refresh button
    const btn = document.querySelector('.btn-refresh');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<span class="loading"></span> Refreshing...';
    btn.disabled = true;

    // Simulate API call (replace with actual AJAX call)
    setTimeout(() => {
        // Update stats with animation
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach(card => {
            card.style.animation = 'none';
            setTimeout(() => {
                card.style.animation = 'slideInUp 0.6s ease-out forwards';
            }, 10);
        });

        btn.innerHTML = originalText;
        btn.disabled = false;
    }, 2000);
}

// Real-time clock
function updateTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString('en-US', {
        hour12: true,
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
    const timeElement = document.getElementById('current-time');
    if (timeElement) {
        timeElement.textContent = timeString;
    }
}

// Initialize admin features
document.addEventListener('DOMContentLoaded', function() {
    // Highlight selected candidate card
    const radioButtons = document.querySelectorAll('input[type="radio"]');
    radioButtons.forEach(radio => {
        radio.addEventListener('change', function() {
            const name = this.getAttribute('name');
            // Remove active class from all cards in the same position group
            document.querySelectorAll(`input[name="${name}"]`).forEach(r => {
                r.closest('.candidate-card').classList.remove('selected');
            });
            // Add active class to selected card
            if (this.checked) {
                this.closest('.candidate-card').classList.add('selected');
            }
        });
    });

    // Start real-time clock for admin dashboard
    if (document.getElementById('current-time')) {
        updateTime();
        setInterval(updateTime, 1000);
    }

    // Add page enter animation
    document.body.classList.add('page-enter');

    // Enhanced form interactions
    const formInputs = document.querySelectorAll('.form-group input, .form-group select, .form-group textarea');
    formInputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.classList.remove('focused');
            }
        });
    });

    // Smooth scroll for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
});
