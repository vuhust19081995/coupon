<?php
/**
 * Single Listing Fields metabox
 *
 * @package Listing\Form\Metaboxes
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Base class for displaying a meta box with all the custom fields attached to a
 * listing.
 */
class APP_Listing_Fields_Metabox extends APP_Meta_Box {

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

		$ptype = $listing->get_type();
		$title = ( ! $title ) ? __( 'Custom Fields', APP_TD ) : $title;

		if ( ! isset( self::$registry[ $ptype ] ) ) {
			self::$registry[ $ptype ] = 0;
		}

		$c = &self::$registry[ $ptype ];

		parent::__construct( "app-{$ptype}-form-" . $c++, $title, $ptype, 'normal', 'high' );
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
	public function form_fields() {

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
	 * @param array $post_data The item posted data.
	 * @param int   $post_id   The item ID.
	 */
	protected function validate_post_data( $post_data, $post_id ) {
		return $this->listing->form->validate_post_data( $this->form_fields(), $post_data );
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
			'type' => array( 'tax_input', 'file' ),
			'name' => array( 'post_title', 'post_content' ),
		);

		$exclude_filters = array_merge_recursive( $exclude_filters, $this->exclude_filters() );
		$include_filters = $this->include_filters();

		return $this->listing->form->get_form_fields( $exclude_filters, $include_filters, $this->get_post_id() );
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
		$field['listing_id']   = $this->get_post_id();
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

}
