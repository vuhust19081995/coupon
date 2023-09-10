<?php
/**
 * Listing Custom Fields meta box
 *
 * @package Listing\Modules\CustomForms\Metaboxes
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Custom Fields meta box class.
 */
class APP_Listing_Custom_Forms_Metabox extends APP_Listing_Fields_Metabox {

	/**
	 * Taxonomy associated with Custom Forms and Listing types.
	 *
	 * @var string
	 */
	protected $taxonomy;

	/**
	 * Constructs the listing custom fields meta box.
	 *
	 * @param APP_Listing $listing  Listing module object.
	 * @param string      $taxonomy Taxonomy that will be used for custom forms
	 *                               items query.
	 * @param string      $title    Meta box title.
	 */
	public function __construct( APP_Listing $listing, $taxonomy, $title = '' ) {

		if ( ! $taxonomy ) {
			return;
		}

		$this->listing  = $listing;
		$this->taxonomy = $taxonomy;

		parent::__construct( $listing, $title );
	}

	/**
	 * Retrieves all the valid fields for the meta box (excludes internal fields
	 * and others set by the include/exclude filters).
	 *
	 * @return array List of valid fields.
	 */
	protected function _get_form_valid_fields() {

		if ( ! $post_id = $this->get_post_id() ) {
			return array();
		}

		$categories = $this->get_the_terms( $post_id );
		$categories = array_keys( $categories );
		$fields     = array();
		$form       = $this->listing->form;

		// Exclude by default.
		$exclude_filters = array(
			'type' => array( 'tax_input' ),
		);

		$exclude_filters = array_merge_recursive( $exclude_filters, $this->exclude_filters() );

		foreach ( $categories as $category ) {

			$custom_fields = APP_Listing_Custom_Forms::get_fields_for_cat( $category, $this->taxonomy );

			foreach ( $custom_fields as $key => &$field ) {

				// Remove field if it is excluded or not included.
				if ( ! $form->filter( $field, $exclude_filters, false ) || ! $form->filter( $field, $this->include_filters() ) ) {
					unset( $custom_fields[ $key ] );
					continue;
				}

				$field = $form->apply_atts( $field, $this->get_post_id() );
				$fields[ $field['name'] ] = $field;
				$fields[ $field['name'] ]['cat'] = $category;

			}
		}

		return $fields;
	}

	function get_the_terms( $post_id = 0 ) {
		$post_id = $post_id ? $post_id : get_the_ID();

		$_terms = get_the_terms( $post_id, $this->taxonomy );

		if ( ! $_terms ) {
			return array();
		}

		// WordPress does not always key with the term_id, but thats what we want for the key.
		$terms = array();
		foreach ( $_terms as $_term ) {
			$terms[ $_term->term_id ] = $_term;
		}

		return $terms;
	}

}
