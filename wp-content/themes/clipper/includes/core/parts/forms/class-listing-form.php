<?php
/**
 * Listing Form submodule
 *
 * @package Listing\Form
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Form processing class
 */
class APP_Listing_Form {

	/**
	 * Current Listing module object.
	 *
	 * @var APP_Listing
	 */
	protected $listing;

	/**
	 * Errors object.
	 *
	 * @var WP_Error
	 */
	protected $errors;

	/**
	 * Cached raw form fields array.
	 *
	 * @var array
	 */
	private $form_raw = array();

	/**
	 * Constructs listing form object
	 *
	 * @param APP_Listing $listing Listing object to assign process with.
	 */
	public function __construct( APP_Listing $listing ) {
		$this->listing = $listing;
		$this->errors  = new WP_Error();
		$this->listing->options->set_defaults( $this->get_defaults() );
	}

	/**
	 * Retrieves core fields
	 *
	 * @return array An array of the fields parameters
	 */
	public function get_core_fields() {
		return apply_filters( 'appthemes_core_fields', array(), $this->listing->get_type() );
	}

	/**
	 * Retrieves module's options default values to be registered in Listing
	 * options object.
	 *
	 * @return array The Form defaults.
	 */
	public function get_defaults() {
		return array(
			'app_form' => $this->get_core_fields(),
		);
	}

	/**
	 * Get form fields metadata retrieved from the listing item.
	 *
	 * @param int $item_id Listing item id.
	 *
	 * @return array Form data
	 */
	public function get_formdata( $item_id ) {
		$formdata = array();

		// Get form field values if it stored somewhere.
		$custom_fields = $this->listing->meta->get_meta( $item_id );

		foreach ( $custom_fields as &$value ) {
			$value = maybe_unserialize( $value[0] );
		}

		$item_data  = $this->listing->meta->get_item( $item_id, ARRAY_A );
		$formdata   = array_merge( $custom_fields, $item_data );
		$taxonomies = get_object_taxonomies( $this->listing->get_type() );

		foreach ( $taxonomies as $taxonomy ) {
			$formdata['tax_input'][ $taxonomy ] = wp_list_pluck( wp_get_object_terms( $item_id, $taxonomy ), 'name', 'term_id' );
		}

		return $formdata;
	}

	/**
	 * Retrieves a raw array of the form fields
	 *
	 * @return array Raw form fields
	 */
	public final function get_form_fields_raw() {

		if ( empty( $this->form_raw ) ) {
			$form_raw = $this->listing->options->app_form;

			if ( empty( $form_raw ) ) {
				$form_raw = $this->get_core_fields();
			}

			$this->form_raw = APP_Form_Builder::prepare_fields( $form_raw );
		}

		return $this->form_raw;
	}

	/**
	 * Retrieves an array of the fields after include/exclude filters to be used
	 * for the form processing or display.
	 *
	 * @param array $exclude_filters An associative array with field
	 *                               attribute/value(s) used to excluded certain
	 *                               fields from the final field list
	 *                               (e.g: array( 'name' => 'my-field' ) ).
	 * @param array $include_filters Retrieves A list of field attributes used
	 *                               to include certain fields in the final
	 *                               field list (similar to $exclude_filters).
	 * @param int   $item_id        Current Listing item ID.
	 *
	 * @return array Form fields.
	 */
	public function get_form_fields( $exclude_filters = array(), $include_filters = array(), $item_id = 0 ) {

		$form = $this->get_form_fields_raw();

		foreach ( $form as $key => &$field ) {

			// Remove field if it is disabled or excluded or not included.
			if ( ! empty( $field['props']['disable'] ) || ! $this->filter( $field, $exclude_filters, false ) || ! $this->filter( $field, $include_filters ) ) {
				unset( $form[ $key ] );
				continue;
			}

			$field = $this->apply_atts( $field, $item_id );
			$field = apply_filters( 'appthemes_form_field', $field, $item_id, $this->listing->get_type() );

			if ( ! $field ) {
				unset( $form[ $key ] );
			}
		}

		return $form;
	}

