<?php
/**
 * Listing Checkout Step
 *
 * @package Listing\Views\Checkouts
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Checkout Step Base class
 */
class APP_Listing_Step extends APP_Checkout_Step {

	/**
	 * Current Listing module object.
	 *
	 * @var APP_Listing
	 */
	protected $listing;

	/**
	 * Constructs Listing step
	 *
	 * @param APP_Listing $listing Listing object to assign step with.
	 * @param string      $step_id Step ID.
	 * @param array       $args    Optional. An array of arguments.
	 */
	public function __construct( APP_Listing $listing, $step_id = '', $args = array() ) {

		$this->listing = $listing;

		parent::__construct( $step_id, $args );
	}

	/**
	 * Displays step
	 *
	 * @param APP_Order            $order    Order object.
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 */
	public function display( $order, $checkout ) {

		appthemes_add_template_var( array(
			'action_text'  => __( 'Next Step', APP_TD ),
			'action_url'   => appthemes_get_step_url(),
			'nonce_check'  => $checkout->get_checkout_type(),
			'action_check' => $this->step_id,
			'app_listing'  => $this->listing,
		) );

		appthemes_locate_template( array(
			"step-{$this->step_id}-{$checkout->get_checkout_type()}.php",
			"step-{$this->step_id}-{$this->listing->get_type()}.php",
			"step-{$this->step_id}.php",
			'step.php',
		), true );
	}


	/**
	 * Test if this step should registered in checkout.
	 *
	 * @return boolean
	 */
	public function condition() {
		return true;
	}

	/**
	 * Conditionally registers step in checkout process.
	 *
	 * @param APP_Dynamic_Checkout $checkout Current Checkout type instance.
	 */
	public function register( $checkout ) {
		if ( $this->condition() ) {
			parent::register( $checkout );
		}
	}

	/**
	 * Checks whether given step was completed.
	 *
	 * @param string $step_id The step ID.
	 *
	 * @return bool True if step was competed, false otherwise.
	 */
	public function check_step( $step_id ) {
		$completed = (array) $this->checkout->get_data( 'step-completed' );
		return in_array( $step_id, $completed, true );
	}

	/**
	 * Checks whether previous step was completed.
	 *
	 * @return boolean
	 */
	public function check_previous() {

		if ( $this->step_id === $this->checkout->get_previous_step() ) {
			return true;
		}

		$previous_step = $this->checkout->get_previous_step( $this->step_id );

		return $this->check_step( $previous_step );
	}

	/**
	 * Finish Checkout Step
	 */
	public function finish_step() {
		$completed = $this->checkout->get_data( 'step-completed' );

		if ( ! is_array( $completed ) ) {
			$completed = array();
		}

		if ( ! in_array( $this->step_id, $completed, true ) ) {
			$completed[] = $this->step_id;
		}

		$this->checkout->add_data( 'step-completed', $completed );

		parent::finish_step();
	}

}
