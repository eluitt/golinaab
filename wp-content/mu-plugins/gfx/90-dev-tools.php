<?php
// File: wp-content/mu-plugins/gfx/gfx/gfx-plugins/90-dev-tools.php
if ( ! defined('ABSPATH') ) {
    exit;
}

// فقط در محیط‌های development اجرا شود
if ( getenv('WP_ENVIRONMENT_TYPE') !== 'development' ) {
    return;
}

// مثال: میوت کردن jQuery Migrate، register کردن stub-script
add_action('wp_loaded', function(){
    foreach ( ['wp-dom-ready','wc-tracks'] as $handle ) {
        if ( ! wp_script_is( $handle, 'registered' ) ) {
            wp_register_script(
                $handle,
                plugin_dir_url( __DIR__ ) . '../js/stub-script.js',
                [],
                '1.0',
                true
            );
        }
    }
}, 1);
