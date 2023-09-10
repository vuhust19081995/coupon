<?php
/**
 * Listing Meta Object.
 *
 * @package Listing\Meta
 * @author  AppThemes
 * @since   Listing 2.0
 */

/**
 * Listing Meta Object abstract class.
 *
 * Provides a methods for data manipulation of an abstract listing item.
 */
abstract class APP_Listing_Meta_Object {

	/**
	 * Retrieves the listing item meta type.
	 *
	 * @return string The listing item meta type.
	 */
	abstract public function get_meta_type();

	/**
	 * Retrieves the listing type by given item id.
	 *
	 * @param string $item_id The item id.
	 *
	 * @return string The listing item type. Empty string on failure.
	 */
	abstract public function get_type( $item_id );

	/**
	 * Retrieves the object all listing meta type labels out of a meta type
	 * object.
	 *
	 * @param string $type The type of item (i.e post type or taxonomy name).
	 */
	abstract public function get_labels( $type );

	/**
	 * Retrieves metadata for a listing item.
	 *
	 * @param int    $item_id Item ID.
	 * @param string $key     Optional. The meta key to retrieve. If no key is
	 *                        provided, fetches all metadata for the term.
	 * @param bool   $single  Whether to return a single value. If false, an
	 *                        array of all values matching the `$item_id`/`$key`
	 *                        pair will be returned. Default: false.
	 *
	 * @return mixed If `$single` is false, an array of metadata values.
	 *               If `$single` is true, a single metadata value.
	 */
	abstract function get_meta( $item_id, $key = '', $single = false );

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
	 * @return int|bool Meta ID if the key didn't exist, true on successful
	 *                  update, false on failure.
	 */
	abstract function update_meta( $item_id, $meta_key, $meta_value, $prev_value = '' );

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
	abstract function delete_meta( $item_id, $meta_key, $meta_value = '' );

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
	abstract function handle_media( $item_id, $fields = array(), $duplicate = false );

	/**
	 * Retrieves item data given a item ID or WordPress item object.
	 *
	 * @param int|object $item    Item ID or Object.
	 * @param string     $output  Optional. The required return type. One of
	 *                            OBJECT, ARRAY_A, or ARRAY_N, which correspond
	 *                            to a item object, an associative array, or a
	 *                            numeric array, respectively. Default OBJECT.
	 * @param string     $filter  Optional. Type of filter to apply. Accepts
	 *                            'raw', 'edit', 'db', or 'display'.
	 *                            Default 'raw'.
	 *
	 * @return object|array|null Type corresponding to $output on success or
	 *                           null on failure. When $output is OBJECT, an
	 *                           appropriate WP instance is returned.
	 */
	abstract function get_item( $item, $output = OBJECT, $filter = 'raw' );

	/**
	 * Retrieves item ID given a WordPress item object.
	 *
	 * @param object $item Item Object.
	 *
	 * @return int|null The item ID.
	 */
	abstract function get_item_id( $item );

	/**
	 * Retrieves the list of core fields that used by WordPress to insert/update
	 * an item.
	 *
	 * @return array Core fields names array.
	 */
	abstract function get_core_fields();

	/**
	 * Updates the listing item with given data.
	 *
	 * @param int  $item_id The item id.
	 * @param bool $args    Arguments to be passed to WordPress update item
	 *                      function.
	 *
	 * @return int|WP_Error The item ID on success or WP_Error on failure.
	 */
	abstract function update_item( $item_id, $args = array() );
}
