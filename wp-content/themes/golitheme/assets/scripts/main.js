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

    // Smooth scrolling for anchor links (robust: same-page URLs with hashes)
    function initSmoothScroll() {
        const header = document.querySelector('.gn-header');
        const getHeaderOffset = () => (header ? header.offsetHeight || 0 : 0);
        const easeOutQuint = t => 1 - Math.pow(1 - t, 5);

        function smoothScrollTo(targetY, durationMs) {
            const startY = window.pageYOffset;
            const delta = targetY - startY;
            const startTime = performance.now();
            function step(now) {
                const elapsed = now - startTime;
                const t = Math.min(1, elapsed / durationMs);
                const eased = easeOutQuint(t);
                window.scrollTo(0, Math.round(startY + delta * eased));
                if (t < 1) requestAnimationFrame(step);
            }
            requestAnimationFrame(step);
        }

        document.addEventListener('click', function(e) {
            const anchor = e.target.closest('a');
            if (!anchor) return;
            const rawHref = anchor.getAttribute('href');
            if (!rawHref || rawHref.indexOf('#') === -1) return;
            let url;
            try { url = new URL(anchor.href, window.location.href); } catch (_) { return; }
            if (url.origin !== window.location.origin) return;
            if (url.pathname !== window.location.pathname) return;
            const hash = url.hash;
            if (!hash || hash === '#') return;
            const target = document.querySelector(hash);
            if (!target) return;

            e.preventDefault();
            const rect = target.getBoundingClientRect();
            const headerOffset = getHeaderOffset();
            const targetY = rect.top + window.pageYOffset - headerOffset - 8;
            smoothScrollTo(targetY, 750);
            if (history.pushState) history.pushState(null, '', hash);
        }, { passive: false });
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

    // Smooth wheel scrolling (rAF-based, respects inner scrollables)
    function initSmoothWheelScroll() {
        // Force enable even if OS has reduced motion (requested by design)
        const scroller = document.scrollingElement || document.documentElement;
        let targetY = scroller.scrollTop;
        let animFrame = null;

        const maxScrollY = () => Math.max(0, scroller.scrollHeight - window.innerHeight);

        function animate() {
            const current = scroller.scrollTop;
            const distance = targetY - current;
            const step = distance * 0.12; // softer smoothing factor
            if (Math.abs(distance) < 0.5) { animFrame = null; return; }
            scroller.scrollTop = current + step;
            animFrame = requestAnimationFrame(animate);
        }

        function normalizeDelta(e) {
            if (e.deltaMode === 1) return e.deltaY * 16; // lines → px
            if (e.deltaMode === 2) return e.deltaY * window.innerHeight; // pages → px
            return e.deltaY;
        }

        function canScrollElement(el, deltaY) {
            let node = el;
            while (node && node !== document.body) {
                const style = window.getComputedStyle(node);
                const overflowY = style.overflowY;
                const isScrollable = (overflowY === 'auto' || overflowY === 'scroll');
                if (isScrollable && node.scrollHeight > node.clientHeight) {
                    if (deltaY > 0 && node.scrollTop + node.clientHeight < node.scrollHeight) return true;
                    if (deltaY < 0 && node.scrollTop > 0) return true;
                }
                node = node.parentElement;
            }
            return false;
        }

        function onWheel(e) {
            if (e.defaultPrevented) return;
            if (e.ctrlKey || e.metaKey || e.shiftKey || e.altKey) return;
            const activeTag = (document.activeElement && document.activeElement.tagName) || '';
            if (/INPUT|TEXTAREA|SELECT/.test(activeTag)) return;
            const deltaY = normalizeDelta(e);
            if (canScrollElement(e.target, deltaY)) return;

            e.preventDefault();
            targetY = Math.max(0, Math.min(targetY + deltaY, maxScrollY()));
            if (animFrame == null) animFrame = requestAnimationFrame(animate);
        }

        window.addEventListener('wheel', onWheel, { passive: false });
        document.addEventListener('wheel', onWheel, { passive: false });

        window.addEventListener('resize', function() {
            targetY = Math.max(0, Math.min(targetY, maxScrollY()));
        });
        window.addEventListener('scroll', function() {
            if (animFrame == null) targetY = scroller.scrollTop;
        }, { passive: true });
    }

    // Initialize everything when DOM is ready
    domReady(function() {
        initMobileMenu();
        initSmoothScroll();
        initSmoothWheelScroll();
        initReducedMotion();
    });

})();
