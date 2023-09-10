<?php
/**
 * Listing Addons module load.
 *
 * @package Listing\Modules\Addons
 * @author  AppThemes
 * @since   Listing 1.0
 */

// @codeCoverageIgnoreStart
if ( class_exists( 'APP_Listing_Autoload' ) ) {
	APP_Listing_Autoload::add_class_map( array(
		'APP_Listing_Addons_Settings'               => dirname( __FILE__ ) . '/admin/class-listing-addons-settings.php',
		'APP_Listing_Plan_Details_Addons_Box'       => dirname( __FILE__ ) . '/admin/class-listing-plan-details-addons-metabox.php',
		'APP_Listing_Plans_Addons_Box'              => dirname( __FILE__ ) . '/admin/class-listing-plans-addons-metabox.php',
		'APP_Listing_Addon_I'                       => dirname( __FILE__ ) . '/interface-listing-addon.php',
		'APP_Listing_General_Addon'                 => dirname( __FILE__ ) . '/class-listing-general-addon.php',
		'APP_Listing_Plan_Addon'                    => dirname( __FILE__ ) . '/class-listing-plan-addon.php',
		'APP_Listing_Purchased_Addon'               => dirname( __FILE__ ) . '/class-listing-purchased-addon.php',
		'APP_Listing_Addons_Emails'                 => dirname( __FILE__ ) . '/class-listing-addons-emails.php',
		'APP_Listing_Addons'                        => dirname( __FILE__ ) . '/class-listing-addons.php',
		'APP_Listing_Current_Plan_Addons_Decorator' => dirname( __FILE__ ) . '/class-listing-current-plan-addons-decorator.php',
		'APP_Listing_Plan_Addons_Decorator'         => dirname( __FILE__ ) . '/class-listing-plan-addons-decorator.php',
	) );
}
