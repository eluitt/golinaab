<?php
/**
 * AJAX handlers (Rental Request)
 * @package GoliNaab
 */

if (!defined('ABSPATH')) { exit; }

function gn_handle_rental_request() {
    // Nonce
    $nonce = isset($_POST['nonce']) ? sanitize_text_field($_POST['nonce']) : '';
    if (!wp_verify_nonce($nonce, 'gn_rental')) {
        wp_send_json(array('success' => false, 'message' => __('اعتبارسنجی امنیتی نامعتبر است.', 'golitheme')));
    }
    // Capability rate-limit (public form): simple transient by IP
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $key = 'gn_rental_rate_' . md5($ip);
    if (get_transient($key)) {
        wp_send_json(array('success' => false, 'message' => __('لطفاً چند ثانیه بعد دوباره تلاش کنید.', 'golitheme')));
    }
    set_transient($key, 1, 30); // 30s

    $start = isset($_POST['start_date']) ? sanitize_text_field($_POST['start_date']) : '';
    $end   = isset($_POST['end_date']) ? sanitize_text_field($_POST['end_date']) : '';
    $phone = isset($_POST['phone']) ? sanitize_text_field($_POST['phone']) : '';
    $notes = isset($_POST['notes']) ? wp_kses_post($_POST['notes']) : '';
    $ack   = isset($_POST['deposit_ack']) ? (int) $_POST['deposit_ack'] : 0;

    if (empty($start) || empty($end) || empty($phone) || !$ack) {
        wp_send_json(array('success' => false, 'message' => __('فیلدهای ضروری ناقص است.', 'golitheme')));
    }
    if (strtotime($end) < strtotime($start)) {
        wp_send_json(array('success' => false, 'message' => __('تاریخ پایان نامعتبر است.', 'golitheme')));
    }

    // Create post
    $post_id = wp_insert_post(array(
        'post_type'   => 'rental_request',
        'post_status' => 'publish',
        'post_title'  => sprintf(__('Rental %s → %s', 'golitheme'), $start, $end),
        'post_content'=> '',
    ));
    if (is_wp_error($post_id)) {
        wp_send_json(array('success' => false, 'message' => __('خطا در ثبت درخواست.', 'golitheme')));
    }

    // Save meta (via ACF if installed or plain meta)
    update_post_meta($post_id, 'gn_start_date', $start);
    update_post_meta($post_id, 'gn_end_date', $end);
    update_post_meta($post_id, 'gn_phone', $phone);
    update_post_meta($post_id, 'gn_notes', $notes);
    update_post_meta($post_id, 'gn_deposit_ack', $ack);

    // Email admin (basic)
    $to = get_option('admin_email');
    $subject = __('New Rental Request', 'golitheme');
    $body = sprintf("Start: %s\nEnd: %s\nPhone: %s\nNotes: %s\n", $start, $end, $phone, wp_strip_all_tags($notes));
    wp_mail($to, $subject, $body);

    wp_send_json(array('success' => true));
}
add_action('wp_ajax_gn_rental_request', 'gn_handle_rental_request');
add_action('wp_ajax_nopriv_gn_rental_request', 'gn_handle_rental_request');
