<?php
/**
 * Listing Plans Registry module load.
 *
 * @package Listing
 * @author  AppThemes
 * @since   Listing 1.0
 */

// @codeCoverageIgnoreStart
// Module files.
if ( class_exists( 'APP_Listing_Autoload' ) ) {
	APP_Listing_Autoload::add_class_map( array(
		'APP_Listing_Plan_Metabox'           => dirname( __FILE__ ) . '/admin/class-listing-plan-metabox.php',
		'APP_Listing_Plans_Settings'         => dirname( __FILE__ ) . '/admin/class-listing-plans-settings.php',
		'APP_Listing_Current_Plan_Decorator' => dirname( __FILE__ ) . '/class-listing-current-plan-decorator.php',
		'APP_Listing_General_Plan'           => dirname( __FILE__ ) . '/class-listing-general-plan.php',
		'APP_Listing_Plan_Decorator'         => dirname( __FILE__ ) . '/class-listing-plan-decorator.php',
		'APP_Listing_Plans_Registry'         => dirname( __FILE__ ) . '/class-listing-plans-registry.php',
		'APP_Listing_Regular_Plan_Decorator' => dirname( __FILE__ ) . '/class-listing-regular-plan-decorator.php',
		'APP_Listing_Current_Plan_I'         => dirname( __FILE__ ) . '/interface-listing-plan.php',
		'APP_Listing_Plan_I'                 => dirname( __FILE__ ) . '/interface-listing-plan.php',
	) );
}
