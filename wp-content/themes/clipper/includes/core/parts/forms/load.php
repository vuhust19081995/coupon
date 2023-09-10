<?php
/**
 * Listing Forms module load.
 *
 * @package Listing
 * @author  AppThemes
 * @since   Listing 1.0
 */

// @codeCoverageIgnoreStart
// Module files.
if ( class_exists( 'APP_Listing_Autoload' ) ) {
	APP_Listing_Autoload::add_class_map( array(
		'APP_Listing_Fields_Metabox'    => dirname( __FILE__ ) . '/admin/class-listing-fields-metabox.php',
		'APP_Listing_Taxonomy_Metabox'  => dirname( __FILE__ ) . '/admin/class-listing-taxonomy-metabox.php',
		'APP_Listing_User_Metabox'      => dirname( __FILE__ ) . '/admin/class-listing-user-metabox.php',
		'APP_Form_Builder_Field_Type'   => dirname( __FILE__ ) . '/admin/class-listing-form-settings.php',
		'APP_Listing_Form_Settings'     => dirname( __FILE__ ) . '/admin/class-listing-form-settings.php',
		'APP_Listing_Media_Metabox'     => dirname( __FILE__ ) . '/admin/class-listing-media-metabox.php',
		'APP_Editor_Field_Type'         => dirname( __FILE__ ) . '/class-listing-form-editor-field-type.php',
		'APP_Form_Field_Validator'      => dirname( __FILE__ ) . '/class-listing-form-field-validator.php',
		'APP_Media_Field_Type'          => dirname( __FILE__ ) . '/class-listing-form-media-field-type.php',
		'APP_Tax_Input_Field_Type'      => dirname( __FILE__ ) . '/class-listing-form-tax-input-field-type.php',
		'APP_Listing_Form'              => dirname( __FILE__ ) . '/class-listing-form.php',
		'APP_Walker_Category_Checklist' => dirname( __FILE__ ) . '/walker-category-checklist-class.php',
	) );
}
