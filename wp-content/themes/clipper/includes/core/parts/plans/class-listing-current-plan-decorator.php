<?php
/**
 * Current Listing Plan Decorator.
 *
 * @package Listing
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * A Current Plan object decorator class.
 *
 * This decorator makes plan object behave as already purchased and activated.
 * It uses reference to listing item for getting some plan options instead of
 * using own meta data.
 *
 * Current plan cannot be applied to order or activated as regular plan, since
 * supposed, that plan already activated. But it allows to upgrade plan by
 * purchasing additional items (i.e. Addons)
 *
 * Also Current Plan object could be used to get plan properties directly
 * applied to listing item.
 * (For example, plan duration will be retrieved from listing item meta instead
 * of using specified plan option).
 */
class APP_Listing_Current_Plan_Decorator extends APP_Listing_Plan_Decorator implements APP_Listing_Current_Plan_I {

	/**
	 * A current listing item ID.
	 *
	 * @var int
	 */
	protected $item_id;

	/**
	 * Constructs decorator object.
	 *
	 * @param int                $item_id Current listing item ID.
	 * @param APP_Listing_Plan_I $plan    Decorated object.
	 * @param mixed              $ref     The reference to Listing object.
	 */
	public function __construct( $item_id, APP_Listing_Plan_I $plan, $ref = null ) {
		$this->item_id = $item_id;
		parent::__construct( $plan, $ref );
	}

	/**
	 * Retrieves the listing item id to which current plan has been applied.
	 *
	 * @return int The Listing Item ID.
	 */
	public function get_item_id() {
		return $this->item_id;
	}

	/**
	 * Retrieves plan duration in days.
	 *
	 * @return int
	 */
	public function get_duration() {
		$duration = get_post_meta( $this->get_item_id(), $this->get_duration_key(), true );
		return absint( $duration );
	}

	/**
	 * Retrieves plan duration in other time units, like months or years.
	 *
	 * @return int
	 */
	public function get_period() {
		return $this->get_duration();
	}

	/**
	 * Generates period text.
	 */
	public function get_period_text() {

		if ( $this->get_expire_date() ) {
			$expiration_date = appthemes_display_date( $this->get_expire_date(), 'date' );
			$output = sprintf( __( ' / until %s', APP_TD ), $expiration_date );
		} else {
			$output = $this->plan->get_period_text();
		}

		return $output;
	}

	/**
	 * Retrieves period type (D, W, M, Y).
	 *
	 * @var string
	 */
	public function get_period_type() {
		return 'D';
	}

	/**
	 * Retrieves the plan expire date for current listing item.
	 *
	 * @return int Expire date timestamp.
	 */
	public function get_expire_date() {
		$duration = $this->get_duration();
		$end_date = 0;

		if ( 'publish' === get_post_status( $this->get_item_id() ) && $duration ) {
			$end_date = get_post_time( 'U', false, $this->get_item_id() ) + ( $duration * DAY_IN_SECONDS );
		}

		return $end_date;
	}

	/**
	 * Deactivates plan.
	 */
	public function deactivate() {}

	/**
	 * Whether current plan could be upgraded.
	 *
	 * @return bool True if current plan could be upgraded, false otherwise.
	 */
	public function is_upgradable() {
		return false;
	}

}
