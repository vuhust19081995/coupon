<?php
/**
 * Stores type load
 *
 * @package Clipper\Stores
 * @author  AppThemes
 * @since   2.0.0
 */

APP_Listing_Autoload::add_class_map( array(
	'CLPR_Store_Listing_Builder'              => dirname( __FILE__ ) . '/class-store-listing-builder.php',
	'CLPR_Store_Listing_Taxonomy_Meta_Object' => dirname( __FILE__ ) . '/class-store-listing-meta-object.php',
	'CLPR_Store_Listing_Form'                 => dirname( __FILE__ ) . '/class-store-listing-form.php',
	'CLPR_Store_Listing_Custom_Forms'         => dirname( __FILE__ ) . '/class-store-listing-custom-forms.php',
	'CLPR_Store_Listing_Taxonomy_Metabox'     => dirname( __FILE__ ) . '/admin/class-store-listing-taxonomy-metabox.php',
	'CLPR_Store_Listing_Process_New_Settings' => dirname( __FILE__ ) . '/admin/class-store-listing-process-new-settings.php',

	// Framework deps.
	'APP_Media_Manager_Meta_Type' => APP_FRAMEWORK_DIR . '/media-manager/media-manager-types.php',
) );
