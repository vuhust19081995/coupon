<?php
/**
 * Listing Taxonomy Surcharge module load.
 *
 * @package Listing\Modules\TaxSurcharge
 * @author  AppThemes
 * @since   Listing 1.0
 */

// @codeCoverageIgnoreStart
// Module files.
if ( class_exists( 'APP_Listing_Autoload' ) ) {
	APP_Listing_Autoload::add_class_map( array(
		'APP_Listing_Taxonomy_Surcharge_Settings'       => dirname( __FILE__ ) . '/admin/class-listing-tax-charge-settings.php',
		'APP_Listing_Plan_Taxonomy_Surcharge_Decorator' => dirname( __FILE__ ) . '/class-listing-plan-tax-charge-decorator.php',
		'APP_Listing_Taxonomy_Surcharge'                => dirname( __FILE__ ) . '/class-listing-tax-charge.php',
	) );
}
