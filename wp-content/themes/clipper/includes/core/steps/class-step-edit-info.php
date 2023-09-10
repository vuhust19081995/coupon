<?php
/**
 * Listing Edit Info Checkout Step
 *
 * @package Listing\Views\Checkouts
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Edit Info Checkout Step class
 */
class APP_Step_Edit_Info extends APP_Listing_Step {

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
					"{$this->listing->get_type()}-edit",
				),
			);
		}

		parent::__construct( $listing, $step_id, $args );
	}

	/**
	 * Displays step
	 *
	 * @param APP_Order            $order    Order object.
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 */
	public function display( $order, $checkout ) {

		$type    = $checkout->get_checkout_type();
		$listing = $this->get_listing_obj( $checkout );

		appthemes_add_template_var( array(
			'form_class'  => "{$this->step_id} {$type} {$this->listing->get_type()}",
			'listing'     => $listing,
			'form_fields' => $this->get_form_fields( $checkout ),
		) );

		parent::display( $order, $checkout );
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

		// Nothing to do if order already completed or activated.
		if ( in_array( $order->get_status(), array( APPTHEMES_ORDER_COMPLETED, APPTHEMES_ORDER_ACTIVATED ) ) ) {
			$this->finish_step();
			return;
		}

		$checkout->add_data( 'title', __( 'Listing Details', APP_TD ) );

		if ( ! isset( $_POST[ $this->step_id ] ) ) { // Input var okay.
			return;
		}

		check_admin_referer( $checkout->get_checkout_type(), $this->step_id );

		$listing_id = $this->process_form( $order, $checkout );

		if ( is_wp_error( $listing_id ) ) {
			$this->errors = $listing_id;
		}

		if ( ! $listing_id ) {
			$listing_id = $checkout->get_data( 'listing_id' );
			trigger_error( 'Lost listing item id during form processing', E_USER_WARNING );
		}

		if ( $this->errors->get_error_codes() ) {
			return;
		}

		$this->update_status( $order, $listing_id );
		$this->finish_step();

		if ( ! $checkout->get_next_step( $checkout->get_current_step() ) ) {
			$listing_id = $checkout->get_data( 'listing_id' );
			$checkout->complete_checkout();
			wp_redirect( get_post_permalink( $listing_id ) );
			appthemes_exit( "finish_step_{$checkout->get_current_step()}" );
		}

	}

	/**
	 * Updates Listing Status
	 *
	 * @param APP_Order $order      Order object.
	 * @param int       $listing_id Updated Listing ID.
	 */
	protected function update_status( $order, $listing_id ) {}

	/**
	 * Processes listing form
	 *
	 * @param APP_Order            $order    Order object.
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 */
	protected function process_form( $order, $checkout ) {
		$ptype_obj = get_post_type_object( $this->listing->get_type() );
		$cap = $ptype_obj->cap->edit_post;

		if ( ! current_user_can( $cap, $checkout->get_data( 'listing_id' ) ) ) {
			$this->errors->add( 'cant_edit', __( 'You do not have sufficient permissions to edit this listing.', APP_TD ) );
			return $this->errors;
		}

		/* @var $form APP_Listing_Form */
		$form       = $this->listing->form;
		$item_id    = $checkout->get_data( 'listing_id' );
		$action     = $checkout->get_checkout_type();
		$nonce_name = $this->step_id;
		$fields     = $this->get_form_fields( $checkout );

		$item_id = $form->process_form( $item_id, $action, $nonce_name, $fields );

		return $item_id;
	}

	/**
	 * Retrieves listing object from checkout or create default one
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @return WP_Post Listing object
	 */
	protected function get_listing_obj( $checkout ) {
		$listing_id = $checkout->get_data( 'listing_id' );
		return get_post( $listing_id );
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
		$form_fields = $form->get_form_fields( array(), array(), $item_id );

		return $form_fields;
	}
}
