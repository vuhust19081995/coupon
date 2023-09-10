<?php
/**
 * Expire Listing Plan Decorator.
 *
 * @package Clipper\Coupons
 * @author  AppThemes
 * @since   2.0.0
 */

/**
 * Listing Plan Expire decorator class.
 */
class CLPR_Coupon_Listing_Plan_Expire_Decorator extends APP_Listing_Plan_Decorator {

	/**
	 * Activates plan.
	 *
	 * @param int       $item_id Listing ID.
	 * @param APP_Order $order   An Order object.
	 */
	public function activate( $item_id, APP_Order $order ) {
		$this->plan->activate( $item_id, $order );

		$checkout = appthemes_get_checkout();

		if ( ! $checkout || ! $data = $checkout->get_data( 'optional_data' ) ) {
			return;
		}

		if ( ! isset( $data['clpr_expire_date'] ) ) {
			return;
		}

		update_post_meta( $item_id, 'clpr_expire_date', $data['clpr_expire_date'] );
	}
}
