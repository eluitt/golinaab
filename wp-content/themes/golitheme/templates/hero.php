<?php
/**
 * Hero Section Template
 * 
 * @package GoliNaab
 * @since 1.0.0
 */

// Get hero background from customizer
$hero_background = get_theme_mod('gn_hero_background', '');
$hero_title = get_theme_mod('gn_hero_title', 'گلی‌ناب: هنر گلسازی پارچه‌ای لوکس');
$hero_subtitle = get_theme_mod('gn_hero_subtitle', 'مجموعه‌ای منحصر به فرد از گل‌های پارچه‌ای دست‌ساز با کیفیت بالا و طراحی‌های خلاقانه');
$hero_cta_text = get_theme_mod('gn_hero_cta_text', 'شروع سفارش');
$hero_cta_url = get_theme_mod('gn_hero_cta_url', '#categories');

// Default background if none set
if (empty($hero_background)) {
    $hero_background = get_template_directory_uri() . '/assets/images/flower.png';
}
?>

<section class="gn-hero" id="hero">
    <div class="gn-hero-background" 
         style="background-image: url('<?php echo esc_url($hero_background); ?>');"
         data-parallax="true">
    </div>
    
    <div class="gn-hero-overlay"></div>
    
    <div class="gn-hero-content">
        <div class="gn-container">
            <div class="gn-hero-text">
                <h1 class="gn-hero-title">
                    <?php echo esc_html($hero_title); ?>
                </h1>
                
                <p class="gn-hero-subtitle">
                    <?php echo esc_html($hero_subtitle); ?>
                </p>
                
                <div class="gn-hero-actions">
                    <a href="<?php echo esc_url($hero_cta_url); ?>" 
                       class="gn-btn gn-btn-primary gn-hero-cta">
                        <?php echo esc_html($hero_cta_text); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scroll indicator -->
    <div class="gn-hero-scroll-indicator">
        <div class="gn-scroll-arrow">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M7 13l3 3 3-3"></path>
                <path d="M7 6l3 3 3-3"></path>
            </svg>
        </div>
    </div>
</section>
