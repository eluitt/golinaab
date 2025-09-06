<?php
// File: wp-content/mu-plugins/gfx/gfx/gfx-plugins/20-network-offline-guard.php
if ( ! defined('ABSPATH') ) {
    exit;
}

add_filter('pre_http_request', function ( $pre, $args, $url ) {
    // دامنه‌های بلاک‌شده (قابل گسترش با فیلتر gfx_blocked_hosts)
    $blocked = apply_filters('gfx_blocked_hosts', [
        'stats.wp.com',
        'pixel.wp.com',
        'gamipress.com',
        // … موارد دیگر
    ], $args, $url);

    // دامنه‌های مجاز – whitelist (قابل گسترش با فیلتر gfx_allowed_hosts)
    $allowed = apply_filters('gfx_allowed_hosts', [
        'fonts.googleapis.com',
        'fonts.gstatic.com',
        'api.zarinpal.com',
        'pay.yekpay.com',
        'api.wordpress.org',
        'downloads.wordpress.org',
    ], $args, $url);

    // اگر URL شامل یکی از allow‌ها بود، هیچ‌چیز را بلاک نکن
    foreach ( $allowed as $host ) {
        if ( stripos( $url, $host ) !== false ) {
            return $pre;
        }
    }

    // اگر URL در لیست blocked بود، پاسخ ساختگی بده
    foreach ( $blocked as $host ) {
        if ( stripos( $url, $host ) !== false ) {
            return [
                'headers'  => [],
                'body'     => '',
                'response' => [ 'code' => 200, 'message' => 'OK (blocked by MU)' ],
                'cookies'  => [],
                'filename' => null,
            ];
        }
    }

    return $pre;
}, 10, 3);
