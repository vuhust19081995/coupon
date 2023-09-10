<?php
/**
 * Activate Listing Order Step
 *
 * @package Listing\Views\Checkouts
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Activate Listing Step class
 */
class APP_Step_Activate_Order extends APP_Listing_Step {

	/**
	 * Construct Activate Order step
	 *
	 * @param APP_Listing $listing Listing object to assign step with.
	 * @param string      $step_id Step ID.
	 * @param array       $args    Optional. An array of arguments.
	 */
	public function __construct( APP_Listing $listing, $step_id = 'activate-order', $args = array() ) {

		$this->listing = $listing;

		if ( empty( $args ) ) {
			$args = array(
				'priority'    => 999,
				'register_to' => array(
					"{$this->listing->get_type()}-new" => array( 'before' => 'activate' ),
					"{$this->listing->get_type()}-renew" => array( 'before' => 'activate' ),
					"{$this->listing->get_type()}-upgrade" => array( 'before' => 'activate' ),
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

		if ( APPTHEMES_ORDER_ACTIVATED === $order->get_status() ) {
			$this->finish_step();
			return;
		}

		if ( ! $this->check_previous() ) {
			$this->cancel_step();
			return;
		}

		$checkout->add_data( 'title', __( 'Activate Order', APP_TD ) );

		if ( APPTHEMES_ORDER_ACTIVATED !== $order->get_status() ) {
			$order->activate();
		}

		$this->finish_step();
	}
}
