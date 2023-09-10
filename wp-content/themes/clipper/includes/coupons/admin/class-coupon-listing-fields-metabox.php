<?php
/**
 * Coupon Listing Fields meta box
 *
 * @package Clipper\Coupons
 * @author  AppThemes
 * @since   2.0.0
 */

/**
 * Coupon Listing Fields meta box class.
 */
class CLPR_Coupon_Listing_Fields_Metabox extends APP_Listing_Fields_Metabox {

	/**
	 * Retrieves a list of field attributes used to excluded certain fields from
	 * the final field list.
	 *
	 * @return array An associative array with field attribute/value(s)
	 *               (e.g: array( 'name' => 'my-unwanted-custom-field' ) ).
	 */
	public function exclude_filters() {
		return array( 'name' => array( 'clpr_expire_date' ) );
	}

}
