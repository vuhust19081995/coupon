<?php
/**
 * Listing Geocode module load.
 *
 * Requires app-geo-2 theme support
 *
 * @package Listing\Modules\Geocode
 * @author  AppThemes
 * @since   Listing 1.0
 */

// @codeCoverageIgnoreStart
// Module files.
if ( class_exists( 'APP_Listing_Autoload' ) ) {
	APP_Listing_Autoload::add_class_map( array(
		'APP_Listing_Geocode' => dirname( __FILE__ ) . '/class-listing-geocode.php',
	) );
}
