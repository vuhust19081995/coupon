<?php
/**
 * Prevent visitors without permissions to access the WordPress backend.
 *
 * @package Clipper\Security
 * @author  AppThemes
 * @since   Clipper 1.0
 */


/**
 * Checks permissions to access the WordPress backend.
 *
 * @return void
 */
function clpr_security_check() {
	global $clpr_options;

	$access_level = $clpr_options->admin_security;
	// if there's no value then give everyone access
	if ( empty( $access_level ) ) {
		$access_level = 'read';
	}

	$doing_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;
	$doing_admin_post = ( basename( $_SERVER['SCRIPT_FILENAME'] ) === 'admin-post.php' );

	if ( $access_level == 'disable' || current_user_can( $access_level ) || $doing_ajax || $doing_admin_post ) {
		return;
	}

	appthemes_add_notice( 'denied-admin-access', __( 'Site administrator has blocked your access to the back-office.', APP_TD ), 'error' );
	wp_redirect( clpr_get_dashboard_url( 'redirect' ) );
	exit();
}
add_action( 'admin_init', 'clpr_security_check', 1 );

