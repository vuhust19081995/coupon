<?php
/**
 * Theme functions file
 *
 * DO NOT MODIFY THIS FILE. Make a child theme instead: http://codex.wordpress.org/Child_Themes
 *
 * @package Clipper
 * @author  AppThemes
 * @since   Clipper 1.0
 */

// Constants.
define( 'CLPR_VERSION', '2.0.10' );
define( 'CLPR_DB_VERSION', '1358' );

// Should reflect the WordPress version in the .testenv file.
define( 'CLPR_WP_COMPATIBLE_VERSION', '4.7.4' );

define( 'APP_POST_TYPE', 'coupon' );
define( 'APP_TAX_CAT', 'coupon_category' );
define( 'APP_TAX_TAG', 'coupon_tag' );
define( 'APP_TAX_TYPE', 'coupon_type' );
define( 'APP_TAX_STORE', 'stores' );
define( 'APP_TAX_IMAGE', 'coupon_image' );
define( 'CLPR_ITEM_FEATURED', 'clpr_featured' );

define( 'CLPR_COUPON_LISTING_TYPE', 'coupon' );

define( 'APP_TD', 'clipper' );

// Listing Core Constants.
if ( ! defined( 'APP_LISTING_DIR_NAME' ) ) {
	define( 'APP_LISTING_DIR_NAME', 'core' );
}
if ( ! defined( 'APP_LISTING_DIR' ) ) {
	define( 'APP_LISTING_DIR', dirname( __FILE__ ) . '/includes/' . APP_LISTING_DIR_NAME );
}
if ( ! defined( 'APP_LISTING_URI' ) ) {
	define( 'APP_LISTING_URI', get_template_directory_uri() . '/includes/' . APP_LISTING_DIR_NAME );
}

// Listings type constants.
define( 'CLPR_LISTINGS_DIR', dirname( __FILE__ ) . '/includes/coupons' );
define( 'CLPR_LISTINGS_URI', get_template_directory_uri() . '/includes/coupons' );

if ( version_compare( $wp_version, CLPR_WP_COMPATIBLE_VERSION, '<' ) ) {
	add_action( 'admin_notices', 'clpr_display_version_warning' );
}

global $clpr_options;

// Legacy variables - some plugins rely on them.
$app_theme      = 'Clipper';
$app_abbr       = 'clpr';
$app_version    = CLPR_VERSION;
$app_db_version = CLPR_DB_VERSION;
$app_edition    = '';

// Framework.
require_once( dirname( __FILE__ ) . '/framework/load.php' );
require_once( dirname( __FILE__ ) . '/theme-framework/load.php' );
require_once( APP_FRAMEWORK_DIR . '/admin/class-meta-box.php' );

add_theme_support( 'app-listing', array( 'dirname' => 'core' ) );

APP_Mail_From::init();

// Define the custom fields used for custom search.
$app_custom_fields = array(
	'clpr_coupon_code',
	'clpr_expire_date',
	CLPR_ITEM_FEATURED,
	'clpr_id',
	'clpr_print_url',
);

// Define the db tables we use.
$app_db_tables = array(
	'clpr_pop_daily',
	'clpr_pop_total',
	'clpr_report',
	'clpr_report_comments',
	'clpr_search_recent',
	'clpr_search_total',
	'clpr_storesmeta',
	'clpr_votes',
	'clpr_votes_total',
);

foreach ( $app_db_tables as $app_db_table ) {
	scb_register_table( $app_db_table );
}
scb_register_table( 'app_pop_daily', 'clpr_pop_daily' );
scb_register_table( 'app_pop_total', 'clpr_pop_total' );
scb_register_table( 'storesmeta', 'clpr_storesmeta' );

// Other Dependencies.
$load_files = array(
	'checkout/load.php',
	'payments/load.php',
	'addons/load.php',
	'reports/load.php',
	'widgets/load.php',
	'stats/load.php',
	'recaptcha/load.php',
	'open-graph/open-graph.php',
	'search-index/load.php',
	'custom-forms/form-builder.php',
	'core/load.php',
	'coupons/load.php',
	'stores/load.php',
	'users/load.php',
	'options.php',
	'appthemes-functions.php',
	'actions.php',
	'core.php',
	'comments.php',
	'custom-header.php',
	'customizer/class-customizer.php',
	'dashboard.php',
	'deprecated.php',
	'enqueue.php',
	'emails.php',
	'functions.php',
	'helpers.php',
	'hooks.php',
	'links.php',
	'payments.php',
	'printable-coupon.php',
	'profile.php',
	'search.php',
	'security.php',
	'sidebars.php',
	'stats.php',
	'stores.php',
	'template-tags.php',
	'theme-support.php',
	'users.php',
	'views.php',
	'voting.php',
	'widgets.php',
);
appthemes_load_files( dirname( __FILE__ ) . '/includes/', $load_files );

// Listing type init.
global $clipper;
$clipper  = new stdClass();
$cbuilder = new CLPR_Coupon_Listing_Builder();
$sbuilder = new CLPR_Store_Listing_Builder();
$ubuilder = new CLPR_User_Listing_Builder();
$director = new APP_Listing_Director();
$clipper->{APP_TAX_STORE} = $director->build( APP_TAX_STORE, $sbuilder );
$clipper->{APP_POST_TYPE} = $director->build( APP_POST_TYPE, $cbuilder );
$clipper->user            = $director->build( 'user', $ubuilder );

