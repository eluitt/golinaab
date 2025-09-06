<?php
// File: wp-content/mu-plugins/gfx/gfx/gfx-plugins/40-woocommerce-lite.php
if ( ! defined('ABSPATH') ) {
    exit;
}

add_action('plugins_loaded', function(){
    if ( ! class_exists('WooCommerce') ) {
        return;
    }

    // 1) فقط dequeue کردن CSS/JS فرانت‌ساید در صفحات غیر ووکامرس
    if ( ! is_admin() ) {
        add_action('wp_enqueue_scripts', function(){
            if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() && ! is_account_page() ) {
                $scripts = [
                    'wc-add-to-cart', 'wc-cart-fragments',
                    'woocommerce',    'wc-checkout',
                    'wc-cart',        'wc-single-product',
                ];
                foreach ( $scripts as $script ) {
                    wp_dequeue_script($script);
                    wp_deregister_script($script);
                }
                wp_dequeue_style('woocommerce-general');
                wp_dequeue_style('woocommerce-layout');
                wp_dequeue_style('woocommerce-smallscreen');
            }
        }, 99);
    }

    // 2) غیرفعال‌سازی تله‌متری
    add_filter('woocommerce_allow_tracking', '__return_false', 999);
});
