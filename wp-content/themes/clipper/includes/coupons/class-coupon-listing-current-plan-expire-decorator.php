<?php
/**
 * Listing Current Plan Expire decorator
 *
 * @package Clipper\Coupons
 * @author  AppThemes
 * @since   2.0.0
 */

/**
 * Listing Current Plan Expire decorator class.
 */
class CLPR_Coupon_Listing_Current_Plan_Expire_Decorator extends APP_Listing_Current_Plan_Expire_Decorator {

	/**
	 * Retrives the plan expire date for current listing item.
	 *
	 * @return int Expire date timestamp.
	 */
	public function get_expire_date() {

		$expire_date = get_post_meta( $this->get_item_id(), 'clpr_expire_date', true );

		if ( $expire_date ) {
			$end_date = mysql2date( 'U', $expire_date );
		} else {
			$end_date = parent::get_expire_date();
		}

		return $end_date;
	}
}
