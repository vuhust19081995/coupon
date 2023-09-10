<?php
/**
 * Store Listing Builder
 *
 * @package Clipper\Stores
 * @author  AppThemes
 * @since   2.0.0
 */

/**
 * Store Listing Builder class
 */
class CLPR_Store_Listing_Builder extends APP_Taxonomy_Listing_Builder {

	/**
	 * Sets up basic listing options.
	 */
	public function set_options() {

		$options  = new APP_Listing_Options( $this->listing );
		$defaults = array(
			'duration' => 0,
			'charge'   => false,
			'moderate' => false,
		);

		$options->set_defaults( $defaults );
		$this->listing->set_options( $options );
	}

	/**
	 * Set up listing meta object.
	 */
	public function set_meta_object() {
		$meta_object = new CLPR_Store_Listing_Taxonomy_Meta_Object();
		$this->listing->set_meta_object( $meta_object );

		APP_Media_Manager_Meta_Type_Object_Factory::add_instance( $meta_object );
	}

	/**
	 * Registers taxonomies associated with current listing type
	 */
	protected function _register_taxonomies() {
		global $clpr_options;

		// Register the store taxonomy for coupons.
		$labels = array(
			'name'                       => _x( 'Stores', 'taxonomy general name', APP_TD ),
			'singular_name'              => _x( 'Store', 'taxonomy singular name', APP_TD ),
			'search_items'               => __( 'Search Stores', APP_TD ),
			'popular_items'              => __( 'Popular Stores', APP_TD ),
			'all_items'                  => __( 'All Stores', APP_TD ),
			'parent_item'                => __( 'Parent Store', APP_TD ),
			'parent_item_colon'          => __( 'Parent Store:', APP_TD ),
			'edit_item'                  => __( 'Edit Store', APP_TD ),
			'view_item'                  => __( 'View Store', APP_TD ),
			'update_item'                => __( 'Update Store', APP_TD ),
			'add_new_item'               => __( 'Add New Store', APP_TD ),
			'new_item_name'              => __( 'New Store Name', APP_TD ),
			'separate_items_with_commas' => __( 'Separate stores with commas', APP_TD ),
			'add_or_remove_items'        => __( 'Add or remove stores', APP_TD ),
			'choose_from_most_used'      => __( 'Choose from the most common stores', APP_TD ),
			'not_found'                  => __( 'No stores found.', APP_TD ),
			'no_terms'                   => __( 'No stores', APP_TD ),
			'items_list_navigation'      => __( 'Stores list navigation', APP_TD ),
			'items_list'                 => __( 'Stores list', APP_TD ),
			'menu_name'                  => _x( 'Stores', 'taxonomy menu name', APP_TD ),
		);

		$args = array(
			'labels'                => $labels,
			'hierarchical'          => true,
			'show_ui'               => true,
			'query_var'             => true,
			'update_count_callback' => '_update_post_term_count',
			'rewrite'               => array(
				'slug'         => $clpr_options->coupon_store_tax_permalink,
				'with_front'   => false,
				'hierarchical' => true,
			),
		);

		register_taxonomy( APP_TAX_STORE, APP_POST_TYPE, $args );
	}

	/**
	 * Configure Listing instance with essential modules included in a
	 * listing package.
	 */
	public function add_modules() {

		$this->listing->add_module( 'form', 'CLPR_Store_Listing_Form' );
		$this->listing->add_module( 'form_fields_box', 'CLPR_Store_Listing_Taxonomy_Metabox', array( 'form', '__admin__' ) );

		$this->listing->add_module( 'process_new_settings',  'CLPR_Store_Listing_Process_New_Settings', array( '__admin__' ) );

		$this->listing->add_module(
			'custom_form',
			'CLPR_Store_Listing_Custom_Forms',
			array( 'form' ),
			array( APP_TAX_STORE )
		);

		parent::add_modules();
	}
}
