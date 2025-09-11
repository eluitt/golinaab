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
        const heroBg = document.querySelector('.gn-hero-background[data-parallax="true"]');
        const heroSection = document.querySelector('.gn-hero');

        if (!heroBg || !heroSection) return;

        // Allow forcing parallax even when reduced motion is enabled
        const forceParallax = heroBg.getAttribute('data-parallax-force') === 'true';
        if (prefersReducedMotion() && !forceParallax) return;

        let ticking = false;

        function update() {
            ticking = false;

            const rect = heroSection.getBoundingClientRect();
            const heroHeight = rect.height;
            const viewportH = window.innerHeight || document.documentElement.clientHeight;

            // Skip when hero is completely out of view
            if (rect.bottom <= 0 || rect.top >= viewportH) return;

            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const heroTop = rect.top + scrollTop;

            // Progress of scroll through hero: 0 (top) → 1 (bottom)
            const rawProgress = (scrollTop - heroTop) / Math.max(1, heroHeight);
            const progress = Math.max(0, Math.min(1, rawProgress));

            // Move background from ~top (5%) to lower (85%) as we scroll
            const posY = 5 + progress * 80; // 5% → 85%
            heroBg.style.backgroundPosition = 'center ' + posY + '%';

            // Add subtle depth with translate; keep scale to avoid edge gaps
            const translateY = Math.round((progress - 0.5) * 40); // ~ -20px → +20px
            heroBg.style.transform = 'translate3d(0,' + translateY + 'px,0) scale(1.06)';
        }

        function onScroll() {
            if (!ticking) {
                ticking = true;
                requestAnimationFrame(update);
            }
        }

        window.addEventListener('scroll', onScroll, { passive: true });
        window.addEventListener('resize', onScroll, { passive: true });

        // Initial position
        requestAnimationFrame(update);
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
        // Trigger a recalculation on resize
        window.requestAnimationFrame(function(){
            window.dispatchEvent(new Event('scroll'));
        });
    }, { passive: true });

})();
