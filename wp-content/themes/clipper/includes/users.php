<?php
/**
 * Main user account functions
 *
 * @package Clipper
 * @since 2.0.0
 */

/**
 * Insert the last login date for each user.
 *
 * @since 2.0.0
 * @param array $login The user login array.
 */
function clpr_last_login( $login ) {
	global $user_id;

	$user = get_user_by( 'login', $login );
	update_user_meta( $user->ID, 'last_login', gmdate( 'Y-m-d H:i:s' ) );

	$counter = absint( $user->total_logins ) + 1;
	update_user_meta( $user->ID, 'total_logins', $counter );
}
add_action( 'wp_login', 'clpr_last_login' );

/**
 * Set the user profile last updated timestamp.
 *
 * @since 2.0.0
 * @param int   $user_id The user that was updated.
 * @param array $old     The profile fields before update.
 */
function clpr_update_profile_last_update_time( $user_id, $old ) {
	update_user_meta( $user_id, 'last_update', gmdate( 'Y-m-d H:i:s' ) );
}
add_action( 'profile_update', 'clpr_update_profile_last_update_time', 10, 2 );
