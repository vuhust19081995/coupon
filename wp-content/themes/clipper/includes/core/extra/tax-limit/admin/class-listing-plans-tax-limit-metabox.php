<?php
/**
 * Single Plan Taxonomy Limit
 *
 * @package Listing\Modules\TaxLimit\Metaboxes
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Plan Taxonomy Limit Meta class.
 */
class APP_Listing_Plans_Taxonomy_Limit_Box {

	/**
	 * Current multiple plans object.
	 *
	 * @var type APP_Listing_Plans
	 */
	protected $plans;

	/**
	 * Current module object.
	 *
	 * @var APP_Listing_Taxonomy_Limit
	 */
	protected $module;

	/**
	 * Construct Plan Taxonomy Limit Meta
	 *
	 * @param APP_Listing_Taxonomy_Limit $module Category Limit module object.
	 * @param APP_Listing_Plans          $plans  Plan module object.
	 */
	public function __construct( APP_Listing_Taxonomy_Limit $module, APP_Listing_Plans $plans ) {

		$this->module = $module;
		$this->plans  = $plans;
		$plan_ptype   = $this->plans->get_plan_type();

		add_filter( "appthemes_{$plan_ptype}-details_metabox_fields", array( $this, '_details_form' ) );
		add_filter( "manage_{$plan_ptype}_posts_columns", array( $this, '_manage_columns' ), 40 );
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
			'title' => __( 'Categories Included', APP_TD ),
			'type'  => 'text',
			'name'  => $this->module->get_meta_key(),
			'desc'  => __( ' ( 0 = Infinite )', APP_TD ),
			'extra' => array(
				'style' => 'width: 50px;',
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
		$columns[$this->module->get_meta_key()] = __( 'Categories Included', APP_TD );
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

		$plan    = $this->plans->get_plan( $post_id );
		$metakey = $this->module->get_meta_key();
		if ( ! $plan || $metakey !== $column_index ) {
			return;
		}

		$included = $plan->{$metakey};
		$included = ! $included ? __( 'Unlimited', APP_TD ) : $included;

		echo $included;
	}
}
