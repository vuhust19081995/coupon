<?php
/**
 * Taxonomy Limit submodule settings
 *
 * @package Listing\Modules\TaxLimit\Settings
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Taxonomy Limit submodule class.
 */
class APP_Listing_Taxonomy_Limit_Settings extends APP_Listing_Core_Settings {

	/**
	 * Current module object.
	 *
	 * @var APP_Listing_Taxonomy_Limit
	 */
	protected $module;

	/**
	 * Construct module.
	 *
	 * @param APP_Listing_Taxonomy_Limit $module  Taxonomy Limit module object.
	 * @param APP_Listing                $listing Listing module object.
	 */
	public function __construct( APP_Listing_Taxonomy_Limit $module, APP_Listing $listing ) {

		$this->module = $module;

		parent::__construct( $listing );
	}

	/**
	 * Registers settings
	 *
	 * @param APP_Listing_Settings $page Current settings page object.
	 */
	public function register( $page ) {

		$options = $this->listing->options;
		$type    = $this->listing->get_type();

		if ( empty( $page->tab_sections[ $type ]['permissions'] ) ) {
			$page->tab_sections[ $type ]['permissions'] = array(
				'title'  => __( 'Permissions', APP_TD ),
				'fields' => array(),
				'options' => $options,
			);
		}

		$page->tab_sections[ $type ]['permissions']['fields'][] = array(
			'title'    => __( 'Categories', APP_TD ),
			'type'     => 'number',
			'name'     => $this->module->get_meta_key(),
			'desc'     => __( 'Number of categories a listing can belong to', APP_TD ),
			'extra'	   => array(
				'class' => 'small-text',
			),
			'sanitize' => 'absint',
			'tip'      => __( "Allows users to choose this amount of categories for their listing. Zero means unlimited. This option only works if 'Charge for Listings' is not enabled.", APP_TD ),
		);

	}
}
