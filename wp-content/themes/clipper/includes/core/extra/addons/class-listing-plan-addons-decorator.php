<?php
/**
 * Listing Plan Addons decorator
 *
 * @package Listing\Modules\Addons
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Plan Addons decorator class.
 */
class APP_Listing_Plan_Addons_Decorator extends APP_Listing_Plan_Decorator {

	/**
	 * Adds Plan specific data to a given checkout object.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object for setup.
	 * @param array                $data     Stripslashed posted data.
	 */
	public function setup( APP_Dynamic_Checkout $checkout, $data = array() ) {
		// Call method for a decorated plan first!
		$this->plan->setup( $checkout, $data );

		$addons    = array();
		$available = $this->_get_available_addons();

		foreach ( $available as $addon ) {
			$type = $addon->get_type();
			$key  = "{$type}_{$this->get_type()}";

			if ( ! empty( $data[ $key ] ) ) {
				$addons[] = $type;
			}
		}

		$checkout->add_data( 'addons', $addons );
	}

	/**
	 * Applies plan attributes to an order item.
	 *
	 * @param int       $item_id Listing ID.
	 * @param APP_Order $order   An Order object.
	 */
	public function apply( $item_id, APP_Order $order ) {
		/* @var $addon APP_Listing_Addon_I */

		// Call method for a decorated plan first!
		$this->plan->apply( $item_id, $order );

		$addons = $this->_get_available_addons();

		foreach ( $addons as $addon ) {
			$addon->apply( $item_id, $order );
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

		$listing     = $this->get_ref_object();
		$item_types  = wp_list_pluck( $order->get_items(), 'type' );
		$addon_types = $listing->addons->get_addons_types();

		$addons = array_intersect( $addon_types, $item_types );

		// No registered addons were added to order, so nothing to do.
		if ( empty( $addons ) ) {
			return;
		}

		foreach ( $addons as $type ) {
			$addon = $listing->addons->get_addon( $type, $this );
			if ( $addon instanceof APP_Listing_Addon_I ) {
				$addon->activate( $item_id );
			}
		}
	}

	/**
	 * Renders optional plan fields.
	 */
	public function render() {
		global $wp_query;

		// Call method for a decorated plan first!
		$this->plan->render();

		$available = $this->_get_available_addons();

		if ( empty( $available ) ) {
			return;
		}

		$wp_query->set( 'addons', $available );

		$located = appthemes_locate_template( 'select-addons.php' );

		if ( ! $located ) {
			$located = dirname( __FILE__ ) . '/select-addons.php';
		}

		load_template( $located, false );
	}

	/**
	 * Determines whether plan has optional items to be selected by user.
	 *
	 * @return bool True if plan has options, False otherwise.
	 */
	public function has_options() {
		$available = $this->_get_available_addons();

		if ( ! empty( $available ) ) {
			return true;
		}

		return $this->plan->has_options();
	}

	/**
	 * Retrieves the list of available addons in context of a current plan.
	 *
	 * @return array The list of available addons objects.
	 */
	protected function _get_available_addons() {
		/* @var $addons APP_Listing_Addons */
		/* @var $addon APP_Listing_Addon_I */

		$duration  = $this->get_duration();
		$listing   = $this->get_ref_object();
		$addons    = $listing->addons;
		$available = array();

		foreach ( $addons->get_addons( $this ) as $addon ) {

			if ( $addon->get_duration() > $duration && $duration ) {
				continue;
			}

			$available[] = $addon;
		}

		return $available;
	}
}
