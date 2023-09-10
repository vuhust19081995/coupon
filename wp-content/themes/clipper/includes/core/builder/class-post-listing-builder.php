<?php
/**
 * Post Type Listing Builder
 *
 * @package Listing\Builder
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * A generic Listing Builder to construct a Listing object associated with
 * already registered post type (like 'post' or 'attachment').
 *
 * Supposed to be a base builder class for any Listing types that depended on
 * custom post types.
 */
class APP_Post_Listing_Builder extends APP_Listing_Builder {

	/**
	 * Set up listing meta object.
	 */
	public function set_meta_object() {
		$meta_object = new APP_Listing_Post_Meta_Object();
		$this->listing->set_meta_object( $meta_object );
	}

	/**
	 * Creates empty Listing instance.
	 *
	 * @param string $type The Listing type.
	 */
	public function create( $type = '' ) {

		if ( ! $type ) {
			$type = 'post';
		}

		$this->modules = new APP_Listing_Dependencies();
		$this->listing = new APP_Listing( $type, $this->modules );
	}

	/**
	 * Registers listing stuff in the WordPress system (i.e. taxonomies, post
	 * types, comment types, etc.).
	 */
	public function register() {
		$this->_register_taxonomies();
		$this->_register_listing_type();
	}

	/**
	 * Sets up basic listing options.
	 */
	public function set_options() {
		$options = new APP_Listing_Options( $this->listing );
		$this->listing->set_options( $options );
	}

	/**
	 * Registers taxonomies associated with current listing type
	 */
	protected function _register_taxonomies() {}

	/**
	 * Registers Listing type as post type
	 */
	protected function _register_listing_type() {}

	/**
	 * Configure Listing instance with essential modules included in a
	 * listing package.
	 */
	public function add_modules() {
		// Add default is_admin() dependency.
		if ( is_admin() ) {
			$this->modules->add( '__admin__', '' );
		}

		if ( $this->listing->options->charge ) {
			$this->listing->add_module( 'payments',          'APP_Listing_Payments' );
			$this->listing->add_module( 'payments_settings', 'APP_Listing_Payments_Settings', array( 'payments', '__admin__' ) );
		}
		$this->listing->add_module( 'charge_settings',   'APP_Listing_Charge_Settings', array( '__admin__' ) );

		$this->listing->add_module( 'plans',                 'APP_Listing_Plans_Registry' );
		$this->listing->add_module( 'plans_settings',        'APP_Listing_Plans_Settings',  array( 'plans', '__admin__' ) );
		$this->listing->add_module( 'plans_price_box',       'APP_Listing_Plans_Price_Box', array( 'payments', '__admin__', 'multi_plans' ) );

		$this->listing->add_module( 'view_process_new',      'APP_View_Process_New' );
		$this->listing->add_module( 'view_process_edit',     'APP_View_Process_Edit' );
		$this->listing->add_module( 'view_process_renew',    'APP_View_Process_Renew' );
		$this->listing->add_module( 'view_process_upgrade',  'APP_View_Process_Upgrade' );
		$this->listing->add_module( 'process_new_settings',  'APP_Listing_Process_New_Settings', array( 'view_process_new', '__admin__' ) );
		$this->listing->add_module( 'process_edit_settings', 'APP_Listing_Process_Edit_Settings', array( 'view_process_edit', '__admin__' ) );
		$this->listing->add_module( 'process_renew_settings','APP_Listing_Process_Renew_Settings', array( 'view_process_renew', '__admin__' ) );
		$this->listing->add_module( 'step_select_plan',      'APP_Step_Select_Plan' );
		$this->listing->add_module( 'step_current_plan',     'APP_Step_Current_Plan' );
		$this->listing->add_module( 'step_activate',         'APP_Step_Activate' );
		$this->listing->add_module( 'step_upgrade',          'APP_Step_Upgrade' );
		$this->listing->add_module( 'step_moderate',         'APP_Step_Moderate' );
		$this->listing->add_module( 'step_activate_order',   'APP_Step_Activate_Order', array( 'payments' ) );
		$this->listing->add_module( 'step_edit_info',        'APP_Step_Edit_Info' );
		$this->listing->add_module( 'step_edit_info_new',    'APP_Step_Edit_Info_New' );
		$this->listing->add_module( 'step_edit_info_optional', 'APP_Step_Edit_Info_Optional' );
		$this->listing->add_module( 'step_create_order',     'APP_Step_Create_Order', array( 'payments' ) );
		$this->listing->add_module( 'step_upgrade_order',    'APP_Step_Create_Upgrade_Order', array( 'payments' ) );
		$this->listing->add_module( 'step_gateway_select',   'APP_Step_Gateway_Select', array( 'payments' ) );
		$this->listing->add_module( 'step_gateway_process',  'APP_Step_Gateway_Process', array( 'payments' ) );
		$this->listing->add_module( 'expire',                'APP_Listing_Expire', array( 'plans' ) );

		$this->listing->add_module( 'form',                  'APP_Listing_Form' );
		$this->listing->add_module( 'form_settings',         'APP_Listing_Form_Settings',  array( 'form', '__admin__' ) );
		$this->listing->add_module( 'form_fields_box',       'APP_Listing_Fields_Metabox', array( 'form', '__admin__' ) );
		$this->listing->add_module( 'form_media_box',        'APP_Listing_Media_Metabox',  array( 'form', '__admin__' ) );

		$this->listing->add_module( 'caps',                  'APP_Listing_Caps' );
		$this->listing->add_module( 'details',               'APP_Listing_Details' );
		$this->listing->add_module( 'details_settings',      'APP_Listing_Details_Settings',  array( 'details', '__admin__' ) );
		$this->listing->add_module( 'process_widget',        'APP_Widget_Process_Listing_Button' );

		$this->listing->add_module( 'settings',              'APP_Listing_Settings',  array( '__admin__' ) );
		$this->listing->add_module( 'admin_security',        'APP_Listing_Admin_Security',  array( '__admin__' ) );

		$this->listing->add_module( 'features',              'APP_Listing_Features' );
		$this->listing->add_module( 'features_settings',     'APP_Listing_Features_Settings',  array( 'features', '__admin__' ) );

		// Optional features.
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
			array(),
			array(
				'optional' => true,
				'label'    => __( 'Listing Pricing Addons', APP_TD ),
			)
		);

