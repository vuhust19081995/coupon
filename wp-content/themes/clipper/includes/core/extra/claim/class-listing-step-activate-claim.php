<?php
/**
 * Activate Claimed Listing Step
 *
 * @package Listing\Views\Checkouts
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Activate Claimed Listing Step class
 */
class APP_Step_Activate_Claim extends APP_Step_Activate {

	/**
	 * Construct Activate Listing step
	 *
	 * @param APP_Listing $listing Listing object to assign step with.
	 * @param string      $step_id Step ID.
	 * @param array       $args    Optional. An array of arguments.
	 */
	public function __construct( APP_Listing $listing, $step_id = 'activate', $args = array() ) {

		$this->listing = $listing;

		if ( empty( $args ) ) {
			$args = array(
				'priority'    => 999,
				'register_to' => array(
					"{$this->listing->get_type()}-claim",
				),
			);
		}

		parent::__construct( $listing, $step_id, $args );
	}

	/**
	 * Activates items.
	 *
	 * @param APP_Order $order Order object.
	 */
	protected function activate_items( APP_Order $order ) {

		$claimee = $this->checkout->get_data( 'initiator' );
		$item_id = $this->checkout->get_data( 'listing_id' );

		wp_update_post( array(
			'ID'          => $item_id,
			'post_author' => $claimee,
		) );

		parent::activate_items( $order );
	}
}
