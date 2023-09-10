<?php
/**
 * Listing User Meta Object.
 *
 * @package Listing\Meta
 * @author  AppThemes
 * @since   Listing 2.0
 */

/**
 * Listing Post Meta Object.
 *
 * Provides a methods for data manipulation of user listing item.
 */
class APP_Listing_User_Meta_Object extends APP_Listing_Meta_Object {

	/**
	 * Retrieves the listing item meta type.
	 *
	 * @return string The listing item meta type.
	 */
	public function get_meta_type() {
		return 'user';
	}

	/**
	 * Retrieves the listing type by given item id.
	 *
	 * @param string $item_id The item id.
	 *
	 * @return string The listing item type. Empty string on failure.
	 */
	public function get_type( $item_id ) {
		return 'user';
	}

	/**
	 * Retrieves the object all user labels out of a user object.
	 *
	 * @param string $type The type of item (not used).
	 *
	 * @return object Object with all the labels as member variables.
	 */
	public function get_labels( $type = '' ) {
		return (object) array(
			'name' => __( 'Users', APP_TD ),
		);
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
		return get_user_meta( $item_id, $key, $single );
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
		return update_user_meta( $item_id, $meta_key, $meta_value, $prev_value );
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
		return delete_user_meta( $item_id, $meta_key, $meta_value );
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
		return APP_Media_Manager::handle_media_upload( $item_id, 'user', $fields, $duplicate );
	}

	/**
	 * Retrieves item data given a item ID.
	 *
	 * @param int|WP_User $item   Item ID or WordPress User object.
	 * @param string      $output Optional. The required return type. One of
	 *                            OBJECT, ARRAY_A, or ARRAY_N, which correspond
	 *                            to a item object, an associative array, or a
	 *                            numeric array, respectively. Default OBJECT.
	 * @param string      $filter Optional. Type of filter to apply. Accepts
	 *                            'raw', 'edit', 'db', or 'display'.
	 *                            Default 'raw'.
	 *
	 * @return WP_User|array|null Type corresponding to $output on success or
	 *                            null on failure. When $output is OBJECT, an
	 *                            appropriate WP instance is returned.
	 */
	function get_item( $item, $output = OBJECT, $filter = 'raw' ) {

		if ( $item instanceof WP_User ) {
			$item_id = $item->ID;
		} elseif ( is_null( $item ) ) {
			$item_id = $this->get_item_id( get_queried_object() );
		} else {
			$item_id = $item;
		}

		$item = get_user_by( 'ID', $item_id );

		if ( ARRAY_A === $output ) {
			$item = $item->to_array();
		} elseif ( ARRAY_N === $output ) {
			$item = array_values( $item->to_array() );
		}

		return $item;
	}

	/**
	 * Retrieves item ID given a WordPress item object.
	 *
	 * @param WP_User $item Item Object.
	 *
	 * @return int|null The item ID.
	 */
	function get_item_id( $item ) {
		if ( $item instanceof WP_User ) {
			return $item->ID;
		}
	}

	/**
	 * Retrieves the list of core fields that used by WordPress to insert/update
	 * an item.
	 *
	 * @see wp_insert_user()
	 * @see _get_additional_user_keys()
	 *
	 * @return array Core fields names array.
	 */
	function get_core_fields() {
		return array_merge( array(
			'ID',
			'user_pass',
			'user_login',
			'user_nicename',
			'user_url',
			'user_email',
			'display_name',
			'user_registered',
			'role',
		), _get_additional_user_keys( null ) );
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
		$defaults = array(
			'ID' => $item_id,
		);

		$args = wp_parse_args( $args, $defaults );

		return wp_update_user( $args );
	}
}
