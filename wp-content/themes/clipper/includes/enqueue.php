<?php
/**
 * Enqueue of scripts and styles.
 *
 * @package Clipper\Enqueue
 * @author  AppThemes
 * @since   Clipper 1.0
 */


/**
 * Enqueue scripts.
 *
 * @return void
 */
function clpr_load_scripts() {
	global $clpr_options;

	$min = clpr_get_minified_suffix();

	// Set the assets path so we don't repeat ourselves.
	$assets_path = get_template_directory_uri() . '/assets';

	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-autocomplete' );

	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( 'jquery-ui-datepicker-lang' );

	wp_enqueue_script( 'jqueryeasing', get_template_directory_uri() . '/includes/js/easing.js', array( 'jquery' ), '1.3' );

	// Eventually deprecate and replace with Slick.
	wp_enqueue_script( 'jcarousellite', get_template_directory_uri() . '/includes/js/jcarousellite.min.js', array( 'jquery' ), '1.8.5' );

	// Load the Slick carousel script.
	wp_enqueue_script( 'slick', $assets_path . "/js/lib/slick/slick{$min}.js", array( 'jquery' ), '1.7.1', true );

	// Load the copy to clipboard on click script.
	wp_enqueue_script( 'clipboardjs', get_template_directory_uri() . '/includes/js/clipboard.min.js', array( 'jquery' ), '1.5.15' );

	/**
	 * Core Scripts.
	 */

	// Load the Foundation scripts.
	wp_enqueue_script( 'foundation', $assets_path . "/js/lib/foundation/foundation{$min}.js", array( 'jquery' ), '6.4.3', true );
	wp_enqueue_script( 'foundation-motion-ui', $assets_path . "/js/lib/foundation/motion-ui{$min}.js", array( 'jquery', 'foundation' ), '1.2.2', true );

	// Load the main theme scripts.
	wp_enqueue_script( 'theme-scripts', $assets_path . "/js/theme-scripts{$min}.js", array( 'jquery' ), CLPR_VERSION, true );


	wp_enqueue_script( 'colorbox' );
	wp_enqueue_script( 'validate' );
	wp_enqueue_script( 'validate-lang' );

	// used to convert header menu into select list on mobile devices
	wp_enqueue_script( 'tinynav', get_template_directory_uri() . '/includes/js/jquery.tinynav.min.js', array( 'jquery' ), '1.2' );

	// adds touch events to jQuery UI on mobile devices
	if ( wp_is_mobile() ) {
		wp_enqueue_script( 'jquery-touch-punch' );
	}

	// only load the general.js if available in child theme
	if ( file_exists( get_stylesheet_directory() . '/general.js' ) ) {
		wp_enqueue_script( 'general', get_stylesheet_directory_uri() . '/general.js', array( 'jquery' ), '1.0' );
	}

	// Comment reply script for threaded comments.
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	/* Script variables */
	$params = array(
		'app_tax_store'             => APP_TAX_STORE,
		'ajax_url'                  => admin_url( 'admin-ajax.php', 'relative' ),
		'templateurl'               => get_template_directory_uri(),
		'is_mobile'                 => wp_is_mobile(),
		'text_copied'               => __( 'Copied', APP_TD ),
		'text_mobile_navigation'    => __( 'Navigation', APP_TD ),
		'text_before_delete_coupon' => __( 'Are you sure you want to delete this coupon?', APP_TD ),
		'text_sent_email'           => __( 'Your email has been sent!', APP_TD ),
		'text_shared_email_success' => __( 'This coupon was successfully shared with', APP_TD ),
		'text_shared_email_failed'  => __( 'There was a problem sharing this coupon with', APP_TD ),
		'direct_links'              => $clpr_options->direct_links,
		'coupon_code_hide'          => $clpr_options->coupon_code_hide,
	);
	wp_localize_script( 'theme-scripts', 'clipper_params', $params );

	// Load dashicons on these pages since we use the password show/hide button.
//	if ( in_array( basename( get_page_template() ), array( 'tpl-registration.php', 'tpl-profile.php', 'tpl-password-reset.php' ) ) ) {
	    wp_enqueue_style( 'dashicons' );
//	}

	// Enqueue reports scripts.
	appthemes_reports_enqueue_scripts();

	$ptype = APP_POST_TYPE;
	if ( is_page_template( array( "process-{$ptype}-edit.php", "process-{$ptype}-new.php", "process-{$ptype}-renew.php" ) ) ) {
		clpr_load_form_scripts();
	}

}
add_action( 'wp_enqueue_scripts', 'clpr_load_scripts' );


/**
 * Enqueue submission form scripts.
 *
 * @return void
 */