		$this->listing->add_module(
			'recurring',
			'APP_Listing_Recurring',
			array( 'payments' ),
			array(),
			array(
				'optional' => true,
				'label'    => __( 'Recurring Payments', APP_TD ),
			)
		);

		// Following taxonomy-depended modules must have dynamic names, i.e "{$taxonomy}_limit".
		$this->listing->add_module(
			'category_limit',
			'APP_Listing_Taxonomy_Limit',
			array(),
			array( 'category' ),
			array(
				'optional' => true,
				'label'    => __( 'Category Limit', APP_TD ),
			)
		);

		$this->listing->add_module(
			'category_surcharge',
			'APP_Listing_Taxonomy_Surcharge',
			array( 'payments' ),
			array( 'category' ),
			array(
				'optional' => true,
				'label'    => __( 'Category Surcharges', APP_TD ),
			)
		);

		$this->listing->add_module(
			'custom_form',
			'APP_Listing_Custom_Forms',
			array( 'form' ),
			array( 'category' ),
			array(
				'optional' => true,
				'label'    => __( 'Custom Forms', APP_TD ),
			)
		);

		$this->listing->add_module(
			'custom_forms_box',
			'APP_Listing_Custom_Forms_Metabox',
			array(
				'custom_form',
				'__admin__',
			),
			array(
				! empty( $this->modules->registered['custom_form']->args[1] ) ? $this->modules->registered['custom_form']->args[1] : 'category',
				__( 'Additional Fields', APP_TD ),
			)
		);

		$this->listing->add_module(
			'claim',
			'APP_Listing_Claim',
			array(),
			array(),
			array(
				'optional' => true,
				'label'    => __( 'Claim Listings', APP_TD ),
			)
		);

		$this->listing->add_module(
			'anonymous',
			'APP_Listing_Anonymous',
			array( 'view_process_new' ),
			array(),
			array(
				'optional' => true,
				'label'    => __( 'Anonymous Listings', APP_TD ),
			)
		);

		$this->listing->add_module( 'step_create_user', 'APP_Step_Create_User', array( 'anonymous' ) );
		$this->listing->add_module( 'anonymous_expire', 'APP_Listing_Anonymous_Expire', array( 'anonymous' ) );

		/**
		 * Following Features can't be enabled just they are. Some code changes
		 * needs to be done in the theme to make it work.
		 */
		/*
		$this->listing->add_module(
			'search_index',
			'APP_Listing_Search_Index',
			array( 'form' ),
			array(),
			array(
				'optional' => true,
				'label'    => __( 'Search Index', APP_TD ),
			)
		);
		*/
	}

	/**
	 * Enqueues a module classes in a Listing's dependency object.
	 *
	 * Filters disabled modules. Only enqueued modules will be instantiated.
	 *
	 * @param mixed $handles Item handle and argument (string) or item handles
	 *                        and arguments (array of strings). If argument is
	 *                        false, all registered items will be enqueued.
	 */
	public function enqueue_modules( $handles = false ) {

		if ( false === $handles ) {
			$handles = array_keys( $this->modules->registered );
		}

		$allowed = (array) $this->listing->options->features;

		// We use helper Dependency object to filter out all disabled features
		// and their dependees.
		$_modules = new APP_Listing_Dependencies();
		$_modules->registered = $this->modules->registered;
		$_handles = array_keys( $_modules->registered );

		foreach ( $_handles as $_handle ) {
			if ( $this->modules->get_data( $_handle, 'optional' ) && ! in_array( $_handle, $allowed ) ) {
				$_modules->remove( $_handle );
			}
		}

		$_modules->all_deps( $handles );
		$handles = $_modules->to_do;

		foreach ( $_modules->to_do as $handle ) {
			$this->modules->enqueue( $handle );
		}
	}

	/**
	 * Instantiates registered submodules.
	 *
	 * Processes the items passed to it or the queue, and their dependencies.
	 *
	 * @param mixed $handles Optional. Items to be processed:
	 *                        Process queue (false),
	 *                        process item (string),
	 *                        process items (array of strings).
	 */
	public function set_modules( $handles = false ) {
		$this->modules->do_items( $handles );
	}

}
