<?php
/**
 * Users type load
 *
 * @package Clipper\Users
 * @author  AppThemes
 * @since   2.0.0
 */

APP_Listing_Autoload::add_class_map( array(
	'CLPR_User_Listing_Builder' => dirname( __FILE__ ) . '/class-user-listing-builder.php',
	'CLPR_User_Listing_Form'    => dirname( __FILE__ ) . '/class-user-listing-form.php',

	// Framework deps.
	'APP_Media_Manager_Meta_Type' => APP_FRAMEWORK_DIR . '/media-manager/media-manager-types.php',
) );
