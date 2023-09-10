<?php
/**
 * Listing Anonymous module load.
 *
 * @package Listing\Modules\Anonymous
 * @author  AppThemes
 * @since   Listing 2.0
 */

// @codeCoverageIgnoreStart
if ( class_exists( 'APP_Listing_Autoload' ) ) {
	APP_Listing_Autoload::add_class_map( array(
		'APP_Listing_Anonymous'        => dirname( __FILE__ ) . '/class-listing-anonymous.php',
		'APP_Listing_Anonymous_Expire' => dirname( __FILE__ ) . '/class-listing-anonymous-expire.php',
		'APP_Step_Create_User'         => dirname( __FILE__ ) . '/class-step-create-user.php',
	) );
}
