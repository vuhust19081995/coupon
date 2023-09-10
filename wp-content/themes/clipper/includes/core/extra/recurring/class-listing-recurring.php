<?php
/**
 * Listing Recurring Payments submodule
 *
 * @package Listing\Modules\Recurring
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Recurring Payments class
 */
class APP_Listing_Recurring {

	/**
	 * Current Listing module object.
	 *
	 * @var APP_Listing
	 */
	protected $listing;

	/**
	 * Recurring settings object.
	 *
	 * @var type APP_Listing_Recurring_Settings
	 */
	public $settings;

	/**
	 * Multiple Plans Recurring metabox.
	 *
	 * @var type APP_Listing_Recurring_Box
	 */
	public $plan_box;

	/**
	 * Construct Listing Recurring Payments module
	 *
	 * @param APP_Listing $listing Listing module object.
	 */
	public function __construct( APP_Listing $listing ) {

		$this->listing = $listing;
		$this->listing->options->set_defaults( $this->get_defaults() );
		$this->activate_modules();

		add_filter( "appthemes_decorate_listing_plan_{$this->listing->get_type()}", array( $this, '_decorate_plan' ), 999 );
		add_filter( "appthemes_decorate_current_listing_plan_{$this->listing->get_type()}", array( $this, '_decorate_current_plan' ), 999 );
		add_action( 'appthemes_transaction_activated', array( $this, '_handle_activated_transaction' ) );

		// TODO: attach it to Delete Listing process in future versions.
		if ( 'post' === $this->listing->meta->get_meta_type() ) {
			add_action( 'transition_post_status', array( $this, '_handle_deleted_listing_status_transition' ), 10, 3 );
			add_action( 'delete_post', array( $this, '_handle_deleted_listing' ) );
		}
	}

	/**
	 * Retrieves module's options default values to be registered in Listing
	 * options object.
	 */
	public function get_defaults() {
		return array( 'recurring' => 'non_recurring' );
	}

	/**
	 * Activates submodules
	 */
	protected function activate_modules() {
		if ( ! $this->settings && is_admin() ) {
			$this->settings = new APP_Listing_Recurring_Settings( $this, $this->listing );
		}

		if ( ! $this->plan_box && is_admin() && $this->listing->multi_plans ) {
			$plan_ptype = $this->listing->multi_plans->get_plan_type();
			$this->plan_box = new APP_Listing_Recurring_Box( $this, $plan_ptype );
		}
	}

	/**
	 * Decorates given plan with a recurring specific.
	 *
	 * @param APP_Listing_Plan_I $plan Given listing plan object.
	 *
	 * @return \APP_Listing_Plan_Recurring_Decorator Decorated plan object.
	 */
	public function _decorate_plan( APP_Listing_Plan_I $plan ) {
		return new APP_Listing_Plan_Recurring_Decorator( $plan );
	}

	/**
	 * Decorates given current plan with a recurring specific.
	 *
	 * @param APP_Listing_Plan_I $plan Given listing plan object.
	 *
	 * @return \APP_Listing_Plan_Recurring_Decorator Decorated plan object.
	 */
	public function _decorate_current_plan( APP_Listing_Plan_I $plan ) {
		return new APP_Listing_Current_Plan_Recurring_Decorator( $plan );
	}

	/**
	 * Re-activates recurring plan after transaction activated.
	 *
	 * Calls on 'appthemes_transaction_activated' action
	 *
	 * @todo maybe it needs to organize dedicated process checkout. Get original
	 * checkout process from the orginal parent order and setup new checkout of
	 * same type. Set 'activate_url' order meta as url to 'activate_order' step
	 * of this process. On 'appthemes_transaction_activated' payments module
	 * will run the checkout and complete the process.
	 *
	 * @param APP_Order $order Order object.
	 * @return type
	 */
	public function _handle_activated_transaction( $order ) {

		if ( ! $order->is_recurring() || ! $order->get_info( 'parent' ) ) {
			return;
		}

		$item_id = $this->listing->payments->get_order_post_id( $order );

		// Needs for checking the listing type.
		if ( ! $item_id ) {
			return;
		}

		/* @var $plans_mod APP_Listing_Plans_Registry */
		/* @var $plan APP_Listing_Plan_I */
		$plans_mod = $this->listing->plans;
		$plan = $plans_mod->get_last_plan( $item_id, $order );

		$plan->activate( $item_id, $order );
	}

	/**
	 * Checks post status transition and cancels recurring orders if needed.
	 *
	 * @param string  $new_status New status.
	 * @param string  $old_status Old status.
	 * @param WP_Post $post       The listing item.
	 */
	public function _handle_deleted_listing_status_transition( $new_status, $old_status, $post ) {
		if ( $post->post_type !== $this->listing->get_type() || ! in_array( $new_status, array( 'trash', 'deleted' ), true ) ) {
			return;
		}

		$this->_handle_deleted_listing( $post->ID );
	}

	/**
	 * Deletes pending recurring orders for given listing ID.
	 *
	 * @param int $item_id The listing item ID.
	 */
	public function _handle_deleted_listing( $item_id ) {
		$item = get_post( $item_id );
		if ( $item->post_type !== $this->listing->get_type() ) {
			return;
		}

		$orders = _appthemes_orders_get_connected( $item_id, array(
			'post_parent__not_in' => array( '0' ),
			'post_status'         => array( APPTHEMES_ORDER_PENDING ),
			'connected_query'     => array( 'post_status' => $item->post_status ),
			'posts_per_page'      => -1,
		) );

		foreach ( $orders->posts as $order ) {
			wp_delete_post( $order->ID );
		}
	}

}
