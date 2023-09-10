<?php
/**
 * Single Taxonomy Listing Fields metabox
 *
 * @package Clipper\Stores
 * @author  AppThemes
 * @since   2.0.0
 */

/**
 * Base class for displaying a meta box with all the custom fields attached to a
 * listing.
 */
class CLPR_Store_Listing_Taxonomy_Metabox extends APP_Listing_Taxonomy_Metabox {

	/**
	 * Retrieves a list of field attributes used to excluded certain fields from
	 * the final field list.
	 *
	 * @return array An associative array with field attribute/value(s)
	 *               (e.g: array( 'name' => 'my-unwanted-custom-field' ) ).
	 */
	protected function exclude_filters() {
		return array(
			'name' => 'new_store_name',
		);
	}

	/**
	 * Retrieve the meta box fields list.
	 *
	 * @return array The fields list.
	 */
	public function form() {
		$fields = parent::form();

		foreach ( $fields as &$field ) {
			if ( 'clpr_store_url' === $field['name'] ) {
				$field['desc'] = __( 'The URL for the store (i.e. http://www.website.com)', APP_TD );
				if ( empty( $field['title'] ) ) {
					$field['title'] = __( 'Store URL', APP_TD );
				}
			}

			if ( 'clpr_store_image_id' === $field['name'] ) {
				$store_image_ids = (array) clpr_get_store_meta( $field['listing_id'], 'clpr_store_image_id', true );
				foreach ( $store_image_ids as $store_image_id ) {
					$meta_set = get_post_meta( $store_image_id, '_app_attachment_parent_id', true );
					if ( empty( $meta_set ) ) {
						update_post_meta( $store_image_id, '_app_attachment_parent_id', $field['listing_id'] );
					}
				}
			}
		}

		$admin_fields = array(
			array(
				'type'  => 'url',
				'name'  => 'clpr_store_aff_url',
				'title' => __( 'Destination URL', APP_TD ),
				'desc'  => __( 'The affiliate URL for the store (i.e. http://www.website.com/?affid=12345)', APP_TD ),
				'props' => array(
					'required' => 0,
				),
			),
			array(
				'type'    => 'select',
				'name'    => 'clpr_store_active',
				'title'   => __( 'Store Active', APP_TD ),
				'desc'    => '',
				'choices' => array(
					'yes' => __( 'Yes', APP_TD ),
					'no'  => __( 'No', APP_TD ),
				),
				'extra' => array(
					'style' => 'min-width:125px;',
				),
				'props' => array(
					'required' => 0,
				),
			),
			array(
				'type'  => 'checkbox',
				'name'  => 'clpr_store_featured',
				'title' => __( 'Store Featured', APP_TD ),
				'desc'  => __( 'Yes', APP_TD ),
				'props' => array(
					'required' => 0,
				),
			),
		);

		$fields = array_merge( $fields, $admin_fields );

		return $fields;
	}

}
