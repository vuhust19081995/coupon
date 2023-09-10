<?php
/**
 * Single Taxonomy Listing Fields metabox
 *
 * @package Listing\Form\Metaboxes
 * @author  AppThemes
 * @since   Listing 2.0
 */

/**
 * Base class for displaying a meta box with all the custom fields attached to a
 * listing.
 */
class APP_Listing_Taxonomy_Metabox extends APP_Taxonomy_Meta_Box {

	/**
	 * The listing object this meta box relates to.
	 *
	 * @var APP_Listing.
	 */
	protected $listing;

	/**
	 * Log registered metabox ids to avoid duplication.
	 *
	 * @var array
	 */
	private static $registry = array();

	/**
	 * Constructs the listing custom fields meta box.
	 *
	 * @param APP_Listing $listing Listing module object.
	 * @param string      $title   Meta box title.
	 */
	public function __construct( APP_Listing $listing, $title = '' ) {

		// Set the listing object.
		$this->listing = $listing;

		$type = $listing->get_type();
		$title = ( ! $title ) ? __( 'Custom Fields', APP_TD ) : $title;

		if ( ! isset( self::$registry[ $type ] ) ) {
			self::$registry[ $type ] = 0;
		}

		$c = &self::$registry[ $type ];

		parent::__construct( "app-{$type}-form-" . ($c++), $title, $type );
	}

	/**
	 * Additional checks before registering the metabox.
	 *
	 * @return bool
	 */
	protected function condition() {
		$valid = $this->_get_form_valid_fields();
		return ! empty( $valid );
	}

	/**
	 * Retrieve the meta box fields list.
	 *
	 * @return array The fields list.
	 */
	public function form() {

		$fields = $this->_get_form_valid_fields();

		foreach ( $fields as &$field ) {
			// Apply additional attributes to each field before outputting it.
			$field = $this->_apply_atts( $field );
		}

		return $fields;
	}

	/**
	 * Retrieves a list of field attributes used to excluded certain fields from
	 * the final field list.
	 *
	 * @return array An associative array with field attribute/value(s)
	 *               (e.g: array( 'name' => 'my-unwanted-custom-field' ) ).
	 */
	protected function exclude_filters() {
		return array();
	}

	/**
	 * Retrieves a list of field attributes used to include certain fields in
	 * the final field list.
	 *
	 * If empty, retrieves all custom fields.
	 *
	 * @return array An associative array with field attribute/value(s)
	 *               (e.g: array( 'name' => 'my-custom-field' ) ).
	 */
	protected function include_filters() {
		return array();
	}

	/**
	 * Look for existing errors after each field is processed.
	 *
	 * @param array $term_data The item posted data.
	 * @param int   $term_id   The item ID.
	 */
	protected function validate_post_data( $term_data, $term_id ) {
		return $this->listing->form->validate_post_data( $this->form_fields(), $term_data );
	}

	/**
	 * Retrieves all the valid fields for the meta box (excludes internal fields
	 * and others set by the include/exclude filters).
	 *
	 * @return array List of valid fields.
	 */
	protected function _get_form_valid_fields() {

		// Exclude by default.
		$exclude_filters = array(
			'name' => $this->listing->meta->get_core_fields(),
		);

		$exclude_filters = array_merge_recursive( $exclude_filters, $this->exclude_filters() );
		$include_filters = $this->include_filters();

		return $this->listing->form->get_form_fields( $exclude_filters, $include_filters, $this->get_term_id() );
	}

