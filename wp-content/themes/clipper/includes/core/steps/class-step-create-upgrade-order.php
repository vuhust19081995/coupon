<?php
/**
 * Create Upgrade Order Listing Step
 *
 * @package Listing\Views\Checkouts
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Create Upgrade Order Listing Step class
 *
 * Used in Upgrade process.
 */
class APP_Step_Create_Upgrade_Order extends APP_Step_Create_Order {

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
					"{$this->listing->get_type()}-upgrade",
				),
			);
		}

		parent::__construct( $listing, $step_id, $args );
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
