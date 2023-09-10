<?php
/**
 * Single User Listing Fields metabox
 *
 * @package Listing\Form\Metaboxes
 * @author  AppThemes
 * @since   Listing 2.0
 */

/**
 * Base class for displaying a meta box with all the custom fields attached to a
 * listing.
 */
class APP_Listing_User_Metabox extends APP_User_Meta_Box {

	/**
	 * The listing object this meta box relates to.
	 *
	 * @var APP_Listing
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
	 * @param array       $args    Meta box arguments (priority).
	 */
	public function __construct( APP_Listing $listing, $title = '', $args = array() ) {

		// Set the listing object.
		$this->listing = $listing;

		$type = $listing->get_type();
		$title = ( ! $title ) ? __( 'Custom Fields', APP_TD ) : $title;

		if ( ! isset( self::$registry[ $type ] ) ) {
			self::$registry[ $type ] = 0;
		}

		$c = &self::$registry[ $type ];

		parent::__construct( "app-{$type}-form-" . ($c++), $title, $args );
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
	 * Returns an array of form fields.
	 *
	 * @return array
	 */
	public function form_fields() {
		return apply_filters( "appthemes_{$this->get_id()}_profile_metabox_fields", $this->form() );
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
	 * @param array $user_data   The item posted data.
	 * @param array $form_fields Array of the fields parameters.
	 * @param int   $user_id     The item ID.
	 */
	protected function validate_fields_data( $user_data, $form_fields, $user_id ) {
		$errors = $this->listing->form->validate_post_data( $this->form_fields(), $user_data );
		if ( $errors instanceof WP_Error ) {
			$this->errors = $errors;
		}
	}

	/**
	 * Adds validation errors to WP_Error object to show them on the form.
	 *
	 * @param object $errors WP_Error object.
	 *
	 * @return type
	 */
	public function update_errors( $errors ) {

		if ( empty( $this->errors ) ) {
			return;
		}

		foreach ( $this->errors->get_error_codes() as $code ) {
			$errors->add( $code, $this->errors->get_error_message( $code ) );
		}
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

		return $this->listing->form->get_form_fields( $exclude_filters, $include_filters, $this->get_user_id() );
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
		$field['listing_id']   = $this->get_user_id();
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
	 * @param int $user_id
	 *
	 * @return void
	 */
	public function save( $user_id ) {

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

		parent::save( $user_id );

		if ( ! empty( $this->errors ) ) {
			return false;
		}

		$this->listing->meta->handle_media( $user_id, array(), true );
	}

	/**
	 * Enqueues admin scripts.
	 *
	 * @return void
	 */
	public function admin_enqueue_scripts() {
		appthemes_enqueue_media_manager();
	}

}