	/**
	 * Applies additional attributes to each field.
	 *
	 * @param array $field   The field parameters array.
	 * @param int   $item_id The item id.
	 *
	 * @return array
	 */
	public function apply_atts( $field, $item_id ) {
		// Wrap radios and checkbox's using unordered lists tags to make them consistent with WP walkers markup.
		if ( 'radio' === $field['type'] || 'checkbox' === $field['type'] ) {
			$field['wrap'] = '<ul>%input%</ul>';
			$field['wrap_each'] = '<li>%input%</li>';
		} elseif ( 'tax_input' === $field['type'] ) {
			$field['name']   = array( 'tax_input', $field['props']['tax'] );
			$field['render'] = array( 'APP_Tax_Input_Field_Type', '_render' );
		} elseif ( 'textarea' === $field['type'] && isset( $field['props']['editor_type'] ) && $field['props']['editor_type'] ) {
			$field['render'] = array( 'APP_Editor_Field_Type', '_render' );
		} elseif ( 'file' === $field['type'] ) {
			$field['render'] = array( 'APP_Media_Field_Type', '_render' );
		}

		if ( ! empty( $field['props']['placeholder'] ) ) {
			$field['extra']['placeholder'] = $field['props']['placeholder'];
		}

		$field['title'] = $field['desc'];
		unset( $field['desc'] );

		// Preserve initial field type.
		$field['props']['type'] = $field['type'];

		return $field;
	}

	/**
	 * Retrieves generated form HTML
	 *
	 * @param int   $item_id     Listing item id.
	 * @param array $form_fields Form fields.
	 *
	 * @return string Generated HTML
	 */
	public function get_form( $item_id, $form_fields = array() ) {

		if ( empty( $form_fields ) ) {
			$form_fields = $this->get_form_fields( array(), array(), $item_id );
		}

		$output   = '';
		$formdata = $this->get_formdata( $item_id );

		// Generate fields html.
		foreach ( $form_fields as $field ) {

			$defaults = array(
				// Might be necessary for rendering.
				'listing_id'   => $item_id,
				'listing_type' => $this->listing->get_type(),
				'wrap'         => scbForms::TOKEN,
			);

			$field = wp_parse_args( $field, $defaults );

			$field['wrap'] = $this->field_wrap( $field['wrap'], $field );

			// Set renderer (by default initial ones).
			if ( ! isset( $field['render'] ) ) {
				$field['render'] = array( scbFormField::create( $field ), 'render' );
			}

			$type = $field['type'];

			// Set 'custom' field type.
			$field['type'] = 'custom';

			$html = $this->field_render( $field, $formdata );

			// Temporary fix scbForms::input() since it doesn't take 'extra parameter for radios and checkboxes'
			// TODO: Provide a smart solution for this fix.
			if ( ( 'radio' === $type || 'checkbox' === $type )
				&& isset( $field['extra'] )
				&& isset( $field['extra']['class'] )
				&& 'required' === $field['extra']['class']
			) {
				$html = str_replace( 'type=', 'class="required" type=', $html );
			}
			$output .= apply_filters( 'appthemes_render_form_field', $html, $field, $item_id );
		}

		return $output;
	}

	/**
	 * Generates field html.
	 *
	 * @uses scbForms::input()
	 *
	 * @param array $field    Field parameters to be used in scbForms.
	 * @param array $formdata Field value.
	 *
	 * @return string Generated field html.
	 */
	public function field_render( $field, $formdata = null ) {
		return scbForms::input( $field, $formdata );
	}

	/**
	 * Wraps form field token and adds label and description.
	 *
	 * @param array $token Field input token to be replaced with generated html.
	 * @param array $field Field parameters.
	 *
	 * @return string Wrapped field html.
	 */
	public function field_wrap( $token, $field ) {
		global $wp_query;

		ob_start();
		$wp_query->set( 'app_field_array', $field );
		$wp_query->set( 'app_field_token', $token );

		appthemes_get_template_part( 'parts/form-field', $field['props']['type'] );
		return ob_get_clean();
	}

