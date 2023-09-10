<?php
/**
 * Listing Custom Form submodule.
 *
 * @package Listing\Modules\CustomForms
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Custom Form processing class
 */
class APP_Listing_Custom_Forms {

	/**
	 * Current Listing module object.
	 *
	 * @var APP_Listing
	 */
	protected $listing;

	/**
	 * Taxonomy associated with Custom Forms and Listing types.
	 *
	 * @var string
	 */
	protected $taxonomy;

	/**
	 * Constructs listing form object
	 *
	 * @param APP_Listing $listing  Listing object to assign process with.
	 * @param string      $taxonomy Taxonomy that will be used for custom forms
	 *                               items query.
	 */
	public function __construct( APP_Listing $listing, $taxonomy ) {

		if ( ! $taxonomy ) {
			return;
		}

		$this->listing  = $listing;
		$this->taxonomy = $taxonomy;

		if ( did_action( 'init' ) ) {
			$this->init();
		} else {
			add_action( 'init', array( $this, 'init' ) );
		}

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_scripts' ), 99 );
		add_action( "wp_ajax_app-render-tax_input[$taxonomy][]-form", array( $this, 'ajax_render_form' ) );
		add_action( "wp_ajax_nopriv_app-render-tax_input[$taxonomy][]-form", array( $this, 'ajax_render_form' ) );

		add_filter( 'appthemes_form_field', array( $this, 'form_field' ), 10, 3 );
		add_filter( 'appthemes_details_fields', array( $this, 'details_fields' ), 10, 3 );
		add_filter( 'appthemes_process_listing_form_fields', array( $this, 'process_form' ), 10, 3 );

		add_filter( 'appthemes_update_search_index_' . $this->listing->get_type(), array( $this, 'update_search_index' ), 10, 2 );
	}

	/**
	 * Init module.
	 */
	public function init() {
		register_taxonomy_for_object_type( $this->taxonomy, APP_FORMS_PTYPE );
	}

	/**
	 * Generates form by ajax request.
	 */
	public function ajax_render_form() {

		check_ajax_referer( 'app-render-tax_input-form', 'security' );

		// TODO: Maybe use APP_Ajax_View ??
		$args = stripslashes_deep( $_POST );

		if ( empty( $args['cats'] ) ) {
			die;
		}

		$cat = $args['cats'];

		$listing_id = ! empty( $args['listing_id'] ) ? (int) $args['listing_id'] : 0;

		$this->render_form( $cat, $this->taxonomy, $listing_id );
		die;
	}

	/**
	 * Renders custom form.
	 *
	 * @param array  $categories Taxonomy terms.
	 * @param string $taxonomy   Taxonomy name.
	 * @param int    $listing_id Listing ID.
	 */
	public function render_form( $categories, $taxonomy, $listing_id = 0 ) {
		$fields = array();

		foreach ( $categories as $category ) {
			foreach ( self::get_fields_for_cat( $category, $taxonomy ) as $field ) {
				$field = $this->listing->form->apply_atts( $field, $listing_id );
				$field = apply_filters( 'appthemes_form_field', $field, $listing_id, $this->listing->get_type() );
				$key   = scbForms::get_name( $field['name'] );
				$fields[ $key ] = $field;
				$fields[ $key ]['cat'] = $category;
			}
		}

		$fields = apply_filters( 'appthemes_render_custom_form_fields', $fields, $listing_id, $categories );

		if ( empty( $fields ) ) {
			return;
		}

		echo $this->listing->form->get_form( $listing_id, $fields );
	}

	/**
	 * Adds Custom fields to the Listing Details list.
	 *
	 * @param array $fields  Custom fields.
	 * @param int   $item_id Listing item ID.
	 * @param int   $type    Listing type.
	 *
	 * @return array Extended form fields array.
	 */
	public function details_fields( $fields, $item_id, $type ) {

		if ( $this->listing->get_type() !== $type ) {
			return $fields;
		}

		$categories = wp_get_object_terms( $item_id, $this->taxonomy, array(
			'fields' => 'ids',
		) );

		foreach ( $categories as $category ) {

			$custom_fields = self::get_fields_for_cat( $category, $this->taxonomy );

			$fields = array_merge( $fields, $custom_fields );
		}

		$_fields = array();
		foreach ( $fields as $field ) {
			$_fields[ $field['name'] ] = $field;
		}

		$fields = array_values( $_fields );

		return $fields;
	}

