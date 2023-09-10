<?php
/**
 * Single Plan Addons metabox
 *
 * @package Listing\Modules\Addons\Metaboxes
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Pricing Addons class
 */
class APP_Listing_Plans_Addons_Box extends APP_Meta_Box {

	/**
	 * Current Addons module object.
	 *
	 * @var APP_Listing_Addons
	 */
	protected $addons;

	/**
	 * Construct Plan details metabox
	 *
	 * @param APP_Listing_Addons $addons     Addons module object.
	 * @param string             $plan_ptype Plan type to add metabox to.
	 */
	public function __construct( APP_Listing_Addons $addons, $plan_ptype = '' ) {

		$this->addons = $addons;

		parent::__construct( "$plan_ptype-addons", __( 'Featured Addons', APP_TD ), $plan_ptype, 'normal', 'low' );
	}

	/**
	 * Returns an array of form fields.
	 *
	 * @return array Form fields
	 */
	public function form_fields() {

		$fields = array();

		foreach ( $this->addons->get_addons_types() as $addon ) {

			$addon_info = appthemes_get_addon_info( $addon );

			if ( isset( $addon_info['title'] ) ) {
				$title = $addon_info['title'];
			} else {
				$title = $addon;
			}

			$fields[] = array(
				'title' => $title,
				'type' => 'checkbox',
				'name' => $addon,
				'desc' => __( 'Included', APP_TD ),
			);

			$fields[] = array(
				'title' => __( 'Duration', APP_TD ),
				'type'  => 'number',
				'name'  => $addon . '_period',
				'desc'  => __( '( 0 = Infinite )', APP_TD ),
				'extra' => array(
					'id'    => $addon . '_period',
					'class' => 'small-text',
					'min'   => 0,
					'max'   => 90,
				),
			);

			$fields[] = array(
				'title' => __( 'Period Type', APP_TD ),
				'type' => 'select',
				'name' => $addon . '_period_type',
				'values' => APP_Listing_Period_Fields::pluck( 'title' ),
				'extra' => array(
					'class' => 'period_type',
					'data-period-item' => $addon,
				),
			);

			$fields[] = array(
				'title' => '',
				'type' => 'hidden',
				'name' => $addon . '_duration',
				'extra' => array(
					'class' => 'duration',
				),
			);
		}

		return $fields;

	}

	/**
	 * Filter data before save.
	 *
	 * @param array $data    Posted data.
	 * @param int   $post_id Plan ID.
	 *
	 * @return array Filtered data
	 */
	public function before_save( $data, $post_id ) {

		foreach ( $this->addons->get_addons_types() as $addon ) {

			if ( empty( $data[ $addon ] ) ) {
				continue;
			}

			// Empty but not Null.
			if ( '' === $data[ $addon . '_period' ] ) {
				$data[ $addon . '_duration' ]    = get_post_meta( $post_id, 'duration', true );
				$data[ $addon . '_period_type' ] = get_post_meta( $post_id, 'period_type', true );
				$data[ $addon . '_period' ]      = get_post_meta( $post_id, 'period', true );
			}

			APP_Listing_Period_Fields::set_values(
				$data[ $addon . '_period_type' ],
				$data[ $addon . '_period' ],
				$data[ $addon . '_duration' ]
			);
		}

		return $data;
	}

	/**
	 * Validate posted data.
	 *
	 * @param array $data    Posted data.
	 * @param int   $post_id Plan ID.
	 *
	 * @return bool|object A WP_Error object if posted data are invalid.
	 */
	public function validate_post_data( $data, $post_id ) {
		$errors = new WP_Error();

		$plan_duration = intval( get_post_meta( $post_id, 'duration', true ) );

		foreach ( $this->addons->get_addons_types() as $addon ) {

			if ( ! empty( $data[ $addon . '_duration' ] ) ) {

				$addon_duration = $data[ $addon . '_duration' ];

				if ( ! is_numeric( $addon_duration ) ) {
					$errors->add( $addon . '_duration', '' );
				}

				if ( intval( $addon_duration ) > $plan_duration && 0 !== $plan_duration ) {
					$errors->add( $addon . '_duration', __( 'Durations must be shorter than the listing duration.', APP_TD ) );
				}

				if ( intval( $addon_duration ) < 0 ) {
					$errors->add( $addon . '_duration', '' );
				}
			}
		}

		return $errors;
	}

	/**
	 * Displays some extra HTML before the form
	 *
	 * @param WP_Post $post Current plan object.
	 */
	public function before_form( $post ) {
		echo html( 'p', array(), __( 'You can include featured addons in a plan. These will be immediately added to the listing upon purchase. After they run out, the customer can then purchase regular featured addons.', APP_TD ) );
	}

	/**
	 * Displays some extra HTML after the form
	 *
	 * @param WP_Post $post Current plan object.
	 */
	public function after_form( $post ) {
		echo html( 'p', array( 'class' => 'howto' ), __( 'Durations must be shorter than the listing duration.', APP_TD ) );
	}

}
