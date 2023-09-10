<?php
/**
 * Single Plan Recurring
 *
 * @package Listing\Modules\Recurring\Metaboxes
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Single Plan Recurring class
 */
class APP_Listing_Recurring_Box {

	/**
	 * Current module object.
	 *
	 * @var APP_Listing_Recurring
	 */
	protected $module;

	/**
	 * Construct Plan details metabox
	 *
	 * @param APP_Listing_Recurring $module     Recurring module object.
	 * @param string                $plan_ptype Plan type to add metabox to.
	 */
	public function __construct( APP_Listing_Recurring $module, $plan_ptype = '' ) {

		$this->module = $module;

		add_filter( "appthemes_{$plan_ptype}-details_metabox_fields", array( $this, '_details_form' ) );
		add_filter( "appthemes_{$plan_ptype}-duration-period_metabox_fields", array( $this, '_duration_form' ) );
		add_filter( "appthemes_{$plan_ptype}-duration-period_before_save", array( $this, '_duration_save' ), 10, 2 );
	}

	/**
	 * Returns an array of form fields.
	 *
	 * @param array $fields Filtered fields array.
	 *
	 * @return array Form fields
	 */
	public function _details_form( $fields ) {

		$fields[] = array(
			'title'  => __( 'Recurring', APP_TD ),
			'type'   => 'select',
			'name'   => 'recurring',
			'values' => array(
				'non_recurring'      => __( 'Non Recurring', APP_TD ),
				'optional_recurring' => __( 'Optional Recurring', APP_TD ),
				'forced_recurring'   => __( 'Forced Recurring', APP_TD ),
			),
		);

		return $fields;

	}

	/**
	 * Returns an array of form fields.
	 *
	 * @param array $fields Filtered fields array.
	 *
	 * @return array Form fields
	 */
	public function _duration_form( $fields ) {

		foreach ( $fields as &$field ) {
			if ( 'period' === $field['name'] ) {
				$field['title'] = __( 'Listing Duration/ Recurring Period', APP_TD );
				break;
			}
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
	public function _duration_save( $data, $post_id ) {

		$recurring = get_post_meta( $post_id, 'recurring', true );

		if ( in_array( $recurring, array( 'optional_recurring', 'forced_recurring' ), true ) ) {
			$data['period'] = max( 1, $data['period'] );
			$data['duration'] = max( 1, $data['duration'] );
		}

		return $data;
	}

}
