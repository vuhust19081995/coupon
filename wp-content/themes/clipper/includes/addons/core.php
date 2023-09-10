<?php
/**
 * Addons API
 *
 * @package Components\Addons
 */

/**
 * Registers Addon
 *
 * @param type $addon_type
 * @param type $args
 */
function appthemes_register_addon( $addon_type, $args = array() ){
	APP_Addon_Registry::register( $addon_type, $args );
}

function appthemes_addon_exists( $addon_type ){
	return APP_Addon_Registry::exists( $addon_type );
}

function appthemes_get_addon_info( $addon_type ){
	return APP_Addon_Registry::get_info( $addon_type );
}

/**
 * Creates an addon with a set duration from the current time on a post if one doesn't already exist
 * Adds additional time to an existing addons
 *
 * @param $post_id the post id to add the addon to
 * @param $addon_type the addon type to add
 * @param $duration how long the addon should last
 */
function appthemes_add_addon( $object_id, $addon_type, $duration ){

	if( ! appthemes_addon_exists( $addon_type ) )
		return false;

	if( appthemes_has_addon( $object_id, $addon_type ) ){

		$current_duration = appthemes_get_addon_duration( $object_id, $addon_type );
		$start_date = appthemes_get_addon_start_date( $object_id, $addon_type );

		$new_duration = $duration ? $current_duration + $duration : $duration;
		appthemes_set_addon( $object_id, $addon_type, $new_duration, $start_date );

		do_action( 'appthemes_addon_updated_' . $addon_type, $object_id );

	}else{

		appthemes_set_addon( $object_id, $addon_type, $duration );
		do_action( 'appthemes_addon_added_' . $addon_type, $object_id );

	}

}

/**
 * Sets an addon on a post. Will override an existing addon with the same type
 *
 * @param $post_id the post id to add the addon to
 * @param $addon_type the addon type to add
 * @param $duration how long the addon should last
 * @param $start_date MySQL timestamp of the start date.
 * 	Used with duration to calculate end date
 */
function appthemes_set_addon( $object_id, $addon_type, $duration, $start_date = '' ){

	if( ! appthemes_addon_exists( $addon_type ) )
		return false;

	extract( appthemes_get_addon_info( $addon_type ), EXTR_SKIP );

	if( empty( $start_date ) ){
		$start_date = current_time( 'mysql' );
	}

	if( $type == 'post' ){
		update_post_meta( $object_id, $flag_key, true );
		update_post_meta( $object_id, $start_date_key, $start_date );
		update_post_meta( $object_id, $duration_key, $duration );
	}else{
		update_user_meta( $object_id, $flag_key, true );
		update_user_meta( $object_id, $start_date_key, $start_date );
		update_user_meta( $object_id, $duration_key, $duration );
	}

}

/**
 * Removes an addon from a post
 *
 * @param $post_id the post id to remove addon from
 * @param $addon_type the addon type to remove
 */
function appthemes_remove_addon( $object_id, $addon_type ){

	if( ! appthemes_addon_exists( $addon_type ) )
		return false;

	extract( appthemes_get_addon_info( $addon_type ) );

	if( $type == 'post' ){
		delete_post_meta( $object_id, $flag_key );
		delete_post_meta( $object_id, $start_date_key );
		delete_post_meta( $object_id, $duration_key );
	}else{
		delete_user_meta( $object_id, $flag_key );
		delete_user_meta( $object_id, $start_date_key );
		delete_user_meta( $object_id, $duration_key );
	}

}

function appthemes_has_addon( $object_id, $addon_type, $bypass_expiration_check = false ){

	if ( ! appthemes_addon_exists( $addon_type ) ) {
		return false;
	}

	if ( ! appthemes_get_addon_flag( $object_id, $addon_type ) ) {
		return false;
	} elseif ( $bypass_expiration_check ) {
		return true;
	}

	$end_time = appthemes_get_addon_end_date( $object_id, $addon_type, true );

	if ( $end_time && $end_time < current_time( 'timestamp' ) ) {
		return false;
	} else {
		return true;
	}

}

function appthemes_get_addon_duration( $object_id, $addon_type ){

	if( ! appthemes_addon_exists( $addon_type ) )
		return false;

	extract( appthemes_get_addon_info( $addon_type ) );
	if( $type == 'post' )
		$value = get_post_meta( $object_id, $duration_key, true );
	else
		$value = get_user_meta( $object_id, $duration_key, true );

	return (int) $value;

}

function appthemes_get_addon_flag( $object_id, $addon_type ) {

	if( ! appthemes_addon_exists( $addon_type ) ) {
		return false;
	}

	extract( appthemes_get_addon_info( $addon_type ) );
	if( $type === 'post' )
		$value = get_post_meta( $object_id, $flag_key, true );
	else
		$value = get_user_meta( $object_id, $flag_key, true );

	return (int) $value;

}

function appthemes_get_addon_start_date( $object_id, $addon_type, $timestamp = false ){

	if( ! appthemes_addon_exists( $addon_type ) )
		return false;

	extract( appthemes_get_addon_info( $addon_type ) );
	if( $type == 'post' )
		$value = get_post_meta( $object_id, $start_date_key, true );
	else
		$value = get_user_meta( $object_id, $start_date_key, true );

	if( empty( $value ) )
		return 0;

	if(is_array( $value ) ) var_dump( $addon_type );

	if( $timestamp ){
		return (int) strtotime( $value );
	}else{
		return $value;
	}

}

function appthemes_get_addon_end_date( $object_id, $addon_type, $timestamp = false ){

	if ( ! appthemes_addon_exists( $addon_type ) ) {
		return false;
	}

	$start_date = appthemes_get_addon_start_date( $object_id, $addon_type, true );
	$duration = appthemes_get_addon_duration( $object_id, $addon_type );

	if( ! $start_date || ! $duration ){
		return 0;
	}

	$days_in_seconds = 86400;
	$end_date = $start_date + ( $duration * $days_in_seconds );
	if( $timestamp ){
		return $end_date;
	}else{
		return gmdate( 'Y-m-d H:i:s', $end_date );
	}

}
