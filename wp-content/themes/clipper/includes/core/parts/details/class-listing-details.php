<?php
/**
 * Listing Details submodule
 *
 * @package Listing\Details
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Prepare listing details to be shown on the front-end
 *
 * @uses APP_Listing_Form To retrieve fields to show.
 */
class APP_Listing_Details {

	/**
	 * Current Listing module object.
	 *
	 * @var APP_Listing
	 */
	protected $listing;

	/**
	 * Constructs listing details object
	 *
	 * @param APP_Listing $listing Listing object to assign process with.
	 */
	public function __construct( APP_Listing $listing ) {

		$this->listing = $listing;

		add_action( 'the_content', array( $this, 'the_content' ), 1, 3 );
		$this->listing->options->set_defaults( $this->get_defaults() );
	}

	/**
	 * Retrieves module's options default values to be registered in Listing
	 * options object.
	 *
	 * @return array The Form defaults.
	 */
	public function get_defaults() {
		return array(
			'content_template' => '[appthemes_listing_content]',
		);
	}

	/**
	 * Adds listing details to the item content.
	 *
	 * This allows to use Content Template option with real content placeholder.
	 *
	 * For example, the original post content is:
	 *     <pre>"Hello World!"</pre>
	 *
	 * The Content Template option value is:
	 *     <pre>"Some text before. [appthemes_listing_content] Some after."</pre>
	 *
	 * Result:
	 *     <pre>"Some text before. Hello World! Some after."</pre>
	 *
	 * Along with simple text a shortcodes might be used.
	 *
	 * For example:
	 *     <pre>
	 *     Categories: [appthemes_listing_terms taxonomies="category"]
	 *     [appthemes_listing_content]
	 *     Phone: [appthemes_listing_details fields="phone"]
	 *     </pre>
	 *
	 * Result:
	 *     <pre>
	 *     Categories: Helloworlding
	 *     Hello World!
	 *     Phone: +0-000-000-0000
	 *     </pre>
	 *
	 * IMPORTANT: The hook automatically applies only to post meta type
	 * listings, for other meta types needs manually apply `the_content` filter.
	 *
	 * @param string $content Original content.
	 *
	 * @return string The content with details html included
	 */
	public function the_content( $content, $item_id = null, $type = '' ) {

		// Fallback item type presumed as post type.
		$type = $type ? $type : get_post_type( $item_id );

		if ( $this->listing->get_type() !== $type ) {
			return $content;
		}

		$item = $this->listing->meta->get_item( $item_id );

		if ( ! $item || is_wp_error( $item ) ) {
			return $content;
		}

		$template = $this->listing->options->content_template;

		if ( strpos( $template, 'appthemes_listing_content' ) ) {
			$content = str_replace( '[appthemes_listing_content]', $content, $template );
		}

		return $content;
	}

	/**
	 * Retrieves listing details array.
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
	 * @return array Fields array.
	 */
	public function get_details_fields( $exclude_filters = array(), $include_filters = array(), $item_id = 0 ) {

		$form   = $this->listing->form;
		$fields = $form->get_form_fields_raw();
		$fields = apply_filters( 'appthemes_details_fields', $fields, $item_id, $this->listing->get_type() );

		foreach ( $fields as $key => &$field ) {

			// Remove field if it is disabled or excluded or not included.
			if ( ! empty( $field['props']['disable'] ) || ! $form->filter( $field, $exclude_filters, false ) || ! $form->filter( $field, $include_filters ) ) {
				unset( $fields[ $key ] );
				continue;
			}

			$field = $this->apply_atts( $field, $item_id );
			$field = apply_filters( 'appthemes_details_field', $field, $item_id, $this->listing->get_type() );

			if ( ! $field ) {
				unset( $fields[ $key ] );
				continue;
			}

			// Might be necessary for rendering.
			$field['listing_id']   = $item_id;
			$field['listing_type'] = $this->listing->get_type();

			// Preserve initial field type.
			$type = $field['type'];
			$name = $field['name'];

			// Set 'custom' field type.
			$field['type'] = 'custom';

			if ( 'tax_input' === $type ) {
				$value = get_the_term_list( $item_id, $field['props']['tax'], '', ', ' );
			} elseif ( 'post_title' === $name ) {
				$value = get_the_title( $item_id );
			} elseif ( 'post_content' === $name ) {
				$value = apply_filters( 'the_content', get_post_field( 'post_content', $item_id ) );
			} else {
				$key    = (array) $name;
				$key    = end( $key );
				$value  = $this->listing->meta->get_meta( $item_id, $key, true );
				if ( 'textarea' === $type ) {
					$value = wpautop( $value );
				}
			}

			$value = scbForms::input_with_value( $field, $value );

			$field['value'] = $value;

			// Restore type.
			$field['type'] = $type;

		}

		return $fields;
	}

