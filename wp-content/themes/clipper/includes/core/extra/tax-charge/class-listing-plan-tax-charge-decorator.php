<?php
/**
 * Listing Plan Taxonomy Surcharge decorator
 *
 * @package Listing\Modules\TaxSurcharge
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Plan Taxonomy Surcharge decorator class.
 */
class APP_Listing_Plan_Taxonomy_Surcharge_Decorator extends APP_Listing_Plan_Decorator {

	/**
	 * Taxonomy name.
	 *
	 * @var string
	 */
	protected $taxonomy = '';

	/**
	 * Constructs decorator object.
	 *
	 * @param APP_Listing_Plan_I $plan Decorated object.
	 * @param mixed              $ref  The reference to Listing module object.
	 * @param string             $tax  The taxonomy name.
	 */
	public function __construct( APP_Listing_Plan_I $plan, $ref = null, $tax = '' ) {
		$this->taxonomy = $tax;
		parent::__construct( $plan, $ref );
	}

	/**
	 * Adds Plan specific data to a given checkout object.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object for setup.
	 * @param array                $data     Stripslashed posted data.
	 */
	public function setup( APP_Dynamic_Checkout $checkout, $data = array() ) {
		// Call method for a decorated plan first!
		$this->plan->setup( $checkout, $data );
		$checkout->add_data( "{$this->taxonomy}_surcharge", true );
	}

	/**
	 * Applies plan attributes to an order item.
	 *
	 * @param int       $item_id Listing item ID.
	 * @param APP_Order $order   An Order object.
	 */
	public function apply( $item_id, APP_Order $order ) {
		// Call method for a decorated plan first!
		$this->plan->apply( $item_id, $order );

		/* @var $tax_surcharge APP_Listing_Taxonomy_Surcharge */
		$listing       = $this->get_ref_object();
		$taxonomy      = $this->taxonomy;
		$module_name   = "{$taxonomy}_surcharge";
		$tax_surcharge = $listing->{$module_name};
		$terms         = array();
		$data          = array();
		$checkout      = appthemes_get_checkout();

		if ( $checkout ) {
			$data = $checkout->get_data( 'optional_data' );
		}

		if ( isset( $data['tax_input'][ $taxonomy ] ) ) {
			$terms = $data['tax_input'][ $taxonomy ];
		} else {
			$_terms = (array) get_the_terms( $item_id, $taxonomy );
			$terms = array_filter( $_terms );
		}

		foreach ( $terms as $term ) {

			$term = get_term( $term );

			if ( ! $term instanceof WP_Term ) {
				continue;
			}

			$surcharge = $tax_surcharge->get_surcharge( $term );
			$item      = "{$taxonomy}_{$term->term_id}";

			if ( ! empty( $surcharge ) ) {
				$order->add_item( $item , $surcharge, $item_id );
			}
		}
	}

	/**
	 * Activates plan.
	 *
	 * @param int       $item_id Listing ID.
	 * @param APP_Order $order   An Order object.
	 */
	public function activate( $item_id, APP_Order $order ) {
		// Call method for a decorated plan first!
		$this->plan->activate( $item_id, $order );

		$taxonomy = $this->taxonomy;
		$checkout = appthemes_get_checkout();

		if ( ! $checkout || ! $data = $checkout->get_data( 'optional_data' ) ) {
			return;
		}

		if ( ! isset( $data['tax_input'][ $taxonomy ] ) ) {
			return;
		}

		wp_update_post( array(
			'ID' => $item_id,
			'tax_input' => array(
				$taxonomy => $data['tax_input'][ $taxonomy ],
			),
		) );
	}

}
