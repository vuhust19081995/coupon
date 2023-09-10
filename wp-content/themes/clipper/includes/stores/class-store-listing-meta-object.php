<?php
/**
 * Store Listing Taxonomy Meta Object.
 *
 * @package Clipper\Stores
 * @author  AppThemes
 * @since   2.0.0
 */

/**
 * Listing Post Meta Object.
 *
 * Provides a methods for data manipulation of taxonomy listing item.
 */
class CLPR_Store_Listing_Taxonomy_Meta_Object extends APP_Listing_Taxonomy_Meta_Object implements APP_Media_Manager_Meta_Type {

	/**
	 * Retrieves the listing item meta type.
	 *
	 * @return string The listing item meta type.
	 */
	public function get_meta_type() {
		return APP_TAX_STORE;
	}

	/**
	 * Retrieves metadata for a listing item.
	 *
	 * @param int    $item_id Item ID.
	 * @param string $key     Optional. The meta key to retrieve. If no key is
	 *                        provided, fetches all metadata for the item.
	 * @param bool   $single  Whether to return a single value. If false, an
	 *                        array of all values matching the `$item_id`/`$key`
	 *                        pair will be returned. Default: false.
	 *
	 * @return mixed If `$single` is false, an array of metadata values.
	 *               If `$single` is true, a single metadata value.
	 */
	function get_meta( $item_id, $key = '', $single = false ) {
		return get_metadata( APP_TAX_STORE, $item_id, $key, $single );
	}

	/**
	 * Update item meta field based on item ID.
	 *
	 * Use the $prev_value parameter to differentiate between meta fields with
	 * the same key and item ID.
	 *
	 * If the meta field for the item does not exist, it will be added.
	 *
	 * @param int    $item_id    Item ID.
	 * @param string $meta_key   Metadata key.
	 * @param mixed  $meta_value Metadata value. Must be serializable if
	 *                           non-scalar.
	 * @param mixed  $prev_value Optional. Previous value to check before
	 *                           removing. Default empty.
	 *
	 * @return int|bool Meta ID if the key didn't exist, true on successful update,
	 *                  false on failure.
	 */
	function update_meta( $item_id, $meta_key, $meta_value, $prev_value = '' ) {
		return update_metadata( APP_TAX_STORE, $item_id, $meta_key, $meta_value, $prev_value );
	}

	/**
	 * Remove metadata matching criteria from an item.
	 *
	 * You can match based on the key, or key and value. Removing based on key
	 * and value, will keep from removing duplicate metadata with the same key.
	 * It also allows removing all metadata matching key, if needed.
	 *
	 * @param int    $item_id    Item ID.
	 * @param string $meta_key   Metadata name.
	 * @param mixed  $meta_value Optional. Metadata value. Must be serializable
	 *                           if non-scalar. Default empty.
	 *
	 * @return bool True on success, false on failure.
	 */
	function delete_meta( $item_id, $meta_key, $meta_value = '' ) {
		return delete_metadata( APP_TAX_STORE, $item_id, $meta_key, $meta_value );
	}

	/**
	 * Handles media related post data.
	 *
	 * @param int   $item_id   Item ID to which attachments will be assigned.
	 * @param array $fields    (optional) The media fields that should be
	 *                         handled. Expects the fields index type: 'attachs'
	 *                         or 'embeds' (e.g:
	 *                             $fields = array(
	 *                                 'attach' => array( 'field1', 'field2' ),
	 *                                 'embeds' => array( 'field1', 'field2' )
	 *                             )
	 *                         ).
	 * @param bool  $duplicate (optional) Should the media files be duplicated,
	 *                         thus keeping the original file unattached.
	 *
	 * @return null|bool False if no media was processed, null otherwise
	 */
	function handle_media( $item_id, $fields = array(), $duplicate = false ) {
		return APP_Media_Manager::handle_media_upload( $item_id, APP_TAX_STORE, $fields, $duplicate );
	}

	/**
	 * Retrieves attachments associated with an object.
	 *
	 * @param int   $item_id The Item ID.
	 * @param array $args    Optional query parameters.
	 */
	public function get_object_attachments( $item_id, $args = array() ) {

		$args['post_type']   = 'attachment';
		$args['meta_query'][] = array(
			'key'   => '_app_attachment_parent_id',
			'value' => $item_id,
		);

		return new WP_Query( $args );
	}

}
