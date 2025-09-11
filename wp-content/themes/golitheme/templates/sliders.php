<?php
/**
 * Home sliders: New Products (featured) & Popular Courses
 *
 * @package GoliNaab
 * @since 1.0.0
 */

$products = new WP_Query(array(
    'post_type'      => 'product',
    'post_status'    => 'publish',
    'posts_per_page' => 6,
    'meta_query'     => array(
        array(
            'key'     => 'gn_featured',
            'value'   => true,
            'compare' => '='
        ),
    ),
));

// Fallback: if no ACF-featured products found, show latest published products
if (!$products->have_posts()) {
    $products = new WP_Query(array(
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 6,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ));
}

$courses = new WP_Query(array(
    'post_type'      => 'course',
    'post_status'    => 'publish',
    'posts_per_page' => 6,
));

function gn_render_card($post_id, $context = 'product') {
    $title = get_the_title($post_id);
    $url   = get_permalink($post_id);
    // Prefer Woo image size when available
    $image = get_the_post_thumbnail_url($post_id, 'woocommerce_thumbnail');
    // WooCommerce placeholder
    if (!$image && function_exists('wc_placeholder_img_src')) {
        $image = wc_placeholder_img_src();
    }
    // Final inline SVG fallback (lavender gradient)
    if (!$image) {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="400" viewBox="0 0 600 400"><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#C8A2C8"/><stop offset="100%" stop-color="#EBDDF9"/></linearGradient></defs><rect width="600" height="400" fill="url(#g)"/></svg>';
        $image = 'data:image/svg+xml;utf8,' . rawurlencode($svg);
    }

    $meta_str = '';
    if ($context === 'product' && function_exists('wc_get_product')) {
        $product = wc_get_product($post_id);
        if ($product) {
            $meta_str = $product->get_price_html();
        }
    } elseif ($context === 'course') {
        $duration = function_exists('get_field') ? get_field('gn_duration', $post_id) : '';
        if (!empty($duration)) {
            $meta_str = esc_html($duration);
        }
    }
    ?>
    <div class="gn-embla__slide">
        <a class="gn-card gn-embla__card" href="<?php echo esc_url($url); ?>">
            <div class="gn-embla__image-wrap">
                <img class="gn-embla__image" src="<?php echo esc_url($image); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy" decoding="async" />
            </div>
            <div class="gn-embla__content">
                <h3 class="gn-embla__title" title="<?php echo esc_attr($title); ?>"><?php echo esc_html($title); ?></h3>
                <?php if (!empty($meta_str)) : ?>
                    <span class="gn-embla__meta"><?php echo wp_kses_post($meta_str); ?></span>
                <?php endif; ?>
            </div>
        </a>
    </div>
    <?php
}
?>

<section class="gn-sliders-section" dir="rtl">
    <div class="gn-container">
        <div class="gn-sliders-grid">
            <!-- New Products -->
            <?php if (!function_exists('gn_is_en_site') || !gn_is_en_site()) : ?>
            <div class="gn-slider-group gn-section-products">
                <div class="gn-slider-header">
                    <h2 class="gn-section-title">محصولات جدید</h2>
                </div>
                <div class="gn-embla" data-embla='{"loop":true,"speed":8,"align":"start","autoplay":true,"respectReducedMotion":false}' aria-label="محصولات جدید">
                    <div class="gn-embla__viewport" aria-live="polite">
                        <div class="gn-embla__container">
                            <?php if ($products->have_posts()) : while ($products->have_posts()) : $products->the_post(); ?>
                                <?php gn_render_card(get_the_ID(), 'product'); ?>
                            <?php endwhile; wp_reset_postdata(); else: ?>
                                <div class="gn-embla__empty"><?php _e('موردی یافت نشد.', 'golitheme'); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <button class="gn-embla__prev" type="button" aria-label="<?php esc_attr_e('قبلی', 'golitheme'); ?>">
                      <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                        <path d="M8 4 L14 10 L8 16" fill="none" stroke="currentColor" stroke-width="2.75" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                    </button>
                    <button class="gn-embla__next" type="button" aria-label="<?php esc_attr_e('بعدی', 'golitheme'); ?>">
                      <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                        <path d="M12 4 L6 10 L12 16" fill="none" stroke="currentColor" stroke-width="2.75" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                    </button>
                    <div class="gn-embla__edges" aria-hidden="true"></div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Popular Courses -->
            <div class="gn-slider-group gn-section-courses">
                <div class="gn-slider-header">
                    <h2 class="gn-section-title">دوره‌های محبوب</h2>
                </div>
                <div class="gn-embla" data-embla='{"loop":true,"speed":8,"align":"start","autoplay":true,"respectReducedMotion":false}' aria-label="دوره‌های محبوب">
                    <div class="gn-embla__viewport" aria-live="polite">
                        <div class="gn-embla__container">
                            <?php if ($courses->have_posts()) : while ($courses->have_posts()) : $courses->the_post(); ?>
                                <?php gn_render_card(get_the_ID(), 'course'); ?>
                            <?php endwhile; wp_reset_postdata(); else: ?>
                                <div class="gn-embla__empty"><?php _e('موردی یافت نشد.', 'golitheme'); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <button class="gn-embla__prev" aria-label="<?php esc_attr_e('قبلی', 'golitheme'); ?>">
                      <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                        <path d="M8 4 L14 10 L8 16" fill="none" stroke="currentColor" stroke-width="2.75" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                    </button>
                    <button class="gn-embla__next" aria-label="<?php esc_attr_e('بعدی', 'golitheme'); ?>">
                      <svg width="20" height="20" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                        <path d="M12 4 L6 10 L12 16" fill="none" stroke="currentColor" stroke-width="2.75" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                    </button>
                    <div class="gn-embla__edges" aria-hidden="true"></div>
                </div>
            </div>
        </div>
    </div>
</section>


