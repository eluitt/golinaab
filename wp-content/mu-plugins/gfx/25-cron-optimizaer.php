<?php
// File: wp-content/mu-plugins/gfx/gfx/gfx-plugins/25-cron-optimizer.php
if ( ! defined('ABSPATH') ) {
    exit;
}

// 1) اگر نیاز به custom schedule دارید، اینجاست
add_filter('cron_schedules', function ( $schedules ) {
    return $schedules;
});

// 2) کم‌کردن فشار روی WP-Cron در ساعات اوج (Peak Hours)
add_filter('cron_request', function ( $req ) {
    $h = (int) date('H');

    // مقادیر پیش‌فرض وِیراستپذیر با این فیلترها
    $peak_start   = apply_filters('gfx_cron_peak_start_h', 9);
    $peak_end     = apply_filters('gfx_cron_peak_end_h', 18);
    $min_interval = apply_filters('gfx_cron_peak_interval_s', 120);

    if ( $h >= $peak_start && $h <= $peak_end ) {
        static $last_peak = 0;
        if ( ( time() - $last_peak ) < $min_interval ) {
            // لغو درخواست جدید تا فاصله‌ی min_interval سپری شود
            return false;
        }
        $last_peak = time();
    }

    // کاهش timeout در حالت local/dev
    $req['args']['timeout'] = 5;
    return $req;
});
