<?php
/**
 * Coupon Edit Info Checkout Step
 *
 * @package Clipper\Views\Checkouts
 * @author  AppThemes
 * @since   2.0.0
 */

/**
 * Listing Edit Info Checkout Step class
 */
class CLPR_Step_Edit_Info extends APP_Step_Edit_Info {

	/**
	 * Displays step
	 *
	 * @param APP_Order            $order    Order object.
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 */
	public function display( $order, $checkout ) {

		appthemes_add_template_var( array(
			'action_text' => __( 'Update Coupon &raquo;', APP_TD ),
		) );

		parent::display( $order, $checkout );
	}

}
