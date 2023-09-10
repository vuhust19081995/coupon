<?php
/**
 * Listing expiration submodule load
 *
 * @package Listing\Expire
 * @author  AppThemes
 * @since   Listing 1.0
 */

// @codeCoverageIgnoreStart
// Module files.
if ( class_exists( 'APP_Listing_Autoload' ) ) {
	APP_Listing_Autoload::add_class_map( array(
		'APP_Listing_Expire'                        => dirname( __FILE__ ) . '/class-listing-expire.php',
		'APP_Listing_Expire_Emails'                 => dirname( __FILE__ ) . '/class-listing-expire-emails.php',
		'APP_Listing_Current_Plan_Expire_Decorator' => dirname( __FILE__ ) . '/class-listing-current-plan-expire-decorator.php',
	) );
}
