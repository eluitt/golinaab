/**
 * Hero Section Functionality - Soft Parallax
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

    // Check if user prefers reduced motion
    function prefersReducedMotion() {
        return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    // Check if device is mobile (basic detection)
    function isMobile() {
        return window.innerWidth <= 768;
    }

    // Soft parallax implementation
    function initParallax() {
        const heroBackground = document.querySelector('.gn-hero-background[data-parallax="true"]');
        
        if (!heroBackground || prefersReducedMotion() || isMobile()) {
            return; // Skip parallax on mobile or if user prefers reduced motion
        }

        let ticking = false;
        let lastScrollY = 0;

        function updateParallax() {
            const scrollY = window.pageYOffset;
            const heroHeight = heroBackground.offsetHeight;
            const windowHeight = window.innerHeight;
            
            // Only apply parallax when hero is in viewport
            if (scrollY < heroHeight) {
                // Calculate parallax offset (subtle movement)
                const parallaxOffset = scrollY * 0.3; // 30% of scroll speed
                
                // Apply transform with GPU acceleration
                heroBackground.style.transform = `translateY(${parallaxOffset}px)`;
            }
            
            ticking = false;
        }

        function requestTick() {
            if (!ticking) {
                requestAnimationFrame(updateParallax);
                ticking = true;
            }
        }

        // Throttled scroll handler
        function handleScroll() {
            const currentScrollY = window.pageYOffset;
            
            // Only update if scroll direction changed or significant movement
            if (Math.abs(currentScrollY - lastScrollY) > 5) {
                requestTick();
                lastScrollY = currentScrollY;
            }
        }

        // Use passive event listener for better performance
        window.addEventListener('scroll', handleScroll, { passive: true });

        // Initial call
        updateParallax();
    }

    // Scroll indicator functionality
    function initScrollIndicator() {
        const scrollIndicator = document.querySelector('.gn-hero-scroll-indicator');
        
        if (!scrollIndicator) return;

        scrollIndicator.addEventListener('click', function() {
            const nextSection = document.querySelector('#categories');
            
            if (nextSection) {
                nextSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });

        // Hide scroll indicator when user scrolls down
        let lastScrollTop = 0;
        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > 100) {
                scrollIndicator.style.opacity = '0';
                scrollIndicator.style.pointerEvents = 'none';
            } else {
                scrollIndicator.style.opacity = '0.7';
                scrollIndicator.style.pointerEvents = 'auto';
            }
            
            lastScrollTop = scrollTop;
        }, { passive: true });
    }

    // Intersection Observer for performance optimization
    function initIntersectionObserver() {
        const heroSection = document.querySelector('.gn-hero');
        
        if (!heroSection || !('IntersectionObserver' in window)) return;

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    // Hero is in viewport - enable parallax
                    entry.target.classList.add('gn-hero-active');
                } else {
                    // Hero is out of viewport - disable parallax
                    entry.target.classList.remove('gn-hero-active');
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '50px'
        });

        observer.observe(heroSection);
    }

    // CTA button enhancement
    function initCTAButton() {
        const ctaButton = document.querySelector('.gn-hero-cta');
        
        if (!ctaButton) return;

        // Add click tracking (for analytics later)
        ctaButton.addEventListener('click', function() {
            // Track CTA click if analytics is available
            if (typeof gtag !== 'undefined') {
                gtag('event', 'click', {
                    event_category: 'Hero',
                    event_label: 'CTA Button',
                    value: 1
                });
            }
        });

        // Add hover effect enhancement
        ctaButton.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px) scale(1.05)';
        });

        ctaButton.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    }

    // Initialize everything when DOM is ready
    domReady(function() {
        initParallax();
        initScrollIndicator();
        initIntersectionObserver();
        initCTAButton();
    });

    // Handle window resize
    window.addEventListener('resize', function() {
        // Reinitialize parallax if screen size changes significantly
        if (isMobile()) {
            const heroBackground = document.querySelector('.gn-hero-background[data-parallax="true"]');
            if (heroBackground) {
                heroBackground.style.transform = 'none';
            }
        }
    }, { passive: true });

})();
