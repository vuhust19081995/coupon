<?php
/**
 * Listing Plan Category Limit module load.
 *
 * @package Listing\Modules\CatLimit
 * @author  AppThemes
 * @since   Listing 1.0
 */

// @codeCoverageIgnoreStart
// Module files.
if ( class_exists( 'APP_Listing_Autoload' ) ) {
	APP_Listing_Autoload::add_class_map( array(
		'APP_Listing_Plans_Taxonomy_Limit_Box' => dirname( __FILE__ ) . '/admin/class-listing-plans-tax-limit-metabox.php',
		'APP_Listing_Taxonomy_Limit_Settings'  => dirname( __FILE__ ) . '/admin/class-listing-tax-limit-settings.php',
		'APP_Listing_Taxonomy_Limit'           => dirname( __FILE__ ) . '/class-listing-tax-limit.php',
	) );
}