	/**
	 * Adds Custom form fields to the Listing form processing method.
	 *
	 * @param array $fields  Form fields.
	 * @param int   $item_id Listing item ID.
	 * @param int   $type    Listing type.
	 *
	 * @return array Extended form fields array.
	 */
	public function process_form( $fields, $item_id, $type ) {

		if ( $this->listing->get_type() !== $type ) {
			return $fields;
		}

		if ( ! isset( $_POST['tax_input'][ $this->taxonomy ] ) || empty( $_POST['tax_input'][ $this->taxonomy ] ) ) {
			$categories = wp_get_object_terms( $item_id, $this->taxonomy, array(
				'fields' => 'ids',
			) );
		} else {
			$categories = array_map( 'intval', (array) $_POST['tax_input'][ $this->taxonomy ] );
		}

		/* @var $form APP_Listing_Form */
		$form = $this->listing->form;

		// Exclude by default.
		$exclude_filters = array(
			'type' => array( 'tax_input' ),
		);

		foreach ( $categories as $category ) {

			$custom_fields = self::get_fields_for_cat( $category, $this->taxonomy );

			foreach ( $custom_fields as $key => &$field ) {

				// Remove field if it is excluded.
				if ( ! $form->filter( $field, $exclude_filters, false ) ) {
					unset( $custom_fields[ $key ] );
					continue;
				}

				$field    = $form->apply_atts( $field, $item_id );
				$fields[] = $field;
			}
		}

		return $fields;
	}

	/**
	 * Retrieves form fields by given taxonomy term.
	 *
	 * @param array  $category Taxonomy terms.
	 * @param string $taxonomy Taxonomy name.
	 *
	 * @return array Custom Form fields.
	 */
	public static function get_fields_for_cat( $category, $taxonomy ) {
		$form = get_posts(
			array(
				'fields'    => 'ids',
				'post_type' => APP_FORMS_PTYPE,
				'tax_query' => array(
					array(
						'taxonomy'         => $taxonomy,
						'terms'            => (array) $category,
						'field'            => 'term_id',
						'include_children' => false,
					),
				),
				'post_status' => 'publish',
				'numberposts' => 1,
			)
		);

		if ( empty( $form ) ) {
			return array();
		}

		return APP_Form_Builder::get_fields( $form[0] );
	}

	/**
	 * Adds extra parameters to listing form field.
	 *
	 * @param array $field   Form field.
	 * @param int   $item_id Listing item ID.
	 * @param int   $type    Listing type.
	 *
	 * @return array Modified Form field.
	 */
	public function form_field( $field, $item_id, $type ) {

		if ( array( 'tax_input', $this->taxonomy ) !== $field['name'] ) {
			return $field;
		}

		if ( $this->listing->get_type() !== $type ) {
			return $field;
		}

		$field['props']['ul_class'] = 'app-category-fields';

		return $field;
	}

	/**
	 * Adds custom fields values to search index.
	 *
	 * @param array  $args Search Index arguments.
	 * @param object $item The listing item.
	 */
	public function update_search_index( $args, $item ) {
		$item_id = $this->listing->meta->get_item_id( $item );

		// Filter items of another meta type.
		if ( ! $item_id ) {
			return $args;
		}

		$type = $this->listing->meta->get_type( $item_id );

		// Filter items of another listing type within meta type.
		if ( $type !== $this->listing->get_type() ) {
			return $args;
		}

		$categories = wp_get_object_terms( $item_id, $this->taxonomy, array(
			'fields' => 'ids',
		) );

		$fields = array();
		$args   = wp_parse_args( $args, array(
			'post_fields' => array(),
			'meta_keys'   => array(),
			'taxonomies'  => array(),
		) );

		foreach ( $categories as $category ) {
			$custom_fields = self::get_fields_for_cat( $category, $this->taxonomy );
			$fields        = array_merge( $fields, $custom_fields );
		}

		$field_names = wp_list_pluck( $fields, 'name' );

		/* @var $form_mod APP_Listing_Form */
		$form_mod = $this->listing->form;

		$core_fields = wp_array_slice_assoc( $field_names, $this->listing->meta->get_core_fields() );
		$post_fields = array_merge( $args['post_fields'], array_fill_keys( $core_fields, true ) );

		$tax_fields  = wp_list_filter( $fields, array( 'type' => 'tax_input' ) );
		$taxonomies  = wp_list_pluck( wp_list_pluck( $tax_fields, 'props' ), 'tax' );
		$taxonomies  = array_unique( array_merge( $args['taxonomies'], $taxonomies ) );

		$meta_keys = $args['post_fields'];

		foreach ( $fields as $field ) {
			if ( $form_mod->filter( $field, array( 'type' => array( 'tax_input', 'file' ), 'name' => $core_fields ), true ) ) {
				continue;
			}

			$meta_keys[] = $field['name'];
		}

		$meta_keys = array_filter( $meta_keys );

		return compact( 'post_fields', 'meta_keys', 'taxonomies' );
	}

	/**
	 * Registers module's scripts.
	 */
	public static function register_scripts() {

		// We can't rely on script dependency behaviour since Query Monitor
		// plugin considering that as an error :/ .
		if ( ! wp_script_is( 'app-listing-validate' ) ) {
			return;
		}

		wp_enqueue_script(
			'app-category-fields',
			APP_LISTING_URI . '/extra/custom-forms/scripts/jquery.app.categoryfields.js',
			array( 'app-listing-validate' ),
			APP_LISTING_VERSION,
			true
		);

		wp_localize_script( 'app-category-fields', 'appCategoryFieldsL10n', array(
			'nonce' => wp_create_nonce( 'app-render-tax_input-form' ),
		) );

		// Function available since WordPress 4.8.
		if ( function_exists( 'wp_enqueue_editor' ) ) {
			wp_enqueue_editor();
		}
	}

}
