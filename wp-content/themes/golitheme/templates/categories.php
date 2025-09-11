<?php
/**
 * Categories grid with cat1 Buy/Rent modal
 *
 * @package GoliNaab
 * @since 1.0.0
 */

$urls = array(
    'cat1_buy'  => esc_url(site_url('/shop')),
    'cat1_rent' => esc_url(site_url('/rental-request')),
    'cat2'      => esc_url(site_url('/shop')),
    'cat3'      => esc_url(site_url('/laser-service')),
    'cat4'      => esc_url(site_url('/courses')),
);

// Helper: resolve category icon image from assets/images/categories with fallback to SVG
if (!function_exists('gn_resolve_category_icon')) {
    function gn_resolve_category_icon($basename) {
        $exts = array('webp', 'png', 'jpg', 'jpeg');
        $base_path = trailingslashit(get_template_directory()) . 'assets/images/categories/';
        $base_url  = trailingslashit(get_template_directory_uri()) . 'assets/images/categories/';
        foreach ($exts as $ext) {
            $path = $base_path . $basename . '.' . $ext;
            if (file_exists($path)) {
                return esc_url($base_url . $basename . '.' . $ext);
            }
        }
        return '';
    }
}
?>

<section id="categories" class="gn-categories-section">
    <div class="gn-container">
        

        <div class="gn-categories-grid">
            <?php if (!function_exists('gn_is_en_site') || !gn_is_en_site()) : ?>
            <!-- Cat1: Collectibles (Buy/Rent split) -->
            <button class="gn-category-card" data-gn-open="cat1-modal" aria-haspopup="dialog" aria-controls="gn-cat1-modal">
                <span class="gn-category-icon" aria-hidden="true">
                    <?php $img = gn_resolve_category_icon('collectibles'); if ($img) : ?>
                        <img src="<?php echo $img; ?>" alt="" loading="lazy" width="56" height="56" />
                    <?php else: ?>
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none"><path d="M12 2c2 3 2 5-1 6 3-1 5-1 6 1-1-3-1-5 2-6-3 1-5 1-6-1 1 3 1 5-1 6 2-1 4-1 5 1-1-2-3-2-5 0 2 0 4 2 4 4s-2 4-4 4-4-2-4-4 2-4 4-4c-2-2-4-2-5 0 1-2 3-2 5-1-2-1-2-3-1-6-1 2-3 2-6 1 3 1 3 3 2 6 1-2 3-2 6-1-3-1-3-3-1-6z" fill="#9A7B9A"/></svg>
                    <?php endif; ?>
                </span>
                <span class="gn-category-title">کالکشن‌های خاص (خرید/اجاره)</span>
                <span class="gn-category-desc">آثار کلکسیونی، محدود و خاص</span>
            </button>

            <!-- Cat2: Supplies -->
            <a class="gn-category-card" href="<?php echo $urls['cat2']; ?>">
                <span class="gn-category-icon" aria-hidden="true">
                    <?php $img = gn_resolve_category_icon('supplies'); if ($img) : ?>
                        <img src="<?php echo $img; ?>" alt="" loading="lazy" width="56" height="56" />
                    <?php else: ?>
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none"><path d="M4 6h16v2H4V6zm2 4h12v8H6v-8z" fill="#D4AF37"/></svg>
                    <?php endif; ?>
                </span>
                <span class="gn-category-title">ملزومات گلسازی</span>
                <span class="gn-category-desc">ابزار و متریال باکیفیت</span>
            </a>

            <!-- Cat3: Laser Services -->
            <a class="gn-category-card" href="<?php echo $urls['cat3']; ?>">
                <span class="gn-category-icon" aria-hidden="true">
                    <?php $img = gn_resolve_category_icon('laser'); if ($img) : ?>
                        <img src="<?php echo $img; ?>" alt="" loading="lazy" width="56" height="56" />
                    <?php else: ?>
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none"><path d="M3 12h18M12 3v18" stroke="#B08FB0" stroke-width="2"/></svg>
                    <?php endif; ?>
                </span>
                <span class="gn-category-title">خدمات لیزر</span>
                <span class="gn-category-desc">طراحی، وکتور و برش</span>
            </a>
            <?php endif; ?>
            <!-- Cat4: Courses -->
            <a class="gn-category-card" href="<?php echo $urls['cat4']; ?>">
                <span class="gn-category-icon" aria-hidden="true">
                    <?php $img = gn_resolve_category_icon('courses'); if ($img) : ?>
                        <img src="<?php echo $img; ?>" alt="" loading="lazy" width="56" height="56" />
                    <?php else: ?>
                        <svg width="36" height="36" viewBox="0 0 24 24" fill="none"><path d="M3 6l9-4 9 4-9 4-9-4zm0 6l9 4 9-4" stroke="#9A7B9A" stroke-width="2"/></svg>
                    <?php endif; ?>
                </span>
                <span class="gn-category-title">دوره‌های آموزشی</span>
                <span class="gn-category-desc">مینی تا جامع</span>
            </a>
        </div>
    </div>

    <!-- Modal: Cat1 Buy/Rent -->
    <div id="gn-cat1-modal" class="gn-modal" role="dialog" aria-modal="true" aria-labelledby="gn-cat1-title" aria-hidden="true">
        <div class="gn-modal__backdrop" data-gn-close></div>
        <div class="gn-modal__dialog" role="document">
            <button class="gn-modal__close" aria-label="بستن" data-gn-close>&times;</button>
            <h3 id="gn-cat1-title" class="gn-modal__title">انتخاب نوع سفارش</h3>
            <p class="gn-modal__desc">یکی از گزینه‌های زیر را انتخاب کنید:</p>
            <div class="gn-modal__actions">
                <a class="gn-btn gn-btn-primary" href="<?php echo $urls['cat1_buy']; ?>">خرید (حراج/پیش‌خرید)</a>
                <a class="gn-btn gn-btn-secondary" href="<?php echo $urls['cat1_rent']; ?>">اجاره (فرم درخواست)</a>
            </div>
        </div>
    </div>
</section>


