<?php
/**
 * Admin Users list.
 *
 * @package Clipper\Admin\Users
 * @author  AppThemes
 * @since   Clipper 1.6.0
 */

/**
 * Adds extra columns to the users overview.
 *
 * @since 1.6.0
 * @since 2.0.0 Added last/total logins and registered columns.
 *
 * @param array $columns
 *
 * @return array
 */
function clpr_manage_users_columns( $columns ) {

	$columns['coupons']         = __( 'Coupons', APP_TD );
	$columns['last_login']      = __( 'Last Login', APP_TD );
	$columns['total_logins']    = __( 'Total Logins', APP_TD );
	$columns['date_registered'] = __( 'Registered', APP_TD );

	return $columns;
}
add_filter( 'manage_users_columns', 'clpr_manage_users_columns' );

/**
 * Returns content for the custom users columns.
 *
 * @since 1.6.0
 *
 * @param string $r
 * @param string $column_name
 * @param int $user_id
 *
 * @return string
 */
function clpr_manage_users_custom_column( $r, $column_name, $user_id ) {
	global $wp_list_table;

	$user = get_userdata( $user_id );

	if ( $column_name == 'coupons' ) {

		// Generate array of users with their coupon counts.
		$coupons_counts = count_many_users_posts( array_keys( $wp_list_table->items ), APP_POST_TYPE );

		if ( $coupons_counts[ $user_id ] > 0 ) {
			$url = add_query_arg( array( 'post_type' => APP_POST_TYPE, 'author' => $user_id ), admin_url( 'edit.php' ) );
			return html( 'a', array( 'href' => esc_url( $url ), 'class' => 'edit' ), $coupons_counts[ $user_id ] );
		} else {
			return 0;
		}
	} elseif ( 'total_logins' == $column_name && $user->total_logins ) {
		return number_format( $user->total_logins );
	} elseif ( 'last_login' == $column_name && $user->last_login ) {
		return date( 'm/d/Y g:ia', strtotime( $user->last_login ) );
	} elseif ( $column_name == 'date_registered' ) {
		return date( 'm/d/Y', strtotime( $user->user_registered ) );
	} else {
		return $r;
	}
}
add_filter( 'manage_users_custom_column', 'clpr_manage_users_custom_column', 10, 3 );