	/**
	 * Apply field attributes by calling a field type specific callback.
	 *
	 * @param  array $field The field properties.
	 * @return array        The resulting field properties.
	 */
	private function _apply_atts( $field ) {

		$field = wp_parse_args( $field, array( 'extra' => array() ) );
		$classes = array();

		if ( isset( $field['extra']['class'] ) ) {
			$classes = array_filter( explode( ' ', trim( $field['extra']['class'] ) ) );
		}

		switch ( $field['type'] ) {

			case 'text':
			case 'tel':
			case 'url':
			case 'email':
			case 'search':
			case 'password':

				if ( ! in_array( 'small-text', $classes ) ) {
					$classes[] = 'regular-text';
				}

				break;

			case 'textarea':

				// Check for a TinyMCE editor 'textarea'.
				if ( ! empty( $field['props']['editor_type'] ) ) {
					// Use the 'custom' renderer already set in the field (same
					// as frontend).
					$field['type'] = 'custom';
					$field['sanitize'] = 'wp_kses_post';
				} else {
					$classes[] = 'large-text';
					$field['extra'] = wp_parse_args( $field['extra'], array(
						'rows'  => 10,
						'cols'  => 10,
					) );
				}

				break;
		}

		if ( ! empty( $field['props']['required'] ) ) {
			$classes[] = 'required';
		}

		if ( ! empty( $classes ) ) {
			$field['extra']['class'] = implode( ' ', array_unique( $classes ) );
		}

		// Might be necessary for rendering.
		$field['listing_id']   = $this->get_term_id();
		$field['listing_type'] = $this->listing->get_type();

		$scbField = scbFormField::create( $field );

		// Set renderer (by default initial ones).
		if ( ! isset( $field['render'] ) ) {
			$field['render'] = array( $scbField, 'render' );
		}

		if ( ! isset( $field['sanitize'] ) ) {
			$field['sanitize'] = array( $scbField, 'validate' );
		}

		// Set 'custom' field type.
		$field['type'] = 'custom';

		return $field;
	}

	/**
	 * Saves metabox form data.
	 *
	 * @param int $term_id
	 *
	 * @return void
	 */
	protected function save( $term_id ) {
		parent::save( $term_id );

		$this->listing->meta->handle_media( $term_id, array(), true );
	}

	/**
	 * Enqueues admin scripts.
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		appthemes_enqueue_media_manager();
	}

	/**
	 * Retrieves metadata for a term.
	 *
	 * @param int    $term_id Term ID.
	 * @param string $key     Optional. The meta key to retrieve. If no key is
	 *                        provided, fetches all metadata for the item.
	 * @param bool   $single  Whether to return a single value. If false, an
	 *                        array of all values matching the `$item_id`/`$key`
	 *                        pair will be returned. Default: false.
	 *
	 * @return mixed If `$single` is false, an array of metadata values.
	 *               If `$single` is true, a single metadata value.
	 */
	protected function get_term_meta( $term_id, $key = '', $single = false ) {
		return $this->listing->meta->get_meta( $term_id, $key, $single );
	}

	/**
	 * Update term meta field based on term ID.
	 *
	 * Use the $prev_value parameter to differentiate between meta fields with
	 * the same key and item ID.
	 *
	 * If the meta field for the item does not exist, it will be added.
	 *
	 * @param int    $term_id    Item ID.
	 * @param string $meta_key   Metadata key.
	 * @param mixed  $meta_value Metadata value. Must be serializable if
	 *                           non-scalar.
	 * @param mixed  $prev_value Optional. Previous value to check before
	 *                           removing. Default empty.
	 *
	 * @return int|bool Meta ID if the key didn't exist, true on successful update,
	 *                  false on failure.
	 */
	protected function update_meta( $term_id, $meta_key, $meta_value, $prev_value = '' ) {
		return $this->listing->meta->update_meta( $term_id, $meta_key, $meta_value, $prev_value );
	}

	/**
	 * Remove metadata matching criteria from a term.
	 *
	 * You can match based on the key, or key and value. Removing based on key
	 * and value, will keep from removing duplicate metadata with the same key.
	 * It also allows removing all metadata matching key, if needed.
	 *
	 * @param int    $term_id    Term ID.
	 * @param string $meta_key   Metadata name.
	 * @param mixed  $meta_value Optional. Metadata value. Must be serializable
	 *                           if non-scalar. Default empty.
	 *
	 * @return bool True on success, false on failure.
	 */
	protected function delete_meta( $term_id, $meta_key, $meta_value = '' ) {
		return $this->listing->meta->delete_meta( $term_id, $meta_key, $meta_value );
	}

}
