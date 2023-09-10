<?php
/**
 * Listing load
 *
 * @package Listing
 * @author  AppThemes
 * @since   Listing 1.0
 */

// @codeCoverageIgnoreStart
define( 'APP_LISTING_VERSION', '2.1' );

if ( current_theme_supports( 'app-listing' ) ) {

	list( $app_listing_args ) = get_theme_support( 'app-listing' );
	$_app_listing_args = array(
		'path'    => dirname( __FILE__ ),
		'url'     => plugins_url( '',  __FILE__ ),
		'dirname' => 'listing',
	);

	$app_listing_args = wp_parse_args( $app_listing_args, $_app_listing_args );

	if ( ! defined( 'APP_LISTING_DIR' ) ) {
		define( 'APP_LISTING_DIR', $app_listing_args['path'] );
	}

	if ( ! defined( 'APP_LISTING_DIR_NAME' ) ) {
		define( 'APP_LISTING_DIR_NAME', $app_listing_args['dirname'] );
	}

	if ( ! defined( 'APP_LISTING_URI' ) ) {
		define( 'APP_LISTING_URI', $app_listing_args['url'] );
	}

	unset( $app_listing_args );
	unset( $_app_listing_args );

	// Register template directory with priority lower than for plugins dirs
	// and higher than theme-compat dir.
	add_filter( 'appthemes_get_template_directories', 'appthemes_listing_template_path', 100 );

}

/**
 * Registers own template directory in the stack
 *
 * @param array $stack Template directories stack array, keyed with
 *                     directories pathes and valued with directories urls.
 *
 * @return array $stack Modified Template directories stack
 */
function appthemes_listing_template_path( $stack ) {
	$stack[ APP_LISTING_DIR . '/templates' ] = APP_LISTING_URI . '/templates';
	return $stack;
}

require_once APP_FRAMEWORK_DIR . '/admin/class-widget.php';

appthemes_load_files( dirname( __FILE__ ) . '/', array(
	'class-autoload.php',
	'functions.php',
) );

