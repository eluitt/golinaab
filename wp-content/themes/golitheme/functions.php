<?php
/**
 * GoliNaab Theme Functions
 * 
 * @package GoliNaab
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define theme constants
define('GN_THEME_VERSION', '1.0.0');
define('GN_THEME_PATH', get_template_directory());
define('GN_THEME_URL', get_template_directory_uri());

/**
 * Theme setup
 */
function gn_theme_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script'
    ));
    add_theme_support('custom-logo');
    add_theme_support('responsive-embeds');
    
    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'golitheme'),
        'footer' => __('Footer Menu', 'golitheme'),
        'social' => __('Social Links', 'golitheme'),
    ));
    
    // Add editor styles
    add_theme_support('editor-styles');
    add_editor_style('assets/styles/editor-style.css');
}
add_action('after_setup_theme', 'gn_theme_setup');

/**
 * Enqueue scripts and styles
 */
function gn_enqueue_assets() {
    // Main stylesheet (Tailwind CSS)
    wp_enqueue_style(
        'gn-style',
        GN_THEME_URL . '/assets/styles/style.css',
        array(),
        GN_THEME_VERSION
    );
    
    // Font preloading for Persian (Doran) and English (Crimson Pro)
    gn_preload_fonts();
    
    // Main theme script
    wp_enqueue_script(
        'gn-script',
        GN_THEME_URL . '/assets/scripts/main.js',
        array(),
        GN_THEME_VERSION,
        true
    );
    
    // Header functionality script (conditional)
    if (is_front_page() || is_home() || is_page()) {
        wp_enqueue_script(
            'gn-header-script',
            GN_THEME_URL . '/assets/scripts/header.js',
            array(),
            GN_THEME_VERSION,
            true
        );
        
        // Add nonce data for search
        wp_add_inline_script('gn-header-script', 'window.gn_ajax = ' . wp_json_encode(array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('gn_ajax_nonce'),
        )), 'before');
    }
    
    // Hero functionality script (conditional - only on home page)
    if (is_front_page() || is_home()) {
        wp_enqueue_script(
            'gn-hero-script',
            GN_THEME_URL . '/assets/scripts/hero.js',
            array(),
            GN_THEME_VERSION,
            true
        );
        // Categories modal script (Step 3)
        wp_enqueue_script(
            'gn-categories-script',
            GN_THEME_URL . '/assets/scripts/categories.js',
            array(),
            GN_THEME_VERSION,
            true
        );
        // Embla vendor and sliders (home only)
        wp_enqueue_script(
            'embla-carousel',
            GN_THEME_URL . '/assets/scripts/vendor/embla-carousel.min.js',
            array(),
            '7.1.0',
            true
        );
        wp_enqueue_script(
            'gn-sliders-script',
            GN_THEME_URL . '/assets/scripts/sliders.js',
            array('embla-carousel'),
            GN_THEME_VERSION,
            true
        );
    }
    
    // Localize script for AJAX
    wp_localize_script('gn-script', 'gn_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('gn_ajax_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'gn_enqueue_assets');

/**
 * Output base meta tags (OG, theme color, manifest)
 */
function gn_output_meta_tags() {
    $title       = is_singular() ? single_post_title('', false) : get_bloginfo('name');
    $description = is_singular() ? wp_strip_all_tags(get_the_excerpt(null)) : get_bloginfo('description');
    $url         = esc_url(home_url(add_query_arg(array(), $_SERVER['REQUEST_URI'] ?? '')));
    $image       = '';
    if (is_singular() && has_post_thumbnail()) {
        $image = esc_url(get_the_post_thumbnail_url(get_queried_object_id(), 'large'));
    } elseif (function_exists('has_site_icon') && has_site_icon()) {
        $image = esc_url(get_site_icon_url(512));
    }
    $theme_color = '#C8A2C8';
    echo "\n<!-- GoliNaab base meta -->\n";
    echo '<meta name="theme-color" content="' . esc_attr($theme_color) . '">' . "\n";
    echo '<link rel="manifest" href="' . esc_url(GN_THEME_URL . '/assets/manifest.json') . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    if (!empty($description)) echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_attr($url) . '">' . "\n";
    if (!empty($image)) echo '<meta property="og:image" content="' . esc_attr($image) . '">' . "\n";
    echo "<!-- /GoliNaab base meta -->\n";
}
add_action('wp_head', 'gn_output_meta_tags', 5);

/**
 * Preload critical fonts
 */
function gn_preload_fonts() {
    // Preload Persian font (Doran)
    echo '<link rel="preload" href="' . GN_THEME_URL . '/assets/fonts/doran-regular.woff2" as="font" type="font/woff2" crossorigin>';
    
    // Preload English font (Crimson Pro) - local fallback
    echo '<link rel="preload" href="' . GN_THEME_URL . '/assets/fonts/crimson-pro-regular.woff2" as="font" type="font/woff2" crossorigin>';
}

/**
 * Include required files
 */
require_once GN_THEME_PATH . '/inc/setup.php';
require_once GN_THEME_PATH . '/inc/search.php';
