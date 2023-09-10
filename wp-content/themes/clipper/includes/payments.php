<?php
/**
 * Payment functions.
 *
 * @package Clipper\Payments
 * @author  AppThemes
 * @since   Clipper 1.5.0
 */

/**
 * Returns associated listing ID for given order, false if not found.
 *
 * @since 1.4.0
 *
 * @param APP_Order $order
 * @return int|bool
 */
function _clpr_get_order_coupon_id( $order ) {
	global $clipper;

	/* @var $payments APP_Listing_Payments */
	$payments = $clipper->{APP_POST_TYPE}->payments;
	$item_id  = false;

	if ( $payments ) {
		$item_id = $payments->get_order_post_id( $order );
	}

	return $item_id;
}

/**
 * Checks if payments are enabled on site.
 *
 * @since 1.4.0
 *
 * @return bool
 */
function clpr_payments_is_enabled() {
	global $clipper;

	if ( ! current_theme_supports( 'app-payments' ) || ! current_theme_supports( 'app-price-format' ) ) {
		return false;
	}

	if ( ! $clipper->{APP_POST_TYPE}->options->charge || ! is_numeric( $clipper->{APP_POST_TYPE}->options->price ) ) {
		return false;
	}

	return true;
}

/**
 * Checks if post have some pending payment orders.
 *
 * @since 1.4.0
 *
 * @param int $post_id
 * @return bool
 */
function clpr_have_pending_payment( $post_id ) {

	if ( ! clpr_payments_is_enabled() ) {
		return false;
	}

	$orders_post = _appthemes_orders_get_connected( $post_id );

	if ( ! $orders_post->posts ) {
		return false;
	}

	$order = appthemes_get_order( $orders_post->post->ID );

	if ( ! $order || ! in_array( $order->get_status(), array( APPTHEMES_ORDER_PENDING, APPTHEMES_ORDER_FAILED ) ) ) {
		return false;
	}

	return true;
}

/**
 * Returns order gateway name.
 *
 * @since 1.4.0
 *
 * @param object $order
 * @return string
 */
function clpr_get_order_gateway_name( $order ) {

	if ( ! clpr_payments_is_enabled() ) {
		return;
	}

	$gateway_id = $order->get_gateway();

	if ( ! empty( $gateway_id ) ) {

		$gateway = APP_Gateway_Registry::get_gateway( $gateway_id );

		if ( $gateway ) {
			$gateway_name = $gateway->display_name( 'admin' );
		} else {
			$gateway_name = __( 'Unknown', APP_TD );
		}
	} else {
		$gateway_name = __( 'Undecided', APP_TD );
	}

	return $gateway_name;
}

/**
 * Displays ordered items.
 *
 * @since 1.4.0
 *
 * @param object $order
 * @return void
 */
function clpr_display_ordered_items( $order ) {

	if ( ! clpr_payments_is_enabled() ) {
		return;
	}

	$items = $order->get_items();

	foreach ( $items as $item ) {

		if ( ! APP_Item_Registry::is_registered( $item['type'] ) ) {
			$item_title = __( 'Unknown', APP_TD );
		} else {
			$item_title = APP_Item_Registry::get_title( $item['type'] );
		}
		echo html( 'div', $item_title );
	}

}

/**
 * Displays Continue button on order summary page.
 *
 * @since 1.4.0
 *
 * @return void
 */
function clpr_payments_display_order_summary_continue_button() {

	$url        = '';
	$text       = '';
	$listing_id = 0;

	$order = get_order();

	if ( $order ) {
		$listing_id = _clpr_get_order_coupon_id( $order );
	} else {
		$checkout = appthemes_get_checkout();
		if ( $checkout ) {
			$listing_id = $checkout->get_data( 'listing_id' );
		}
	}

	if ( $listing_id ) {
		$url  = get_permalink( $listing_id );
		$text = __( 'Continue to coupon', APP_TD );
	}

	if ( $url && $text ) {
		if ( $order && ! in_array( $order->get_status(), array( APPTHEMES_ORDER_PENDING, APPTHEMES_ORDER_FAILED ) ) ) {
			?>
			<p><?php echo esc_html( __( 'Your order has been completed.', APP_TD ) ); ?></p>
			<?php
		} elseif ( ! $order ) {
			?>
			<p><?php echo esc_html( __( 'Your coupon has been submitted.', APP_TD ) ); ?></p>
			<?php
		}
		?>
		<a href="<?php echo esc_url( $url ); ?>">
			<button class="btn"><?php echo esc_html( $text ); ?></button>
		</a>
		<?php
	}
}
