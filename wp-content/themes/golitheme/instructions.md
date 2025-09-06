# instructions.md — GoliNaab Build Plan (v2, No Code Here)

**How to use:** Execute **one step at a time** via Cursor (say "Run Step <N>"). Ask clarifying questions before any changes. After each step, report: changed files, key decisions, risks, and acceptance results (e.g., Lighthouse scores, Query Monitor status). Strictly follow .cursorrules. Priority: Complete home page first (Steps 0-5), then others.

---

## Step 0 — Theme Skeleton & Tooling
**Goal:** Set up directories, core theme files, Tailwind pipeline, theme.json tokens (colors/typography), base enqueues (fonts, critical CSS/JS), ACF JSON setup. Git init with .gitignore.

**Expected changes**
- Create folders: `assets/{styles,scripts,icons}`, `components/`, `templates/`, `inc/`, `acf-json/`.
- Core files: `style.css` (header comment, enqueue Tailwind), `functions.php` (theme support: title-tag, post-thumbnails, html5; conditional enqueues for fonts/Doran & Crimson Pro; include inc/setup.php), `index.php` (basic loop), minimal `header.php`/`footer.php` (wp_head/body_open).
- Tailwind config: `tailwind.config.js` (content: ['./**/*.php'], purge enabled, RTL support, custom colors: lavenderGradient from #C8A2C8 to #EBDDF9, gold #D4AF37, etc.), PostCSS config, npm scripts (build/watch).
- `theme.json`: Palette (primary: #C8A2C8, accent: #D4AF37), typography (fontFamilies: {fa: "Doran", en: "Crimson Pro"}), radii (custom: "rounded-3xl": "1.5rem"), shadows (soft: "0 4px 20px rgba(200,162,200,0.1)").
- Font hooks: Preload self-hosted Doran (fa.woff2) and Crimson Pro (local fallback); `font-display: swap`.
- `.gitignore`: node_modules/, dist/, .env, uploads/, vendor/. WP-standard ignores.
- package.json: Dependencies (tailwindcss, postcss, autoprefixer); scripts: "build": "tailwindcss -i ./assets/styles/input.css -o ./assets/styles/style.css --postcss".

**Acceptance**
- Site loads blank but styled (Tailwind classes work); no PHP errors.
- CSS/JS sizes: Critical CSS <10KB, fonts loaded fast (no FOIT).
- ACF recognizes acf-json/ (test: Create dummy field group, saves as JSON).
- Lighthouse base ≥85 (mobile); Git commit: `chore(init): setup theme skeleton and tooling`.

**Pitfalls**
- Tailwind over-purging (test classes in templates); missing wp_body_open hook; CDN blocks (use local fonts).
- npm issues in Laragon: Ensure Node 20+ installed.

---

## Step 1 — Header (Desktop/Mobile) + Slide-in Menu + Predictive Search
**Goal:** RTL-aware header: Logo | Predictive search (wide on desktop, full-width mobile) | Menu (4 items: Home, Shop, Services, Courses) + Account icon. Mobile: Hamburger → right slide-in (lavender backdrop, fade-in, ESC/close overlay, focus trap, body scroll lock).

**Expected**
- PHP partial in components/header.php: `wp_nav_menu('primary')` (register menu location in functions.php).
- JS: Vanilla for toggle (ARIA: role="dialog", aria-expanded; focus trap with tabbing).
- Search: Input with debounce (200ms), fetch to REST endpoint `/wp-json/gn/v1/search` (nonce-protected, returns JSON: {products: [...], courses: [...]} with highlighted titles via server-side regex).
- Results: Dropdown with ≤5 per category, ellipsis long titles, keyboard nav (↑/↓ select, Enter go, Esc close). Min length 1, cache transients.
- Conditional: Enqueue search JS only on home/header.

**Acceptance**
- Slide-in smooth (300ms transition, no CLS); search yields results on 1 char (e.g., "گل" → products); TBT <80ms.
- A11y: Screen reader announces results; no focus leaks. Query Monitor: No slow queries.
- Git: `feat(header): implement slide-in menu and predictive search with debounce`.

**Pitfalls**
- Focus trap bugs on mobile; excessive API calls (add rate-limit); RTL search input flip.

---

## Step 2 — Full-width Hero with Soft Parallax + Primary CTA
**Goal:** Hero section: Full-bleed background image (admin-uploadable via customizer/ACF), ultra-soft parallax on scroll (subtle Y-shift), heading "گلی‌ناب: هنر گلسازی پارچه‌ای لوکس" + subtext + CTA button "شروع سفارش" (links to cat1).

**Expected**
- In templates/hero.php: Background via inline style (esc_url), parallax with IntersectionObserver + CSS transform (translateY(-10% to 0)).
- Respect reduced-motion: Media query disable transform.
- Responsive: Mobile no parallax (static for perf); CTA rounded-full, gold accent.

**Acceptance**
- Parallax fluid (60fps); AA contrast on text/CTA; CLS=0 (reserve height 100vh).
- No jank on scroll; mobile LCP <2s.
- Git: `feat(hero): add parallax section with gentle motion and CTA`.

**Pitfalls**
- Image heavy (optimize with WP WebP); parallax on low-end devices (fallback to static).

---

## Step 3 — Category Cards (4-up Desktop / 2x2 Mobile) + Buy/Rent Split for cat1
**Goal:** Grid of 4 cards (cat1: Collectibles, cat2: Supplies, cat3: Laser Services, cat4: Courses) with PNG icons (admin-replaceable), titles, short desc. cat1 click: Morph to overlay popup splitting Buy (auction/pre-order) vs Rent (form link), reversible animation (fade + scale).

**Expected**
- Responsive grid (CSS Grid/Tailwind: 4-col desktop, 2-col mobile), rounded-3xl cards, soft shadows.
- Interaction: JS for popup (modal with ARIA, ESC close, keyboard nav); links to respective pages/CPTs.
- Icons: ACF media field for each cat (default placeholders).

**Acceptance**
- Touch/click responsive; popup accessible (focus first element); AA contrast.
- No overlap on RTL; mobile stack clean.
- Git: `feat(categories): add responsive cards with cat1 buy/rent popup`.

**Pitfalls**
- Animation conflicts (use unique classes); icon sizing inconsistencies.

---

## Step 4 — Dual Sliders (Embla): “New Products” & “Popular Courses”
**Goal:** Two side-by-side sliders on home: Left "محصولات جدید" (Woo featured products ≤6, cat1/cat2), Right "دوره‌های محبوب" (CPT courses ≤6). Auto-play, faded edges, single-line titles (ellipsis), touch/keyboard nav.

**Expected**
- Embla init in JS (enqueue only on home): Options { loop: false, speed: 10, align: 'start' }.
- Cards: Minimal (image, title, price/duration; link to detail).
- Query: WP_Query for featured (gn_featured=true).

**Acceptance**
- FPS stable (>50); Embla not loaded off-home; swipe works on mobile.
- Lazy images; no jank on auto-play.
- Git: `feat(sliders): integrate Embla for products and courses with auto-play`.

**Pitfalls**
- Embla RTL issues (set dir='rtl'); query N+1 (use pre_get_posts).

---

## Step 5 — Footer with Faded Top Border + Base Meta
**Goal:** Footer: Gradient fade from lavender to transparent top border; sections: Links (Terms/Privacy/Contact via menu), social icons, copyright. Add base meta: Favicon (admin upload), OG tags (title/desc/image via wp_head), manifest.json for PWA basics.

**Expected**
- components/footer.php: `wp_nav_menu('footer')`, faded border CSS (linear-gradient).
- Meta: Hooks in functions.php for og:site_name etc. (use Rank Math for advanced).

**Acceptance**
- Visual merge with body (no hard line); links accessible; meta validates in source (Facebook debugger).
- Mobile footer sticky if short content.
- Git: `feat(footer): add faded design with meta and links`.

**Pitfalls**
- OG image sizing (1200x630); footer height on mobile.

---

## Step 6 — Lightweight English Path `/en`
**Goal:** Detect /en → LTR dir, English font (Crimson Pro), show ONLY courses (hide cat1-3 via CSS/conditional queries). Header switcher button (toggle lang, set cookie, redirect).

**Expected**
- In functions.php: Hook to template_redirect; add body class 'en-site'; switch font enqueue.
- Templates: Conditional (if en: query_posts('post_type=course'); hide sections with display:none or if(!en)).
- SEO: Add hreflang links in wp_head; separate sitemap (custom endpoint later).

**Acceptance**
- Switcher works (redirect /en/home → LTR courses only); no RTL bleed in EN.
- Content translated? (Manual for MVP); clean meta.
- Git: `feat(multilingual): implement /en path with LTR and courses-only`.

**Pitfalls**
- Cookie privacy (GDPR note?); query filters breaking pagination.

---

## Step 7 — WooCommerce Minimal + MU-Plugin Integration
**Goal:** Minimal Woo: Strip assets off non-shop (use gn_is_shop_context()); disable tracking/cron via MU-plugin; HPOS ready; respect existing MU-plugins (no re-enable).

**Expected**
- inc/woocommerce.php: Helper function gn_is_shop_context() (check pages/shortcodes/blocks).
- Hooks: wp_enqueue_scripts – if !context, wp_dequeue_style/script for wc-*. For mini-cart: Custom REST if fragments off.
- MU-plugin (in wp-content/mu-plugins/gn-woo-minimal.php): Remove_action for tracking, disable cron jobs.
- Auctions: Install Woo Auctions; set for cat1 products.

**Acceptance**
- Non-shop pages: No Woo JS/CSS (check network tab); speed boost (Lighthouse +5 perf).
- Query Monitor: No warnings (l10n/deps); mini-cart updates via AJAX.
- Git: `fix(woo): minimal integration with conditional assets and MU-plugin`.

**Pitfalls**
- Shortcode detection misses (test has_shortcode); HPOS migration errors (backup first).

---

## Step 8 — ACF Field Groups & Content Types
**Goal:** Register CPTs (Course, Laser_Service, Rental_Request) via inc/cpt.php. Define ACF groups (JSON) for Products/Woo, Courses, Laser, Rentals as per .cursorrules.

**Expected**
- CPT args: Public=true, supports=title/editor/thumbnail, rewrite slug (e.g., 'course').
- ACF: Field groups in admin (e.g., for Laser: file upload with validation hook). Sync to acf-json/.
- For cat3 cost: JS calculator on frontend (outline * count * factor).

**Acceptance**
- CPTs appear in admin; fields save/fetch correctly (no XSS via esc).
- JSON committed; admin UI clean (no errors).
- Git: `chore(acf): define field groups and CPTs with JSON sync`.

**Pitfalls**
- ACF Pro license (use free if possible); upload validation fails on MIME.

---

## Step 9 — Rental Request Flow + Terms & Deposit Page
**Goal:** For cat1 Rent: Custom page (templates/page-rental.php) with form (CF7 or custom: dates, deposit checkbox, calc total). Submit → Create Rental_Request CPT, email summary. Terms page with legal text.

**Expected**
- Form: Nonce, validation (end >= start, deposit ack), AJAX submit to wp_ajax_gn_rental_request.
- Calc: Simple JS (deposit = 20% value). Store as post, notify admin.

**Acceptance**
- Form validates/errors clean; spam-resistant (honeypot or reCAPTCHA if needed).
- Email sends (test wp_mail); no direct SQL.
- Git: `feat(rental): implement MVP request form with calculation and CPT`.

**Pitfalls**
- Date picker RTL (use flatpickr if native bad); ZarinPal integrate for deposit later.

---

## Step 10 — Final Optimization & Audits
**Goal:** Implement lazy-load (wp 5.5+ native), defer all JS, final Tailwind purge, trim unused code. Audits: A11y (WAVE tool), SEO (screaming frog), perf (Lighthouse/PageSpeed).

**Expected**
- .htaccess: Cache headers, WebP rewrite.
- JS: Async/defer all; remove console.logs.
- Gamification basics: Add progress bar CSS/JS for courses.

**Acceptance**
- Lighthouse: Perf ≥90, A11y ≥95, Best ≥95, SEO ≥95 (mobile 4G).
- No errors; budgets met; full home page complete (short scroll, all sections).
- Git: `perf(optimize): final audits and lazy-loading tweaks`.

**Pitfalls**
- Purge removes needed classes (test all pages); audit false positives.

---

## Phase 2 (Post-MVP, After Home Complete)
- Woo Bookings for true rentals (calendars/deposits).
- LMS (Tutor LMS free) for courses with full gamification/certificates.
- PWA: Manifest + service worker (cache strategies, offline courses).
- Advanced: Vector design tool (SVG editor JS), laser cost API.

---
### Commit Policy (Conventional Commits)
Examples:
- `feat(header): add RTL slide-in menu with focus trap and ARIA`
- `perf(search): implement debounce and cache for under 2KB payload`
- `fix(woo): dequeue unnecessary blocks on non-shop pages`
- `chore(acf): sync laser service fields to acf-json`
- `refactor(sliders): extract Embla config to module for reusability`