<?php
/**
 * Recurring Payments module load.
 *
 * @package Listing\Modules\Recurring
 * @author  AppThemes
 * @since   Listing 1.0
 */

// @codeCoverageIgnoreStart
// Module files.
if ( class_exists( 'APP_Listing_Autoload' ) ) {
	APP_Listing_Autoload::add_class_map( array(
		'APP_Listing_Recurring_Box'                    => dirname( __FILE__ ) . '/admin/class-listing-recurring-metabox.php',
		'APP_Listing_Recurring_Settings'               => dirname( __FILE__ ) . '/admin/class-listing-recurring-settings.php',
		'APP_Listing_Current_Plan_Recurring_Decorator' => dirname( __FILE__ ) . '/class-listing-current-plan-recurring-decorator.php',
		'APP_Listing_Plan_Recurring_Decorator'         => dirname( __FILE__ ) . '/class-listing-plan-recurring-decorator.php',
		'APP_Listing_Recurring'                        => dirname( __FILE__ ) . '/class-listing-recurring.php',
	) );
}
