<?php
/**
 * Custom Post Types (Courses, Laser Service, Rental Request)
 *
 * @package GoliNaab
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Register custom post types
 */
function gn_register_cpts() {
	// Course CPT
	register_post_type('course', array(
		'labels' => array(
			'name' => __('Courses', 'golitheme'),
			'singular_name' => __('Course', 'golitheme'),
			'add_new_item' => __('Add New Course', 'golitheme'),
			'edit_item' => __('Edit Course', 'golitheme'),
		),
		'public' => true,
		'has_archive' => true,
		'show_in_rest' => true,
		'menu_icon' => 'dashicons-welcome-learn-more',
		'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
		'rewrite' => array('slug' => 'course', 'with_front' => false),
	));

	// Laser Service CPT
	register_post_type('laser_service', array(
		'labels' => array(
			'name' => __('Laser Services', 'golitheme'),
			'singular_name' => __('Laser Service', 'golitheme'),
			'add_new_item' => __('Add New Laser Service', 'golitheme'),
			'edit_item' => __('Edit Laser Service', 'golitheme'),
		),
		'public' => true,
		'has_archive' => true,
		'show_in_rest' => true,
		'menu_icon' => 'dashicons-lightbulb',
		'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
		'rewrite' => array('slug' => 'laser-service', 'with_front' => false),
	));

	// Rental Request CPT
	register_post_type('rental_request', array(
		'labels' => array(
			'name' => __('Rental Requests', 'golitheme'),
			'singular_name' => __('Rental Request', 'golitheme'),
			'add_new_item' => __('Add New Rental Request', 'golitheme'),
			'edit_item' => __('Edit Rental Request', 'golitheme'),
		),
		'public' => true,
		'has_archive' => true,
		'show_in_rest' => true,
		'menu_icon' => 'dashicons-clipboard',
		'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
		'rewrite' => array('slug' => 'rental-request', 'with_front' => false),
	));
}
add_action('init', 'gn_register_cpts');
