<?php
/**
 * Create Anonymous User Checkout Step.
 *
 * @package Listing\Views\Checkouts
 * @author  AppThemes
 * @since   Listing 2.0
 */

/**
 * Create User Checkout Step class
 */
class APP_Step_Create_User extends APP_Listing_Step {

	/**
	 * Constructs step
	 *
	 * @param APP_Listing $listing Listing object to assign step with.
	 * @param string      $step_id Step ID.
	 * @param array       $args    Optional. An array of arguments.
	 */
	public function __construct( APP_Listing $listing, $step_id = 'create-user', $args = array() ) {

		$this->listing = $listing;

		if ( empty( $args ) ) {
			$args = array(
				'register_to' => array(
					"{$this->listing->get_type()}-anonym",
				),
			);
		}

		parent::__construct( $listing, $step_id, $args );
	}

	/**
	 * Test if this step should registered in checkout.
	 *
	 * @return boolean
	 */
	public function condition() {
		return ! is_user_logged_in();
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

		// Nothing to do if user already logged-in.
		if ( is_user_logged_in() ) {
			$this->finish_step();
			return;
		}

		if ( ! isset( $_POST[ $this->step_id ] ) ) { // Input var okay.
			return;
		}

		check_admin_referer( $checkout->get_checkout_type(), $this->step_id );

		$data = wp_unslash( $_POST ); // Input var okay.

		$this->validate( $data );

		if ( $this->errors->get_error_codes() ) {
			return false;
		}

		$user_id = wp_insert_user( array(
			'user_login'    => $this->listing->anonymous->get_login( $checkout ),
			'nickname'      => $this->listing->anonymous->get_nickname(),
			'display_name'  => __( 'Anonymous', APP_TD ),
			'role'          => 'contributor',
			'user_pass'     => '',
		) );

		if ( is_wp_error( $user_id ) ) {
			$this->errors = $user_id;
		} else {
			$this->finish_step();

			// Set the WP login cookie (log the user in).
			wp_set_current_user( $user_id );
			wp_set_auth_cookie( $user_id, true, is_ssl() );

			$redirect_url = esc_url_raw( appthemes_get_checkout_url() );

			// Redirect.
			wp_redirect( $redirect_url );
			appthemes_exit( 'redirect_to_create_listing_page' );
		}
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
			'form_class'  => "{$this->step_id} {$type} {$this->listing->get_type()}",
			'action_text' => __( 'Continue anonymously', APP_TD ),
		) );

		parent::display( $order, $checkout );
	}

	/**
	 * Validates data.
	 *
	 * @param array $data Stripslashed posted data.
	 */
	protected function validate( $data ) {
		// Allows to add custom errors.
		$this->errors = apply_filters( 'appthemes_validate_create_anonymous_fields', $this->errors, $data );
	}
}