APP_Listing_Autoload::add_class_map( array(
	// Core.
	'APP_Listing_Caps'                  => dirname( __FILE__ ) . '/class-listing-caps.php',
	'APP_Listing_Dependencies'          => dirname( __FILE__ ) . '/class-listing-dependencies.php',
	'APP_Detail_Publisher'              => dirname( __FILE__ ) . '/class-listing-details.php',
	'APP_Listing_Director'              => dirname( __FILE__ ) . '/class-listing-director.php',
	'APP_Listing_Emails'                => dirname( __FILE__ ) . '/class-listing-emails.php',
	'APP_Listing_Options'               => dirname( __FILE__ ) . '/class-listing-options.php',
	'APP_Listing_Features'              => dirname( __FILE__ ) . '/class-listing-features.php',
	'APP_Listing'                       => dirname( __FILE__ ) . '/class-listing.php',
	'APP_Listing_Builder'               => dirname( __FILE__ ) . '/builder/class-listing-builder.php',
	'APP_Post_Listing_Builder'          => dirname( __FILE__ ) . '/builder/class-post-listing-builder.php',
	'APP_User_Listing_Builder'          => dirname( __FILE__ ) . '/builder/class-user-listing-builder.php',
	'APP_Taxonomy_Listing_Builder'      => dirname( __FILE__ ) . '/builder/class-taxonomy-listing-builder.php',
	'APP_Listing_Meta_Object'           => dirname( __FILE__ ) . '/meta/class-listing-meta-object.php',
	'APP_Listing_Post_Meta_Object'      => dirname( __FILE__ ) . '/meta/class-listing-post-meta-object.php',
	'APP_Listing_User_Meta_Object'      => dirname( __FILE__ ) . '/meta/class-listing-user-meta-object.php',
	'APP_Listing_Taxonomy_Meta_Object'  => dirname( __FILE__ ) . '/meta/class-listing-taxonomy-meta-object.php',
	'APP_Listing_Admin_Security'        => dirname( __FILE__ ) . '/admin/class-listing-admin-security.php',
	'APP_Listing_Core_Settings'         => dirname( __FILE__ ) . '/admin/class-listing-core-settings.php',
	'APP_Listing_Settings'              => dirname( __FILE__ ) . '/admin/class-listing-settings.php',
	'APP_Listing_Features_Settings'     => dirname( __FILE__ ) . '/admin/class-listing-features-settings.php',
	'APP_Listing_Process_Edit_Settings' => dirname( __FILE__ ) . '/admin/class-listing-process-edit-settings.php',
	'APP_Listing_Process_New_Settings'  => dirname( __FILE__ ) . '/admin/class-listing-process-new-settings.php',
	'APP_Listing_Process_Renew_Settings' => dirname( __FILE__ ) . '/admin/class-listing-process-renew-settings.php',
	'APP_View_Process_Edit'             => dirname( __FILE__ ) . '/processes/class-view-process-edit.php',
	'APP_View_Process_New'              => dirname( __FILE__ ) . '/processes/class-view-process-new.php',
	'APP_View_Process_Renew'            => dirname( __FILE__ ) . '/processes/class-view-process-renew.php',
	'APP_View_Process_Upgrade'          => dirname( __FILE__ ) . '/processes/class-view-process-upgrade.php',
	'APP_View_Process'                  => dirname( __FILE__ ) . '/processes/class-view-process.php',
	'APP_Listing_Process_Emails'        => dirname( __FILE__ ) . '/processes/class-listing-process-emails.php',
	'APP_Listing_Step'                  => dirname( __FILE__ ) . '/steps/class-listing-step.php',
	'APP_Step_Activate_Order'           => dirname( __FILE__ ) . '/steps/class-step-activate-order.php',
	'APP_Step_Activate'                 => dirname( __FILE__ ) . '/steps/class-step-activate.php',
	'APP_Step_Create_Order'             => dirname( __FILE__ ) . '/steps/class-step-create-order.php',
	'APP_Step_Create_Upgrade_Order'     => dirname( __FILE__ ) . '/steps/class-step-create-upgrade-order.php',
	'APP_Step_Current_Plan'             => dirname( __FILE__ ) . '/steps/class-step-current-plan.php',
	'APP_Step_Edit_Info_New'            => dirname( __FILE__ ) . '/steps/class-step-edit-info-new.php',
	'APP_Step_Edit_Info_Optional'       => dirname( __FILE__ ) . '/steps/class-step-edit-info-optional.php',
	'APP_Step_Edit_Info'                => dirname( __FILE__ ) . '/steps/class-step-edit-info.php',
	'APP_Step_Gateway_Process'          => dirname( __FILE__ ) . '/steps/class-step-gateway-process.php',
	'APP_Step_Gateway_Select'           => dirname( __FILE__ ) . '/steps/class-step-gateway-select.php',
	'APP_Step_Moderate'                 => dirname( __FILE__ ) . '/steps/class-step-moderate.php',
	'APP_Step_Select_Plan'              => dirname( __FILE__ ) . '/steps/class-step-select-plan.php',
	'APP_Step_Upgrade'                  => dirname( __FILE__ ) . '/steps/class-step-upgrade.php',
	'APP_Date_Field_Type'               => dirname( __FILE__ ) . '/utils/admin/class-date-field-type.php',
	'APP_Listing_Period_Fields'         => dirname( __FILE__ ) . '/utils/admin/class-listing-period-fields.php',
	'APP_Widget_Action_Button'          => dirname( __FILE__ ) . '/widgets/class-widget-action-button.php',
	'APP_Widget_Process_Listing_Button' => dirname( __FILE__ ) . '/widgets/class-widget-process-listing-button.php',

	// Framework deps.
	'APP_Media_Manager_Metabox' => APP_FRAMEWORK_DIR . '/admin/class-media-manager-metabox.php',
	'APP_Settings'              => APP_FRAMEWORK_DIR . '/admin/class-settings.php',
	'APP_Taxonomy_Meta_Box'     => APP_FRAMEWORK_DIR . '/admin/class-taxonomy-meta-box.php',
	'APP_User_Meta_Box'         => APP_FRAMEWORK_DIR . '/admin/class-user-meta-box.php',
) );

APP_Listing_Autoload::register();

appthemes_load_files( dirname( __FILE__ ) . '/parts/', array(
	// Parts.
	'payments/load.php',
	'plans/load.php',
	'forms/load.php',
	'details/load.php',
	'expire/load.php',
) );

appthemes_load_files( dirname( __FILE__ ) . '/extra/', array(
	'addons/load.php',
	'plans/load.php',
	'recurring/load.php',
	'tax-limit/load.php',
	'tax-charge/load.php',
	'claim/load.php',
	'custom-forms/load.php',
	'anonymous/load.php',
	'search-index/load.php',
	'geocode/load.php',
) );
