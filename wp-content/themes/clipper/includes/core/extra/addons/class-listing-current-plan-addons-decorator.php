<?php
/**
 * Current Listing Plan Addons decorator
 *
 * @package Listing\Modules\Recurring
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Current Listing Plan Addons decorator class.
 */
class APP_Listing_Current_Plan_Addons_Decorator extends APP_Listing_Plan_Addons_Decorator implements APP_Listing_Current_Plan_I {

	/**
	 * Decorated plan object.
	 *
	 * @var APP_Listing_Current_Plan_I
	 */
	protected $plan;

	/**
	 * Retrieves the listing item id to which current plan has been applied.
	 *
	 * @return int The Listing Item ID.
	 */
	public function get_item_id() {
		return $this->plan->get_item_id();
	}

	/**
	 * Retrives the plan expire date.
	 *
	 * @return int Expire date timestamp.
	 */
	public function get_expire_date() {
		return $this->plan->get_expire_date();
	}

	/**
	 * Deactivates plan.
	 */
	public function deactivate() {
		$this->plan->deactivate();
	}

	/**
	 * Whether current plan could be upgraded.
	 *
	 * @return bool True if current plan could be upgraded, false otherwise.
	 */
	public function is_upgradable() {
		$available = $this->_get_available_addons();

		foreach ( $available as $plan ) {
			if ( ! $plan instanceof APP_Listing_Purchased_Addon ) {
				return true;
			}
		}

		return $this->plan->is_upgradable();
	}

	/**
	 * Retrieves the list of available addons in context of a current plan.
	 *
	 * @return array The list of available addons objects.
	 */
	protected function _get_available_addons() {
		/* @var $addons APP_Listing_Addons */
		/* @var $addon APP_Listing_Addon_I */

		$duration = 0;
		$expire   = $this->get_expire_date();

		if ( $expire ) {
			// Remainder duration, actually.
			$duration = ceil( ( $expire - time() ) / DAY_IN_SECONDS );
		}

		$listing   = $this->get_ref_object();
		$addons    = $listing->addons;
		$available = array();

		foreach ( $addons->get_addons( $this ) as $addon ) {
			$purchased      = $addon instanceof APP_Listing_Purchased_Addon;
			$addon_duration = $addon->get_duration();

			if ( ! $purchased && $addon_duration > $duration && $duration ) {
				continue;
			}

			$available[] = $addon;
		}

		return $available;
	}
}
