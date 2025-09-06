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

    <?php get_template_part('templates/sliders'); ?>
</main>

<?php get_footer(); ?>


