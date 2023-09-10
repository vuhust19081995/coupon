<?php
/**
 * Pricing Plan Select Checkout Step
 *
 * @package Listing\Views\Checkouts
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Pricing Plan Select Checkout Step class
 */
class APP_Step_Select_Plan extends APP_Listing_Step {

	/**
	 * Constructs Select Pricing Plan step
	 *
	 * @param APP_Listing $listing Listing object to assign step with.
	 * @param string      $step_id Step ID.
	 * @param array       $args    Optional. An array of arguments.
	 */
	public function __construct( APP_Listing $listing, $step_id = 'select-plan', $args = array() ) {

		$this->listing = $listing;

		if ( empty( $args ) ) {
			$args = array(
				'register_to' => array(
					"{$this->listing->get_type()}-new",
					"{$this->listing->get_type()}-renew",
				),
			);
		}

		parent::__construct( $listing, $step_id, $args );
	}

	/**
	 * Processes step.
	 *
	 * Retrieves Plan type and stores it in Checkout meta for further use.
	 *
	 * @param APP_Order            $order    Order object.
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 */
	public function process( $order, $checkout ) {

		// Nothing to do if order already completed or activated.
		if ( in_array( $order->get_status(), array( APPTHEMES_ORDER_COMPLETED, APPTHEMES_ORDER_ACTIVATED ) ) ) {
			$this->finish_step();
			return;
		}

		$checkout->add_data( 'title', __( 'Pricing Plan', APP_TD ) );

		if ( $this->listing->options->bypass_plan && $this->get_plan( $this->listing->get_type() ) ) {
			$data = array( 'plan' => $this->listing->get_type() );
		} else {
			if ( ! isset( $_POST[ $this->step_id ] ) || ! isset( $_POST['plan'] ) ) { // Input var okay.
				return;
			}

			check_admin_referer( $checkout->get_checkout_type(), $this->step_id );

			$data  = wp_unslash( $_POST ); // Input var okay.
		}

		$plan_type = sanitize_text_field( $data['plan'] );

		$this->validate( $data );

		if ( $this->errors->get_error_codes() ) {
			return false;
		}

		$plan = $this->get_plan( $plan_type );
		$plan->setup( $checkout, $data );

		do_action( 'appthemes_process_purchase_fields', $checkout );

		$this->finish_step();
	}

	/**
	 * Displays step
	 *
	 * @param APP_Order            $order    Order object.
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 */
	public function display( $order, $checkout ) {

		$type  = $checkout->get_checkout_type();
		$plans = $this->get_plans();

		appthemes_add_template_var( array(
			'form_class' => "{$this->step_id} {$type} {$this->listing->get_type()}",
			'plans'      => $plans,
		) );

		parent::display( $order, $checkout );
	}

	/**
	 * Retrieves the list of plans available to select from.
	 *
	 * @return array The list of available plans objects.
	 */
	protected function get_plans() {
		return $this->listing->plans->get_plans();
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
	 * Validates given plan type.
	 *
	 * @param array $data Stripslashed posted data.
	 */
	protected function validate( $data ) {

		if ( empty( $data['plan'] ) ) {
			$this->errors->add( 'no-plan', __( 'No plan was chosen.', APP_TD ) );
			return;
		}

		$plan = $this->get_plan( $data['plan'] );

		if ( ! $plan ) {
			$this->errors->add( 'invalid-plan', __( 'The plan you choose no longer exists.', APP_TD ) );
			return;
		}

		// Allows to add custom errors.
		$this->errors = apply_filters( 'appthemes_validate_purchase_fields', $this->errors, $data );
	}
}
