<?php
/**
 * Listing Taxonomy Meta Object.
 *
 * @package Listing\Meta
 * @author  AppThemes
 * @since   Listing 2.0
 */

/**
 * Listing Post Meta Object.
 *
 * Provides a methods for data manipulation of taxonomy listing item.
 */
class APP_Listing_Taxonomy_Meta_Object extends APP_Listing_Meta_Object {

	/**
	 * Retrieves the listing item meta type.
	 *
	 * @return string The listing item meta type.
	 */
	public function get_meta_type() {
		return 'term';
	}

	/**
	 * Retrieves the listing type by given item id.
	 *
	 * @param string $item_id The item id.
	 *
	 * @return string The listing item type. Empty string on failure.
	 */
	public function get_type( $item_id ) {
		$item = $this->get_item( $item_id );
		$type = '';

		if ( $item && ! is_wp_error( $item ) ) {
			$type = $item->taxonomy;
		}

		return $type;
	}

	/**
	 * Retrieves the object all taxonomy labels out of a taxonomy object.
	 *
	 * @param string $type The type of item (taxonomy name).
	 *
	 * @return object Object with all the labels as member variables.
	 */
	public function get_labels( $type = '' ) {
		$tax = get_taxonomy( $type );
		return get_taxonomy_labels( $tax );
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
		return get_term_meta( $item_id, $key, $single );
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
		return update_term_meta( $item_id, $meta_key, $meta_value, $prev_value );
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
		return delete_term_meta( $item_id, $meta_key, $meta_value );
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
		return APP_Media_Manager::handle_media_upload( $item_id, 'term', $fields, $duplicate );
	}

	/**
	 * Retrieves item data given a item ID.
	 *
	 * @param int|WP_Term $item   Item ID or WordPress Term object.
	 * @param string      $output Optional. The required return type. One of
	 *                            OBJECT, ARRAY_A, or ARRAY_N, which correspond
	 *                            to a item object, an associative array, or a
	 *                            numeric array, respectively. Default OBJECT.
	 * @param string      $filter Optional. Type of filter to apply. Accepts
	 *                            'raw', 'edit', 'db', or 'display'.
	 *                            Default 'raw'.
	 *
	 * @return WP_Term|array|null Type corresponding to $output on success or
	 *                            null on failure. When $output is OBJECT, an
	 *                            appropriate WP instance is returned.
	 */
	function get_item( $item, $output = OBJECT, $filter = 'raw' ) {
		if ( ! $item ) {
			$item = $this->get_item_id( get_queried_object() );
		}

		return get_term( $item, '', $output, $filter );
	}

	/**
	 * Retrieves item ID given a WordPress item object.
	 *
	 * @param WP_Term $item Item Object.
	 *
	 * @return int|null The item ID.
	 */
	function get_item_id( $item ) {
		if ( $item instanceof WP_Term ) {
			return $item->term_id;
		}
	}

	/**
	 * Retrieves the list of core fields that used by WordPress to insert/update
	 * an item.
	 *
	 * @see wp_insert_term()
	 *
	 * @return array Core fields names array.
	 */
	function get_core_fields() {
		return array(
			'name',
			'taxonomy',
			'alias_of',
			'description',
			'parent',
			'slug',
		);
	}

	/**
	 * Updates the listing item with given data.
	 *
	 * @param int  $item_id The item id.
	 * @param bool $args    Arguments to be passed to WordPress update item
	 *                      function.
	 *
	 * @return int|WP_Error The item ID on success or WP_Error on failure.
	 */
	function update_item( $item_id, $args = array() ) {
		$taxonomy = $this->get_type( $item_id );
		$term_ids = wp_update_term( $item_id, $taxonomy, $args );

		if ( ! empty( $term_ids['term_id'] ) ) {
			$term_ids = $term_ids['term_id'];
		}
		return $term_ids;
	}

}
