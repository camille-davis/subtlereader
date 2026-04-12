<?php
/**
 * Misc block customizations for Subtle And Reader theme:
 * - Add is-fullwidth-image toggle to image block
 *
 * @package Subtle
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Block Customizations Class
 */
class Subtle_Blocks {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter(
			'render_block_core/image',
			function( $block_content, $block ) {
				return $this->add_block_class( $block_content, $block, 'wp-block-image', 'is-fullwidth-image', 'fullwidth' );
			},
			10,
			2
		);
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_block_editor_assets' ) );
	}

	/**
	 * Enqueue block editor assets.
	 *
	 * @return void
	 */
	public function enqueue_block_editor_assets() {
		$theme_version     = subtle_get_theme_version();
		$block_editor_deps = array(
			'wp-blocks',
			'wp-block-editor',
			'wp-components',
			'wp-compose',
			'wp-element',
			'wp-hooks',
		);

		$editor_scripts = array(
			array(
				'handle' => 'subtle-image-block-options',
				'file'   => 'image-block-options.js',
				'deps'   => $block_editor_deps,
			),
			array(
				'handle' => 'subtle-add-editor-classes',
				'file'   => 'add-editor-classes.js',
				'deps'   => array(),
			),
		);

		$script_base = get_template_directory_uri() . '/js/';
		foreach ( $editor_scripts as $spec ) {
			wp_enqueue_script(
				$spec['handle'],
				$script_base . $spec['file'],
				$spec['deps'],
				$theme_version,
				true
			);
		}
	}

	/**
	 * Add class to block content when attribute is set.
	 *
	 * @param string $block_content The block content.
	 * @param array  $block The block data.
	 * @param string $block_class The block class to match (e.g., 'wp-block-image').
	 * @param string $css_class The CSS class to add (e.g., 'is-fullwidth-image').
	 * @param string $attr_key The attribute key to check (e.g., 'fullwidth').
	 * @return string Modified block content.
	 */
	private function add_block_class( $block_content, $block, $block_class, $css_class, $attr_key ) {
		$attr_value = $block['attrs'][ $attr_key ] ?? false;

		if ( ! $attr_value ) {
			return $block_content;
		}

		// Check if class already exists in the target element's class attribute (not just anywhere in content).
		// This prevents false positives when the class exists in nested blocks.
		$pattern_check = '/(<[^>]*\bclass="[^"]*' . preg_quote( $block_class, '/' ) . '[^"]*' . preg_quote( $css_class, '/' ) . '[^"]*")/';
		if ( preg_match( $pattern_check, $block_content ) ) {
			return $block_content;
		}

		$pattern = '/(<[^>]*\bclass="[^"]*' . preg_quote( $block_class, '/' ) . '[^"]*)(")/';
		return preg_replace(
			$pattern,
			'$1 ' . $css_class . '$2',
			$block_content,
			1
		);
	}
}

new Subtle_Blocks();
