<?php
/**
 * Listing Basic Process email Notifications
 *
 * @package Listing\Emails
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Email processing class
 */
class APP_Listing_Process_Emails extends APP_Listing_Emails {

	/**
	 * Sends email to admin about new pending listing.
	 *
	 * @param WP_Post $post Current listing object.
	 */
	public function notify_admin_pending_listing( $post ) {

		if ( ! $this->listing->options->get( "notify_pending_{$this->listing->get_type()}" ) ) {
			return;
		}

		$permalink   = html_link( get_permalink( $post ), $post->post_title );
		$review_link = admin_url( "edit.php?post_status=pending&post_type={$this->listing->get_type()}" );

		$message = html( 'p', sprintf( __( 'A listing is awaiting moderation: %s', APP_TD ), $permalink ) );
		$message .= html( 'p', html_link( $review_link, __( 'Review pending listings', APP_TD ) ) );

		$blogname = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );

		$subject = sprintf( __( '[%1$s] Pending Listing: "%2$s"', APP_TD ), $blogname, $post->post_title );

		$this->send( 'notify_admin_pending_listing', array(
			'to'      => get_option( 'admin_email' ),
			'subject' => wp_specialchars_decode( $subject ),
			'message' => $message,
			'listing' => $post,
		) );
	}

	/**
	 * Sends email to user about his new pending listing.
	 *
	 * Does nothing actually, just adds ability to send such notification.
	 *
	 * @param WP_Post $post Current listing object.
	 */
	public function notify_user_pending_listing( $post ) {

		if ( ! $this->listing->options->get( "notify_user_pending_{$this->listing->get_type()}" ) ) {
			return;
		}

		$recipient = get_user_by( 'id', $post->post_author );
		$permalink = html_link( get_permalink( $post ), $post->post_title );

		$message = html( 'p', sprintf( __( 'Hello %s,', APP_TD ), $recipient->display_name ) );
		$message .= html( 'p', sprintf( __( 'Your "%s" listing has been received and will not appear live on our site until it has been approved.', APP_TD ), $permalink ) );

		$blogname = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );

		$subject = sprintf( __( '[%1$s] Listing Pending: "%2$s"', APP_TD ), $blogname, $post->post_title );

		$this->send( 'notify_user_pending_listing', array(
			'to'      => $recipient->user_email,
			'subject' => wp_specialchars_decode( $subject ),
			'message' => $message,
			'listing' => $post,
		) );
	}

	/**
	 * Sends email to user when their listing has been approved.
	 *
	 * @param WP_Post $post Current listing object.
	 */
	public function notify_user_approved_listing( $post ) {

		$recipient = get_user_by( 'id', $post->post_author );
		$permalink = html_link( get_permalink( $post ), $post->post_title );

		$message = html( 'p', sprintf( __( 'Hello %s,', APP_TD ), $recipient->display_name ) );
		$message .= html( 'p', sprintf( __( 'Your "%s" listing has been approved.', APP_TD ), $permalink ) );

		$blogname = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );

		$subject = sprintf( __( '[%1$s] Listing Approved: "%2$s"', APP_TD ), $blogname, $post->post_title );

		$this->send( 'notify_user_approved_listing', array(
			'to'      => $recipient->user_email,
			'subject' => wp_specialchars_decode( $subject ),
			'message' => $message,
			'listing' => $post,
		) );
	}

	/**
	 * Sends email to admin after listing has been published.
	 *
	 * @param WP_Post $post published listing object.
	 */
	public function notify_admin_publish_listing( $post ) {

		if ( ! $this->listing->options->get( "notify_new_{$this->listing->get_type()}" ) ) {
			return;
		}

		$permalink = html_link( get_permalink( $post ), $post->post_title );

		$message = html( 'p', sprintf( __( 'A new listing has been published: %s', APP_TD ), $permalink ) );

		$blogname = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );

		$subject = sprintf( __( '[%1$s] Published Listing: "%2$s"', APP_TD ), $blogname, $post->post_title );

		$this->send( 'notify_admin_publish_listing', array(
			'to'      => get_option( 'admin_email' ),
			'subject' => wp_specialchars_decode( $subject ),
			'message' => $message,
			'listing' => $post,
		) );
	}

	/**
	 * Sends email to user after listing has been published.
	 *
	 * @param WP_Post $post published listing object.
	 */
	public function notify_user_publish_listing( $post ) {

		$recipient = get_user_by( 'id', $post->post_author );
		$permalink = html_link( get_permalink( $post ), $post->post_title );

		$message = html( 'p', sprintf( __( 'Your "%s" listing has been published.', APP_TD ), $permalink ) );

		$blogname = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );

		$subject = sprintf( __( '[%1$s] Published Listing: "%2$s"', APP_TD ), $blogname, $post->post_title );

		$this->send( 'notify_user_publish_listing', array(
			'to'      => $recipient->user_email,
			'subject' => wp_specialchars_decode( $subject ),
			'message' => $message,
			'listing' => $post,
		) );
	}

	/**
	 * Sends email to admin after listing has been renewed.
	 *
	 * @param WP_Post $post Renewed listing object.
	 */
	public function notify_admin_renewed_listing( $post ) {

		if ( ! $this->listing->options->get( "notify_renew_{$this->listing->get_type()}" ) ) {
			return;
		}

		$permalink = html_link( get_permalink( $post ), $post->post_title );

		$message = html( 'p', sprintf( __( 'A listing has been renewed: %s', APP_TD ), $permalink ) );

		$blogname = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );

		$subject = sprintf( __( '[%1$s] Renewed Listing: "%2$s"', APP_TD ), $blogname, $post->post_title );

		$this->send( 'notify_admin_renewed_listing', array(
			'to'      => get_option( 'admin_email' ),
			'subject' => wp_specialchars_decode( $subject ),
			'message' => $message,
			'listing' => $post,
		) );
	}

	/**
	 * Sends emails to user after listing renewed.
	 *
	 * @param WP_Post $post Renewed listing object.
	 */
	public function notify_user_renewed_listing( $post ) {

		$recipient = get_user_by( 'id', $post->post_author );
		$permalink = html_link( get_permalink( $post ), $post->post_title );

		$message = html( 'p', sprintf( __( 'Your "%s" listing has been published.', APP_TD ), $permalink ) );

		$blogname = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );

		$subject = sprintf( __( '[%1$s] Renewed Listing: "%2$s"', APP_TD ), $blogname, $post->post_title );

		$this->send( 'notify_user_renewed_listing', array(
			'to'      => $recipient->user_email,
			'subject' => wp_specialchars_decode( $subject ),
			'message' => $message,
			'listing' => $post,
		) );
	}

}
