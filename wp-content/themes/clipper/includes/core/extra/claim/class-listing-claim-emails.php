<?php
/**
 * Listing Claim Email Notifications
 *
 * @package Listing\Modules\Claim
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Emails processing class
 */
class APP_Listing_Claim_Emails extends APP_Listing_Emails {

	/**
	 * Sends notification to user when his listing claim has been rejected.
	 *
	 * @param WP_Post $post Listing Item.
	 */
	public function notify_user_rejected_claim( $post ) {

		$recipient_id = get_post_meta( $post->ID, 'claimee', true );
		$recipient    = get_user_by( 'id', $recipient_id );

		$content = '';

		$content .= html( 'p', sprintf( __( 'Hello %s,', APP_TD ), $recipient->display_name ) );

		$content .= html( 'p', sprintf(
			__( 'Your "%s" listing claim has been denied.', APP_TD ),
			html_link( get_permalink( $post ), $post->post_title )
		) );

		$blogname = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );

		$subject = sprintf( __( '[%s] Listing Claim Denied: "%s"', APP_TD ), $blogname, $post->post_title );

		$this->send( 'notify_user_rejected_claim', array(
			'to'      => $recipient->user_email,
			'subject' => wp_specialchars_decode( $subject ),
			'message' => $content,
			'listing' => $post,
		) );
	}

	/**
	 * Sends notification to user when his listing claim has been approved.
	 *
	 * @param WP_Post $post Listing Item.
	 */
	function notify_user_approved_claim( $post ) {

		$recipient_id = get_post_meta( $post->ID, 'claimee', true );
		$recipient    = get_user_by( 'id', $recipient_id );

		$content = '';

		$content .= html( 'p', sprintf( __( 'Hello %s,', APP_TD ), $recipient->display_name ) );

		$content .= html( 'p', sprintf(
			__( 'Your "%s" listing claim has been approved.', APP_TD ),
			html_link( get_permalink( $post ), $post->post_title )
		) );

		$blogname = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );

		$subject = sprintf( __( '[%s] Claimed Listing Approved: "%s"', APP_TD ), $blogname, $post->post_title );

		$this->send( 'notify_user_approved_claim', array(
			'to'      => $recipient->user_email,
			'subject' => wp_specialchars_decode( $subject ),
			'message' => $content,
			'listing' => $post,
		) );
	}

	/**
	 * Sends notification to admin about new pending claimed listing.
	 *
	 * @param WP_Post $post Listing Item.
	 */
	function notify_admin_pending_claimed_listing( $post ) {
		$content = '';

		$content .= html( 'p', sprintf(
			__( 'A new listing claim is awaiting moderation: %s', APP_TD ),
			html_link( get_permalink( $post ), $post->post_title ) ) );

		$content .= html( 'p', html_link(
			admin_url( "edit.php?post_status=pending-claimed&post_type={$post->post_type}" ),
			__( 'Review pending claimed listings', APP_TD ) ) );

		$blogname = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );

		$subject = sprintf( __( '[%s] Pending Claimed Listing: "%s"', APP_TD ), $blogname, $post->post_title );

		$this->send( 'notify_admin_pending_claimed_listing', array(
			'to'      => get_option( 'admin_email' ),
			'subject' => wp_specialchars_decode( $subject ),
			'message' => $content,
			'listing' => $post,
		) );
	}

	/**
	 * Sends notification to claimee about pending claimed listing.
	 *
	 * @param WP_Post $post Listing Item.
	 */
	function notify_user_pending_claimed_listing( $post ) {

		$recipient_id = get_post_meta( $post->ID, 'claimee', true );
		$recipient    = get_user_by( 'id', $recipient_id );

		$content = '';

		$content .= html( 'p', sprintf( __( 'Hello %s,', APP_TD ), $recipient->display_name ) );

		$content .= html( 'p', sprintf(
			__( 'Your "%s" listing claim is awaiting approval.', APP_TD ),
			html_link( get_permalink( $post ), $post->post_title )
		) );

		$blogname = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );

		$subject = sprintf( __( '[%s] Claimed Listing Pending: "%s"', APP_TD ), $blogname, $post->post_title );

		$this->send( 'notify_user_pending_claimed_listing', array(
			'to'      => $recipient->user_email,
			'subject' => wp_specialchars_decode( $subject ),
			'message' => $content,
			'listing' => $post,
		) );
	}
}
