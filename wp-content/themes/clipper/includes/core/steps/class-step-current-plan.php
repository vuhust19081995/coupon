<?php
/**
 * Current Plan Checkout Step
 *
 * @package Listing\Views\Checkouts
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Current Plan Checkout Step class
 */
class APP_Step_Current_Plan extends APP_Step_Select_Plan {

	/**
	 * Constructs Current Plan step
	 *
	 * @param APP_Listing $listing Listing object to assign step with.
	 * @param string      $step_id Step ID.
	 * @param array       $args    Optional. An array of arguments.
	 */
	public function __construct( APP_Listing $listing, $step_id = 'current-plan', $args = array() ) {

		$this->listing = $listing;

		if ( empty( $args ) ) {
			$args = array(
				'register_to' => array(
					"{$this->listing->get_type()}-upgrade",
				),
			);
		}

		parent::__construct( $listing, $step_id, $args );
	}

	/**
	 * Retrieves the list of plans available to select from.
	 *
	 * @return array The list of available plans objects.
	 */
	protected function get_plans() {
		$plan      = $this->get_plan();
		$plans     = array( $plan->get_type() => $plan );

		return $plans;
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
