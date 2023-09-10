<?php
/**
 * Listing Taxonomy Surcharge submodule settings
 *
 * @package Listing\Modules\TaxSurcharge\Settings
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Taxonomy Surcharge submodule class.
 */
class APP_Listing_Taxonomy_Surcharge_Settings extends APP_Listing_Payments_Settings {

	/**
	 * Current module object.
	 *
	 * @var APP_Listing_Taxonomy_Surcharge
	 */
	protected $module;

	/**
	 * Current settings page.
	 *
	 * @var APP_Payments_Settings_Admin
	 */
	protected $page;

	/**
	 * Construct Listing Taxonomy Surcharge module settings.
	 *
	 * @param APP_Listing_Taxonomy_Surcharge $module  Taxonomy Surcharge module object.
	 * @param APP_Listing                    $listing Listing module object.
	 */
	public function __construct( APP_Listing_Taxonomy_Surcharge $module, APP_Listing $listing ) {

		$this->module = $module;

		parent::__construct( $listing );
	}

	/**
	 * Registers Listing Taxonomy Surcharge settings
	 *
	 * @param APP_Payments_Settings_Admin $page Current settings page object.
	 */
	public function register( $page ) {

		$options = $this->listing->options;
		$type    = $this->listing->get_type();
		$name    = get_post_type_object( $type )->labels->name;
		$fields  = array();
		$_tax    = get_taxonomy( $this->module->get_taxonomy() );

		$this->page = $page;

		$page->tabs->add_after( 'general', $type, $name );

		$terms = get_terms( $_tax->name, array( 'hide_empty' => false ) );

		foreach ( $terms as $term ) {
			$fields = array_merge( $fields, self::generate_fields( $term, $this->module->get_meta_key() ) );
		}

		$page->tab_sections[ $type ][ 'category-surcharge-taxonomy_' . $_tax->name ] = array(
			'title'    => sprintf( __( 'Surcharges for %s', APP_TD ),  $_tax->labels->name ),
			'renderer' => array( $this, 'render' ),
			'taxonomy' => $_tax,
			'terms'    => $terms,
			'fields'   => $fields,
			'options'  => $options,
		);
	}

	/**
	 * Section renderer.
	 *
	 * @param array $section Section parameters.
	 */
	public function render( $section ) {

		$columns = array(
			'type'  => $section['taxonomy']->labels->name,
			'price' => sprintf( __( 'Price %s', APP_TD ), html( 'p', array( 'class' => 'description' ), APP_Currencies::get_current_currency( 'code' ) ) ),
		);

		$header = '';
		foreach ( $columns as $label ) {
			$header .= html( 'th', $label );
		}

		$rows = '';
		foreach ( $section['terms'] as $term ) {
			$row = html( 'td', $term->name );

			foreach ( self::generate_fields( $term, $this->module->get_meta_key() ) as $field ) {
				$row .= html( 'td', $this->page->input( $field, $section['options']->get() ) );
			}

			$rows .= html( 'tr', $row );
		}

		$table_id = "taxonomy-{$section['taxonomy']->name}-surcharge-pricing";

		echo html( 'table id="' . $table_id . '" class="widefat"', html( 'thead', html( 'tr', $header ) ), html( 'tbody', $rows ) );
	}

	/**
	 * Generates Term fields set.
	 *
	 * @param WP_Term $term Term object.
	 * @param string  $name Field base name.
	 *
	 * @return array Generated fields array.
	 */
	protected static function generate_fields( WP_Term $term, $name ) {

		return array(
			array(
				'type' => 'text',
				'name' => array( $name, $term->slug, 'surcharge' ),
				'sanitize' => 'appthemes_absfloat',
				'extra' => array( 'size' => 3 ),
			),
		);
	}

}
