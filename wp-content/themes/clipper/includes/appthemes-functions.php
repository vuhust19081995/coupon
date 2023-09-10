<?php

/**
 * AppThemes common functions.
 *
 * @version 1.0
 * @author AppThemes
 *
 * DO NOT UPDATE WITHOUT UPDATING ALL OTHER THEMES!
 *
 * Add new functions to the /framework/ folder and move existing functions there as well, when you need to modify them.
 *
 */

// get the page view counters and display on the page
function appthemes_get_stats( $post_id ) {
	global $app_abbr;

	$daily_views = get_post_meta( $post_id, $app_abbr . '_daily_count', true );
	$total_views = get_post_meta( $post_id, $app_abbr . '_total_count', true );

	if ( ! empty( $total_views ) && ( ! empty( $daily_views ) ) ) {
		echo number_format( $total_views ) . '&nbsp;' . __( 'total views', APP_TD ) . ',&nbsp;' . number_format( $daily_views ) . '&nbsp;' . __( 'today', APP_TD );
	} else {
		echo __( 'no views yet', APP_TD );
	}
}

// give us either the uploaded profile pic, a gravatar, or a placeholder
function appthemes_get_profile_pic( $author_id, $author_email, $avatar_size ) {
//    if(function_exists('userphoto_exists')) {
//        if(userphoto_exists($author_id))
//			//if the size of userphoto called is less then 32px, it must be looking for the thumbnail
//			if($avatar_size <= 32)
//            	userphoto_thumbnail($author_id);
//			else
//				userphoto($author_id);
//        else
//            echo get_avatar($author_email, $avatar_size);
//      } else {
	echo get_avatar( $author_email, $avatar_size );
//     }
}

/**
 *
 * Helper functions
 *
 */


// mb_strtoupper compatibility check.
if ( ! function_exists( 'mb_strtoupper' ) ) :

	function mb_strtoupper( $str, $encoding = null ) {
		return strtoupper( $str );
	}

endif;

// insert the last login date for each user
function appthemes_last_login( $login ) {
	$user = get_user_by( 'login', $login );
	update_user_meta( $user->ID, 'last_login', gmdate( 'Y-m-d H:i:s' ) );
}

add_action( 'wp_login', 'appthemes_last_login' );

// get the last login date for a user
function appthemes_get_last_login( $user_id ) {
	$last_login     = get_user_meta( $user_id, 'last_login', true );
	$date_format    = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
	$the_last_login = mysql2date( $date_format, $last_login, true );
	echo $the_last_login;
}

// format the user registration date used in the sidebar-user.php template
function appthemes_get_reg_date( $reg_date ) {
	$date_format  = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
	$the_reg_date = mysql2date( $date_format, $reg_date, true );
	echo $the_reg_date;
}

// deletes all the theme database tables
function appthemes_delete_db_tables() {
	global $wpdb, $app_db_tables;

	echo '<div class="update-msg">';
	foreach ( $app_db_tables as $key => $value ) :

		$sql = "DROP TABLE IF EXISTS " . $wpdb->prefix . $value;
		$wpdb->query( $sql );

		printf( '<div class="delete-item">' . __( "Table '%s' has been deleted.", APP_TD ) . '</div>', $value );

	endforeach;
	echo '</div>';
}

// deletes all the theme database options
function appthemes_delete_all_options() {
	global $wpdb, $app_abbr;

	$sql = "DELETE FROM $wpdb->options WHERE option_name LIKE '" . $app_abbr . "_%'";
	$wpdb->query( $sql );

	echo '<div class="update-msg">';
	echo '<div class="delete-item">' . __( 'All theme options have been deleted.', APP_TD ) . '</div>';
	echo '</div>';
}

/**
 * save all site search queries. called in the search.php template
 * @since 1.1
 */
function appthemes_save_search() {
	global $wpdb, $wp_query, $app_abbr;

	// define the table names used
	$tsearch_total  = $wpdb->clpr_search_total;
	$tsearch_recent = $wpdb->clpr_search_recent;

	// Make sure it's only search, not paged, not admin, and not a spider.
	if ( is_search() && ! is_paged() && ! is_admin() && isset( $_SERVER['HTTP_REFERER'] ) ) {

		// Search string is the raw query.
		$search_string = $wp_query->query_vars['s'];

		// Search terms is the words in the query.
		$search_terms = preg_replace( '/[," ]+/', ' ', $search_string );
		$search_terms = trim( $search_terms );
		$hit_count    = $wp_query->found_posts;
		// Other useful details of the search.
		$details      = '';

		// Save header info with the search.
		foreach ( array( 'REQUEST_URI', 'REQUEST_METHOD', 'QUERY_STRING', 'REMOTE_ADDR', 'HTTP_USER_AGENT', 'HTTP_REFERER' ) as $header ) {
			$details .= $header . ': ' . $_SERVER[$header] . "\n";
		}

		// Sanitize as necessary.
		$search_string = esc_sql( $search_string );
		$search_terms  = esc_sql( $search_terms );
		$details       = esc_sql( $details );

		// Save the individual search to the db.
		$query   = "INSERT INTO `$tsearch_recent` (`terms`, `datetime`, `hits`, `details`) VALUES ('$search_string', NOW(), $hit_count,'$details')";
		$success = $wpdb->query( $query );

		$query   = "UPDATE `$tsearch_total` SET `count` = `count` + 1, `last_hits` = $hit_count WHERE `terms` = '$search_terms' AND `date` = CURDATE()";
		$results = $wpdb->query( $query );

		// If the search terms don't already exist, let's create it.
		if ( ! $results ) {
			$query   = "INSERT INTO `$tsearch_total` (`terms`, `date`, `count`, `last_hits`) VALUES ('$search_terms', CURDATE(), 1, $hit_count)";
			$results = $wpdb->query( $query );
		}
	}
}
