<?php
/**
 * Coupon Listing Plans Settings submodule
 *
 * @package Clipper\Coupons
 * @author  AppThemes
 * @since   2.0.2
 */

/**
 * Listing Settings class
 *
 */
class CLPR_Coupon_Listing_Plans_Settings extends APP_Listing_Plans_Settings {

	/**
	 * Registers Listing settings
	 *
	 * @param APP_Tabs_Page $page Current settings page object.
	 */
	public function register( $page ) {
		parent::register( $page );

		$fields = &$page->tab_sections[ $this->listing->get_type() ]['default_plan']['fields'];
		$fields = wp_list_filter( $fields, array( 'name' => 'duration' ), 'NOT' );
	}

}
