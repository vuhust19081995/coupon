<?php
/**
 * Stores taxonomy related functions.
 *
 * @package Clipper\Stores
 * @author  AppThemes
 * @since   Clipper 1.6
 */


add_action( 'appthemes_advertise_content', 'clpr_adbox_taxonomy_page' );

add_action( 'pending_to_publish', 'clpr_do_publish_post', 10, 1 );


/**
 * Returns an array of hidden stores IDs.
 *
 * @return array
 */
function clpr_hidden_stores() {
	global $wpdb;

	$hidden_stores = get_transient( 'clpr_hidden_stores_ids' );

	if ( empty( $hidden_stores ) || ! is_array( $hidden_stores ) ) {
		// get ids of all hidden stores
		$hidden_stores_query = "SELECT $wpdb->clpr_storesmeta.stores_id FROM $wpdb->clpr_storesmeta WHERE $wpdb->clpr_storesmeta.meta_key = %s AND $wpdb->clpr_storesmeta.meta_value = %s";
		$hidden_stores = $wpdb->get_col( $wpdb->prepare( $hidden_stores_query, 'clpr_store_active', 'no' ) );
		set_transient( 'clpr_hidden_stores_ids', $hidden_stores, 60*60*24 ); // cache for 1 day
	}

	return $hidden_stores;
}


/**
 * Returns an array of featured stores IDs.
 *
 * @return array
 */
function clpr_featured_stores() {
	global $wpdb;

	$featured_stores = get_transient( 'clpr_featured_stores_ids' );

	if ( empty( $featured_stores ) || ! is_array( $featured_stores ) ) {
		// get ids of all featured stores
		$featured_stores_query = "SELECT $wpdb->clpr_storesmeta.stores_id FROM $wpdb->clpr_storesmeta WHERE $wpdb->clpr_storesmeta.meta_key = %s AND $wpdb->clpr_storesmeta.meta_value = %s";
		$featured_stores = $wpdb->get_col( $wpdb->prepare( $featured_stores_query, 'clpr_store_featured', '1' ) );
		set_transient( 'clpr_featured_stores_ids', $featured_stores, 60*60*24 ); // cache for 1 day
	}

	return $featured_stores;
}

/**
 * Retrieve store meta field for a store.
 *
 * @since 1.5
 *
 * @param int $store_id Store ID.
 * @param string $key (optional) The meta key to retrieve. By default, returns data for all keys.
 * @param bool $single (optional) Whether to return a single value.
 *
 * @return mixed Will be an array if $single is false. Will be value of meta data field if $single is true.
 */
function clpr_get_store_meta( $store_id, $key = '', $single = false ) {
	global $clipper;
	return $clipper->{APP_TAX_STORE}->meta->get_meta( $store_id, $key, $single );
}


/**
 * Update store meta field based on store ID.
 *
 * @since 1.5
 *
 * @param int $store_id Store ID.
 * @param string $meta_key Metadata key.
 * @param mixed $meta_value Metadata value. Must be serializable if non-scalar.
 * @param mixed $prev_value (optional) Previous value to check before removing.
 *
 * @return bool True on success, false on failure.
 */
function clpr_update_store_meta( $store_id, $meta_key, $meta_value, $prev_value = '' ) {
	global $clipper;
	return $clipper->{APP_TAX_STORE}->meta->update_meta( $store_id, $meta_key, $meta_value, $prev_value );
}


/**
 * Adds advertise to taxonomy store page.
 * @since 1.4
 *
 * @return void
 */
function clpr_adbox_taxonomy_page() {

	if ( ! is_tax( array( APP_TAX_STORE ) ) ) {
		return;
	}

	clpr_adbox_336x280();
}


/**
 * Activates store when a related coupon has been published.
 *
 * @param object $post
 *
 * @return void
 */
function clpr_do_publish_post( $post ) {
	if ( ! $post || $post->post_type != APP_POST_TYPE ) {
		return;
	}

	// get the store id
	$store_id = appthemes_get_custom_taxonomy( $post->ID, APP_TAX_STORE, 'term_id' );

	// if the coupon has been approved then change the store to active
	if ( $store_id ) {
		clpr_update_store_meta( $store_id, 'clpr_store_active', 'yes' );
	}
}


