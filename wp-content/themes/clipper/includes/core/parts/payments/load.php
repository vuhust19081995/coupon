<?php
/**
 * Listing Payments submodule load.
 *
 * @package Listing\Payments
 * @author  AppThemes
 * @since   Listing 1.0
 */

// @codeCoverageIgnoreStart
// Module files.
if ( class_exists( 'APP_Listing_Autoload' ) ) {
	APP_Listing_Autoload::add_class_map( array(
		'APP_Listing_Payments_Settings'    => dirname( __FILE__ ) . '/admin/class-listing-payments-settings.php',
		'APP_Listing_Charge_Settings'      => dirname( __FILE__ ) . '/admin/class-listing-charge-settings.php',
		'APP_Listing_Plans_Price_Box'      => dirname( __FILE__ ) . '/admin/class-listing-plans-price-metabox.php',
		'APP_Listing_Payments_Emails'      => dirname( __FILE__ ) . '/class-listing-payments-emails.php',
		'APP_Listing_Payments'             => dirname( __FILE__ ) . '/class-listing-payments.php',
		'APP_Listing_Plan_Price_Decorator' => dirname( __FILE__ ) . '/class-listing-plan-price-decorator.php',
	) );
}
