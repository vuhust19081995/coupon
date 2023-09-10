<?php
/**
 * Addons cron jobs
 *
 * @package Components\Addons
 */

add_action( 'init', 'appthemes_schedule_prune_addons_hourly' );
add_action( 'appthemes_prune_addons_hourly', 'appthemes_prune_addons' );
add_action( 'posts_clauses', '_appthemes_prune_post_addons_sql', 10, 2 );

function appthemes_prune_addons(){
	$addons = APP_Addon_Registry::get_addons();
	foreach( $addons as $addon_type ){

		$options = appthemes_get_addon_info( $addon_type );
		switch( $options['type'] ){
			case 'post':
				_appthemes_prune_post_addons( $addon_type );
				break;
			case 'user':
				_appthemes_prune_user_addons( $addon_type );
				break;
		}

		do_action( 'appthemes_prune_' . $addon_type . '_addons' );
	}
}

function _appthemes_prune_post_addons( $addon_type ){

	$expired_posts = new WP_Query( array(
		'post_type' => 'any', // Note: this won't include CPTs with 'exclude_from_search' = true
		'nopaging' => true,
		'expired_addon' => $addon_type
	) );

	if( ! $expired_posts->posts )
		return;

	foreach( $expired_posts->posts as $post ){
		do_action( 'appthemes_prune_post_addon', $post, $addon_type );
		appthemes_remove_addon( $post->ID, $addon_type );
	}

}

function _appthemes_prune_post_addons_sql( $clauses, $wp_query ){
	global $wpdb;

	$addon_type = $wp_query->get( 'expired_addon' );
	if( !$addon_type )
		return $clauses;

	extract( appthemes_get_addon_info( $addon_type ) );
	$current_time = current_time( 'mysql' );

	$clauses['join'] .= " INNER JOIN {$wpdb->postmeta} as duration ON ({$wpdb->posts}.ID = duration.post_id )";
	$clauses['join'] .= " INNER JOIN {$wpdb->postmeta} as start ON ({$wpdb->posts}.ID = start.post_id )";

	$clauses['where'] = ' AND (';
	$clauses['where'] .= " duration.meta_key = '{$duration_key}' AND";
	$clauses['where'] .= " start.meta_key = '{$start_date_key}' AND";
	$clauses['where'] .= " DATE_ADD( start.meta_value, INTERVAL duration.meta_value DAY ) < '{$current_time}'";
	$clauses['where'] .= ' AND duration.meta_value != 0';
	$clauses['where'] .= ' )';

	return $clauses;
}

add_filter( 'pre_user_query', 'apptest' );

function apptest( $wp_user_query ){
	global $wpdb;

	$addon_type = $wp_user_query->get( 'expired_addon' );
	if( !$addon_type )
		return $wp_user_query;

	extract( appthemes_get_addon_info( $addon_type ) );
	$current_time = current_time( 'mysql' );

	$join = " INNER JOIN {$wpdb->usermeta} as duration ON ({$wpdb->users}.ID = duration.user_id )";
	$join .= " INNER JOIN {$wpdb->usermeta} as start ON ({$wpdb->users}.ID = start.user_id )";

	$where = ' AND (';
	$where .= " duration.meta_key = '{$duration_key}' AND";
	$where .= " start.meta_key = '{$start_date_key}' AND";
	$where .= " DATE_ADD( start.meta_value, INTERVAL duration.meta_value DAY ) < '{$current_time}'";
	$where .= ' AND duration.meta_value != 0';
	$where .= ' )';

	$wp_user_query->query_from .= $join;
	$wp_user_query->query_where .= $where;
	return $wp_user_query;
}

function _appthemes_prune_user_addons( $addon_type ){

	$expired_users = new WP_User_Query( array(
		'nopaging' => true,
		'expired_addon' => $addon_type
	) );

	if( ! $expired_users->results )
		return;

	foreach( $expired_users->results as $user ){
		do_action( 'appthemes_prune_user_addon', $user, $addon_type );
		appthemes_remove_addon( $user->ID, $addon_type );
	}
}

function appthemes_schedule_prune_addons_hourly(){

	if( ! wp_next_scheduled( 'appthemes_prune_addons_hourly' ) ){
		wp_schedule_event( time(), 'hourly', 'appthemes_prune_addons_hourly' );
	}

}
