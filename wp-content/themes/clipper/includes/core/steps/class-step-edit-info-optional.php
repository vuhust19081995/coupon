<?php
/**
 * Listing Optional Edit Info Checkout Step.
 *
 * To be used in the processes where no need edit listing info, however some
 * fields needs to be editable.
 *
 * For example, using Taxonomy Surcharge feature, user can't edit taxonomy
 * terms since they are locked on the form (since they affect total listing
 * price). Only one way to change it is Renew Listing process where user can
 * change taxonomy terms and pay for them additianally.
 * Renew process includes Optional Edit Info Step for such needs.
 * So plugin/module developer could add necessary field on a form in this step.
 *
 * @package Listing\Views\Checkouts
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Edit Info Checkout Step class
 */
class APP_Step_Edit_Info_Optional extends APP_Step_Edit_Info {

	/**
	 * Construct Edit Listing Info step
	 *
	 * @param APP_Listing $listing Listing object to assign step with.
	 * @param string      $step_id Step ID.
	 * @param array       $args    Optional. An array of arguments.
	 */
	public function __construct( APP_Listing $listing, $step_id = 'edit-info', $args = array() ) {

		$this->listing = $listing;

		if ( empty( $args ) ) {
			$args = array(
				'register_to' => array(
					"{$this->listing->get_type()}-renew",
				),
			);
		}

		parent::__construct( $listing, $step_id, $args );
	}

	/**
	 * Processes step
	 *
	 * @param APP_Order            $order    Order object.
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 */
	public function process( $order, $checkout ) {

		if ( ! $this->check_previous() ) {
			$this->cancel_step();
			return;
		}

		$form_fields = $this->get_form_fields( $checkout );

		// No fields were included, so nothing to do here.
		if ( empty( $form_fields ) ) {
			$this->finish_step();
			return;
		}

		parent::process( $order, $checkout );

	}

	/**
	 * Processes listing form
	 *
	 * @param APP_Order            $order    Order object.
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 */
	protected function process_form( $order, $checkout ) {
		/* @var $form APP_Listing_Form */
		$form       = $this->listing->form;
		$item_id    = $checkout->get_data( 'listing_id' );
		$action     = $checkout->get_checkout_type();
		$nonce_name = $this->step_id;
		$fields     = $this->get_form_fields( $checkout );

		check_admin_referer( $action, $nonce_name );

		$field_names = wp_list_pluck( $fields, 'name' );

		foreach ( $field_names as $key => $field_name ) {
			if ( is_array( $field_name ) ) {
				$field_names[ $key ] = array_shift( $field_name );
			}
		}

		$field_names = array_unique( $field_names );
		$formdata    = wp_array_slice_assoc( wp_unslash( $_POST ), $field_names ); // Input var okay.
		$formdata    = $form->validate_post_data( $fields, $formdata );

		if ( $formdata instanceof WP_Error && $formdata->get_error_codes() ) {
			return $formdata;
		}

		$checkout->add_data( 'optional_data', $formdata );

		return $item_id;
	}

	/**
	 * Retrieves form fields to be processed/displayed.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @return array Form fields array.
	 */
	protected function get_form_fields( APP_Dynamic_Checkout $checkout ) {

		/* @var $form APP_Listing_Form */
		$form        = $this->listing->form;
		$item_id     = $checkout->get_data( 'listing_id' );
		$include     = $this->include_fields();
		$form_fields = $form->get_form_fields( array(), $include, $item_id );

		return $form_fields;
	}

	/**
	 * Filters original listing form fields.
	 *
	 * Retrieves a list of field attributes used to include certain fields in
	 * the final field list (e.g: array( 'name' => 'my-field' ) ).
	 *
	 * Filter excludes all fields from the listing form, but leaves only
	 * included ones.
	 *
	 * @return array Field attributes array.
	 */
	protected function include_fields() {
		return apply_filters( 'appthemes_optional_form_fields_include_filters', array() );
	}

	/**
	 * Test if this step should registered in checkout.
	 *
	 * @return boolean
	 */
	public function condition() {
		$include = $this->include_fields();
		return ! empty( $include );
	}


}
