<?php
/**
 * Search functionality
 * 
 * @package GoliNaab
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register search REST endpoint
 */
function gn_register_search_endpoint() {
    register_rest_route('gn/v1', '/search', array(
        'methods' => 'GET',
        'callback' => 'gn_handle_search_request',
        'permission_callback' => '__return_true',
        'args' => array(
            'q' => array(
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field',
            ),
            'nonce' => array(
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field',
            ),
        ),
    ));
}
add_action('rest_api_init', 'gn_register_search_endpoint');

/**
 * Handle search request
 */
function gn_handle_search_request($request) {
    $query = $request->get_param('q');
    $nonce = $request->get_param('nonce');
    
    // Verify nonce
    if (!wp_verify_nonce($nonce, 'gn_ajax_nonce')) {
        return new WP_Error('invalid_nonce', __('Invalid nonce', 'golitheme'), array('status' => 403));
    }
    
    // Rate limiting
    $user_ip = $_SERVER['REMOTE_ADDR'];
    $cache_key = 'gn_search_' . md5($user_ip);
    $last_search = get_transient($cache_key);
    
    if ($last_search && (time() - $last_search) < 1) {
        return new WP_Error('rate_limited', __('Too many requests', 'golitheme'), array('status' => 429));
    }
    
    set_transient($cache_key, time(), 30);
    
    if (strlen($query) < 1) {
        return new WP_REST_Response(array(
            'products' => array(),
            'courses' => array(),
        ));
    }
    
    // Search products
    $products = gn_search_products($query);
    
    // Search courses
    $courses = gn_search_courses($query);
    
    // Cache results for 5 minutes
    $cache_key_results = 'gn_search_results_' . md5($query);
    set_transient($cache_key_results, array(
        'products' => $products,
        'courses' => $courses,
    ), 300);
    
    return new WP_REST_Response(array(
        'products' => $products,
        'courses' => $courses,
    ));
}

/**
 * Search products
 */
function gn_search_products($query) {
    $args = array(
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => 5,
        'meta_query' => array(
            'relation' => 'OR',
            array(
                'key' => 'gn_featured',
                'value' => true,
                'compare' => '='
            )
        ),
        's' => $query,
    );
    
    $products_query = new WP_Query($args);
    $products = array();
    
    if ($products_query->have_posts()) {
        while ($products_query->have_posts()) {
            $products_query->the_post();
            global $product;
            
            $products[] = array(
                'id' => get_the_ID(),
                'title' => gn_highlight_search_term(get_the_title(), $query),
                'url' => get_permalink(),
                'price' => $product ? $product->get_price_html() : '',
                'image' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'),
                'excerpt' => gn_highlight_search_term(get_the_excerpt(), $query),
            );
        }
        wp_reset_postdata();
    }
    
    return $products;
}

/**
 * Search courses
 */
function gn_search_courses($query) {
    $args = array(
        'post_type' => 'course',
        'post_status' => 'publish',
        'posts_per_page' => 5,
        's' => $query,
    );
    
    $courses_query = new WP_Query($args);
    $courses = array();
    
    if ($courses_query->have_posts()) {
        while ($courses_query->have_posts()) {
            $courses_query->the_post();
            
            $courses[] = array(
                'id' => get_the_ID(),
                'title' => gn_highlight_search_term(get_the_title(), $query),
                'url' => get_permalink(),
                'duration' => get_field('gn_duration') ?: '',
                'image' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail'),
                'excerpt' => gn_highlight_search_term(get_the_excerpt(), $query),
            );
        }
        wp_reset_postdata();
    }
    
    return $courses;
}

/**
 * Highlight search terms in text
 */
function gn_highlight_search_term($text, $term) {
    if (empty($term)) {
        return $text;
    }
    
    $highlighted = preg_replace(
        '/(' . preg_quote($term, '/') . ')/iu',
        '<mark>$1</mark>',
        $text
    );
    
    return $highlighted ?: $text;
}
