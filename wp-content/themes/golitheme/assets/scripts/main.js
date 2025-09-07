/**
 * GoliNaab Theme Main JavaScript
 * 
 * @package GoliNaab
 * @since 1.0.0
 */

(function() {
    'use strict';

    // Shared scroll controller (single source of truth)
    const SCROLLER = document.scrollingElement || document.documentElement;
    let scrollTargetY = SCROLLER ? SCROLLER.scrollTop : 0;
    let scrollAnimFrame = null;
    function stopScrollAnimation() {
        if (scrollAnimFrame != null) {
            cancelAnimationFrame(scrollAnimFrame);
            scrollAnimFrame = null;
        }
    }

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
            stopScrollAnimation();
            const startY = SCROLLER.scrollTop;
            const delta = targetY - startY;
            const startTime = performance.now();
            function step(now) {
                const elapsed = now - startTime;
                const t = Math.min(1, elapsed / durationMs);
                const eased = easeOutQuint(t);
                SCROLLER.scrollTop = Math.round(startY + delta * eased);
                if (t < 1) {
                    scrollAnimFrame = requestAnimationFrame(step);
                } else {
                    scrollAnimFrame = null;
                }
            }
            scrollAnimFrame = requestAnimationFrame(step);
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
            const desired = rect.top + SCROLLER.scrollTop - headerOffset - 8;
            scrollTargetY = desired;
            smoothScrollTo(desired, 750);
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
        const maxScrollY = () => Math.max(0, SCROLLER.scrollHeight - window.innerHeight);

        function animate() {
            const current = SCROLLER.scrollTop;
            const distance = scrollTargetY - current;
            const step = distance * 0.12; // softer smoothing factor
            if (Math.abs(distance) < 0.5) { scrollAnimFrame = null; return; }
            SCROLLER.scrollTop = current + step;
            scrollAnimFrame = requestAnimationFrame(animate);
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
            const deltaY = normalizeDelta(e);
            // If event target or active element can scroll, let native behavior handle it
            if (canScrollElement(e.target, deltaY)) return;
            const ae = document.activeElement;
            if (ae) {
                const tag = ae.tagName || '';
                const type = (ae.type || '').toString();
                // Allow native wheel on inputs that use wheel to change value
                if (/SELECT/i.test(tag) || (/INPUT/i.test(tag) && /(number|range|date|time|month|week)/i.test(type))) return;
                if (canScrollElement(ae, deltaY)) return;
            }

            e.preventDefault();
            scrollTargetY = Math.max(0, Math.min(scrollTargetY + deltaY, maxScrollY()));
            if (scrollAnimFrame == null) scrollAnimFrame = requestAnimationFrame(animate);
        }

        window.addEventListener('wheel', onWheel, { passive: false });
        document.addEventListener('wheel', onWheel, { passive: false });

        window.addEventListener('resize', function() {
            scrollTargetY = Math.max(0, Math.min(scrollTargetY, maxScrollY()));
        });
        window.addEventListener('scroll', function() {
            if (scrollAnimFrame == null) scrollTargetY = SCROLLER.scrollTop;
        }, { passive: true });

        // Cancel animation when user drags scrollbar or touches
        window.addEventListener('pointerdown', function() { stopScrollAnimation(); }, { passive: true });
        window.addEventListener('pointerup', function() { scrollTargetY = SCROLLER.scrollTop; }, { passive: true });
    }

    // Initialize everything when DOM is ready
    domReady(function() {
        initMobileMenu();
        initSmoothScroll();
        initSmoothWheelScroll();
        initReducedMotion();
    });

})();
