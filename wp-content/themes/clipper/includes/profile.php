<?php
/**
 * User Profile.
 *
 * @package Clipper\Profile
 * @author  AppThemes
 * @since   Clipper 1.0
 */


/**
 * Adds more contact methods to user profile.
 *
 * @param array $methods
 *
 * @return array
 */
function clpr_user_contact_methods( $methods ) {
	// remove old WP default contact methods
	$methods = array_diff_key( $methods, array_flip( array( 'aim', 'yim', 'jabber' ) ) );

	$methods['twitter_id'] = __( 'Twitter', APP_TD );
	$methods['facebook_id'] = __( 'Facebook', APP_TD );

	return $methods;
}
add_action( 'user_contactmethods', 'clpr_user_contact_methods' );


/**
 * Returns description for additional contact methods.
 *
 * @param string $field
 *
 * @return string
 */
function clpr_profile_fields_description( $field ) {
	$description = array(
		'twitter_id' => __( 'Enter your Twitter username without the URL.', APP_TD ),
		'facebook_id' => sprintf( __( "Enter your Facebook username without the URL. <br />Don't have one yet? <a target='_blank' href='%s'>Get a custom URL.</a>", APP_TD ), 'http://www.facebook.com/username/' ),
	);
	return isset( $description[ $field ] ) ? '<span class="description">' . $description[ $field ] . '</span>' : '';
}

