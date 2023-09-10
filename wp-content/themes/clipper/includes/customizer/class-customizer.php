<?php
/**
 * Customizer core theme functionality.
 *
 * @package Clipper
 *
 * @since 2.0.0
 */

 /**
  * Setup the Clipper customizer class.
  *
  * @package Clipper
  *
  * @since 2.0.0
  */
 class CLPR_Customizer {

 	/**
 	 * Constructor
 	 */
 	public function __construct() {
		add_action( 'customize_register',                 array( $this, 'custom_panels' ), 10 );
		add_action( 'customize_register',                 array( $this, 'custom_sections' ), 20 );
		add_action( 'after_setup_theme',                  array( $this, 'custom_styles' ) );
		//add_action( 'customize_preview_init',             array( $this, 'clpr_customize_preview_js' ) );
		//add_action( 'customize_controls_enqueue_scripts', array( $this, 'clpr_customize_controls_js' ) );

		add_filter( 'body_class',                         array( $this, 'custom_body_class' ) );
	}

	/**
	 * Load custom panels.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function custom_panels( $wp_customize ) {
		foreach ( glob( dirname( __FILE__ ) . '/panels/*.php' ) as $file ) {
			include_once( $file );
		}
	}

	/**
	 * Load custom sections.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function custom_sections( $wp_customize ) {
		foreach ( glob( dirname( __FILE__ ) . '/sections/*.php' ) as $file ) {
			include_once( $file );
		}
	}

	/**
	 * Load custom styles for the front-end.
	 *
	 * @since 2.0.0
	 *
	 * @return void
	 */
	public function custom_styles( $wp_customize ) {
		foreach ( glob( dirname( __FILE__ ) . '/styles/*.php' ) as $file ) {
			include_once( $file );
		}
	}

	/**
	 * Binds JS handlers to make the Customizer preview reload changes asynchronously.
	 *
	 * @since 2.0.0
	 */
	public function clpr_customize_preview_js() {
		wp_enqueue_script( 'theme-customize-preview', get_template_directory_uri() . '/assets/js/customize-preview.js', array( 'customize-preview' ), CLPR_VERSION, true );
	}

	/**
	 * Load dynamic logic for the customizer controls area.
	 *
	 * @since 2.0.0
	 */
	public function clpr_customize_controls_js() {
		wp_enqueue_script( 'theme-customize-controls', get_template_directory_uri() . '/assets/js/customize-controls.js', array(), CLPR_VERSION, true );
	}

	/**
	 * Adds custom classes to the array of body classes.
	 *
	 * @since 2.0.0
	 *
	 * @param array $classes Classes for the body element.
	 * @return array
	 */
	function custom_body_class( $classes ) {

		// Add class if we're viewing the Customizer for easier styling of theme options.
		if ( is_customize_preview() ) {
			$classes[] = 'clpr-customizer';
		}

		// Get the color scheme or the default if there isn't one.
		$colors = get_theme_mod( 'color_scheme', 'aqua' );
		$classes[] = 'theme-' . $colors;

		return $classes;
	}

}

new CLPR_Customizer;
