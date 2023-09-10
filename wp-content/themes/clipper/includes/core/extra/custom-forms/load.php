<?php
/**
 * Custom Forms module load.
 *
 * @package Listing\Modules\CustomForms
 * @author  AppThemes
 * @since   Listing 1.0
 */

// @codeCoverageIgnoreStart
// Module files.
if ( class_exists( 'APP_Listing_Autoload' ) ) {
	APP_Listing_Autoload::add_class_map( array(
		'APP_Listing_Custom_Forms_Metabox' => dirname( __FILE__ ) . '/admin/class-listing-custom-forms-metabox.php',
		'APP_Listing_Custom_Forms'         => dirname( __FILE__ ) . '/class-listing-custom-forms.php',
	) );
}
