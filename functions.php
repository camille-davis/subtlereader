<?php
/**
 * Subtle Theme Functions
 *
 * @package Subtle
 */

// ============================================================================
// Theme Includes
// ============================================================================

require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/misc-block-customizations.php';

// ============================================================================
// Theme Setup
// ============================================================================

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * @return void
 */
function subtle_setup() {
	// Let WordPress manage the document title via wp_head().
	add_theme_support( 'title-tag' );

	// Add support for custom logo.
	add_theme_support( 'custom-logo' );

	// Add support for editor styles.
	add_theme_support( 'editor-styles' );
	add_editor_style( 'style.css' );
}
add_action( 'after_setup_theme', 'subtle_setup' );

// ============================================================================
// Navigation
// ============================================================================

/**
 * Registers navigation menu locations.
 *
 * @return void
 */
function subtle_register_menus() {
	register_nav_menus(
		array(
			'header' => __( 'Navigation', 'subtlereader' ),
		)
	);
}
add_action( 'init', 'subtle_register_menus' );

// ============================================================================
// Widget Areas
// ============================================================================

/**
 * Registers widget areas (sidebars) for the theme.
 *
 * @return void
 */
function subtle_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Footer', 'subtlereader' ),
			'id'            => 'footer',
			'before_widget' => '',
			'after_widget'  => '',
		)
	);
}
add_action( 'widgets_init', 'subtle_widgets_init' );

// ============================================================================
// Theme Helpers
// ============================================================================

/**
 * Get the theme version.
 *
 * @return string Theme version.
 */
function subtle_get_theme_version() {
	static $version = null;
	if ( null === $version ) {
		$version = wp_get_theme()->get( 'Version' );
	}
	return $version;
}

// ============================================================================
// Assets (Styles)
// ============================================================================

/**
 * Enqueues theme styles.
 *
 * @return void
 */
function subtle_enqueue_assets() {
	$theme_version = subtle_get_theme_version();

	// Main stylesheet.
	wp_enqueue_style(
		'subtle-style',
		get_stylesheet_uri(),
		array(),
		$theme_version
	);
}
add_action( 'wp_enqueue_scripts', 'subtle_enqueue_assets' );

// ============================================================================
// Misc
// ============================================================================

/**
 * Raises excerpt word count to 200 words.
 *
 * @param int $length Excerpt length in words.
 * @return int
 */
function subtle_excerpt_length( $length ) {
	return 100;
}
add_filter( 'excerpt_length', 'subtle_excerpt_length' );

/**
 * Disables WordPress automatic image resizing.
 * This prevents WordPress from creating multiple image sizes.
 *
 * @return void
 */
function subtle_disable_image_resizing() {
	add_filter( 'intermediate_image_sizes_advanced', '__return_empty_array' );
}
add_action( 'init', 'subtle_disable_image_resizing' );

// ============================================================================
// Development Helpers
// ============================================================================

/**
 * Removes version query strings from styles and scripts.
 * Only active when WP_DEBUG is enabled.
 *
 * @param string $src The source URL.
 * @return string Modified source URL.
 */
function subtle_remove_version_scripts_styles( $src ) {
	if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
		return $src;
	}

	return strpos( $src, 'ver=' ) ? remove_query_arg( 'ver', $src ) : $src;
}
add_filter( 'style_loader_src', 'subtle_remove_version_scripts_styles', 9999 );
add_filter( 'script_loader_src', 'subtle_remove_version_scripts_styles', 9999 );

