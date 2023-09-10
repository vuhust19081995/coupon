<?php
/**
 * Listing Taxonomy Surcharge module.
 *
 * This module allows you to charge an additional fee depending on the listing
 * category.
 *
 * @package Listing\Modules\TaxSurcharge
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Business Listing Taxonomy Surcharge class.
 */
class APP_Listing_Taxonomy_Surcharge {

	/**
	 * Current Listing module object.
	 *
	 * @var APP_Listing
	 */
	protected $listing;

	/**
	 * Current taxonomy name.
	 *
	 * @var string
	 */
	protected $taxonomy;

	/**
	 * Taxonomy Surcharge meta key.
	 *
	 * @var string
	 */
	protected $meta_key;

	/**
	 * Settings object.
	 *
	 * @var type APP_Listing_Taxonomy_Surcharge_Settings
	 */
	public $settings;

	/**
	 * Constructs Listing Taxonomy Surcharge object.
	 *
	 * @param APP_Listing $listing  Listing object to assign process with.
	 * @param string      $taxonomy Limited taxonomy name.
	 */
	public function __construct( APP_Listing $listing, $taxonomy ) {

		$this->listing  = $listing;
		$ltype          = $listing->get_type();
		$this->taxonomy = $taxonomy;
		$this->meta_key = "{$taxonomy}_surcharges";
		$this->listing->options->set_defaults( $this->get_defaults() );

		if ( is_admin() ) {
			$this->settings = new APP_Listing_Taxonomy_Surcharge_Settings( $this, $listing );
		}

		if ( did_action( 'init' ) ) {
			$this->_register();
		} else {
			add_action( 'init', array( $this, '_register' ), 1000 );
		}

		add_filter( 'appthemes_form_field', array( $this, 'form_field' ), 99, 2 );
		add_filter( 'appthemes_process_listing_form_fields', array( $this, '_process_form_field' ), 99, 2 );
		add_filter( 'appthemes_optional_form_fields_include_filters', array( $this, '_include_filters' ) );
		add_filter( 'appthemes_listing_form_term_label', array( $this, 'form_term_label' ), 10, 4 );
		add_filter( "appthemes_decorate_listing_plan_{$ltype}", array( $this, '_decorate_plan' ) );
	}

	/**
	 * Decorates given plan.
	 *
	 * @param APP_Listing_Plan_I $plan Given listing plan object.
	 *
	 * @return \APP_Listing_Plan_Taxonomy_Surcharge_Decorator Decorated plan object.
	 */
	public function _decorate_plan( APP_Listing_Plan_I $plan ) {
		$ltype     = $this->listing->get_type();
		$tax       = $this->get_taxonomy();
		$decorator = new APP_Listing_Plan_Taxonomy_Surcharge_Decorator( $plan, $ltype, $tax );

		return $decorator;
	}

	/**
	 * Registers Taxonomy Surcharges in Payments module.
	 */
	public function _register() {

		$args = array(
			'orderby'    => 'name',
			'hide_empty' => false,
		);

		$tax   = $this->get_taxonomy();
		$terms = get_terms( $this->get_taxonomy(), $args );

		foreach ( $terms as $term ) {
			$title = sprintf( __( 'Category: "%s"', APP_TD ), $term->name );
			$item  = "{$tax}_{$term->term_id}";

			APP_Item_Registry::register( $item, $title, $term );
		}
	}

	/**
	 * Adds extra parameters to listing form field.
	 *
	 * @param array $field   Form field.
	 * @param int   $item_id Listing item ID.
	 *
	 * @return array Modified Form field.
	 */
	public function form_field( $field, $item_id ) {

		if ( ! isset( $field['name'] ) || array( 'tax_input', $this->get_taxonomy() ) !== $field['name'] ) {
			return $field;
		}

		$item = get_post( $item_id );

		if ( ! $item || $this->listing->get_type() !== $item->post_type ) {
			return $field;
		}

		if ( $this->is_locked() ) {
			$field['extra']['disabled'] = true;
			$field['title'] = __( 'Categories locked', APP_TD );
		}

		return $field;
	}

