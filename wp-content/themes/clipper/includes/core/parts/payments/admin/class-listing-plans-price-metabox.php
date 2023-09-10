<?php
/**
 * Single Plan Price field
 *
 * @package Listing\Modules\Payments\Metaboxes
 * @author  AppThemes
 * @since   Listing 2.1
 */

/**
 * Plan Price Meta class.
 */
class APP_Listing_Plans_Price_Box {

	/**
	 * The listing object this meta box relates to.
	 *
	 * @var APP_Listing.
	 */
	protected $listing;

	/**
	 * Construct Plan details metabox
	 *
	 * @param APP_Listing $listing Listing module object.
	 */
	public function __construct( APP_Listing $listing ) {

		// Set the listing object.
		$this->listing = $listing;
		$plan_ptype    = $this->listing->multi_plans->get_plan_type();

		add_filter( "appthemes_{$plan_ptype}-details_metabox_fields", array( $this, '_details_form' ) );
		add_filter( "manage_{$plan_ptype}_posts_columns", array( $this, '_manage_columns' ), 20 );
		add_action( "manage_{$plan_ptype}_posts_custom_column", array( $this, '_add_column_data' ), 10, 2 );
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
			'title' => __( 'Price', APP_TD ),
			'type' => 'number',
			'name' => 'price',
			'desc' => sprintf( __( 'Example: %s ' , APP_TD ), '1.00' ),
			'extra'	 => array(
				'class' => 'small-text',
				'step' => 0.01,
			),
		);

		return $fields;

	}

	/**
	 * Modifies columns on admin listing package page.
	 *
	 * @param array $columns Registered columns.
	 *
	 * @return array
	 */
	public function _manage_columns( $columns ) {
		$columns['price'] = __( 'Price', APP_TD );
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

		$plan = $this->listing->multi_plans->get_plan( $post_id );
		if ( ! $plan || 'price' !== $column_index ) {
			return;
		}

		appthemes_display_price( $plan->get_price() );
	}
}
