<?php
/**
 * Listing Expire Emails.
 *
 * @package Listing\Expire
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Expire Emails class.
 */
class APP_Listing_Expire_Emails extends APP_Listing_Emails {

	/**
	 * Sends notification to user when his listing has expired.
	 *
	 * @param WP_Post $post Current listing object.
	 */
	public function notify_user_expired_listing( $post ) {

		$recipient = get_user_by( 'id', $post->post_author );
		$permalink = html_link( get_permalink( $post ), $post->post_title );

		$message = html( 'p', sprintf( __( 'Hello %s,', APP_TD ), $recipient->display_name ) );
		$message .= html( 'p', sprintf( __( 'Your listing: "%s" has expired (it is not visible to the public anymore).', APP_TD ), $permalink ) );

		if ( $this->listing->options->allow_renew ) {
			$renew_link = html_link( $this->listing->view_process_renew->basic_url( $post->ID ) );
			$message .= html( 'p', sprintf( __( 'You can renew your listing and re-publish it again using following link: %s', APP_TD ), $renew_link ) );
		}

		$blogname = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );

		$subject = sprintf( __( '[%1$s] Listing Expired: "%2$s"', APP_TD ), $blogname, $post->post_title );

		$this->send( 'notify_user_expired_listing', array(
			'to'      => $recipient->user_email,
			'subject' => wp_specialchars_decode( $subject ),
			'message' => $message,
			'listing' => $post,
		) );
	}
}