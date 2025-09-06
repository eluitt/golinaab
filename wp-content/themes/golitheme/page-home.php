<?php
/**
 * Home Page Template
 * 
 * @package GoliNaab
 * @since 1.0.0
 */

get_header(); ?>

<main id="main" class="gn-main">
    <?php
    // Include hero section
    get_template_part('templates/hero');
    ?>
    
    <!-- Categories section will be added in Step 3 -->
    <section id="categories" class="gn-categories-section">
        <div class="gn-container">
            <h2 class="gn-section-title">دسته‌بندی‌ها</h2>
            <p class="gn-section-subtitle">انتخاب کنید که چه چیزی می‌خواهید</p>
            <!-- Category cards will be added in Step 3 -->
        </div>
    </section>
    
    <!-- Sliders section will be added in Step 4 -->
    <section class="gn-sliders-section">
        <div class="gn-container">
            <!-- New Products and Popular Courses sliders will be added in Step 4 -->
        </div>
    </section>
</main>

<?php get_footer(); ?>
