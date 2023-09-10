<?php
/**
 * Gateway Process Checkout Step
 *
 * @package Listing\Views\Checkouts
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Gateway Process Checkout Step class
 */
class APP_Step_Gateway_Process extends APP_Listing_Step {

	/**
	 * Construct Gateway Process step
	 *
	 * @param APP_Listing $listing Listing object to assign step with.
	 * @param string      $step_id Step ID.
	 * @param array       $args    Optional. An array of arguments.
	 */
	public function __construct( APP_Listing $listing, $step_id = 'gateway-process', $args = array() ) {

		$this->listing = $listing;

		if ( empty( $args ) ) {
			$args = array(
				'register_to' => array(
					"{$this->listing->get_type()}-new" => array( 'after' => 'gateway-select' ),
					"{$this->listing->get_type()}-renew" => array( 'after' => 'gateway-select' ),
					"{$this->listing->get_type()}-upgrade" => array( 'after' => 'gateway-select' ),
				),
			);
		}

		parent::__construct( $listing, $step_id, $args );
	}

	/**
	 * Check if this step should registered in checkout.
	 *
	 * @return boolean
	 */
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
			update_post_meta( $order->get_id(), 'activate_url', appthemes_get_step_url( 'activate-order' ) );
			$this->finish_step();
			return;
		}

		if ( ! $this->check_previous() ) {
			$this->cancel_step();
			return;
		}

		$checkout->add_data( 'title', __( 'Order Process', APP_TD ) );
		update_post_meta( $order->get_id(), 'complete_url', appthemes_get_step_url( $this->step_id ) );
		update_post_meta( $order->get_id(), 'cancel_url', appthemes_get_step_url( 'gateway-select' ) );

		if ( $order->get_total() > 0 ) {
			wp_redirect( $order->get_return_url() );
			appthemes_exit( 'exit_step_' . $checkout->get_current_step() );
		}

		$order->complete();
		$this->finish_step();
	}
}
