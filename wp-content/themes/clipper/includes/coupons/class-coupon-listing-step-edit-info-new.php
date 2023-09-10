<?php
/**
 * New Coupon Edit Info Checkout Step
 *
 * @package Clipper\Coupons
 * @author  AppThemes
 * @since   2.0.0
 */

/**
 * Listing Edit Info Checkout Step class
 */
class CLPR_Step_Edit_Info_New extends APP_Step_Edit_Info_New {

	/**
	 * Displays step
	 *
	 * @param APP_Order            $order    Order object.
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 */
	public function display( $order, $checkout ) {

		appthemes_add_template_var( array(
			'action_text' => ( $this->listing->options->charge ) ? __( 'Continue', APP_TD ) : __( 'Share It!', APP_TD ),
		) );

		parent::display( $order, $checkout );
	}

	/**
	 * Processes listing form
	 *
	 * @param APP_Order            $order    Order object.
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 */
	protected function process_form( $order, $checkout ) {
		$item_id = parent::process_form( $order, $checkout );
		$this->errors = apply_filters_deprecated( 'clpr_coupon_validate_fields', array( $this->errors ), '2.0.0' );

		if ( $this->errors->get_error_codes() ) {
			return $this->errors;
		}

		do_action( 'clpr_update_listing', $item_id, $order, $checkout );

		return $item_id;
	}
}
