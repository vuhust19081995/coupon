<?php
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 *
 * @package Clipper
 * @since 2.0.0
 */

if ( ! function_exists( 'clpr_setup_theme_support' ) ) :
/**
 * Runs the after setup theme action hook.
 *
 * @since 2.0.0
 */
function clpr_setup_theme_support() {
	global $clpr_options, $clipper;

	/**
	 * Enable support for post thumbnails on posts and pages.
	 *
	 * @since 1.0.0
	 */
	add_theme_support( 'post-thumbnails' );

	/**
	 * Set default post thumbnail size.
	 *
	 * Use a max width of 1200 and unlimited height for better responsive support.
	 *
	 * @since 1.0.0
	 * @since 2.0.0 Changed default size of 110
	 */
	set_post_thumbnail_size( 1200, 9999 );

	/**
	 * Add default posts and comments RSS feed links to head.
	 *
	 * @since 1.0.0
	 */
	add_theme_support( 'automatic-feed-links' );

	/**
	 * We want more control over the language file location.
	 *
	 * @todo Remove this once the function has been deprecated from theme-framework.
	 *
	 * @since 2.0.0
	 */
	remove_action( 'appthemes_theme_framework_loaded', 'appthemes_load_textdomain' );

	/**
	 * Add support for language files.
	 *
	 * Looks in WP_LANG_DIR . '/themes/' first otherwise
	 * For example: wp-content/languages/themes/clipper-de_DE.mo
	 *
	 * Otherwise defaults to: "wp-content/themes/clipper/languages/$domain . '-' . $locale.mo"
	 *
	 * @since 2.0.0
	 */
	load_theme_textdomain( APP_TD, get_template_directory() . '/languages' );

	/**
	 * AppThemes specific theme support items.
	 *
	 * @todo Add comment blocks.
	 */

	add_theme_support( 'app-versions', array(
		'update_page'     => 'admin.php?page =app-setup&firstrun =1',
		'current_version' => CLPR_VERSION,
		'option_key'      => 'clpr_version',
	) );

	add_theme_support( 'app-wrapping' );

	add_theme_support( 'app-search-index', array(
		'admin_page'           => true,
		'admin_top_level_page' => 'app-dashboard',
		'admin_sub_level_page' => 'app-system-info',
	) );

	add_theme_support( 'app-login', array(
		'login'         => 'tpl-login.php',
		'register'      => 'tpl-registration.php',
		'recover'       => 'tpl-password-recovery.php',
		'reset'         => 'tpl-password-reset.php',
		'redirect'      => $clpr_options->disable_wp_login,
		'settings_page' => 'admin.php?page=app-settings&tab=advanced',
	) );

	add_theme_support( 'app-feed', array(
		'post_type'          => APP_POST_TYPE,
		'blog_template'      => 'index.php',
		'alternate_feed_url' => $clpr_options->feedburner_url,
	) );

	add_theme_support( 'app-payments', array(
		'options'          => $clpr_options,
		'items_post_types' => APP_POST_TYPE,
	) );

	add_theme_support( 'app-price-format', array(
		'currency_default'    => $clpr_options->currency_code,
		'currency_identifier' => $clpr_options->currency_identifier,
		'currency_position'   => $clpr_options->currency_position,
		'thousands_separator' => $clpr_options->thousands_separator,
		'decimal_separator'   => $clpr_options->decimal_separator,
		'hide_decimals'       => false,
	) );

	add_theme_support( 'app-term-counts', array(
		'post_type'   => array( APP_POST_TYPE ),
		'post_status' => array( 'publish', 'unreliable' ),
		'taxonomy'    => array( APP_TAX_CAT, APP_TAX_TAG, APP_TAX_TYPE, APP_TAX_STORE ),
	) );

	add_theme_support( 'app-comment-counts' );

	add_theme_support( 'app-stats', array(
		'cache'       => 'today',
		'table_daily' => 'clpr_pop_daily',
		'table_total' => 'clpr_pop_total',
		'meta_daily'  => 'clpr_daily_count',
		'meta_total'  => 'clpr_total_count',
	) );

	add_theme_support( 'app-reports', array(
		'post_type'            => array( APP_POST_TYPE ),
		'options'              => $clpr_options,
		'admin_top_level_page' => 'app-dashboard',
		'admin_sub_level_page' => 'app-settings',
	) );

	add_theme_support( 'app-form-builder', array(
		'show_in_menu'       => true,
		'register_post_type' => true,
	) );

	add_theme_support( 'app-media-manager' );

	add_theme_support( 'app-addons-mp', array(
		'product' => array( 474 ),
	) );

	add_theme_support( 'app-require-updater', true );
}
endif;
add_action( 'after_setup_theme', 'clpr_setup_theme_support' );

/**
 * Sets the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 *
 * @since 2.0.0
 */
function clpr_content_width() {
	/**
	 * Filter the content width in pixels.
	 *
	 * @since 2.0.0
	 *
	 * @param int $width The content width in pixels.
	 */
	$GLOBALS['content_width'] = apply_filters( 'clpr_content_width', 600 );
}
add_action( 'after_setup_theme', 'clpr_content_width', 0 );

/**
 * Disable the front-end admin bar for non-admins.
 *
 * @since 2.0.0
 */
function clpr_disable_admin_bar() {
	/**
	 * Filter the allowed capability to view the front-end admin bar.
	 *
	 * @url https://codex.wordpress.org/Roles_and_Capabilities
	 *
	 * @since 2.0.0
	 *
	 * @param string $capability The minimum WordPress capability allowed.
	 *                           Defaults to 'manage_options'.
	 */
	$capability = apply_filters( 'clpr_disable_admin_bar', 'manage_options' );

	if ( ! current_user_can( $capability ) && ! is_admin() ) {
		add_filter( 'show_admin_bar', '__return_false' );
	}
}
add_action( 'after_setup_theme', 'clpr_disable_admin_bar' );
