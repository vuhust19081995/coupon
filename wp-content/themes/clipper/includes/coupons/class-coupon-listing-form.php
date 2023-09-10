<?php
/**
 * Coupon Listing Form submodule
 *
 * @package Clipper\Coupons
 * @author  AppThemes
 * @since   2.0.0
 */

/**
 * Form processing class
 */
class CLPR_Coupon_Listing_Form extends APP_Listing_Form {

	/**
	 * Constructs listing form object
	 *
	 * @param APP_Listing $listing Listing object to assign process with.
	 */
	public function __construct( APP_Listing $listing ) {

		$this->listing = $listing;

		parent::__construct( $listing );

		add_action( 'appthemes_handle_media_field', array( $this, 'set_printable_coupon' ), 10, 2 );
	}

	/**
	 * Retrieves core fields
	 *
	 * @return array An array of the fields parameters
	 */
	public function get_core_fields() {

		$fields = array(

			// 'Essential info'
			array(
				'type'  => 'input_text',
				'id'    => 'post_title',
				'props' => array(
					'required'    => 1,
					'label'       => __( 'Coupon Title', APP_TD ),
				),
			),
			array(
				'type'  => 'tax_input',
				'id'    => 'tax_input[' . APP_TAX_STORE . ']',
				'props' => array(
					'required'   => 1,
					'label'      => __( 'Store', APP_TD ),
					'tax'        => APP_TAX_STORE,
					'field_type' => 'select',
				),
			),
			array(
				'type'  => 'tax_input',
				'id'    => 'tax_input[' . APP_TAX_CAT . ']',
				'props' => array(
					'required'   => 1,
					'label'      => __( 'Category', APP_TD ),
					'tax'        => APP_TAX_CAT,
					'field_type' => 'select',
				),
			),
			array(
				'type'  => 'tax_input',
				'id'    => 'tax_input[' . APP_TAX_TYPE . ']',
				'props' => array(
					'required'   => 1,
					'label'      => __( 'Type', APP_TD ),
					'tax'        => APP_TAX_TYPE,
					'field_type' => 'select',
				),
			),

			array(
				'type'  => 'url',
				'id'    => 'clpr_coupon_aff_url',
				'props' => array(
					'required'    => 1,
					'label'       => __( 'Destination', APP_TD ),
					'placeholder' => _x( 'http://', 'placeholder', APP_TD ),
				),
			),
			array(
				'type'  => 'input_text',
				'id'    => 'clpr_expire_date',
				'props' => array(
					'required'    => 0,
					'label'       => __( 'Expires', APP_TD ),
				),
			),
			array(
				'type'  => 'tax_input',
				'id'    => 'tax_input[' . APP_TAX_TAG . ']',
				'props' => array(
					'required'   => 0,
					'label'      => __( 'Tags', APP_TD ),
					'tax'        => APP_TAX_TAG,
					'field_type' => 'text',
					'tip'        => __( 'Separate tags with commas', 'tooltip', APP_TD ),
				),
			),
			array(
				'type'  => 'textarea',
				'id'    => 'post_content',
				'props' => array(
					'required'    => 1,
					'label'       => __( 'Description', APP_TD ),
					'editor_type' => '',
				),
			),
		);

		return apply_filters( 'appthemes_core_fields', $fields, $this->listing->get_type() );
	}

	public function apply_atts( $field, $item_id ) {
		$field = parent::apply_atts( $field, $item_id );
		if ( 'tax_input' === $field['type'] && 'select' === $field['props']['field_type'] ) {
			$field['props']['dropdown_options'] = array(
				'show_option_none'  => __( '&mdash; Select One &mdash;', APP_TD ),
				'option_none_value' => '',
			);

			if ( APP_TAX_STORE === $field['props']['tax'] ) {
				$unique_id        = uniqid( rand( 10, 1000 ), false );
				$show_option_none = $field['props']['dropdown_options']['show_option_none'] . $unique_id;
				$field['props']['dropdown_options'] = array(
					'add_new_option_id' => $unique_id,
					'show_option_none'  => $show_option_none,
					'option_none_value' => '',
					'exclude'           => clpr_hidden_stores(),
					'orderby'           => 'name',
				);
				$field['listing_id']   = $item_id;
			}
		}

		// Check for any files uploaded before 4.0 and upgrade it to the new media manager.
		if ( 'file' === $field['type'] ) {

			if ( '_printable_coupon' === $field['name'] && ! in_array( $field['name'], (array) get_post_custom_keys( $item_id ) ) ) {
				$attachments = get_children( array(
					'post_parent' => $item_id,
					'post_status' => 'inherit',
					'post_type' => 'attachment',
					'post_mime_type' => 'image',
					APP_TAX_IMAGE => 'printable-coupon',
					'numberposts' => 1,
					'order' => 'ASC',
					'orderby' => 'ID',
					'fields' => 'ids',
				) );

				update_post_meta( $item_id, $field['name'], $attachments );

				foreach ( $attachments as $attach_id ) {
					update_post_meta( $attach_id, '_app_attachment_type', 'file' );
				}
			}
		}

		if ( 'clpr_expire_date' === $field['name'] ) {
			$field['sanitizers'][] = 'clpr_expiration_date_validator';
		}

		return $field;
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
		$output = parent::field_render( $field, $formdata );

		if ( 'tax_input' === $field['props']['type'] && 'select' === $field['props']['field_type'] && APP_TAX_STORE === $field['props']['tax'] ) {
			// prepare 'add new store' option, and add unique ID after option 'none'
			$add_store_option = '</option><option value="add-new">' . __( 'Add New Store', APP_TD );
			$id_to_replace    = $field['props']['dropdown_options']['add_new_option_id'];

			// replace unique ID with 'add new store' option
			$output = str_replace( $id_to_replace, $add_store_option, $output );
		}

		return $output;
	}

	public function set_printable_coupon( $item_id, $field ) {

		if ( '_printable_coupon' !== $field ) {
			return;
		}

		if ( $item_id ) {
			 $item = get_post( $item_id );
		}

		if ( ! $item || $this->listing->get_type() !== $item->post_type ) {
			return;
		}

		// Must be an array.
		$attachs = (array) get_post_meta( $item_id, $field, true );
		$attachs = array_filter( $attachs );

		// Go see if any images are associated with the coupon.
		$args = array(
			'post_parent' => $item_id,
			'post_status' => 'inherit',
			'post_type' => 'attachment',
			//'post_mime_type' => 'image',
			APP_TAX_IMAGE => 'printable-coupon',
		);
		$images = get_children( $args );

		foreach ( $images as $image ) {
			if ( ! in_array( $image->ID, $attachs ) ) {
				wp_remove_object_terms( $image->ID, 'printable-coupon', APP_TAX_IMAGE );
			}
		}

		foreach ( $attachs as $attachment_id ) {
			wp_set_object_terms( $attachment_id, 'printable-coupon', APP_TAX_IMAGE, false );
		}
	}

}
