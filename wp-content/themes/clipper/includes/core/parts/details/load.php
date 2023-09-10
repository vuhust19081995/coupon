<?php
/**
 * Listing Details submodule load
 *
 * @package Listing\Details
 * @author  AppThemes
 * @since   Listing 1.0
 */

// @codeCoverageIgnoreStart
// Module files.
if ( class_exists( 'APP_Listing_Autoload' ) ) {
	APP_Listing_Autoload::add_class_map( array(
		'APP_Listing_Details'          => dirname( __FILE__ ) . '/class-listing-details.php',
		'APP_Listing_Details_Settings' => dirname( __FILE__ ) . '/admin/class-listing-details-settings.php',
	) );
}
