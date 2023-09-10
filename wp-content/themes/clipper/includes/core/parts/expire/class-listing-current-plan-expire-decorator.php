<?php
/**
 * Listing Current Plan Expire decorator
 *
 * @package Listing\Expire
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Plan Expire decorator class.
 */
class APP_Listing_Current_Plan_Expire_Decorator extends APP_Listing_Current_Plan_Decorator {

	/**
	 * Constructs decorator object.
	 *
	 * @param APP_Listing_Current_Plan_I $plan Decorated object.
	 * @param mixed                      $ref  The reference to Listing module
	 *                                         object.
	 */
	public function __construct( APP_Listing_Current_Plan_I $plan, $ref = null ) {
		$this->plan = $plan;
		$this->ref  = $ref;
	}

	/**
	 * Retrieves the listing item id to which current plan has been applied.
	 *
	 * @return int The Listing Item ID.
	 */
	public function get_item_id() {
		return $this->plan->get_item_id();
	}

	/**
	 * Deactivates plan.
	 */
	public function deactivate() {
		wp_update_post( array(
			'ID'          => $this->get_item_id(),
			'post_status' => APP_POST_STATUS_EXPIRED,
		) );

		$item = get_post( $this->get_item_id() );
		$emails = new APP_Listing_Expire_Emails( $this->get_ref_object() );
		$emails->notify_user_expired_listing( $item );

		$this->plan->deactivate();
	}
}
