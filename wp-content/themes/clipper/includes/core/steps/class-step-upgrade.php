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
class APP_Step_Upgrade extends APP_Step_Activate {

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
					"{$this->listing->get_type()}-upgrade",
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

		$checkout->add_data( 'title', __( 'Summary', APP_TD ) );
		$item_id = $checkout->get_data( 'listing_id' );
		$item    = get_post( $item_id );

		if ( ! $item ) {
			$this->errors->add( 'no-item', __( 'No listing item for processing', APP_TD ) );
			return;
		}

		// Pass the step for already activated item.
		if ( $this->check_step( $this->step_id ) ) {
			$this->finish_step();
			return;
		}

		if ( ! $this->check_previous() ) {
			$this->cancel_step();
			return;
		}

		$this->activate_items( $order );
		$this->finish_step();
	}

	/**
	 * Retrieves a current plan.
	 *
	 * @param string $plan_type Plan type.
	 *
	 * @return APP_Listing_Current_Plan_I The plan object.
	 */
	protected function get_plan( $plan_type = '' ) {
		/* @var $plans_mod APP_Listing_Plans_Registry */

		$item_id   = $this->checkout->get_data( 'listing_id' );
		$plans_mod = $this->listing->plans;
		$plan      = $plans_mod->get_current_plan( $item_id );

		return $plan;
	}
}
