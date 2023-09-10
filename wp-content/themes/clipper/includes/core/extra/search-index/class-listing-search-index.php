<?php
/**
 * Listing Search Index submodule.
 *
 * This module can't work from the box since it requires manual setting up the
 * search clauses.
 *
 * We need to introduce a common Listing Search module where the Search Index
 * can safely change clauses.
 *
 * @package Listing\Modules\SearchIndex
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Custom Form processing class
 */
class APP_Listing_Search_Index {

	/**
	 * Current Listing module object.
	 *
	 * @var APP_Listing
	 */
	protected $listing;

	/**
	 * Constructs object
	 *
	 * @param APP_Listing $listing  Listing object to assign process with.
	 */
	public function __construct( APP_Listing $listing ) {

		$this->listing  = $listing;

		if ( did_action( 'init' ) ) {
			$this->init();
		} else {
			add_action( 'init', array( $this, 'init' ) );
		}
	}

	/**
	 * Init module.
	 */
	public function init() {
		if ( ! class_exists( 'APP_Search_Index' ) ) {
			return;
		}
		$this->register();

		add_filter( 'appthemes_handle_listing_form', array( $this, 'update_search_index' ), 99999, 5 );
	}

	/**
	 * Registers listing type in the Search Index module.
	 */
	protected function register() {
		/* @var $form_mod APP_Listing_Form */
		$form_mod    = $this->listing->form;
		$core_fields = $this->listing->meta->get_core_fields();
		$post_fields = array_fill_keys( $core_fields, true );
		$tax_fields  = $form_mod->get_form_fields( array(), array( 'type' => 'tax_input' ) );
		$meta_fields = $form_mod->get_form_fields( array( 'type' => array( 'tax_input', 'file' ), 'name' => $core_fields ), array() );
		$taxonomies  = wp_list_pluck( wp_list_pluck( $tax_fields, 'props' ), 'tax' );
		$meta_keys   = wp_list_pluck( $meta_fields, 'name' );

		$args = compact( 'post_fields', 'meta_keys', 'taxonomies' );

		APP_Search_Index::register( $this->listing->get_type(), $args );
	}

	/**
	 * Updates search index after the listing form processed.
	 *
	 * @param int      $item_id      Listing Item ID.
	 * @param array    $form_fields  Form fields.
	 * @param array    $formdata     Posted form data.
	 * @param WP_Error $errors       Errors object.
	 * @param string   $listing_type The Listing type.
	 *
	 * @return int Listing Item ID.
	 */
	public function update_search_index( $item_id, $form_fields, $formdata, $errors, $listing_type ) {
		if ( $listing_type !== $this->listing->get_type() ) {
			return $item_id;
		}

		appthemes_update_search_index( $this->listing->meta->get_item( $item_id ) );

		return $item_id;
	}
}
