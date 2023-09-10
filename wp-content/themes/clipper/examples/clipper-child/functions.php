<?php
/**
 * Clipper child theme functions.
 *
 * BEFORE USING: Move the clipper-child theme into the /themes/ folder.
 *
 * @package Clipper\Functions
 * @author  AppThemes
 * @since   Clipper 2.0.2
 */

/**
 * Registers the stylesheet for the child theme.
 */
function clipper_child_styles() {
	global $clpr_options;

	wp_enqueue_style( 'child-style', get_stylesheet_uri() );

	// Enqueue color scheme.
	wp_enqueue_style( 'at-color', get_template_directory_uri() . '/styles/' . $clpr_options->stylesheet );

	// Disable the Clipper default styles.
	//wp_dequeue_style( 'at-main' );

	// Disable the Foundation framework styles.
	//wp_dequeue_style( 'foundation' );
}
add_action( 'wp_enqueue_scripts', 'clipper_child_styles', 999 );

/**
 * Registers the scripts for the child theme.
 */
function clipper_child_scripts() {
	wp_enqueue_script( 'child-script', get_stylesheet_directory_uri() . '/general.js' );

	// Disable the Clipper default scripts.
	//wp_dequeue_script( 'theme-scripts' );

	// Disable the Foundation framework scripts.
	//wp_dequeue_script( 'foundation' );
	//wp_dequeue_script( 'foundation-motion-ui' );
}
add_action( 'wp_enqueue_scripts', 'clipper_child_scripts', 999 );

/**
 * This function migrates parent theme mods to the child theme.
 */
function clipper_child_assign_mods_on_activation() {

	if ( empty( get_theme_mod( 'migrated_from_parent' ) ) ) {
		$theme = get_option( 'stylesheet' );
		update_option( "theme_mods_$theme", get_option( 'theme_mods_clipper' ) );
		set_theme_mod( 'migrated_from_parent', 1 );
	}
}
add_action( 'after_switch_theme', 'clipper_child_assign_mods_on_activation' );

// You can add you own actions, filters and code below.
