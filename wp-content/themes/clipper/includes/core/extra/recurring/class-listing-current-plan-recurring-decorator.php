<?php
/**
 * Listing Plan Recurring decorator
 *
 * @package Listing\Modules\Recurring
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Plan Recurring decorator class.
 */
class APP_Listing_Current_Plan_Recurring_Decorator extends APP_Listing_Plan_Decorator implements APP_Listing_Current_Plan_I {

	/**
	 * Decorated plan object.
	 *
	 * @var APP_Listing_Current_Plan_I
	 */
	protected $plan;

	/**
	 * Retrieves the listing item id to which current plan has been applied.
	 *
	 * @return int The Listing Item ID.
	 */
	public function get_item_id() {
		return $this->plan->get_item_id();
	}

	/**
	 * Retrives the plan expire date.
	 *
	 * @return int Expire date timestamp.
	 */
	public function get_expire_date() {
		return $this->plan->get_expire_date();
	}

	/**
	 * Deactivates plan.
	 */
	public function deactivate() {

		$order = $this->_get_pending_recurring_order();

		if ( ! $order ) {
			$this->plan->deactivate();
		}
	}

	/**
	 * Generates period text.
	 */
	public function get_period_text() {

		$order = $this->_get_pending_recurring_order();

		if ( ! $order ) {
			return $this->plan->get_period_text();
		}

		$order_date = get_post_time( 'U', false, $order->get_id() );
		$next_date  = appthemes_display_date( $order_date, 'date' );
		$output     = sprintf( __( 'Next payment on %s', APP_TD ), $next_date );

		return $output;
	}

	/**
	 * Retrieves pending recurring order associated with current listing item
	 * and plan.
	 *
	 * @return APP_Order Order object.
	 */
	private function _get_pending_recurring_order() {
		$order = appthemes_get_order_connected_to( $this->get_item_id(),
			array(
				'post_status' => array( APPTHEMES_ORDER_PENDING ),
				'meta_query'  => array(
					array(
						'key'     => 'recurring_period',
						'value'   => '',
						'compare' => '!=',
					),
				),
				'connected_meta' => array(
					array(
						'key'     => 'type',
						'value'   => $this->get_type(),
					),
				),
			)
		);

		if ( $order && $order->is_recurring() ) {
			return $order;
		}
	}

	/**
	 * Whether current plan could be upgraded.
	 *
	 * @return bool True if current plan could be upgraded, false otherwise.
	 */
	public function is_upgradable() {
		return $this->plan->is_upgradable();
	}
}
