<?php
/**
 * Listing Addons Email Notifications
 *
 * @package Listing\Modules\Addons
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Emails processing class
 */
class APP_Listing_Addons_Emails extends APP_Listing_Emails {

	/**
	 * Sends notification to user when his listing addon has expired.
	 *
	 * @param WP_Post $post  Listing Item.
	 * @param string  $addon The title of expired addon.
	 */
	function notify_user_expired_addon( $post, $addon ) {

		$recipient   = get_user_by( 'id', $post->post_author );
		$upgrade_url = appthemes_get_process_url( $post->post_type, 'upgrade', $post->ID, $recipient );

		$content = '';
		$content .= html( 'p', sprintf( __( 'Hello %s,', APP_TD ), $recipient->display_name ) );

		$content .= html( 'p', sprintf(
			__( 'Your upgrade: "%s" has expired for your listing: "%s".', APP_TD ),
			$addon,
			html_link( get_permalink( $post ), $post->post_title )
		) );

		$content .= html( 'p', html_link( $upgrade_url, __( 'Upgrade listing!', APP_TD ) ) );

		$blogname = wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES );

		$subject = sprintf( __( '[%s] Listing Upgrade Expired for: "%s"', APP_TD ), $blogname, $post->post_title );

		$this->send( 'notify_user_expired_addon', array(
			'to'      => $recipient->user_email,
			'subject' => wp_specialchars_decode( $subject ),
			'message' => $content,
			'listing' => $post,
		) );
	}
}