	/**
	 * Apply additional args, setup default renderers.
	 *
	 * @param array $field   Field parameters.
	 * @param int   $item_id An Item ID.
	 */
	public function apply_atts( $field, $item_id ) {

		if ( ! isset( $field['render'] ) ) {
			if ( 'file' === $field['type'] ) {
				$field['render'] = array( 'APP_Media_Field_Type', '_publish' );
			} else {
				$field['render'] = array( 'APP_Detail_Publisher', '_publish' );
			}
		}

		$field['title'] = $field['desc'];
		unset( $field['desc'] );

		return $field;
	}

	/**
	 * Retrieves generated listing details HTML
	 *
	 * @param object  $item    Current listing item object.
	 * @param array   $exclude An associative array with field
	 *                          attribute/value(s) used to excluded certain
	 *                          fields from the final field list
	 *                          (e.g: array( 'name' => 'my-field' ) ).
	 * @param array   $include  Retrieves A list of field attributes used to
	 *                          include certain fields in the final field list
	 *                          (similar to $exclude).
	 * @param string  $format   The string that contains tokens to be replaced
	 *                          with field value, title, id and type.
	 *                          The list of allowed tokens:
	 *                           - `%value%`
	 *                           - `%title%`
	 *                           - `%id%`
	 *                           - `%type%`
	 *                          Example:
	 *                          `<li id="%id%"><b>%title%</b>: %value%</li>`.
	 *
	 * @return string Generated HTML
	 */
	public function get_details( $item = null, $exclude = array(), $include = array(), $format = '' ) {

		$item = $this->listing->meta->get_item( $item );

		if ( ! $item || is_wp_error( $item ) ) {
			return;
		}

		$details = $this->get_details_fields( $exclude, $include, $this->listing->meta->get_item_id( $item ) );
		$output  = '';

		if ( empty( $details ) ) {
			return;
		}

		// Generate fields html.
		foreach ( $details as $detail ) {

			if ( empty( $detail['value'] ) ) {
				continue;
			}

			if ( ! $format || ! is_string( $format ) ) {
				$format = '<div id="%id%" class="post-meta-field"><span class="post-meta-key">%title%:</span> %value%</div>' . "\n";
			}

			$tokens = array(
				'%value%' => appthemes_make_clickable( implode( ', ', (array) $detail['value'] ) ),
				'%title%' => $detail['title'],
				'%type%'  => $detail['type'],
				'%id%'    => sanitize_title_with_dashes( $detail['name'] ),
			);

			$search  = array_keys( $tokens );
			$replace = array_values( $tokens );

			$output .= str_replace( $search, $replace, $format );
		}

		return $output;
	}

}

/**
 * Field Detail Publisher.
 *
 * Used as a fallback renderer for scbCustomField.
 */
class APP_Detail_Publisher {

	/**
	 * Retrieves field html
	 *
	 * @param mixed          $value Field value.
	 * @param scbCustomField $inst  Field object.
	 *
	 * @return string Generated html
	 */
	public static function _publish( $value, $inst ) {
		return $value;
	}
}