	/**
	 * Removes locked tax field from the form process list.
	 *
	 * @param array $fields  Form fields.
	 * @param int   $item_id Listing item ID.
	 *
	 * @return array Modified Form fields list.
	 */
	public function _process_form_field( $fields, $item_id ) {
		$item = get_post( $item_id );

		if ( ! $item || $this->listing->get_type() !== $item->post_type ) {
			return $fields;
		}

		foreach ( $fields as $key => $field ) {
			if ( array( 'tax_input', $this->get_taxonomy() ) === $field['name'] ) {
				if ( $this->is_locked() ) {
					unset( $fields[ $key ] );
				}
				break;
			}
		}

		return $fields;
	}

	/**
	 * Changes given term for field label.
	 *
	 * @param string  $label    Original label.
	 * @param WP_Term $term     Given term object.
	 * @param string  $taxonomy Givent taxonomy name.
	 * @param int     $item_id  Listing item id.
	 *
	 * @return string Changed label.
	 */
	public function form_term_label( $label, $term, $taxonomy, $item_id ) {

		if ( $this->get_taxonomy() !== $taxonomy || $this->is_locked() ) {
			return $label;
		}

		$item = get_post( $item_id );

		if ( ! $item || $this->listing->get_type() !== $item->post_type ) {
			return $label;
		}

		$surcharge = 0;

		$surcharge = $this->get_surcharge( $term );

		if ( $surcharge ) {
			$label .= sprintf( __( ' (add %s)', APP_TD ), appthemes_get_price( $surcharge ) );
		}

		return $label;
	}

	/**
	 * Includes current taxonomy field to the Optional Edit Info form.
	 *
	 * This allows user change locked taxonomy in the Renew and similar
	 * processes and while taxonomy will locked in the Edit Listing form.
	 *
	 * @param array $include A list of field attributes used to include certain
	 *                       fields in the final field list
	 *                       (e.g: array( 'name' => 'my-field' ) ).
	 *
	 * @return array Filtered includes array.
	 */
	public function _include_filters( $include ) {

		$checkout = appthemes_get_checkout();

		if ( ! $checkout ) {
			return $include;
		}

		$item_id = $checkout->get_data( 'listing_id' );
		$item = get_post( $item_id );

		if ( ! $item || $this->listing->get_type() !== $item->post_type ) {
			return $include;
		}

		$filters = array(
			'name' => array( "tax_input[{$this->get_taxonomy()}]" ),
		);

		$include = array_merge_recursive( $include, $filters );

		return $include;
	}

	/**
	 * Retrieves surcharge value for given term.
	 *
	 * @param WP_Term $term Taxonomy Term.
	 *
	 * @return float Surcharge value.
	 */
	public function get_surcharge( WP_Term $term ) {
		$slug      = $term->slug;
		$options   = $this->listing->options->get( $this->get_meta_key() );
		$surcharge = 0;

		if ( isset( $options[ $slug ] ) && isset( $options[ $slug ]['surcharge'] ) ) {
			$surcharge = $options[ $slug ]['surcharge'];
		}

		return $surcharge;
	}

	/**
	 * Retrieves module's options default values to be registered in Listing
	 * options object.
	 */
	public function get_defaults() {
		return array( $this->get_meta_key() => array() );
	}

	/**
	 * Retrieve taxomony name associated with current module.
	 *
	 * @return string Taxonomy name
	 */
	public function get_taxonomy() {
		return $this->taxonomy;
	}

	/**
	 * Retrieves taxonomy surcharges meta key.
	 *
	 * @return type
	 */
	public function get_meta_key() {
		return $this->meta_key;
	}

	/**
	 * Determines whether taxonomy field is locked on the edit listing form.
	 *
	 * @return bool True if taxonomy field is locked, False otherwise.
	 */
	public function is_locked() {

		$checkout = appthemes_get_checkout();

		if ( ! $checkout ) {
			$locked = false;
		} else {
			$locked = ! $checkout->get_data( "{$this->taxonomy}_surcharge" );
		}

		return $locked;
	}

}
