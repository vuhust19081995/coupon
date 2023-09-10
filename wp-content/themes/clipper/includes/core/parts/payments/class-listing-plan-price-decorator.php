<?php
/**
 * Listing Plan Price Decorator.
 *
 * @package Listing\Modules\Payments
 * @author  AppThemes
 * @since   Listing 2.1
 */

/**
 * A Plan object decorator class.
 *
 * This decorator implements Plan Price for purchasable items.
 */
class APP_Listing_Plan_Price_Decorator extends APP_Listing_Plan_Decorator {

	/**
	 * Applies plan price to an order item.
	 *
	 * @param int       $item_id Listing ID.
	 * @param APP_Order $order   An Order object.
	 */
	public function apply( $item_id, APP_Order $order ) {
		$this->plan->apply( $item_id, $order );

		if ( $order->get_item( $this->get_type() ) ) {
			return;
		}

		$order->add_item( $this->get_type(), $this->get_price(), $item_id );
	}

	/**
	 * Retrieves plan price.
	 *
	 * @return string
	 */
	public function get_price() {
		return $this->price;
	}
}