	/**
	 * Processes listing form
	 *
	 * @param int    $listing_id  Listing ID.
	 * @param string $action      Action nonce.
	 * @param string $name        Nonce name.
	 * @param array  $form_fields Form fields.
	 *
	 * @return int Processed listing ID
	 */
	public function process_form( $listing_id, $action, $name, $form_fields = array() ) {

		check_admin_referer( $action, $name );

		if ( empty( $form_fields ) ) {
			$form_fields = $this->get_form_fields( array(), array(), $listing_id );
		}

		/**
		 * Allows to add or remove fields within processing method.
		 */
		$form_fields = apply_filters( 'appthemes_process_listing_form_fields', $form_fields, $listing_id, $this->listing->get_type() );

		if ( empty( $form_fields ) ) {
			return $listing_id;
		}

		$field_names = wp_list_pluck( $form_fields, 'name' );

		foreach ( $field_names as $key => $field_name ) {
			if ( is_array( $field_name ) ) {
				$field_names[ $key ] = array_shift( $field_name );
			}
		}

		$field_names = array_unique( $field_names );
		$formdata    = wp_array_slice_assoc( wp_unslash( $_POST ), $field_names ); // Input var okay.
		$formdata    = $this->validate_post_data( $form_fields, $formdata );

		if ( $formdata instanceof WP_Error && $formdata->get_error_codes() ) {
			return $this->errors;
		}

		$core_fields = $this->listing->meta->get_core_fields();
		$postarr     = wp_array_slice_assoc( $formdata, $core_fields );

		if ( $postarr ) {
			$listing_id  = $this->listing->meta->update_item( $listing_id, $postarr );
		}

		if ( is_wp_error( $listing_id ) ) {
			return $listing_id;
		}

		$meta_fields = wp_array_slice_assoc( $formdata, array_diff( array_keys( $formdata ), $core_fields ) );

		foreach ( $meta_fields as $meta_field => $value ) {
			$this->listing->meta->update_meta( $listing_id, $meta_field, $value );
		}

		$this->listing->meta->handle_media( $listing_id, array(), true );

		$listing_id = apply_filters( 'appthemes_handle_listing_form', $listing_id, $form_fields, $formdata, $this->errors, $this->listing->get_type() );

		if ( $this->errors->get_error_codes() ) {
			$listing_id = $this->errors;
		}

		return $listing_id;
	}

	/**
	 * Sanitizes and validates post data.
	 *
	 * @param array $form_fields Form fields to be validated.
	 * @param array $formdata    Posted data.
	 *
	 * @return array|WP_Error Validated post data or error object.
	 */
	public function validate_post_data( $form_fields, $formdata ) {

		$validator = new APP_Form_Field_Validator( $this->errors );

		foreach ( $form_fields as &$field ) {

			// 'sanitizers' item conteins numeric array of the validation methods
			// first item is native scbForm field validate method, others custom
			$sanitizers = array();

			if ( 'textarea' === $field['type'] ) {
				if ( isset( $field['props']['editor_type'] ) && $field['props']['editor_type'] ) {
					$sanitizers[] = 'wp_kses_post';
				}
				if ( ! current_user_can( 'edit_others_posts' ) && ! is_admin() ) {
					$sanitizers[] = 'strip_shortcodes';
				}
			} else {
				$scbfield = scbFormField::create( $field );
				if ( $scbfield instanceof scbTextField && ! isset( $scbfield->sanitize ) ) {
					// Replace default 'wp_kses_post' sanitizer for non-scalar
					// values.
					$sanitizers[] = 'wp_kses_post_deep';
				} else {
					$sanitizers[] = array( $scbfield, 'validate' );
				}
			}

			// Validate required fields (common method).
			$sanitizers[] = array( $validator, 'required' );

			if ( in_array( $field['type'], array( 'url', 'email', 'number' ), true ) ) {
				$sanitizers[] = array( $validator, $field['type'] );
			}

			// Set general sanitize/validate method which calls all sanitizers.
			$field['sanitize'] = array( $validator, 'validate' );

			if ( isset( $field['sanitizers'] ) && is_array( $field['sanitizers'] ) ) {
				$sanitizers = array_merge( $sanitizers, $field['sanitizers'] );
			}

			/**
			 * Allows to add extra sanitize/validate methods.
			 */
			$sanitizers = apply_filters( 'appthemes_form_field_validators', $sanitizers, $field, $this->listing->get_type() );

			$field['sanitizers'] = $sanitizers;

			// Preserve initial field type.
			$field['props']['type'] = $field['type'];
			// Set 'custom' field type.
			$field['type'] = 'custom';
		}

		$formdata = scbForms::validate_post_data( $form_fields, $formdata, $formdata );

		if ( $this->errors->get_error_codes() ) {
			$formdata = $this->errors;
		}

		return $formdata;
	}

	/**
	 * Helper function to filter out a field based on its attributes.
	 *
	 * @param  array   $field   The field array.
	 * @param  array   $filters The filter array with key/value pairs of field
	 *                          attributes (e.g: 'name' => 'my-custom-field' ).
	 * @param  boolean $include Whether to include or exclude field by given
	 *                          filters.
	 *
	 * @return boolean Retrieves the boolean value set in '$include' if the
	 *                 filter condition is met, opposite value otherwise.
	 *                 Returns TRUE, if empty filters.
	 */
	public final function filter( $field, $filters, $include = true ) {

		if ( empty( $filters ) ) {
			return true;
		}

		// Exclude fields based on certain criteria.
		foreach ( (array) $filters as $att => $values ) {

			if ( isset( $field[ $att ] ) && in_array( $field[ $att ], (array) $values ) ) {
				return $include;
			}
		}

		return ! $include;
	}
}
