/**
 * Hero Particles Overlay — lightweight, above all slides
 * - Attaches a canvas over `.gn-hero`
 * - Respects prefers-reduced-motion
 * - Mobile-throttled; ~30fps with frame skip
 */
(function() {
    'use strict';

    function domReady(fn) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fn);
        } else {
            fn();
        }
    }

    function prefersReducedMotion() {
        return window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    }

    domReady(function() {
        var hero = document.querySelector('.gn-hero');
        if (!hero) return;

        var forceParticles = hero.getAttribute('data-particles-force') === 'true';
        if (prefersReducedMotion() && !forceParticles) return;

        // Create and mount canvas overlay
        var canvas = document.createElement('canvas');
        canvas.id = 'gn-hero-canvas';
        canvas.className = 'gn-hero-canvas';
        canvas.setAttribute('aria-hidden', 'true');
        canvas.style.position = 'absolute';
        canvas.style.inset = '0';
        canvas.style.pointerEvents = 'none';
        canvas.style.zIndex = '9'; // below content (z=10), above overlay
        hero.appendChild(canvas);

        var ctx = canvas.getContext('2d');
        try { console.debug('[GN] particles: init'); } catch(e) {}

        // DPR-aware sizing
        var dpr = Math.max(1, Math.min(2, window.devicePixelRatio || 1));
        function sizeToHero() {
            var rect = hero.getBoundingClientRect();
            // Use layout px for style, DPR-scaled for buffer
            canvas.style.width = rect.width + 'px';
            canvas.style.height = rect.height + 'px';
            canvas.width = Math.max(1, Math.floor(rect.width * dpr));
            canvas.height = Math.max(1, Math.floor(rect.height * dpr));
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        }
        sizeToHero();
        window.addEventListener('resize', function() {
            sizeToHero();
            rebuildParticles();
        }, { passive: true });

        // Build a soft radial sprite once
        var SPRITE_BASE = 8; // smaller base for finer particles
        var sprite = document.createElement('canvas');
        sprite.width = SPRITE_BASE;
        sprite.height = SPRITE_BASE;
        var sctx = sprite.getContext('2d');
        var grad = sctx.createRadialGradient(
            SPRITE_BASE / 2, SPRITE_BASE / 2, 0,
            SPRITE_BASE / 2, SPRITE_BASE / 2, SPRITE_BASE / 2
        );
        grad.addColorStop(0.0, 'rgba(245,232,255,0.92)'); // brighter core
        grad.addColorStop(0.55, 'rgba(200,162,200,0.45)');
        grad.addColorStop(1.0, 'rgba(255,255,255,0)');
        sctx.fillStyle = grad;
        sctx.beginPath();
        sctx.arc(SPRITE_BASE/2, SPRITE_BASE/2, SPRITE_BASE/2, 0, Math.PI*2);
        sctx.fill();

        // Particles model
        var particles = [];
        function spawn(i) {
            var width = canvas.width / dpr;
            var height = canvas.height / dpr;
            var bias = Math.pow(Math.random(), 2); // denser near bottom
            particles[i] = {
                x: Math.random() * width,
                y: height * (1 - bias),
                size: 0.4 + Math.random() * 0.7, // smaller overall scale
                speed: 0.22 + Math.random() * 0.35,
                drift: (Math.random() - 0.5) * 0.28
            };
        }

        function computeCount() {
            var width = canvas.width / dpr;
            var height = canvas.height / dpr;
            var area = width * height;
            // Rough density target; clamp for perf
            var count = Math.round(area / 16000); // ~75 on 1440x900
            if (window.innerWidth <= 768) count = Math.round(count * 0.6);
            return Math.max(48, Math.min(120, count));
        }

        function rebuildParticles() {
            var count = computeCount();
            particles.length = 0;
            for (var i = 0; i < count; i++) spawn(i);
        }
        rebuildParticles();
        try { console.debug('[GN] particles: count', particles.length); } catch(e) {}

        // Animation loop (approximately 30fps via frame skip)
        var frame = 0;
        var running = true;
        document.addEventListener('visibilitychange', function() {
            running = document.visibilityState === 'visible';
        });

        function animate() {
            requestAnimationFrame(animate);
            if (!running) return;
            if ((++frame & 1) === 1) return; // skip every other frame

            var width = canvas.width / dpr;
            var height = canvas.height / dpr;

            ctx.clearRect(0, 0, width, height);
            ctx.globalCompositeOperation = 'lighter';

            for (var i = 0; i < particles.length; i++) {
                var p = particles[i];
                p.y -= p.speed;
                p.x += p.drift;
                if (p.y < -12) { spawn(i); p.y = height + 12; }

                // Fade out in the top 25%
                var fade = Math.min(1, p.y / (height * 0.25));
                ctx.globalAlpha = fade;

                var s = SPRITE_BASE * p.size;
                ctx.drawImage(sprite, p.x - s/2, p.y - s/2, s, s);
            }

            ctx.globalAlpha = 1;
            ctx.globalCompositeOperation = 'source-over';
        }

        animate();
    });
})();


