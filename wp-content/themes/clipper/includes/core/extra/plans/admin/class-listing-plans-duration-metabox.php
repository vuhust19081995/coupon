<?php
/**
 * Single Plan Duration metabox
 *
 * @package Listing\Modules\Plan\Metaboxes
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Plan Duration Meta Box class.
 */
class APP_Listing_Plans_Duration_Box extends APP_Meta_Box {

	/**
	 * Current plans object.
	 *
	 * @var APP_Listing_Plans
	 */
	protected $plans;

	/**
	 * Construct Plans duration metabox
	 *
	 * @param APP_Listing_Plans $plans Plan module object.
	 */
	public function __construct( APP_Listing_Plans $plans ) {

		$this->plans = $plans;
		$plan_ptype  = $this->plans->get_plan_type();

		add_filter( "manage_{$plan_ptype}_posts_columns", array( $this, '_manage_columns' ), 30 );
		add_action( "manage_{$plan_ptype}_posts_custom_column", array( $this, '_add_column_data' ), 10, 2 );

		parent::__construct( "$plan_ptype-duration-period", __( 'Listing Duration Period', APP_TD ), $plan_ptype, 'normal', 'default' );
	}

	/**
	 * Returns an array of form fields.
	 *
	 * @return array
	 */
	public function form() {
		$fields = array();

		$fields[] = array(
			'title' => __( 'Listing Duration', APP_TD ),
			'type'  => 'number',
			'name'  => 'period',
			'desc'  => __( '( 0 = Infinite )', APP_TD ),
			'extra' => array(
				'id'    => 'listing_period',
				'class' => 'small-text',
				'min'   => 0,
				'max'   => 90,
			),
		);

		$fields[] = array(
			'title' => __( 'Period Type', APP_TD ),
			'type' => 'select',
			'name' => 'period_type',
			'values' => APP_Listing_Period_Fields::pluck( 'title' ),
			'extra' => array(
				'class' => 'period_type',
				'data-period-item' => 'listing',
			),
		);

		$fields[] = array(
			'title' => '',
			'type' => 'hidden',
			'name' => 'duration',
			'extra' => array(
				'id' => 'listing_duration',
			),
		);

		return $fields;
	}

	/**
	 * Enqueues
	 */
	public function admin_enqueue_scripts() {
		APP_Listing_Period_Fields::enqueue_scripts();
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

		if ( $data['period'] < 0 ) {
			$errors->add( 'period', '' );
		}

		return $errors;
	}

	/**
	 * Filter data before save.
	 *
	 * @param array $data    Posted data.
	 * @param int   $post_id Post ID.
	 *
	 * @return array
	 */
	function before_save( $data, $post_id ) {

		APP_Listing_Period_Fields::set_values( $data['period_type'], $data['period'], $data['duration'] );
		$data = apply_filters( "appthemes_{$this->box_id}_before_save", $data, $post_id );

		return $data;
	}

	/**
	 * Modifies columns on admin listing package page.
	 *
	 * @param array $columns Registered columns.
	 *
	 * @return array
	 */
	public function _manage_columns( $columns ) {
		$columns['duration'] = __( 'Duration', APP_TD );
		return $columns;
	}


	/**
	 * Displays listing plan custom columns data.
	 *
	 * @param string $column_index The column name.
	 * @param int    $post_id      The plan post ID.
	 *
	 * @return void
	 */
	public function _add_column_data( $column_index, $post_id ) {

		$plan = $this->plans->get_plan( $post_id );
		if ( ! $plan || 'duration' !== $column_index ) {
			return;
		}

		$period_text = $plan->get_period_text();
		$period_text = str_replace( '/', '', $period_text );

		echo $period_text;
	}

}
