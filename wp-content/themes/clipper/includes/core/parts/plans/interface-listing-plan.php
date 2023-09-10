<?php
/**
 * Listing Plan Interface.
 *
 * @package Listing
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Basic Listing Plan Interface.
 */
interface APP_Listing_Plan_I {

	/**
	 * Adds Plan specific data to a given checkout object.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object for setup.
	 * @param array                $data     Stripslashed posted data.
	 */
	public function setup( APP_Dynamic_Checkout $checkout, $data = array() );

	/**
	 * Renders optional plan fields
	 */
	public function render();

	/**
	 * Applies plan attributes to an order item.
	 *
	 * @param int       $item_id Listing ID.
	 * @param APP_Order $order   An Order object.
	 */
	public function apply( $item_id, APP_Order $order );

	/**
	 * Activates plan.
	 *
	 * @param int       $item_id Listing ID.
	 * @param APP_Order $order   An Order object.
	 */
	public function activate( $item_id, APP_Order $order );

	/**
	 * Determines whether plan has optional items to be selected by user.
	 *
	 * @return bool True if plan has options, False otherwise.
	 */
	public function has_options();

	/**
	 * Retrieves a plan type.
	 *
	 * @return string Plan type.
	 */
	public function get_type();

	/**
	 * Retrieves a plan title.
	 *
	 * @return string Plan title.
	 */
	public function get_title();

	/**
	 * Retrieves a plan description.
	 *
	 * @return string Plan description.
	 */
	public function get_description();

	/**
	 * Retrieves plan duration in days.
	 *
	 * @return int
	 */
	public function get_duration();

	/**
	 * Retrieves plan duration meta key.
	 *
	 * @return int
	 */
	public function get_duration_key();

	/**
	 * Retrieves plan duration in other time units, like months or years.
	 *
	 * @return int
	 */
	public function get_period();

	/**
	 * Generates period text.
	 */
	public function get_period_text();

	/**
	 * Retrieves period type (D, W, M, Y).
	 *
	 * @var string
	 */
	public function get_period_type();

	/**
	 * Retrieves plan price.
	 *
	 * @return string
	 */
	public function get_price();

}

/**
 * Current Listing Plan Interface.
 */
interface APP_Listing_Current_Plan_I extends APP_Listing_Plan_I {

	/**
	 * Retrieves the listing item id to which current plan has been applied.
	 *
	 * @return int The Listing Item ID.
	 */
	public function get_item_id();

	/**
	 * Retrives the plan expire date.
	 *
	 * @return int Expire date timestamp.
	 */
	public function get_expire_date();

	/**
	 * Deactivates plan.
	 */
	public function deactivate();

	/**
	 * Whether current plan could be upgraded.
	 *
	 * @return bool True if current plan could be upgraded, false otherwise.
	 */
	public function is_upgradable();

}
