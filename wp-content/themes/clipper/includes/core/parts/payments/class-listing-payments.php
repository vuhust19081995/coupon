<?php
/**
 * Listing Payments submodule.
 *
 * @package Listing\Payments
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Payments class.
 */
class APP_Listing_Payments {

	/**
	 * Current Listing module object.
	 *
	 * @var APP_Listing
	 */
	protected $listing;

	/**
	 * Construct Payments Listing module
	 *
	 * @param APP_Listing $listing Listing module object.
	 */
	public function __construct( APP_Listing $listing ) {

		$this->listing = $listing;
		$this->listing->options->set_defaults( $this->get_defaults() );

		if ( did_action( 'init' ) ) {
			$this->_register_payment_item();
		} else {
			add_action( 'init', array( $this, '_register_payment_item' ), 1000 );
		}

		// Register order item posts types in Payments module.
		add_filter( 'appthemes_order_item_posts_types', array( $this, '_order_item_posts_types' ) );

		// Handle checkout triggers.
		add_action( 'appthemes_checkout_completed', array( $this, '_handle_completed_checkout' ) );
		add_action( 'appthemes_transaction_completed', array( $this, '_handle_completed_transaction' ) );
		add_action( 'appthemes_transaction_activated', array( $this, '_handle_activated_transaction' ) );
		add_action( 'appthemes_transaction_failed', array( $this, '_handle_failed_transaction' ) );

		add_filter( "appthemes_decorate_listing_plan_{$this->listing->get_type()}", array( $this, '_decorate_plan' ) );
	}

	/**
	 * Retrieves module's options default values to be registered in Listing
	 * options object.
	 *
	 * @return array The Form defaults.
	 */
	public function get_defaults() {
		return array(
			'price' => 0,
		);
	}

	/**
	 * Registers post types in Payments module
	 *
	 * @param array $initial_types  Already registered post types.
	 * @return array $initial_types Updated array
	 */
	public function _order_item_posts_types( $initial_types ) {
		$initial_types = wp_parse_args( $initial_types, array( $this->listing->get_type() ) );
		return $initial_types;
	}

	/**
	 * Registers Listing type as regular payment item
	 */
	public function _register_payment_item() {

		$post_type = get_post_type_object( $this->listing->get_type() );
		$singular  = $post_type->labels->singular_name;

		APP_Item_Registry::register( $this->listing->get_type(), $singular );
		APP_Item_Registry::register( "{$this->listing->get_type()}-renew", __( 'Listing Renewal', APP_TD ) );
	}

	/**
	 * Ensures completed order at the end of checkout.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 */
	public function _handle_completed_checkout( $checkout ) {

		$order_id = $checkout->get_data( 'order_id' );

		if ( ! $order_id ) {
			return;
		}

		$order = appthemes_get_order( $order_id );

		if ( APPTHEMES_ORDER_PENDING === $order->get_status() ) {
			$order->complete();
		}

		if ( APPTHEMES_ORDER_COMPLETED === $order->get_status() ) {
			$order->activate();
		}
	}

	/**
	 * Continues checkout after transaction completed.
	 *
	 * Calls on 'appthemes_transaction_completed' action
	 *
	 * @param APP_Order $order Order object.
	 */
	public function _handle_completed_transaction( $order ) {

		$listing_id = $this->get_order_post_id( $order );

		// Needs for checking the listing type.
		if ( ! $listing_id ) {
			return;
		}

		$emails = new APP_Listing_Payments_Emails( $this->listing );
		$emails->send_user_receipt( $order, $listing_id );

		$complete_url  = get_post_meta( $order->get_id(), 'complete_url', true );
		$checkout_type = get_post_meta( $order->get_id(), 'checkout_type', true );

		if ( $complete_url ) {
			appthemes_process_checkout_by_url( $checkout_type, $complete_url );
		} elseif ( $checkout_type ) {
			$hash = get_post_meta( $order->get_id(), 'checkout_hash', true );
			appthemes_process_background_checkout( $checkout_type, $hash, 'gateway-process' );
		}

	}

	/**
	 * Continues checkout after transaction activated
	 *
	 * Calls on 'appthemes_transaction_activated' action
	 *
	 * @param APP_Order $order Order object.
	 * @return type
	 */
	public function _handle_activated_transaction( $order ) {

		$item_id = $this->get_order_post_id( $order );

		// Needs for checking the listing type.
		if ( ! $item_id ) {
			return;
		}

		$activate_url  = get_post_meta( $order->get_id(), 'activate_url', true );
		$checkout_type = get_post_meta( $order->get_id(), 'checkout_type', true );

		// Process current checkout if exists.
		if ( $checkout_type && $activate_url ) {
			appthemes_process_checkout_by_url( $checkout_type, $activate_url );
		} elseif ( $checkout_type ) {
			$hash = get_post_meta( $order->get_id(), 'checkout_hash', true );
			appthemes_process_background_checkout( $checkout_type, $hash, 'activate-order' );
		}
	}

	/**
	 * Handles failed transactions.
	 *
	 * @param APP_Order $order Order object.
	 */
	public function _handle_failed_transaction( $order ) {

		$listing_id = $this->get_order_post_id( $order );

		// Needs for checking the listing type.
		if ( ! $listing_id ) {
			return;
		}

		$emails = new APP_Listing_Payments_Emails( $this->listing );
		$emails->notify_admin_failed_transaction( $order );
	}

	/**
	 * Decorates given plan.
	 *
	 * @param APP_Listing_Plan_I $plan Given listing plan object.
	 *
	 * @return \APP_Listing_Plan_Price_Decorator Decorated plan object.
	 */
	public function _decorate_plan( APP_Listing_Plan_I $plan ) {
		return new APP_Listing_Plan_Price_Decorator( $plan, $this->listing->get_type() );
	}

	/**
	 * Retrieves a listing id by given Order object
	 *
	 * @param APP_Order $order Order object.
	 *
	 * @return int Listing ID
	 */
	public function get_order_post_id( $order ) {
		$items = $order->get_items();

		foreach ( $items as $item ) {
			if ( $item['post']->post_type === $this->listing->get_type() ) {
				return $item['post_id'];
			}
		}
	}
}
