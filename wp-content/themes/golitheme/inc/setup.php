<?php
/**
 * Theme setup and configuration
 * 
 * @package GoliNaab
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Set content width
 */
if (!isset($content_width)) {
    $content_width = 1200;
}

/**
 * Add ACF JSON support
 */
function gn_acf_json_save_point($path) {
    return GN_THEME_PATH . '/acf-json';
}
add_filter('acf/settings/save_json', 'gn_acf_json_save_point');

function gn_acf_json_load_point($paths) {
    unset($paths[0]);
    $paths[] = GN_THEME_PATH . '/acf-json';
    return $paths;
}
add_filter('acf/settings/load_json', 'gn_acf_json_load_point');

/**
 * Add body classes for language detection
 */
function gn_body_classes($classes) {
    // Check if we're on English path
    if (strpos($_SERVER['REQUEST_URI'], '/en') === 0) {
        $classes[] = 'en-site';
        $classes[] = 'ltr';
    } else {
        $classes[] = 'fa-site';
        $classes[] = 'rtl';
    }
    
    return $classes;
}
add_filter('body_class', 'gn_body_classes');

/**
 * Add theme support for customizer
 */
function gn_customize_register($wp_customize) {
    // Add hero section
    $wp_customize->add_section('gn_hero', array(
        'title'    => __('Hero Section', 'golitheme'),
        'priority' => 30,
    ));
    
    $wp_customize->add_setting('gn_hero_background', array(
        'default'           => '',
        'sanitize_callback' => 'esc_url_raw',
    ));
    
    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, 'gn_hero_background', array(
        'label'    => __('Hero Background Image', 'golitheme'),
        'section'  => 'gn_hero',
        'settings' => 'gn_hero_background',
    )));
}
add_action('customize_register', 'gn_customize_register');
