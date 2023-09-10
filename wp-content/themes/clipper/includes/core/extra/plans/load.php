<?php
/**
 * Listing Plans module load.
 *
 * @package Listing\Modules\Plan
 * @author  AppThemes
 * @since   Listing 1.0
 */

// @codeCoverageIgnoreStart
if ( class_exists( 'APP_Listing_Autoload' ) ) {
	APP_Listing_Autoload::add_class_map( array(
		'APP_Listing_Plans_Details_Box'  => dirname( __FILE__ ) . '/admin/class-listing-plans-details-metabox.php',
		'APP_Listing_Plans_Duration_Box' => dirname( __FILE__ ) . '/admin/class-listing-plans-duration-metabox.php',
		'APP_Listing_Plans'              => dirname( __FILE__ ) . '/class-listing-plans.php',
		'APP_Listing_Post_Plan'          => dirname( __FILE__ ) . '/class-listing-post-plan.php',
	) );
}
