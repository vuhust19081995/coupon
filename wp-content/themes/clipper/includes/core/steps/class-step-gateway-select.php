<?php
/**
 * Gateway Select Checkout Step
 *
 * @package Listing\Views\Checkouts
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Gateway Select Checkout Step class
 */
class APP_Step_Gateway_Select extends APP_Listing_Step {

	/**
	 * Construct Gateway Select step
	 *
	 * @param APP_Listing $listing Listing object to assign step with.
	 * @param string      $step_id Step ID.
	 * @param array       $args    Optional. An array of arguments.
	 */
	public function __construct( APP_Listing $listing, $step_id = 'gateway-select', $args = array() ) {

		$this->listing = $listing;

		if ( empty( $args ) ) {
			$args = array(
				'register_to' => array(
					"{$this->listing->get_type()}-new" => array( 'after' => 'create-order' ),
					"{$this->listing->get_type()}-renew" => array( 'after' => 'create-order' ),
					"{$this->listing->get_type()}-upgrade" => array( 'after' => 'create-order' ),
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

		$type = $checkout->get_checkout_type();

		appthemes_add_template_var( array(
			'app_order'  => $order,
			'recurring'  => (bool) $checkout->get_data( 'recurring' ),
			'form_class' => "{$this->step_id} {$type} {$this->listing->get_type()}",
		) );

		parent::display( $order, $checkout );
	}

	public function condition() {
		return $this->listing->options->charge;
	}

	/**
	 * Processes step
	 *
	 * @param APP_Order            $order    Order object.
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 */
	public function process( $order, $checkout ) {

		if ( in_array( $order->get_status(), array( APPTHEMES_ORDER_COMPLETED, APPTHEMES_ORDER_ACTIVATED ) ) ) {
			$this->finish_step();
			return;
		}

		if ( ! $this->check_previous() ) {
			$this->cancel_step();
			return;
		}

		// Nothing to do with an empty orders.
		if ( $order->get_total() <= 0 ) {
			$this->finish_step();
			return;
		}

		$checkout->add_data( 'title', __( 'Select Gateway', APP_TD ) );

		if ( ! isset( $_POST[ $this->step_id ] ) ) { // Input var okay.
			return;
		}

		check_admin_referer( $checkout->get_checkout_type(), $this->step_id );

		if ( ! empty( $_POST['payment_gateway'] ) ) { // Input var okay.
			$gateway = sanitize_text_field( wp_unslash( $_POST['payment_gateway'] ) ); // Input var okay.
			$is_valid = $order->set_gateway( $gateway );
			if ( ! $is_valid ) {
				return;
			}

			$this->finish_step();
		}
	}
}
