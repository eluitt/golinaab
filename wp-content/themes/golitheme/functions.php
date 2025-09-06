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
    
    // Localize script for AJAX
    wp_localize_script('gn-script', 'gn_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('gn_ajax_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'gn_enqueue_assets');

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
