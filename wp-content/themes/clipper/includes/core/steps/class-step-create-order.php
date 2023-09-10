<?php
/**
 * Create Order Listing Step
 *
 * @package Listing\Views\Checkouts
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Create Order Listing Step class
 */
class APP_Step_Create_Order extends APP_Listing_Step {

	/**
	 * Construct Create Order step
	 *
	 * @param APP_Listing $listing Listing object to assign step with.
	 * @param string      $step_id Step ID.
	 * @param array       $args    Optional. An array of arguments.
	 */
	public function __construct( APP_Listing $listing, $step_id = 'create-order', $args = array() ) {

		$this->listing = $listing;

		if ( empty( $args ) ) {
			$args = array(
				'register_to' => array(
					"{$this->listing->get_type()}-new" => array( 'after' => 'edit-info' ),
					"{$this->listing->get_type()}-renew",
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

		if ( ! $this->check_previous() ) {
			$this->cancel_step();
			return;
		}

		$checkout->add_data( 'title', __( 'Create Order', APP_TD ) );

		// Pass the step if order is already completed or activated.
		if ( in_array( $order->get_status(), array( APPTHEMES_ORDER_COMPLETED, APPTHEMES_ORDER_ACTIVATED ) ) ) {
			$this->finish_step();
			return;
		}

		$item_id = $checkout->get_data( 'listing_id' );
		$item    = get_post( $item_id );

		if ( ! $item ) {
			$this->errors->add( 'no-item', __( 'No listing item for processing', APP_TD ) );
			return;
		}

		// Clear all already added items.
		$order->remove_item();

		// Apply items.
		$this->apply_items( $order, $checkout );
		$this->add_order_description( $order, $item_id );

		if ( $this->errors->get_error_codes() ) {
			$this->cancel_step();
			return;
		}

		do_action( 'appthemes_create_order', $order );

		// Created order can live two weeks awaiting payment.
		$checkout->set_expiration( 14 * DAY_IN_SECONDS );

		$this->finish_step();

	}

	/**
	 * Applies items to an order
	 *
	 * @param APP_Order $order Order object.
	 */
	protected function apply_items( APP_Order $order ) {

		$item_id = $this->checkout->get_data( 'listing_id' );
		$plan_id = $this->checkout->get_data( 'plan' );

		$plan = $this->get_plan( $plan_id );

		if ( $plan instanceof APP_Listing_Plan_I ) {
			$plan->apply( $item_id, $order );
		}

	}

	/**
	 * Retrieves a plan by given type.
	 *
	 * @param string $plan_type Plan type.
	 *
	 * @return APP_Listing_Plan_I The plan object.
	 */
	protected function get_plan( $plan_type = '' ) {
		/* @var $plans APP_Listing_Plans_Registry */

		$plans = $this->listing->plans;

		return $plans->get_plan( $plan_type );
	}

	/**
	 * Adds descrition to the order
	 *
	 * @param APP_Order $order   The Order object.
	 * @param int       $item_id The Item ID.
	 */
	protected function add_order_description( $order, $item_id ) {

		$type_obj = get_post_type_object( get_post_type( $item_id ) );
		$tags = array(
			'%title%' => get_the_title( $item_id ),
			'%type%'  => $type_obj->labels->name,
		);

		$tags   = apply_filters( 'appthemes_order_description_tags', $tags );
		$format = apply_filters( 'appthemes_order_description_format', __( '%type%: %title%', APP_TD ) );

		foreach ( $tags as $tag => $value ) {
			$format = str_ireplace( $tag, $value, $format );
		}

		$order->set_description( $format );
	}
}
