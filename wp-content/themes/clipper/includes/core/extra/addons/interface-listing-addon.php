<?php
/**
 * Listing Addon Interface.
 *
 * @package Listing\Modules\Addons
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Addon Interface.
 */
interface APP_Listing_Addon_I {

	/**
	 * Adds addon to an order item.
	 *
	 * @param int       $item_id Listing ID.
	 * @param APP_Order $order   An Order object.
	 */
	public function apply( $item_id, APP_Order $order );

	/**
	 * Activates addon.
	 *
	 * @param int $item_id Listing ID.
	 */
	public function activate( $item_id );

	/**
	 * Connect Plan object to Addon.
	 *
	 * @param APP_Listing_Plan_I $plan The Plan object.
	 */
	public function connect( APP_Listing_Plan_I $plan );

	/**
	 * Retrieves an addon type.
	 *
	 * @return string Addon type.
	 */
	public function get_type();

	/**
	 * Retrieves an addon title.
	 *
	 * @return string Addon title.
	 */
	public function get_title();

	/**
	 * Retrieves addon duration in days.
	 *
	 * @return int
	 */
	public function get_duration();

	/**
	 * Retrieves addon duration in other time units, like months or years.
	 *
	 * @return int
	 */
	public function get_period();

	/**
	 * Retrieves period type (D, W, M, Y).
	 *
	 * @var string
	 */
	public function get_period_type();

	/**
	 * Retrieves addon price.
	 *
	 * @return string
	 */
	public function get_price();

	/**
	 * Generates period text.
	 */
	public function get_period_text();

	/**
	 * Generates purchase addon option HTML.
	 *
	 * @return string Generated HTML.
	 */
	public function get_purchase_option();

}
