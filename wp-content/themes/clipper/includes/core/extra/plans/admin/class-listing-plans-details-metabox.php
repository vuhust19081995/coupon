<?php
/**
 * Single Plan Details metabox
 *
 * @package Listing\Modules\Plan\Metaboxes
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Plan Details Meta Box class
 */
class APP_Listing_Plans_Details_Box extends APP_Meta_Box {

	/**
	 * Current plan object.
	 *
	 * @var type APP_Listing_Plans
	 */
	protected $plans;

	/**
	 * Construct Plan details metabox
	 *
	 * @param APP_Listing_Plans $plan Plan module object.
	 */
	public function __construct( APP_Listing_Plans $plan ) {
		$this->plans = $plan;
		$plan_ptype  = $this->plans->get_plan_type();

		add_filter( "manage_{$plan_ptype}_posts_columns", array( $this, '_manage_columns' ), 10 );
		add_action( "manage_{$plan_ptype}_posts_custom_column", array( $this, '_add_column_data' ), 10, 2 );

		parent::__construct( "$plan_ptype-details", __( 'Pricing Plan Details', APP_TD ), $plan_ptype, 'normal', 'high' );
	}

	/**
	 * Displays some extra HTML before the form
	 *
	 * @param WP_Post $post Current plan object.
	 */
	public function before_form( $post ) {
		?><style type="text/css">#notice{ display: none; }</style><?php
	}

	/**
	 * Returns an array of form fields.
	 *
	 * @return array Form fields
	 */
	public function form() {
		$plan_form = array();

		$plan_form[] = array(
			'title' => __( 'Description', APP_TD ),
			'type' => 'textarea',
			'name' => 'description',
			'extra' => array(
				'rows' => 10,
				'cols' => 50,
				'class' => 'large-text code',
			),
		);

		return $plan_form;
	}

	/**
	 * Retrieves post updated messages for the Plan post type
	 *
	 * @param array $messages Messages.
	 *
	 * @return array Modified messages
	 */
	public function post_updated_messages( $messages ) {
		$messages[ $this->plans->get_plan_type() ] = array(
		 	1 => __( 'Plan updated.', APP_TD ),
		 	4 => __( 'Plan updated.', APP_TD ),
		 	6 => __( 'Plan created.', APP_TD ),
		 	7 => __( 'Plan saved.', APP_TD ),
		 	9 => __( 'Plan scheduled.', APP_TD ),
			10 => __( 'Plan draft updated.', APP_TD ),
		);
		return $messages;
	}


	/**
	 * Modifies columns on admin listing package page.
	 *
	 * @param array $columns Registered columns.
	 *
	 * @return array
	 */
	public function _manage_columns( $columns ) {
		if ( isset( $columns['date'] ) ) {
			unset( $columns['date'] );
		}
		$columns['description'] = __( 'Description', APP_TD );
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

		$plan = get_post( $post_id );
		if ( ! $plan || 'description' !== $column_index ) {
			return;
		}

		echo strip_tags( $plan->description );
	}

}
