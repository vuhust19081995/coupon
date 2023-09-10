<?php
/**
 * Listing Payments Emails.
 *
 * @package Listing\Payments
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Payments Emails class.
 */
class APP_Listing_Payments_Emails extends APP_Listing_Emails {

	/**
	 * Sends email notification to admin if payment failed.
	 *
	 * @param APP_Order $order Order object.
	 */
	public function notify_admin_failed_transaction( $order ) {

		if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
			return;
		}

		$permalink = get_edit_post_link( $order->get_id() );

		$message = html( 'p', sprintf( __( 'Payment for the order #%s has failed.', APP_TD ), $order->get_id() ) );
		$message .= html( 'p', sprintf( __( 'Please <a href="%s">review this order</a>, and if necessary disable assigned services.', APP_TD ), $permalink ) );

		$blogname = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );

		$subject = sprintf( __( '[%1$s] Failed Order #%2$d', APP_TD ), $blogname, $order->get_id() );

		$this->send( 'notify_admin_failed_transaction', array(
			'to'      => get_option( 'admin_email' ),
			'subject' => wp_specialchars_decode( $subject ),
			'message' => $message,
			'order'   => $order,
		) );
	}

	/**
	 * Sends email with receipt to customer after completed purchase.
	 *
	 * @param APP_Order $order   Order object.
	 * @param int       $item_id Listing Item ID.
	 */
	public function send_user_receipt( $order, $item_id ) {
		$recipient = get_user_by( 'id', $order->get_author() );
		$item_link = html( 'p', html_link( get_permalink( $item_id ), get_the_title( $item_id ) ) );

		$table = new APP_Order_Summary_Table( $order );
		ob_start();
		$table->show();
		$table_output = ob_get_clean();

		$message = html( 'p', sprintf( __( 'Hello %s,', APP_TD ), $recipient->display_name ) );
		$message .= html( 'p', __( 'This email confirms that you have purchased the following item:', APP_TD ) );
		$message .= $item_link;
		$message .= html( 'p', __( 'Order Summary:', APP_TD ) );
		$message .= $table_output;

		$blogname = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );

		$subject = sprintf( __( '[%s] Receipt for your order #%d', APP_TD ), $blogname, $order->get_id() );

		$this->send( 'send_user_receipt', array(
			'to'      => $recipient->user_email,
			'subject' => wp_specialchars_decode( $subject ),
			'message' => $message,
			'order'   => $order,
		) );
	}
}