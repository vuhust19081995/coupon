<?php
/**
 * Single Plan Details Addons metabox
 *
 * @package Listing\Modules\Addons\Metaboxes
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing plan Addons class
 */
class APP_Listing_Plan_Details_Addons_Box {

	/**
	 * Current Addons module object.
	 *
	 * @var APP_Listing_Addons
	 */
	protected $module;

	/**
	 * Construct Plan details metabox
	 *
	 * @param APP_Listing_Addons $module        Addons module object.
	 * @param string             $listing_ptype Listing item type.
	 */
	public function __construct( APP_Listing_Addons $module, $listing_ptype = '' ) {

		$this->module = $module;

		add_filter( "appthemes_{$listing_ptype}-plan_metabox_fields", array( $this, '_plan_form' ) );
		add_filter( "appthemes_{$listing_ptype}-plan_before_save", array( $this, '_plan_save' ), 10, 2 );
	}

	/**
	 * Returns an array of form fields.
	 *
	 * @param array $fields Filtered fields array.
	 *
	 * @return array Form fields
	 */
	public function _plan_form( $fields ) {

		$addons = $this->module->get_addons_types();

		foreach ( $addons as $addon_type ) {
			$addon  = appthemes_get_addon_info( $addon_type );
			$fields[] = $addon;
		}

		return $fields;
	}

	/**
	 * Filter data before save.
	 *
	 * @param array $data    Posted data.
	 * @param int   $post_id Post ID.
	 *
	 * @return array
	 */
	public function _plan_save( $data, $post_id ) {

		$addons = $this->module->get_addons_types();

		foreach ( $addons as $addon_type ) {

			$addon = (object) appthemes_get_addon_info( $addon_type );

			if ( $data[ $addon->flag_key ] ) {

				$data[ $addon->duration_key ] = absint( $data[ $addon->duration_key ] );

				if ( empty( $data[ $addon->start_date_key ] ) ) {
					$data[ $addon->start_date_key ] = current_time( 'mysql' );
				}
			} else {
				$data[ $addon->duration_key ] = '';
				$data[ $addon->start_date_key ] = '';
			}
		}

		return $data;
	}

}
