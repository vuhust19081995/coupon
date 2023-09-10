<?php
/**
 * Activate Listing Step
 *
 * @package Listing\Views\Checkouts
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Activate Listing Step class
 */
class APP_Step_Activate extends APP_Listing_Step {

	/**
	 * Construct Activate Listing step
	 *
	 * @param APP_Listing $listing Listing object to assign step with.
	 * @param string      $step_id Step ID.
	 * @param array       $args    Optional. An array of arguments.
	 */
	public function __construct( APP_Listing $listing, $step_id = 'activate', $args = array() ) {

		$this->listing = $listing;

		if ( empty( $args ) ) {
			$args = array(
				'priority'    => 999,
				'register_to' => array(
					"{$this->listing->get_type()}-new",
					"{$this->listing->get_type()}-renew",
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

		if ( ! $order instanceof APP_Draft_Order ) {
			appthemes_add_template_var( 'app_order' , $order );
		}

		$post_type_obj = get_post_type_object( $this->listing->get_type() );

		appthemes_add_template_var( array(
			'action_text'  => sprintf( __('Continue to %s', APP_TD ), $post_type_obj->labels->singular_name ),
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

		$checkout->add_data( 'title', __( 'Summary', APP_TD ) );
		$item_id = $checkout->get_data( 'listing_id' );
		$item    = get_post( $item_id );

		if ( ! $item ) {
			$this->errors->add( 'no-item', __( 'No listing item for processing', APP_TD ) );
			return;
		}

		// Pass the step for already activated item.
		if ( 'publish' === $item->post_status && $this->check_step( $this->step_id ) ) {
			$this->finish_step();
			return;
		}

		if ( ! $this->check_previous() ) {
			$this->cancel_step();
			return;
		}

		$this->activate_items( $order );

		wp_update_post( array(
			'ID' => $item_id,
			'post_status' => 'publish',
		) );

		$this->finish_step();
	}

	/**
	 * Activates items.
	 *
	 * @param APP_Order $order Order object.
	 */
	protected function activate_items( APP_Order $order ) {

		$item_id = $this->checkout->get_data( 'listing_id' );
		$plan_id = $this->checkout->get_data( 'plan' );

		$plan = $this->get_plan( $plan_id );

		if ( $plan instanceof APP_Listing_Plan_I ) {
			$plan->activate( $item_id, $order );
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
}
