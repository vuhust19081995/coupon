<?php
/**
 * New Listing Edit Info Checkout Step
 *
 * @package Listing\Views\Checkouts
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * New Listing Edit Info Checkout Step class
 */
class APP_Step_Edit_Info_New extends APP_Step_Edit_Info {

	/**
	 * Construct New Listing edit info step
	 *
	 * @param APP_Listing $listing Listing object to assign step with.
	 * @param string      $step_id Step ID.
	 * @param array       $args    Optional. An array of arguments.
	 */
	public function __construct( APP_Listing $listing, $step_id = 'edit-info', $args = array() ) {

		$this->listing = $listing;

		if ( empty( $args ) ) {
			$args = array(
				'register_to' => array(
					"{$this->listing->get_type()}-new" => array( 'after' => 'select-plan' ),
				),
			);
		}

		parent::__construct( $listing, $step_id, $args );
	}

	/**
	 * Updates listing status depending on the listing options
	 *
	 * @param APP_Order $order      Current Order object.
	 * @param int       $listing_id Listing ID.
	 */
	protected function update_status( $order, $listing_id ) {

		// Realize the item.
		$item = get_post( $listing_id );

		if ( 'auto-draft' === $item->post_status ) {
			wp_update_post( array(
				'ID' => $listing_id,
				'post_status' => 'draft',
			) );
		}

	}

	/**
	 * Retrieves listing object from checkout or create default one
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @return WP_Post Listing object
	 */
	protected function get_listing_obj( $checkout ) {

		$listing = parent::get_listing_obj( $checkout );

		// Clear the default 'Auto Draft' title.
		if ( __( 'Auto Draft' ) === $listing->post_title ) {
			$listing->post_title = '';

			add_filter( 'wp_insert_post_empty_content', '__return_false', 789, 1 );
			wp_update_post( $listing );
			remove_filter( 'wp_insert_post_empty_content', '__return_false', 789, 1 );
		}

		return $listing;
	}

}
