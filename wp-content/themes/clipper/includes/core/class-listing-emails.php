<?php
/**
 * Listing email Notifications submodule
 *
 * @package Listing\Emails
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Emails processing class
 */
class APP_Listing_Emails {

	/**
	 * Current Listing module object
	 * @var APP_Listing
	 */
	protected $listing;

	/**
	 * Construct Listing Notifications module
	 *
	 * @param APP_Listing $listing Listing module object.
	 */
	public function __construct( APP_Listing $listing ) {
		$this->listing = $listing;
	}

	/**
	 * A wrapper for wp_mail()
	 *
	 * @uses wp_mail()
	 * @param string $id Notification ID.
	 * @param array  $args An array of wp_mail() arguments, including the "to",
	 *                    "subject", "message", "headers", "attachments" and
	 *                    other custom values ("listing" or "order", etc.).
	 */
	protected function send( $id, $args = array() ) {

		$defaults = array(
			'to'          => '',
			'subject'     => '',
			'message'     => '',
			'attachments' => array(),
			'headers'     => array(
				'type' => 'Content-Type: text/html; charset="' . get_bloginfo( 'charset' ) . '"',
			),
		);

		/**
		 * Filter the Listing standard notifications.
		 *
		 * The dynamic portions of the hook name, `$id`, refer to the particular
		 * notification type.
		 *
		 * @since 1.0
		 *
		 * @param array $args An array of wp_mail() arguments, including the
		 *                    "to", "subject", "message", "headers",
		 *                    "attachments" and other custom values.
		 */
		$args = wp_parse_args( apply_filters( "appthemes_{$id}", $args ), $defaults );

		wp_mail(
			$args['to'],
			$args['subject'],
			$args['message'],
			$args['headers'],
			$args['attachments']
		);

		/**
		 * Fires action after notification was actually sent.
		 *
		 * @since 1.0
		 *
		 * @param string $id   Notification id.
		 * @param array  $args An array of wp_mail() arguments, including the
		 *                     "to", "subject", "message", "headers",
		 *                     "attachments" and other custom values.
		 */
		do_action( 'appthemes_notification_sent', $id, $args );
	}
}
