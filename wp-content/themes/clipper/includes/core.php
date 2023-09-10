<?php
/**
 * Core functions.
 *
 * @package Clipper\Core
 * @author  AppThemes
 * @since   Clipper 1.5.0
 */

/**
 * Register custom post type and post status for coupons.
 *
 * @since 1.0.0
 *
 * @return void
 */
function clpr_register_post_types() {
	// Register post status for unreliable coupons.
	$status_args = array(
		'label'                     => __( 'Unreliable', APP_TD ),
		'label_count'               => _n_noop( 'Unreliable <span class="count">(%s)</span>', 'Unreliable <span class="count">(%s)</span>', APP_TD ),
		'public'                    => true,
		'_builtin'                  => true,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'capability_type'           => APP_POST_TYPE,
	);

	register_post_status( 'unreliable', $status_args );
}
add_action( 'init', 'clpr_register_post_types', 9 );

/**
 * Callback for WordPress 'post_updated_messages' filter.
 *
 * @since 2.0.0
 *
 * @param  array $messages The array of messages.
 * @return array $messages The array of messages.
 */
function clpr_post_updated_messages( $messages ) {
	global $post, $post_ID;

	$messages[ APP_POST_TYPE ] = array(
		0  => '', // Unused. Messages start at index 1.
		1  => sprintf( __( 'Coupon updated. <a href="%s">View coupon</a>', APP_TD ), esc_url( get_permalink( $post_ID ) ) ),
		2  => __( 'Coupon updated.', APP_TD ),
		3  => __( 'Coupon deleted.', APP_TD ),
		4  => __( 'Coupon updated.', APP_TD ),
		5  => isset( $_GET['revision'] ) ? sprintf( __( 'Coupon restored to revision from %s', APP_TD ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
		6  => sprintf( __( 'Coupon published. <a href="%s">View coupon</a>', APP_TD ), esc_url( get_permalink( $post_ID ) ) ),
		7  => __( 'Coupon saved.', APP_TD ),
		8  => sprintf( __( 'Coupon submitted. <a target="_blank" href="%s">Preview coupon</a>', APP_TD ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		9  => sprintf( __( 'Coupon scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview coupon</a>', APP_TD ), date_i18n( __( 'M j, Y @ G:i', APP_TD ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
		10 => sprintf( __( 'Coupon draft updated. <a target="_blank" href="%s">Preview coupon</a>', APP_TD ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
	);

	return $messages;
}
add_filter( 'post_updated_messages', 'clpr_post_updated_messages' );

/**
 * Register menus.
 *
 * @return void
 */
function clpr_register_menus() {

	register_nav_menu( 'primary', __( 'Primary Navigation', APP_TD ) );
	register_nav_menu( 'secondary', __( 'Footer Navigation', APP_TD ) );
}
add_action( 'after_setup_theme', 'clpr_register_menus' );

/**
 * Build Search Index for past items.
 *
 * @since 1.0.0
 *
 * @return void
 */
function _clpr_setup_build_search_index() {

	if ( ! current_theme_supports( 'app-search-index' ) ) {
		return;
	}

	appthemes_add_instance( 'APP_Build_Search_Index' );
}
add_action( 'init', '_clpr_setup_build_search_index', 100 );

/**
 * Register items to index, post types, taxonomies, and custom fields.
 *
 * @since 1.0.0
 *
 * @return void
 */
function clpr_register_search_index_items() {

	if ( ! current_theme_supports( 'app-search-index' ) || isset( $_GET['firstrun'] ) ) {
		return;
	}

	// Blog posts
	$post_index_args = array(
		'taxonomies' => array( 'category', 'post_tag' ),
	);
	APP_Search_Index::register( 'post', $post_index_args );

	// Pages
	APP_Search_Index::register( 'page' );
}
add_action( 'init', 'clpr_register_search_index_items', 10 );

/**
 * Whether the Search Index is ready to use.
 *
 * @since 1.0.0
 *
 * @return bool
 */
function clpr_search_index_enabled() {

	if ( ! current_theme_supports( 'app-search-index' ) ) {
		return false;
	}

	return apply_filters( 'clpr_search_index_enabled', appthemes_get_search_index_status() );
}

/**
 * Customize the output of menus for Foundation top bar
 *
 * @package Clipper
 * @since 2.0.2
 */
class CLPR_Topbar_Menu_Walker extends Walker_Nav_Menu {

	/**
	 * Starts the list before the elements are added.
	 *
	 * @see Walker_Nav_Menu::start_lvl()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param int    $depth  Depth of page. Used for padding.
	 * @param array  $args   Not used.
	 */
	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent = str_repeat( "\t", $depth );
		$output .= "\n$indent<ul class=\"vertical menu is-dropdown-submenu\">\n";
	}
}