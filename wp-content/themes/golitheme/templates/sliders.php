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

$courses = new WP_Query(array(
    'post_type'      => 'course',
    'post_status'    => 'publish',
    'posts_per_page' => 6,
));

function gn_render_card($post_id, $context = 'product') {
    $title = get_the_title($post_id);
    $url   = get_permalink($post_id);
    $image = get_the_post_thumbnail_url($post_id, 'medium');
    $image = $image ? $image : get_template_directory_uri() . '/assets/images/placeholder.png';

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
            <div class="gn-slider-group">
                <div class="gn-slider-header">
                    <h2 class="gn-section-title">محصولات جدید</h2>
                </div>
                <div class="gn-embla" data-embla='{"loop":false,"speed":10,"align":"start"}'>
                    <div class="gn-embla__viewport">
                        <div class="gn-embla__container">
                            <?php if ($products->have_posts()) : while ($products->have_posts()) : $products->the_post(); ?>
                                <?php gn_render_card(get_the_ID(), 'product'); ?>
                            <?php endwhile; wp_reset_postdata(); else: ?>
                                <div class="gn-embla__empty"><?php _e('موردی یافت نشد.', 'golitheme'); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <button class="gn-embla__prev" aria-label="<?php esc_attr_e('قبلی', 'golitheme'); ?>">&lsaquo;</button>
                    <button class="gn-embla__next" aria-label="<?php esc_attr_e('بعدی', 'golitheme'); ?>">&rsaquo;</button>
                    <div class="gn-embla__edges" aria-hidden="true"></div>
                </div>
            </div>

            <!-- Popular Courses -->
            <div class="gn-slider-group">
                <div class="gn-slider-header">
                    <h2 class="gn-section-title">دوره‌های محبوب</h2>
                </div>
                <div class="gn-embla" data-embla='{"loop":false,"speed":10,"align":"start"}'>
                    <div class="gn-embla__viewport">
                        <div class="gn-embla__container">
                            <?php if ($courses->have_posts()) : while ($courses->have_posts()) : $courses->the_post(); ?>
                                <?php gn_render_card(get_the_ID(), 'course'); ?>
                            <?php endwhile; wp_reset_postdata(); else: ?>
                                <div class="gn-embla__empty"><?php _e('موردی یافت نشد.', 'golitheme'); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <button class="gn-embla__prev" aria-label="<?php esc_attr_e('قبلی', 'golitheme'); ?>">&lsaquo;</button>
                    <button class="gn-embla__next" aria-label="<?php esc_attr_e('بعدی', 'golitheme'); ?>">&rsaquo;</button>
                    <div class="gn-embla__edges" aria-hidden="true"></div>
                </div>
            </div>
        </div>
    </div>
</section>


