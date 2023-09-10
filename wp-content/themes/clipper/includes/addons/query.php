<?php
/**
 * Addons query filter
 *
 * @package Components\Addons
 */

add_action( 'pre_get_posts', 'appthemes_addon_query_filter' );
add_action( 'pre_get_posts', 'appthemes_addon_on_top_query' );

function appthemes_addon_query_filter( $wp_query ){

	$addon_type = $wp_query->get( 'addon' );
	if ( ! $addon_type || ! appthemes_addon_exists( $addon_type ) ) {
		return;
	}

	$addon_info = appthemes_get_addon_info( $addon_type );
	$flag_key   = $addon_info['flag_key'];

	$wp_query->set( 'meta_key', $flag_key );
	$wp_query->set( 'meta_value', 1 );
}

function appthemes_addon_on_top_query( $wp_query ){

	$addon_type = $wp_query->get( 'addon_on_top' );

	if( ! $addon_type || ! appthemes_addon_exists( $addon_type ) ) {
		return;
	}

	$addon_info = appthemes_get_addon_info( $addon_type );
	$flag_key   = $addon_info['flag_key'];
	$meta_query = (array) $wp_query->get( 'meta_value', 1 );
	$meta_query = array_filter( $meta_query );
	$meta_query[] = array(
		'relation' => 'OR',
		array(
			'key'     => $flag_key,
			'compare' => 'NOT EXISTS',
		),
		array(
			'relation' => 'OR',
			array(
				'key'   => $flag_key,
				'value' => 1,
			),
			array(
				'key'     => $flag_key,
				'value'   => 1,
				'compare' => '!=',
			),
		),
	);

	$wp_query->set( 'meta_query', $meta_query );
	$wp_query->set( 'orderby', array( 'meta_value' => 'DESC', 'date' => 'DESC' ) );
}
