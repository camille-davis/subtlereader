<?php
/**
 * Customizer functionality for Subtle And Reader theme.
 *
 * @package Subtle
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Customizer class.
 */
class Subtle_Customizer {

	/**
	 * Default fonts URL.
	 *
	 * @var string
	 */
	private const DEFAULT_FONTS_URL = 'https://fonts.googleapis.com/css2?family=Noto+Serif:ital@0;1&family=Playfair+Display:ital@0;1&display=swap';

	/**
	 * Allowed font hosts.
	 *
	 * @var array
	 */
	private const ALLOWED_FONT_HOSTS = array( 'fonts.googleapis.com', 'fonts.gstatic.com' );

	/**
	 * Default typography values.
	 *
	 * @var array
	 */
	private const DEFAULT_TYPOGRAPHY = array(
		'heading_font'          => 'Playfair Display',
		'body_font'             => 'Noto Serif',
		'site_name_font_size'   => '3rem',
		'nav_link_font_size'    => '1.25rem',
		'body_font_size' => '1rem',
		'h1_font_size'   => '2.5rem',
		'h2_font_size'   => '2rem',
		'h3_font_size'   => '1.75rem',
		'h4_font_size'   => '1.5rem',
		'h5_font_size'   => '1.25rem',
		'h6_font_size'   => '1rem',
	);

	/**
	 * Default layout values.
	 *
	 * @var array
	 */
	private const DEFAULT_LAYOUT = array(
		'content_width' => '60rem',
	);

