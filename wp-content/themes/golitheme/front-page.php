<?php
/**
 * Front Page Template
 *
 * Ensures the hero section is rendered on the site's home page.
 *
 * @package GoliNaab
 * @since 1.0.0
 */

get_header(); ?>

<main id="main" class="gn-main">
    <?php
    // Hero section
    get_template_part('templates/hero');
    ?>

    <!-- Categories section placeholder (Step 3 will populate) -->
    <section id="categories" class="gn-categories-section">
        <div class="gn-container">
            <h2 class="gn-section-title">دسته‌بندی‌ها</h2>
            <p class="gn-section-subtitle">انتخاب کنید که چه چیزی می‌خواهید</p>
        </div>
    </section>

    <!-- Sliders section placeholder (Step 4 will populate) -->
    <section class="gn-sliders-section">
        <div class="gn-container">
        </div>
    </section>
</main>

<?php get_footer(); ?>


