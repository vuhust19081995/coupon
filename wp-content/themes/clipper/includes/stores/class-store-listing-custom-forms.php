<?php
/**
 * Store Listing Custom Form submodule.
 *
 * @package Clipper\Stores
 * @author  AppThemes
 * @since   2.0.0
 */

/**
 * Custom Form processing class
 */
class CLPR_Store_Listing_Custom_Forms extends APP_Listing_Custom_Forms {

	/**
	 * Init module.
	 */
	public function init() {}

	/**
	 * Renders custom form.
	 *
	 * @param array  $categories Taxonomy terms.
	 * @param string $taxonomy   Taxonomy name.
	 * @param int    $listing_id Listing ID.
	 */
	public function render_form( $categories, $taxonomy, $listing_id = 0 ) {
		if ( ! in_array( 'add-new', $categories, true ) ) {
			return;
		}

		wp_nonce_field( "{$taxonomy}_form_action", "{$taxonomy}_form_nonce" );

		echo $this->listing->form->get_form( 0 );
	}

	/**
	 * Adds extra parameters to listing form field.
	 *
	 * @param array $field   Form field.
	 * @param int   $item_id Listing item ID.
	 * @param int   $type    Listing type.
	 *
	 * @return array Modified Form field.
	 */
	public function form_field( $field, $item_id, $type ) {

		if ( array( 'tax_input', $this->taxonomy ) !== $field['name'] ) {
			return $field;
		}

		$item = get_post( $item_id );

		if ( ! $item || APP_POST_TYPE !== $item->post_type ) {
			return $field;
		}

		$field['props']['ul_class'] = 'app-category-fields';
		$field['sanitizers'][]      = array( $this, 'validate_form' );
		return $field;
	}

	/**
	 * Validates New Store field and processes the Store type form within Coupon
	 * form.
	 *
	 * @param mixed          $value  Posted field value.
	 * @param scbCustomField $inst   Field object.
	 * @param WP_Error       $errors Errors object.
	 *
	 * @return mixed Validated value.
	 */
	public function validate_form( $value, $inst, $errors ) {
		global $clpr_options;

		if ( 'add-new' !== $value ) {
			return $value;
		}

		/* @var $form APP_Listing_Form */
		$action = "{$this->taxonomy}_form_action";
		$name   = "{$this->taxonomy}_form_nonce";
		$form   = $this->listing->form;

		// Check if has been marked as invalid during validation.
		$fields   = $form->get_form_fields( array(), array( 'name' =>  array( 'new_store_name' ) ), 0 );
		$formdata = wp_array_slice_assoc( wp_unslash( $_POST ), array( 'new_store_name' ) ); // Input var okay.
		$formdata = $form->validate_post_data( $fields, $formdata );

		if ( $formdata instanceof WP_Error && $formdata->get_error_codes() ) {
			foreach ( $formdata->get_error_codes() as $error_code ) {
				$errors->add( $error_code, $formdata->get_error_message( $error_code ) );
			}
			return '';
		}

		// Insert the new store.
		$term_ids = wp_insert_term( $formdata['new_store_name'], APP_TAX_STORE );

		if ( is_wp_error( $term_ids ) ) {
			return '';
		}

		$value    = $term_ids['term_id'];
		$store_id = $value;

		// We've got the new term id and now we can process the term form.
		$fields = $form->get_form_fields( array(
			'name' => array(
				'new_store_name',
			),
		), array(), $store_id );

		$store_id = $form->process_form( $store_id, $action, $name, $fields );

		if ( $store_id instanceof WP_Error && $store_id->get_error_codes() ) {
			foreach ( $store_id->get_error_codes() as $error_code ) {
				$errors->add( $error_code, $store_id->get_error_message( $error_code ) );
			}
			return '';
		}

		// TODO: Maybe this needs to be made within a listing process step?
		// Check if new stores require moderation before going live.
		if ( $this->listing->options->moderate ) {
			$this->listing->meta->update_meta( $store_id, 'clpr_store_active', 'no' );
		}

		return $value;
	}

	/**
	 * Adds Custom form fields to the Listing form processing method.
	 *
	 * @param array $fields  Form fields.
	 * @param int   $item_id Listing item ID.
	 * @param int   $type    Listing type.
	 *
	 * @return array Extended form fields array.
	 */
	public function process_form( $fields, $item_id, $type ) {
		return $fields;
	}

}
