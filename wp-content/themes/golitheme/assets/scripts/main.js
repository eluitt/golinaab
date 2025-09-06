/**
 * GoliNaab Theme Main JavaScript
 * 
 * @package GoliNaab
 * @since 1.0.0
 */

(function() {
    'use strict';

    // DOM ready utility
    function domReady(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    }

    // Mobile menu toggle
    function initMobileMenu() {
        const menuToggle = document.querySelector('.gn-menu-toggle');
        const primaryMenu = document.querySelector('#gn-primary-menu');
        
        if (!menuToggle || !primaryMenu) return;

        menuToggle.addEventListener('click', function() {
            const isExpanded = this.getAttribute('aria-expanded') === 'true';
            
            this.setAttribute('aria-expanded', !isExpanded);
            primaryMenu.classList.toggle('gn-menu-open');
            document.body.classList.toggle('gn-menu-open');
        });

        // Close menu on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && primaryMenu.classList.contains('gn-menu-open')) {
                menuToggle.click();
            }
        });
    }

    // Smooth scrolling for anchor links
    function initSmoothScroll() {
        const links = document.querySelectorAll('a[href^="#"]');
        
        links.forEach(link => {
            link.addEventListener('click', function(e) {
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                
                if (targetElement) {
                    e.preventDefault();
                    targetElement.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    // Respect reduced motion preference
    function initReducedMotion() {
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
        
        function handleReducedMotion() {
            if (prefersReducedMotion.matches) {
                document.documentElement.style.setProperty('--animation-duration', '0.01ms');
                document.documentElement.style.setProperty('--transition-duration', '0.01ms');
            } else {
                document.documentElement.style.removeProperty('--animation-duration');
                document.documentElement.style.removeProperty('--transition-duration');
            }
        }
        
        handleReducedMotion();
        prefersReducedMotion.addEventListener('change', handleReducedMotion);
    }

    // Initialize everything when DOM is ready
    domReady(function() {
        initMobileMenu();
        initSmoothScroll();
        initReducedMotion();
    });

})();
