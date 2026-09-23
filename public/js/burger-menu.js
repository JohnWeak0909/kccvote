/**
 * Burger Menu Component - JavaScript
 * Handles responsive navigation for KEVS (KCC e-Voting System)
 */

class BurgerMenu {
    constructor(options = {}) {
        this.burgerBtn = document.getElementById(options.burgerBtnId || 'burger-btn');
        this.burgerMenu = document.getElementById(options.burgerMenuId || 'burger-menu');
        this.burgerOverlay = document.getElementById(options.burgerOverlayId || 'burger-overlay');
        
        this.isOpen = false;
        this.isAnimating = false;
        
        // Optional: auto-detect active page
        this.autoDetectActive = options.autoDetectActive !== false;
        
        if (this.burgerBtn && this.burgerMenu && this.burgerOverlay) {
            this.init();
        }
    }

    /**
     * Initialize burger menu
     */
    init() {
        this.attachEventListeners();
        this.handleWindowResize();
        if (this.autoDetectActive) {
            this.setActivePage();
        }
    }

    /**
     * Attach event listeners
     */
    attachEventListeners() {
        // Burger button click
        this.burgerBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggle();
        });

        // Overlay click
        this.burgerOverlay.addEventListener('click', () => this.close());

        // Menu links click
        const links = this.burgerMenu.querySelectorAll('.burger-nav-link:not(.logout):not(.burger-dropdown-toggle)');
        links.forEach(link => {
            link.addEventListener('click', () => {
                // Close menu after navigation
                setTimeout(() => this.close(), 100);
            });
        });

        // Dropdown toggles
        const dropdownToggles = this.burgerMenu.querySelectorAll('.burger-dropdown-toggle');
        dropdownToggles.forEach(toggle => {
            toggle.addEventListener('click', (event) => {
                event.preventDefault();
                const parent = toggle.closest('.burger-dropdown');
                const submenu = parent.querySelector('.burger-subnav');
                const expanded = toggle.getAttribute('aria-expanded') === 'true';
                toggle.setAttribute('aria-expanded', String(!expanded));
                parent.classList.toggle('open', !expanded);
                submenu.classList.toggle('open', !expanded);
            });
        });

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.close();
            }
        });

        // Handle window resize
        window.addEventListener('resize', () => this.handleWindowResize());

        // Prevent body scroll when menu is open
        this.burgerMenu.addEventListener('wheel', (e) => {
            if (this.isOpen) {
                e.stopPropagation();
            }
        }, { passive: true });
    }

    /**
     * Handle window resize
     */
    handleWindowResize() {
        if (window.innerWidth > 768 && this.isOpen) {
            this.close();
        }
    }

    /**
     * Toggle menu open/close
     */
    toggle() {
        if (this.isOpen) {
            this.close();
        } else {
            this.open();
        }
    }

    /**
     * Open menu
     */
    open() {
        if (this.isAnimating || this.isOpen) return;
        
        this.isAnimating = true;
        
        // Add active classes
        this.burgerMenu.classList.add('active');
        this.burgerOverlay.classList.add('active');
        this.burgerBtn.classList.add('active');
        
        // Disable body scroll
        document.body.style.overflow = 'hidden';
        
        // Focus management for accessibility
        this.burgerMenu.focus();
        
        // Trigger reflow to ensure animation
        void this.burgerMenu.offsetHeight;
        
        this.isOpen = true;
        
        setTimeout(() => {
            this.isAnimating = false;
        }, 300);

        // Dispatch custom event
        this.dispatchEvent('burgerMenuOpened');
    }

    /**
     * Close menu
     */
    close() {
        if (this.isAnimating || !this.isOpen) return;
        
        this.isAnimating = true;
        
        // Remove active classes
        this.burgerMenu.classList.remove('active');
        this.burgerOverlay.classList.remove('active');
        this.burgerBtn.classList.remove('active');
        
        // Enable body scroll
        document.body.style.overflow = 'auto';
        
        // Focus burger button for accessibility
        this.burgerBtn.focus();
        
        this.isOpen = false;
        
        setTimeout(() => {
            this.isAnimating = false;
        }, 300);

        // Dispatch custom event
        this.dispatchEvent('burgerMenuClosed');
    }

    /**
     * Set active page based on current URL
     */
    setActivePage() {
        const currentUrl = window.location.pathname;
        const links = this.burgerMenu.querySelectorAll('.burger-nav-link');
        
        links.forEach(link => {
            link.classList.remove('active');
            
            // Get the href attribute
            const href = link.getAttribute('href');
            
            if (href) {
                // Check if current URL contains this link's path
                if (currentUrl.includes(href.replace(/^\.\.\/|^\//g, ''))) {
                    link.classList.add('active');
                    const parentDropdown = link.closest('.burger-dropdown');
                    if (parentDropdown) {
                        parentDropdown.classList.add('open');
                        const toggle = parentDropdown.querySelector('.burger-dropdown-toggle');
                        const submenu = parentDropdown.querySelector('.burger-subnav');
                        if (toggle) toggle.setAttribute('aria-expanded', 'true');
                        if (submenu) submenu.classList.add('open');
                    }                }
                
                // Also check for exact match or parent match
                if (currentUrl.endsWith(href) || currentUrl.endsWith(href + '/')) {
                    link.classList.add('active');
                }
            }
        });
    }

    /**
     * Manually set active link
     */
    setActiveLink(selector) {
        const links = this.burgerMenu.querySelectorAll('.burger-nav-link');
        links.forEach(link => link.classList.remove('active'));
        
        const activeLink = this.burgerMenu.querySelector(selector);
        if (activeLink) {
            activeLink.classList.add('active');
        }
    }

    /**
     * Dispatch custom event
     */
    dispatchEvent(eventName) {
        const event = new CustomEvent(eventName, {
            detail: { menu: this }
        });
        document.dispatchEvent(event);
    }

    /**
     * Add menu item dynamically
     */
    addMenuItem(label, href, icon = 'bi-circle', options = {}) {
        const li = document.createElement('li');
        li.className = 'burger-nav-item';
        
        const isLogout = options.logout || false;
        const link = document.createElement('a');
        link.className = `burger-nav-link ${isLogout ? 'logout' : ''}`;
        link.href = href;
        
        if (options.target) {
            link.setAttribute('target', options.target);
        }
        
        link.innerHTML = `
            <span class="burger-nav-icon">
                <i class="bi ${icon}"></i>
            </span>
            <span class="burger-nav-text">${label}</span>
        `;
        
        link.addEventListener('click', () => {
            if (!isLogout) {
                setTimeout(() => this.close(), 100);
            }
        });
        
        li.appendChild(link);
        
        // Insert before logout if it exists
        const nav = this.burgerMenu.querySelector('.burger-nav');
        const logoutItem = nav.querySelector('.burger-nav-item:has(.logout)');
        
        if (logoutItem) {
            logoutItem.parentNode.insertBefore(li, logoutItem);
        } else {
            nav.appendChild(li);
        }
        
        return link;
    }

    /**
     * Remove menu item
     */
    removeMenuItem(selector) {
        const item = this.burgerMenu.querySelector(selector);
        if (item) {
            item.remove();
        }
    }

    /**
     * Update menu item label
     */
    updateMenuLabel(selector, newLabel) {
        const link = this.burgerMenu.querySelector(selector);
        if (link) {
            const textSpan = link.querySelector('.burger-nav-text');
            if (textSpan) {
                textSpan.textContent = newLabel;
            }
        }
    }

    /**
     * Destroy menu instance
     */
    destroy() {
        this.burgerBtn.removeEventListener('click', null);
        this.burgerOverlay.removeEventListener('click', null);
        document.body.style.overflow = 'auto';
        this.burgerMenu.classList.remove('active');
        this.burgerOverlay.classList.remove('active');
        this.burgerBtn.classList.remove('active');
    }
}

/**
 * Auto-initialize burger menu on page load
 */
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize if burger menu elements exist
    if (document.getElementById('burger-btn') && 
        document.getElementById('burger-menu') && 
        document.getElementById('burger-overlay')) {
        window.burgerMenu = new BurgerMenu({
            autoDetectActive: true
        });
    }
});

/**
 * Export for module usage
 */
if (typeof module !== 'undefined' && module.exports) {
    module.exports = BurgerMenu;
}
