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

    <?php get_template_part('templates/categories'); ?>

    <!-- Sliders section placeholder (Step 4 will populate) -->
    <section class="gn-sliders-section">
        <div class="gn-container">
        </div>
    </section>
</main>

<?php get_footer(); ?>


