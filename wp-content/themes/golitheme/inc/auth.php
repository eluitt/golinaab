<?php
defined('ABSPATH') || exit;

/**
 * Return custom account page URL (page slug: account), fallback to /account.
 */
function gn_get_account_page_url() {
    $page = get_page_by_path('account');
    if ($page instanceof WP_Post) {
        return get_permalink($page);
    }
    return home_url('/account');
}

/**
 * If WooCommerce account page is hit, redirect to our custom account page.
 * Prevent loop by skipping when already on our account page.
 */
function gn_redirect_woo_account_to_custom() {
    if (function_exists('is_account_page') && is_account_page()) {
        if (!is_page('account')) {
            wp_safe_redirect(gn_get_account_page_url());
            exit;
        }
    }
}
add_action('template_redirect', 'gn_redirect_woo_account_to_custom', 8);

/**
 * Protect sensitive pages for guests → redirect to /account with return_to.
 */
function gn_protect_sensitive_pages() {
    if (headers_sent() || is_user_logged_in()) {
        return;
    }
    $is_checkout = function_exists('is_checkout') && is_checkout();
    $is_woo_account = function_exists('is_account_page') && is_account_page();
    if ($is_checkout || $is_woo_account) {
        $request_uri = isset($_SERVER['REQUEST_URI']) ? wp_unslash($_SERVER['REQUEST_URI']) : '/';
        $return_to   = home_url($request_uri);
        $target      = add_query_arg('return_to', rawurlencode($return_to), gn_get_account_page_url());
        wp_safe_redirect($target);
        exit;
    }
}
add_action('template_redirect', 'gn_protect_sensitive_pages', 9);


