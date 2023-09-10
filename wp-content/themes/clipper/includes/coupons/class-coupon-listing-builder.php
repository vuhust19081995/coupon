<?php
/**
 * Coupon Listing Builder
 *
 * @package Clipper\Coupons
 * @author  AppThemes
 * @since   2.0.0
 */

/**
 * Coupon Listing Builder class
 */
class CLPR_Coupon_Listing_Builder extends APP_Post_Listing_Builder {

	/**
	 * Sets up basic listing options.
	 */
	public function set_options() {

		$options  = new APP_Listing_Options( $this->listing );
		$ptype    = $this->listing->get_type();
		$defaults = array(
			'duration'                 => 0,
			'notify_renew_' . $ptype   => true,
			'notify_pending_' . $ptype => true,
		);

		$options->set_defaults( $defaults );
		$this->listing->set_options( $options );
	}

	/**
	 * Registers taxonomies associated with current listing type
	 */
	protected function _register_taxonomies() {
		global $clpr_options;

		// Register the category taxonomy for coupons.
		$labels = array(
			'name'                       => _x( 'Categories', 'taxonomy general name', APP_TD ),
			'singular_name'              => _x( 'Coupon Category', 'taxonomy singular name', APP_TD ),
			'search_items'               => __( 'Search Coupon Categories', APP_TD ),
			'popular_items'              => __( 'Popular Coupon Categories', APP_TD ),
			'all_items'                  => __( 'All Coupon Categories', APP_TD ),
			'parent_item'                => __( 'Parent Coupon Category', APP_TD ),
			'parent_item_colon'          => __( 'Parent Coupon Category:', APP_TD ),
			'edit_item'                  => __( 'Edit Coupon Category', APP_TD ),
			'view_item'                  => __( 'View Coupon Category', APP_TD ),
			'update_item'                => __( 'Update Coupon Category', APP_TD ),
			'add_new_item'               => __( 'Add New Coupon Category', APP_TD ),
			'new_item_name'              => __( 'New Coupon Category Name', APP_TD ),
			'separate_items_with_commas' => __( 'Separate coupon categories with commas', APP_TD ),
			'add_or_remove_items'        => __( 'Add or remove coupon categories', APP_TD ),
			'choose_from_most_used'      => __( 'Choose from the most used coupon categories', APP_TD ),
			'not_found'                  => __( 'No coupon categories found.', APP_TD ),
			'no_terms'                   => __( 'No coupon categories', APP_TD ),
			'items_list_navigation'      => __( 'Coupon categories list navigation', APP_TD ),
			'items_list'                 => __( 'Coupon categories list', APP_TD ),
			'menu_name'                  => _x( 'Categories', 'taxonomy menu name', APP_TD ),
		);

		$args = array(
			'labels'                => $labels,
			'hierarchical'          => true,
			'show_ui'               => true,
			'query_var'             => true,
			'update_count_callback' => '_update_post_term_count',
			'rewrite'               => array( 'slug' => $clpr_options->coupon_cat_tax_permalink, 'with_front' => false, 'hierarchical' => true ),
		);

		register_taxonomy( APP_TAX_CAT, APP_POST_TYPE, $args );

		// Register the tag taxonomy for coupons.
		$labels = array(
			'name'                       => _x( 'Coupon Tags', 'taxonomy general name', APP_TD ),
			'singular_name'              => _x( 'Coupon Tag', 'taxonomy singular name', APP_TD ),
			'search_items'               => __( 'Search Coupon Tags', APP_TD ),
			'popular_items'              => __( 'Popular Coupon Tags', APP_TD ),
			'all_items'                  => __( 'All Coupon Tags', APP_TD ),
			'parent_item'                => __( 'Parent Coupon Tag', APP_TD ),
			'parent_item_colon'          => __( 'Parent Coupon Tag:', APP_TD ),
			'edit_item'                  => __( 'Edit Coupon Tag', APP_TD ),
			'view_item'                  => __( 'View Coupon Tag', APP_TD ),
			'update_item'                => __( 'Update Coupon Tag', APP_TD ),
			'add_new_item'               => __( 'Add New Coupon Tag', APP_TD ),
			'new_item_name'              => __( 'New Coupon Tag Name', APP_TD ),
			'separate_items_with_commas' => __( 'Separate coupon tags with commas', APP_TD ),
			'add_or_remove_items'        => __( 'Add or remove coupon tags', APP_TD ),
			'choose_from_most_used'      => __( 'Choose from the most common coupon tags', APP_TD ),
			'not_found'                  => __( 'No coupon tags found.', APP_TD ),
			'no_terms'                   => __( 'No coupon tags', APP_TD ),
			'items_list_navigation'      => __( 'Coupon tags list navigation', APP_TD ),
			'items_list'                 => __( 'Coupon tags list', APP_TD ),
			'menu_name'                  => _x( 'Coupon Tags', 'taxonomy menu name', APP_TD ),
		);

		$args = array(
			'labels'                => $labels,
			'hierarchical'          => false,
			'show_ui'               => true,
			'query_var'             => true,
			'update_count_callback' => '_update_post_term_count',
			'rewrite'               => array( 'slug' => $clpr_options->coupon_tag_tax_permalink, 'with_front' => false, 'hierarchical' => true ),
		);

		register_taxonomy( APP_TAX_TAG, APP_POST_TYPE, $args );

		// Register the type taxonomy for coupons.
		$labels = array(
			'name'                       => _x( 'Coupon Types', 'taxonomy general name', APP_TD ),
			'singular_name'              => _x( 'Coupon Type', 'taxonomy singular name', APP_TD ),
			'search_items'               => __( 'Search Coupon Types', APP_TD ),
			'popular_items'              => __( 'Popular Coupon Types', APP_TD ),
			'all_items'                  => __( 'All Coupon Types', APP_TD ),
			'parent_item'                => __( 'Parent Coupon Type', APP_TD ),
			'parent_item_colon'          => __( 'Parent Coupon Type:', APP_TD ),
			'edit_item'                  => __( 'Edit Coupon Type', APP_TD ),
			'view_item'                  => __( 'View Coupon Type', APP_TD ),
			'update_item'                => __( 'Update Coupon Type', APP_TD ),
			'add_new_item'               => __( 'Add New Coupon Type', APP_TD ),
			'new_item_name'              => __( 'New Coupon Type Name', APP_TD ),
			'separate_items_with_commas' => __( 'Separate coupon types with commas', APP_TD ),
			'add_or_remove_items'        => __( 'Add or remove coupon types', APP_TD ),
			'choose_from_most_used'      => __( 'Choose from the most used coupon types', APP_TD ),
			'not_found'                  => __( 'No coupon types found.', APP_TD ),
			'no_terms'                   => __( 'No coupon types', APP_TD ),
			'items_list_navigation'      => __( 'Coupon types list navigation', APP_TD ),
			'items_list'                 => __( 'Coupon types list', APP_TD ),
			'menu_name'                  => _x( 'Coupon Types', 'taxonomy menu name', APP_TD ),
		);

		$args = array(
			'labels'                => $labels,
			'hierarchical'          => true,
			'show_ui'               => true,
			'query_var'             => true,
			'update_count_callback' => '_update_post_term_count',
			'rewrite'               => array( 'slug' => $clpr_options->coupon_type_tax_permalink, 'with_front' => false, 'hierarchical' => true ),
		);

		register_taxonomy( APP_TAX_TYPE, APP_POST_TYPE, $args );

		// Register taxonomy for printable coupon images.
		$labels = array(
			'name'                       => _x( 'Coupon Images', 'taxonomy general name', APP_TD ),
			'singular_name'              => _x( 'Coupon Image', 'taxonomy singular name', APP_TD ),
			'search_items'               => __( 'Search Coupon Images', APP_TD ),
			'popular_items'              => __( 'Popular Coupon Images', APP_TD ),
			'all_items'                  => __( 'All Coupon Images', APP_TD ),
			'parent_item'                => __( 'Parent Coupon Image', APP_TD ),
			'parent_item_colon'          => __( 'Parent Coupon Image:', APP_TD ),
			'edit_item'                  => __( 'Edit Coupon Image', APP_TD ),
			'view_item'                  => __( 'View Coupon Image', APP_TD ),
			'update_item'                => __( 'Update Coupon Image', APP_TD ),
			'add_new_item'               => __( 'Add New Coupon Image', APP_TD ),
			'new_item_name'              => __( 'New Coupon Image Name', APP_TD ),
			'separate_items_with_commas' => __( 'Separate coupon images with commas', APP_TD ),
			'add_or_remove_items'        => __( 'Add or remove coupon images', APP_TD ),
			'choose_from_most_used'      => __( 'Choose from the most used coupon images', APP_TD ),
			'not_found'                  => __( 'No coupon images found.', APP_TD ),
			'no_terms'                   => __( 'No coupon images', APP_TD ),
			'items_list_navigation'      => __( 'Coupon images list navigation', APP_TD ),
			'items_list'                 => __( 'Coupon images list', APP_TD ),
			'menu_name'                  => _x( 'Coupon Images', 'taxonomy menu name', APP_TD ),
		);

		$args = array(
			'labels'                => $labels,
			'hierarchical'          => false,
			'public'                => false,
			'show_ui'               => false,
			'query_var'             => true,
			'update_count_callback' => '_update_post_term_count',
			'rewrite'               => array( 'slug' => $clpr_options->coupon_image_tax_permalink, 'with_front' => false, 'hierarchical' => false ),
		);

		register_taxonomy( APP_TAX_IMAGE, 'attachment', $args );
	}
	/**
	 * Registers Listing type as post type
	 */
	protected function _register_listing_type() {
		global $clpr_options;

		// register post type for coupons
		$labels = array(
			'name'                  => _x( 'Coupons', 'post type general name', APP_TD ),
			'singular_name'         => _x( 'Coupon', 'post type singular name', APP_TD ),
			'add_new'               => __( 'Add New', APP_TD ),
			'add_new_item'          => __( 'Add New Coupon', APP_TD ),
			'edit_item'             => __( 'Edit Coupon', APP_TD ),
			'new_item'              => __( 'New Coupon', APP_TD ),
			'view_item'             => __( 'View Coupon', APP_TD ),
			'view_items'            => __( 'View Coupons', APP_TD ),
			'search_items'          => __( 'Search Coupons', APP_TD ),
			'not_found'             => __( 'No coupons found.', APP_TD ),
			'not_found_in_trash'    => __( 'No coupons found in trash.', APP_TD ),
			'parent_item_colon'     => __( 'Parent Coupon:', APP_TD ),
			'menu_name'             => _x( 'Coupons', 'post type menu name', APP_TD ),
			'all_items'             => __( 'All Coupons', APP_TD ),
			'archives'              => __( 'Coupon Archives', APP_TD ),
			'attributes'            => __( 'Coupon Attributes', APP_TD ),
			'insert_into_item'      => __( 'Insert into coupon', APP_TD ),
			'uploaded_to_this_item' => __( 'Uploaded to this coupon', APP_TD ),
			'featured_image'        => __( 'Featured Image', APP_TD ),
			'set_featured_image'    => __( 'Set featured image', APP_TD ),
			'remove_featured_image' => __( 'Remove featured image', APP_TD ),
			'use_featured_image'    => __( 'Use as featured image', APP_TD ),
			'filter_items_list'     => __( 'Filter coupons list', APP_TD ),
			'items_list_navigation' => __( 'Coupons list navigation', APP_TD ),
			'items_list'            => __( 'Coupons list', APP_TD ),
		);

		$args = array(
			'labels'              => $labels,
			'description'         => __( 'This is where you can create new coupon listings on your site.', APP_TD ),
			'public'              => true,
			'show_ui'             => true,
			'has_archive'         => true,
			'capability_type'     => 'post',
			'publicly_queryable'  => true,
			'exclude_from_search' => false,
			'menu_position'       => 8,
			'menu_icon'           => 'dashicons-tickets-alt',
			'hierarchical'        => false,
			'rewrite'             => array( 'slug' => $clpr_options->coupon_permalink, 'with_front' => false, 'feeds' => true ),
			'query_var'           => true,
			'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'trackbacks', 'custom-fields', 'comments', 'revisions', 'sticky' ),
		);

		register_post_type( APP_POST_TYPE, $args );
	}
	/**
	 * Configure Listing instance with essential modules included in a
	 * listing package.
	 */
	public function add_modules() {

		$this->listing->add_module( 'plans_settings',        'CLPR_Coupon_Listing_Plans_Settings',  array( 'plans', '__admin__' ) );
		$this->listing->add_module( 'view_process_new',      'APP_View_Process_New', array(), array( 'new', array( 'process_title' => __( 'Share Coupon', APP_TD ) ) ) );
		$this->listing->add_module( 'view_process_edit',     'APP_View_Process_Edit', array(), array( 'edit', array( 'process_title' => __( 'Edit Coupon', APP_TD ) ) ) );
		$this->listing->add_module( 'view_process_renew',    'APP_View_Process_Renew', array(), array( 'renew', array( 'process_title' => __( 'Renew Coupon', APP_TD ) ) ) );
		$this->listing->add_module( 'step_select_plan',      'APP_Step_Select_Plan' ); // Needs for correct checkout list tree building.
		$this->listing->add_module( 'step_edit_info',        'CLPR_Step_Edit_Info' );
		$this->listing->add_module( 'step_edit_info_new',    'CLPR_Step_Edit_Info_New' );
		$this->listing->add_module( 'expire',                'CLPR_Coupon_Listing_Expire' );
		$this->listing->add_module( 'form',                  'CLPR_Coupon_Listing_Form' );
		$this->listing->add_module( 'form_fields_box',       'CLPR_Coupon_Listing_Fields_Metabox',  array( 'form', '__admin__' ), array( __( 'Coupon Details', APP_TD ) ) );

		// Needs for correct dependencies tree building.
		$this->listing->add_module(
			'multi_plans',
			'APP_Listing_Plans',
			array( 'plans' ),
			array(),
			array(
				'optional' => true,
				'label'    => __( 'Multiple Listing Plans', APP_TD ),
			)
		);

		$this->listing->add_module(
			'addons',
			'APP_Listing_Addons',
			array( 'payments' ),
			array(
				array(
					CLPR_ITEM_FEATURED => array(
						'title'    => __( 'Featured Coupon', APP_TD ),
						'duration' => 0,
						'period'   => 0,
						'flag_key' => CLPR_ITEM_FEATURED,
					),
				),
			)
		);

		// Following taxonomy-depended modules must have dynamic names, i.e "{$taxonomy}_limit".
		$this->listing->add_module(
			APP_TAX_CAT . '_limit',
			'APP_Listing_Taxonomy_Limit',
			array(),
			array( APP_TAX_CAT ),
			array(
				'optional' => true,
				'label'    => __( 'Category Limit', APP_TD ),
			)
		);

		$this->listing->add_module(
			APP_TAX_CAT . '_surcharge',
			'APP_Listing_Taxonomy_Surcharge',
			array( 'payments' ),
			array( APP_TAX_CAT ),
			array(
				'optional' => true,
				'label'    => __( 'Category Surcharges', APP_TD ),
			)
		);

		$this->listing->add_module(
			APP_TAX_TYPE . '_surcharge',
			'APP_Listing_Taxonomy_Surcharge',
			array( 'payments' ),
			array( APP_TAX_TYPE ),
			array(
				'optional' => true,
				'label'    => __( 'Coupon Type Surcharges', APP_TD ),
			)
		);

		$this->listing->add_module(
			'custom_form',
			'APP_Listing_Custom_Forms',
			array( 'form' ),
			array( APP_TAX_CAT ),
			array(
				'optional' => true,
				'label'    => __( 'Custom Forms', APP_TD ),
			)
		);

		$this->listing->add_module(
			'coupon_type_form',
			'APP_Listing_Custom_Forms',
			array( 'form' ),
			array( APP_TAX_TYPE )
		);

		$this->listing->add_module(
			'coupon_type_form_fields_box',
			'CLPR_Coupon_Listing_Type_Fields_Metabox',
			array(
				'coupon_type_form',
				'__admin__',
			),
			array(
				APP_TAX_TYPE,
			)
		);

		$this->listing->add_module(
			'coupon_type_form_box',
			'CLPR_Coupon_Listing_Type_Metabox',
			array(
				'coupon_type_form_fields_box',
				'__admin__',
			),
			array(
				APP_TAX_TYPE,
				__( 'Coupon Type', APP_TD ),
			)
		);

		/**
		 * Following Features can't be enabled just they are. Some code changes
		 * needs to be done in the theme to make it work.
		 */
		$this->listing->add_module(
			'search_index',
			'APP_Listing_Search_Index',
			array( 'form' ),
			array(),
			array(
				'optional' => false,
				'label'    => __( 'Search Index', APP_TD ),
			)
		);

		// Disabled Modules.
		$this->listing->add_module( 'view_process_upgrade', '' );
		$this->listing->add_module( 'step_current_plan',    '' );
		$this->listing->add_module( 'step_upgrade',         '' );
		$this->listing->add_module( 'step_upgrade_order',   '' );
		$this->listing->add_module( 'process_widget',       '' );
		$this->listing->add_module( 'recurring',            '' );
		$this->listing->add_module( 'category_limit',       '' );
		$this->listing->add_module( 'category_surcharge',   '' );
		$this->listing->add_module( 'claim',                '' );

		parent::add_modules();
	}
}
