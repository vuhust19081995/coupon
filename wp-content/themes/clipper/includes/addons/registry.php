<?php
/**
 * Addons Registry class
 *
 * @package Components\Addons
 */
class APP_Addon_Registry{

	private static $addons = array();

	public static function register( $addon_type, $args = array() ){

		$args = wp_parse_args( $args, array(
			'type' => 'post',
			'flag_key' => '_' . $addon_type,
			'duration_key' => '_' . $addon_type . '_duration',
			'start_date_key' => '_' . $addon_type . '_start_date',
		) );

		if( ! in_array( $args['type'], array( 'post', 'user' ) ) )
			$args['type'] = 'post';

		self::$addons[ $addon_type ] = $args;

	}

	public static function exists( $addon_type ){

		if( isset( self::$addons[ $addon_type ] ) )
			return true;
		else
			return false;

	}

	public static function get_addons(){

		return array_keys( self::$addons );

	}

	public static function get_info( $addon_type ){

		if( isset( self::$addons[ $addon_type ] ) )
			return self::$addons[ $addon_type ];
		else
			return array();

	}

}