// Load Classes.
$load_classes = array(
	'CLPR_Blog_Archive',
	'CLPR_Coupons_Home',
	'CLPR_Coupon_Categories',
	'CLPR_Coupon_Stores',
	'CLPR_Coupon_Single',
	'CLPR_Open_Graph',
	'CLPR_User_Dashboard',
	'CLPR_User_Orders',
	'CLPR_User_Profile',
	// Widgets
	'CLPR_Widget_Facebook',
	'CLPR_Widget_Subscribe',
	'CLPR_Widget_Share_Coupon',
	'CLPR_Widget_Popular_Coupons',
	'CLPR_Widget_Expiring_Coupons',
	'CLPR_Widget_Related_Coupons',
	'CLPR_Widget_Top_Coupons_Today',
	'CLPR_Widget_Top_Coupons_Overall',
	'CLPR_Widget_Sub_Stores',
	'CLPR_Widget_Popular_Stores',
	'CLPR_Widget_Featured_Stores',
	'CLPR_Widget_Coupon_Categories',
	'CLPR_Widget_Coupon_Sub_Categories',
	'CLPR_Widget_Coupons_Tag_Cloud',
	'CLPR_Widget_Popular_Searches',
	'CLPR_Widget_Tabbed_Blog',
	//'CLPR_Widget_Contact_Footer',
);
appthemes_add_instance( $load_classes );


// Admin only.
if ( is_admin() ) {
	require_once( APP_FRAMEWORK_DIR . '/admin/importer.php' );

	$load_files = array(
		'class-term-description.php',
		'admin.php',
		'dashboard.php',
		'enqueue.php',
		'install.php',
		'importer.php',
		'listing-single.php',
		'listing-list.php',
		'post-status.php',
		'settings.php',
		'class-admin-setup.php',
		'stores-single.php',
		'stores-list.php',
		'system-info.php',
		'users.php',
		'addons-mp/load.php',
	);
	appthemes_load_files( dirname( __FILE__ ) . '/includes/admin/', $load_files );

	$load_classes = array(
		'CLPR_Theme_Dashboard',
		'CLPR_Theme_Settings_General' => $clpr_options,
		'CLPR_Theme_Settings_Emails' => $clpr_options,
		'CLPR_Admin_Setup' => $clpr_options,
		'CLPR_Theme_System_Info',
		// Metaboxes
		'CLPR_Listing_Info_Metabox',
		'CLPR_Listing_Author_Metabox',
		'CLPR_Listing_Publish_Moderation',
		// Stores
		'CLPR_Store_Term_Description',
	);
	appthemes_add_instance( $load_classes );
}

// Integrations
if ( defined( 'STARSTRUCK_VERSION' ) ) {
	appthemes_load_files( dirname( __FILE__ ) . '/includes/integrations/starstruck/', array( 'class-starstruck.php' ) );
	new CLPR_StarStruck();
}

// Front-end only.
if ( ! is_admin() ) {
	clpr_load_all_page_templates();
}

// Constants.
define( 'CLPR_COUPON_REDIRECT_BASE_URL', trailingslashit( $clpr_options->coupon_redirect_base_url ) );
define( 'CLPR_STORE_REDIRECT_BASE_URL', trailingslashit( $clpr_options->store_redirect_base_url ) );
define( 'CLPR_DASHBOARD_URL', get_permalink( CLPR_User_Dashboard::get_id() ) );
define( 'CLPR_ORDERS_URL', get_permalink( CLPR_User_Orders::get_id() ) );
define( 'CLPR_PROFILE_URL', get_permalink( CLPR_User_Profile::get_id() ) );

// Ajax.
add_action( 'wp_ajax_nopriv_ajax-thumbsup', 'clpr_vote_update' );
add_action( 'wp_ajax_ajax-thumbsup', 'clpr_vote_update' );

add_action( 'wp_ajax_nopriv_comment-form', 'clpr_comment_form' );
add_action( 'wp_ajax_comment-form', 'clpr_comment_form' );

add_action( 'wp_ajax_nopriv_post-comment', 'clpr_post_comment_ajax' );
add_action( 'wp_ajax_post-comment', 'clpr_post_comment_ajax' );

add_action( 'wp_ajax_nopriv_coupon-code-popup', 'clpr_coupon_code_popup' );
add_action( 'wp_ajax_coupon-code-popup', 'clpr_coupon_code_popup' );

add_action( 'wp_ajax_ajax-resetvotes', 'clpr_reset_coupon_votes_ajax' );


// Image sizes.
add_image_size( 'thumb-small', 30 ); // used in the sidebar widget
add_image_size( 'thumb-med', 75 ); // used on the admin coupon list view
add_image_size( 'thumb-store', 150 ); // used on the store page
add_image_size( 'thumb-featured', 160 ); // used in featured coupons slider
add_image_size( 'thumb-large', 180 );
add_image_size( 'thumb-large-preview', 250 ); // used on the admin edit store page

appthemes_init();

function clpr_display_version_warning(){
	global $wp_version;

	$message = sprintf( __( 'Clipper version %1$s is not compatible with WordPress version %2$s. Correct work is not guaranteed. Please upgrade the WordPress at least to version %3$s or downgrade the Clipper theme.', APP_TD ), CLPR_VERSION, $wp_version, CLPR_WP_COMPATIBLE_VERSION );
	echo '<div class="error fade"><p>' . $message .'</p></div>';
}