	/**
	 * Theme color settings: label, editor palette slug, CSS variable stem, default hex.
	 * Single source used for Customizer controls, editor palette, and inline CSS variables.
	 *
	 * @return array Setting id => array with keys label, slug, css_var, default; optional allow_empty uses transparent when cleared.
	 */
	private static function get_color_setting_definitions() {
		static $definitions = null;

		if ( null !== $definitions ) {
			return $definitions;
		}

		$definitions = array(
			'background_color'     => array(
				'label'   => __( 'Background Color', 'subtlereader' ),
				'slug'    => 'background-color',
				'css_var' => 'background-color',
				'default' => '#FFFFFF',
			),
			'text_color'           => array(
				'label'   => __( 'Text Color', 'subtlereader' ),
				'slug'    => 'text-color',
				'css_var' => 'text',
				'default' => '#222222',
			),
			'link_color'           => array(
				'label'   => __( 'Link Color', 'subtlereader' ),
				'slug'    => 'link-color',
				'css_var' => 'link-color',
				'default' => '#a52a2a',
			),
		);

		return $definitions;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'customize_register', array( $this, 'register_controls' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'add_reset_buttons' ) );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'add_customizer_style' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_inline_css' ), 20 );
		add_action( 'after_setup_theme', array( $this, 'register_editor_color_palette' ) );
		add_filter( 'block_editor_settings_all', array( $this, 'add_editor_inline_css' ), 10, 1 );
	}

	/**
	 * Register customizer controls and settings.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	public function register_controls( $wp_customize ) {
		$this->register_color_controls( $wp_customize );
		$this->register_layout_controls( $wp_customize );
		$this->register_typography_controls( $wp_customize );
	}

	/**
	 * Register color controls.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	private function register_color_controls( $wp_customize ) {

		// Colors Section.
		$wp_customize->add_section(
			'subtle_colors',
			array(
				'title'    => __( 'Theme Colors', 'subtlereader' ),
				'priority' => 30,
			)
		);

		foreach ( self::get_color_setting_definitions() as $setting_id => $def ) {
			$sanitize = ! empty( $def['allow_empty'] )
				? array( $this, 'sanitize_optional_hex_color' )
				: 'sanitize_hex_color';

			$wp_customize->add_setting(
				$setting_id,
				array(
					'default'           => $def['default'],
					'sanitize_callback' => $sanitize,
				)
			);

			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					$setting_id,
					array(
						'label'   => $def['label'],
						'section' => 'subtle_colors',
					)
				)
			);
		}
	}

	/**
	 * Sanitize optional hex color; empty string means no color (cleared control).
	 *
	 * @param string $color Submitted value.
	 * @return string Sanitized hex or empty string.
	 */
	public function sanitize_optional_hex_color( $color ) {
		if ( '' === $color || null === $color ) {
			return '';
		}

		return sanitize_hex_color( $color );
	}

	/**
	 * Sanitize content width CSS length; empty uses default.
	 *
	 * @param string $value Submitted value.
	 * @return string Non-empty sanitized length.
	 */
	public function sanitize_content_width( $value ) {
		$value = is_string( $value ) ? sanitize_text_field( $value ) : '';
		if ( '' === $value ) {
			return self::DEFAULT_LAYOUT['content_width'];
		}
		return $value;
	}

	/**
	 * Register layout controls.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	private function register_layout_controls( $wp_customize ) {

		$wp_customize->add_panel(
			'subtle_layout',
			array(
				'title'    => __( 'Layout', 'subtlereader' ),
				'priority' => 32,
			)
		);

		$wp_customize->add_section(
			'subtle_layout',
			array(
				'title'    => __( 'Content', 'subtlereader' ),
				'panel'    => 'subtle_layout',
				'priority' => 10,
			)
		);

		$wp_customize->add_setting(
			'content_width',
			array(
				'default'           => self::DEFAULT_LAYOUT['content_width'],
				'sanitize_callback' => array( $this, 'sanitize_content_width' ),
			)
		);

		$wp_customize->add_control(
			'content_width',
			array(
				'label'       => __( 'Content Width', 'subtlereader' ),
				'description' => __( 'CSS length for the main content column (e.g. 50rem, 720px).', 'subtlereader' ),
				'section'     => 'subtle_layout',
				'type'        => 'text',
			)
		);
	}

	/**
	 * Register typography controls.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 */
	private function register_typography_controls( $wp_customize ) {

		$wp_customize->add_panel(
			'subtle_typography',
			array(
				'title'    => __( 'Typography', 'subtlereader' ),
				'priority' => 35,
			)
		);

		$wp_customize->add_section(
			'subtle_typography_fonts',
			array(
				'title'    => __( 'Fonts', 'subtlereader' ),
				'panel'    => 'subtle_typography',
				'priority' => 10,
			)
		);

		$wp_customize->add_section(
			'subtle_typography_font_sizes',
			array(
				'title'    => __( 'Font Sizes', 'subtlereader' ),
				'panel'    => 'subtle_typography',
				'priority' => 20,
			)
		);

		// Fonts URL.
		$wp_customize->add_setting(
			'fonts_url',
			array(
				'default'           => self::DEFAULT_FONTS_URL,
				'sanitize_callback' => array( $this, 'sanitize_font_url' ),
			)
		);

		$wp_customize->add_control(
			'fonts_url',
			array(
				'label'   => __( 'Fonts URL', 'subtlereader' ),
				'section' => 'subtle_typography_fonts',
				'type'    => 'text',
			)
		);

		$font_name_controls = array(
			'body_font'    => __( 'Body Font', 'subtlereader' ),
			'heading_font' => __( 'Heading Font', 'subtlereader' ),
		);

		foreach ( $font_name_controls as $setting_id => $label ) {
			$this->register_text_control( $wp_customize, $setting_id, $label, 'subtle_typography_fonts' );
		}

		$this->register_text_control( $wp_customize, 'site_name_font_size', __( 'Site Name Font Size', 'subtlereader' ), 'subtle_typography_font_sizes' );
		$this->register_text_control( $wp_customize, 'nav_link_font_size', __( 'Nav Link Font Size', 'subtlereader' ), 'subtle_typography_font_sizes' );
		$this->register_text_control( $wp_customize, 'body_font_size', __( 'Body Font Size', 'subtlereader' ), 'subtle_typography_font_sizes' );

		$headings = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );
		foreach ( $headings as $heading ) {
			$setting_id = "{$heading}_font_size";
			$label      = sprintf(
				// translators: %s is the heading name.
				__( '%s Size', 'subtlereader' ),
				ucfirst( $heading )
			);
			$this->register_text_control( $wp_customize, $setting_id, $label, 'subtle_typography_font_sizes' );
		}
	}

	/**
	 * Register a text control in the customizer.
	 *
	 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
	 * @param string               $setting_id   Setting ID.
	 * @param string               $label        Control label.
	 * @param string               $section      Section ID.
	 */
	private function register_text_control( $wp_customize, $setting_id, $label, $section ) {
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => self::DEFAULT_TYPOGRAPHY[ $setting_id ] ?? '',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			$setting_id,
			array(
				'label'   => $label,
				'section' => $section,
				'type'    => 'text',
			)
		);
	}

	/**
	 * Sanitize font URL to only allow trusted sources.
	 *
	 * @param string $url The font URL to sanitize.
	 * @return string Sanitized URL or default if invalid.
	 */
	public function sanitize_font_url( $url ) {
		$url = esc_url_raw( $url );

		// If empty, return default.
		if ( empty( $url ) ) {
			return self::DEFAULT_FONTS_URL;
		}

		$parsed = wp_parse_url( $url );

		// If URL cannot be parsed or has no host, return default.
		if ( ! $parsed || empty( $parsed['host'] ) ) {
			return self::DEFAULT_FONTS_URL;
		}

		$host = strtolower( $parsed['host'] );

		// Allow same domain or subdomain.
		if ( isset( $_SERVER['HTTP_HOST'] ) && strpos( $host, strtolower( $_SERVER['HTTP_HOST'] ) ) !== false ) {
			return $url;
		}

		// Check against allowed hosts.
		foreach ( self::ALLOWED_FONT_HOSTS as $allowed ) {
			if ( $host === $allowed || strpos( $host, '.' . $allowed ) !== false ) {
				return $url;
			}
		}

		// If not allowed, return default.
		return self::DEFAULT_FONTS_URL;
	}

	/**
	 * Add reset buttons to customizer controls.
	 */
	public function add_reset_buttons() {
		global $wp_customize;

		// Get typography setting defaults and build inline script in a single loop.
		$inline_script = '';
		$controls      = $wp_customize->controls();
		foreach ( $controls as $control ) {
			if ( 'subtle_typography_fonts' !== $control->section
				&& 'subtle_typography_font_sizes' !== $control->section
				&& 'subtle_layout' !== $control->section ) {
				continue;
			}
			$setting = $wp_customize->get_setting( $control->id );
			if ( $setting ) {
				$inline_script .= sprintf(
					'wp.customize("%s", function(setting) { setting.default = %s; });',
					$control->id,
					wp_json_encode( $setting->default )
				);
			}
		}
		wp_add_inline_script( 'customize-controls', $inline_script );

		// Add 'Reset' buttons.
		$theme_version = subtle_get_theme_version();
		wp_enqueue_script(
			'subtle-customizer',
			get_template_directory_uri() . '/js/customizer.js',
			array( 'jquery', 'customize-controls' ),
			$theme_version,
			true
		);

		wp_localize_script(
			'subtle-customizer',
			'subtleCustomizer',
			array(
				'resetText' => __( 'Reset', 'subtlereader' ),
			)
		);
	}

	/**
	 * Add customizer styles.
	 */
	public function add_customizer_style() {
		$theme_version = subtle_get_theme_version();
		wp_enqueue_style(
			'subtle-customizer',
			get_template_directory_uri() . '/css/customizer.css',
			array(),
			$theme_version
		);
	}

	/**
	 * Add CSS variables to editor canvas only (not sidebar).
	 *
	 * @param array $editor_settings Editor settings array.
	 * @return array Modified editor settings.
	 */
	public function add_editor_inline_css( $editor_settings ) {

		$inline_css = self::get_inline_css();

		if ( '' === trim( $inline_css ) ) {
			return $editor_settings;
		}

		// Ensure styles array exists.
		if ( ! isset( $editor_settings['styles'] ) ) {
			$editor_settings['styles'] = array();
		}

		// Prepend CSS variables so they're available before other styles.
		// Must set isGlobalStyles to false or it will be stripped out.
		array_unshift(
			$editor_settings['styles'],
			array(
				'css'            => $inline_css,
				'__unstableType' => 'theme',
				'isGlobalStyles' => false,
			)
		);

		return $editor_settings;
	}

	/**
	 * Register editor color palette with theme colors from customizer.
	 */
	public function register_editor_color_palette() {
		$editor_color_palette = array();
		$seen_hex             = array();
		foreach ( self::get_color_setting_definitions() as $setting_id => $def ) {
			if ( ! empty( $def['allow_empty'] ) ) {
				continue;
			}

			$color = get_theme_mod( $setting_id, $def['default'] );
			$key   = strtolower( $color );
			if ( isset( $seen_hex[ $key ] ) ) {
				continue;
			}
			$seen_hex[ $key ] = true;
			$editor_color_palette[] = array(
				'name'  => $def['label'],
				'slug'  => $def['slug'],
				'color' => $color,
			);
		}

		add_theme_support( 'editor-color-palette', $editor_color_palette );
	}

	/**
	 * Get inline CSS with CSS variables.
	 *
	 * @return string CSS string with variables.
	 */
	public static function get_inline_css() {
		$css = '';

		// Output font import.
		$fonts_url = get_theme_mod( 'fonts_url', self::DEFAULT_FONTS_URL );
		if ( $fonts_url ) {
			$css .= '@import url("' . esc_url_raw( $fonts_url ) . '");';
		}

		// Theme colors: read each theme mod once and build CSS variables.
		$color_variables = array();
		foreach ( self::get_color_setting_definitions() as $setting_id => $def ) {
			$color   = get_theme_mod( $setting_id, $def['default'] );
			$css_var = $def['css_var'];

			if ( ! empty( $def['allow_empty'] ) ) {
				$color_variables[ $css_var ] = $color ? $color : 'transparent';
				continue;
			}

			$color_variables[ $css_var ] = $color;
		}

		// Build typography CSS variables.
		$typography_variables = array(
			'heading-font'           => get_theme_mod( 'heading_font', self::DEFAULT_TYPOGRAPHY['heading_font'] ) . ', serif',
			'body-font'              => get_theme_mod( 'body_font', self::DEFAULT_TYPOGRAPHY['body_font'] ) . ', serif',
			'site-name-font-size'    => get_theme_mod( 'site_name_font_size', self::DEFAULT_TYPOGRAPHY['site_name_font_size'] ),
			'nav-link-font-size'     => get_theme_mod( 'nav_link_font_size', self::DEFAULT_TYPOGRAPHY['nav_link_font_size'] ),
			'body-font-size'         => get_theme_mod( 'body_font_size', self::DEFAULT_TYPOGRAPHY['body_font_size'] ),
		);
		$headings = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' );
		foreach ( $headings as $heading ) {
			$setting_id = "{$heading}_font_size";
			$typography_variables[ "{$heading}-font-size" ] = get_theme_mod( $setting_id, self::DEFAULT_TYPOGRAPHY[ $setting_id ] );
		}

		$content_width = get_theme_mod( 'content_width', self::DEFAULT_LAYOUT['content_width'] );
		if ( '' === $content_width ) {
			$content_width = self::DEFAULT_LAYOUT['content_width'];
		}
		$layout_variables = array(
			'content-width' => $content_width,
		);

		// Combine all CSS variables.
		$custom_css_variables = array_merge( $color_variables, $typography_variables, $layout_variables );

		$css .= ':root {';
		foreach ( $custom_css_variables as $name => $value ) {
			$css .= "--{$name}: {$value};";
		}
		$css .= '}';

		return $css;
	}

	/**
	 * Attach inline CSS to the main theme stylesheet on the frontend.
	 */
	public function enqueue_frontend_inline_css() {
		$inline_css = self::get_inline_css();

		if ( empty( $inline_css ) ) {
			return;
		}

		// Attach the CSS variables to the main theme stylesheet.
		wp_add_inline_style( 'subtle-style', $inline_css );
	}
}

new Subtle_Customizer();