function clpr_load_form_scripts() {
	$checkout   = appthemes_get_checkout();
	$listing_id = 0;

	if ( $checkout ) {
		$listing_id = (int) $checkout->get_data( 'listing_id' );
	}

	appthemes_enqueue_media_manager( array(
		'post_id' => $listing_id,
	) );

	wp_enqueue_script(
		'app-listing-validate',
		CLPR_LISTINGS_URI . '/scripts/app.listing.validate.js',
		array( 'validate-lang', 'jquery-ui-sortable' ),
		CLPR_VERSION,
		true
	);

	wp_localize_script(
		'app-listing-validate',
		'CLPR_i18n',
		array(
			'category_limit' => __( 'You have exceeded the category selection quantity limit ({0}).', APP_TD ),
		)
	);
}


/**
 * Enqueue styles.
 *
 * @return void
 */
function clpr_load_styles() {
	global $clpr_options;

	$min = clpr_get_minified_suffix();

	// Set the assets path so we don't repeat ourselves.
	$assets_path = get_template_directory_uri() . '/assets';

	// Load the core foundation files.
	wp_enqueue_style( 'foundation', get_template_directory_uri() . "/assets/css/foundation{$min}.css", array(), '6.4.3' );

	// Load the Slick carousel stylesheet.
	wp_enqueue_style( 'slick', $assets_path . "/js/lib/slick/slick{$min}.css", array(), '1.7.1' );

	// Load the Slick theme carousel stylesheet.
	wp_enqueue_style( 'slick-theme', $assets_path . "/js/lib/slick/slick-theme{$min}.css", array(), '1.7.1' );

	// Load the font awesome toolkit from framework.
	wp_enqueue_style( 'font-awesome' );

	// Load the theme stylesheet.
	wp_enqueue_style( 'at-main', $assets_path . "/css/style{$min}.css", array(), CLPR_VERSION );

	// Load the rtl theme stylesheet.
	wp_style_add_data( 'at-main', 'rtl', 'replace' );

	/**
	 * We are replacing the existing file, and we use a
	 * suffix like `-min` so we need to let core know about it.
	 * That way, it can keep that suffix after the added `-rtl`.
	 * Without this code, we get 404 with this style.min-rtl.css.
	 */
	wp_style_add_data( 'at-main', 'suffix', $min );

	// Disable color scheme stylesheet if there's a child theme.
	if ( ! is_child_theme() ) {
		if ( $clpr_options->stylesheet ) {
			wp_enqueue_style( 'at-color', get_template_directory_uri() . '/styles/' . $clpr_options->stylesheet );
		} else {
			wp_enqueue_style( 'at-color', get_template_directory_uri() . '/styles/red.css' );
		}
	}

	// Include the custom stylesheet for current theme/child theme.
	// @todo this should be deprecated as we don't want to encourage custom styles this way.
	if ( file_exists( get_stylesheet_directory() . '/styles/custom.css' ) ) {
		wp_enqueue_style( 'at-custom', get_stylesheet_directory_uri() . '/styles/custom.css' );
	}

	wp_enqueue_style( 'colorbox' );

	// Use a pretty datepicker calendar when editing coupons.
	wp_enqueue_style( 'jquery-ui-style' );
	wp_enqueue_style( 'wp-jquery-ui-datepicker', APP_FRAMEWORK_URI . '/styles/datepicker/datepicker.css' );

	// enqueue reports styles
	appthemes_reports_enqueue_styles();

}
add_action( 'wp_enqueue_scripts', 'clpr_load_styles' );

/**
 * Load up the admin scripts and stylesheets.
 *
 * @since 2.0.0
 */
function clpr_scripts_and_styles_admin() {

	$min = clpr_get_minified_suffix();

	// Load the theme admin scripts.
	//wp_enqueue_script( 'theme-admin-scripts', get_template_directory_uri() . "/assets/js/theme-admin-scripts{$min}.js", array( 'jquery' ), VA_VERSION, true );

	// Load the theme admin stylesheet.
	wp_enqueue_style( 'theme-styles-admin', get_template_directory_uri() . "/assets/css/style-admin{$min}.css", array(), CLPR_VERSION );

	// Use a pretty datepicker calendar when editing coupons.
	wp_enqueue_style( 'jquery-ui-style' );
	wp_enqueue_style( 'wp-jquery-ui-datepicker', APP_FRAMEWORK_URI . '/styles/datepicker/datepicker.css' );

}
add_action( 'admin_enqueue_scripts', 'clpr_scripts_and_styles_admin' );

/**
 * Retrieves the '.min' suffix for CSS/JS files on a live site considering SCRIPT_DEBUG constant.
 *
 * @since 2.0.0
 */
function clpr_get_minified_suffix() {
	return ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
}
