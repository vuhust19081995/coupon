<?php
/**
 * User Dashboard functions.
 *
 * @package Clipper\Dashboard
 * @author  AppThemes
 * @since   Clipper 1.6
 */


/**
 * Returns user coupon listings for his dashboard.
 *
 * @param array $args (optional)
 *
 * @return object|bool A boolean False if no coupons found.
 */
function clpr_get_user_dashboard_listings( $args = array() ) {

	$defaults = array(
		'post_type' => APP_POST_TYPE,
		'post_status' => array( 'publish', 'unreliable', 'pending', 'draft', APP_POST_STATUS_EXPIRED ),
		'author' => get_current_user_id(),
		'paged' => ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1,
		'suppress_filters' => false,
	);
	$args = wp_parse_args( $args, $defaults );

	$args = apply_filters( 'clpr_user_dashboard_listings_args', $args );

	$listings = new WP_Query( $args );

	if ( ! $listings->have_posts() ) {
		return false;
	}

	return apply_filters( 'clpr_user_dashboard_listings', $listings );
}


/**
 * Returns user dashboard coupon listing actions.
 *
 * @param int $listing_id (optional)
 *
 * @return array
 */
function clpr_get_dashboard_listing_actions( $listing_id = 0 ) {
	global $clpr_options, $clipper;

	$actions = array();
	$listing_id = $listing_id ? $listing_id : get_the_ID();

	$listing = get_post( $listing_id );
	if ( ! $listing ) {
		return $actions;
	}

	$listing_status = clpr_get_listing_status_name( $listing_id );

	// edit button
	if ( $clipper->{APP_POST_TYPE}->options->allow_edit ) {
		$edit_attr = array(
			'title' => __( 'Edit Coupon', APP_TD ),
			'href' => clpr_get_edit_coupon_url( $listing->ID, 'raw' ),
		);
		if ( in_array( $listing_status, array( 'live', 'live_unreliable', 'offline' ) ) ) {
			$actions['edit'] = $edit_attr;
		}
	}

	// delete button
	$actions['delete'] = array(
		'title' => __( 'Delete Coupon', APP_TD ),
		'href' => add_query_arg( array( 'aid' => $listing->ID, 'action' => 'delete' ), clpr_get_dashboard_url( 'raw' ) ),
		'onclick' => 'return confirm(clipper_params.text_before_delete_coupon);',
	);

	// pause button
	if ( in_array( $listing_status, array( 'live', 'live_unreliable' ) ) ) {
		$actions['pause'] = array(
			'title' => __( 'Pause Coupon', APP_TD ),
			'href' => add_query_arg( array( 'aid' => $listing->ID, 'action' => 'pause' ), clpr_get_dashboard_url( 'raw' ) ),
		);
	}

	// restart button
	if ( in_array( $listing_status, array( 'offline' ) ) ) {
		$actions['restart'] = array(
			'title' => __( 'Restart Coupon', APP_TD ),
			'href' => add_query_arg( array( 'aid' => $listing->ID, 'action' => 'restart' ), clpr_get_dashboard_url( 'raw' ) ),
		);
	}

	// relist link
	if ( in_array( $listing_status, array( 'ended', 'live_expired' ) ) && $clipper->{APP_POST_TYPE}->options->allow_renew ) {
		$relist_url = clpr_get_renew_coupon_url( $listing->ID, 'raw' );
		if ( $relist_url ) {
			$actions['relist'] = array(
				'title' => __( 'Relist', APP_TD ),
				'href' => $relist_url,
			);
		}
	}

	// payment links
	if ( $listing_status == 'pending_payment' ) {
		$order = appthemes_get_order_connected_to( $listing->ID );
		// pay order
		$actions['pay_order'] = array(
			'title' => __( 'Pay now', APP_TD ),
			'href' => appthemes_get_order_url( $order->get_id() ),
		);
		if ( $order->get_gateway() ) {
			// reset gateway
			$actions['reset_gateway'] = array(
				'title' => __( 'Reset Gateway', APP_TD ),
				'href' => get_the_order_cancel_url( $order->get_id() ),
			);
		}
	}

	return apply_filters( 'clpr_dashboard_listing_actions', $actions, $listing );
}


/**
 * Displays user dashboard coupon listing actions.
 *
 * @param int $listing_id (optional)
 *
 * @return void
 */
function clpr_dashboard_listing_actions( $listing_id = 0 ) {
	$listing_id = $listing_id ? $listing_id : get_the_ID();

	$actions = clpr_get_dashboard_listing_actions( $listing_id );
	$li = '';

	foreach ( $actions as $action => $attr ) {
		$a = html( 'a', $attr, $attr['title'] );
		$li .= html( 'li', array( 'class' => $action ), $a );
	}

	$ul = html( 'ul', array( 'id' => 'listing-actions-' . $listing_id, 'class' => 'listing-actions' ), $li );
	echo $ul;
}


/**
 * Returns user orders for his dashboard.
 *
 * @param array $args (optional)
 *
 * @return object|bool A boolean False if no orders found.
 */
function clpr_get_user_dashboard_orders( $args = array() ) {

	if ( ! clpr_payments_is_enabled() ) {
		return false;
	}

	$defaults = array(
		'post_type' => APPTHEMES_ORDER_PTYPE,
		'author' => get_current_user_id(),
		'paged' => ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1,
		'suppress_filters' => false,
	);
	$args = wp_parse_args( $args, $defaults );

	$args = apply_filters( 'clpr_user_dashboard_orders_args', $args );

	$orders = new WP_Query( $args );

	if ( ! $orders->have_posts() ) {
		return false;
	}

	return apply_filters( 'clpr_user_dashboard_orders', $orders );
}


/**
 * Returns user dashboard order actions.
 *
 * @param int $order_id (optional)
 *
 * @return array
 */
function clpr_get_dashboard_order_actions( $order_id = 0 ) {
	$actions = array();

	if ( ! clpr_payments_is_enabled() ) {
		return $actions;
	}

	$order_id = $order_id ? $order_id : get_the_ID();

	$order = appthemes_get_order( $order_id );
	if ( ! $order ) {
		return $actions;
	}

	// payment links
	if ( in_array( $order->get_status(), array( APPTHEMES_ORDER_PENDING, APPTHEMES_ORDER_FAILED ) ) ) {
		// pay order
		$actions['pay_order'] = array(
			'title' => __( 'Pay now', APP_TD ),
			'href' => appthemes_get_order_url( $order->get_id() ),
		);
		if ( $order->get_gateway() ) {
			// reset gateway
			$actions['reset_gateway'] = array(
				'title' => __( 'Reset Gateway', APP_TD ),
				'href' => get_the_order_cancel_url( $order->get_id() ),
			);
		}
	}

	return apply_filters( 'clpr_dashboard_order_actions', $actions, $order );
}


/**
 * Displays user dashboard order actions.
 *
 * @param int $order_id (optional)
 *
 * @return void
 */
function clpr_dashboard_order_actions( $order_id = 0 ) {
	if ( ! clpr_payments_is_enabled() ) {
		return;
	}

	$order_id = $order_id ? $order_id : get_the_ID();

	$actions = clpr_get_dashboard_order_actions( $order_id );
	$li = '';

	foreach ( $actions as $action => $attr ) {
		$a = html( 'a', $attr, $attr['title'] );
		$li .= html( 'li', array( 'class' => $action ), $a );
	}

	$ul = html( 'ul', array( 'id' => 'order-actions-' . $order_id, 'class' => 'order-actions' ), $li );
	echo $ul;
}


