<?php
// File: wp-content/mu-plugins/gfx/gfx/gfx-plugins/30-admin-debloat.php
if ( ! defined('ABSPATH') ) {
    exit;
}

// 1) حذف ویجت‌های غیر ضروری داشبورد برای کاربران غیر‌مدیر
add_action('wp_dashboard_setup', function(){
    if ( current_user_can('manage_options') ) {
        return;
    }
    remove_meta_box('dashboard_primary',  'dashboard', 'side');
    remove_meta_box('dashboard_secondary','dashboard','side');
    remove_meta_box('dashboard_quick_press','dashboard','side');
    remove_meta_box('dashboard_recent_comments','dashboard','normal');
    // … ویجت‌های دیگر
});

// 2) Dequeue کردن اسکریپت/استایل‌های اضافی در admin برای غیر‌مدیران
add_action('admin_enqueue_scripts', function ( $hook ) {
    if ( current_user_can('manage_options') ) {
        return;
    }
    // استثنا: صفحه ووکامرس، گیمیپرس، تیوتور و …
    if ( strpos($hook,'woocommerce')!==false || strpos($hook,'tutor')!==false || strpos($hook,'gamipress')!==false ) {
        return;
    }
    foreach ( ['select2','select2-css','cmb2-scripts','cmb2-styles','wp-color-picker','iris'] as $h ) {
        wp_dequeue_style($h);
        wp_dequeue_script($h);
    }
});
