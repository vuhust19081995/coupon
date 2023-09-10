<?php
/**
 * Listing Upgrade submodule
 *
 * @package Listing\Views\Processes
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Upgrade Listing processing class.
 *
 * Upgrade process allows to listing owner extend his listing plan with
 * additional paid items.
 *
 *
 * Requires theme supports:
 *	- app-framework
 *  - app-checkout
 *  - app-payments
 */
class APP_View_Process_Upgrade extends APP_View_Process_Edit {

	/**
	 * Construct Upgrade Listing processing page view.
	 *
	 * @param APP_Listing $listing Listing object to assign process with.
	 * @param string      $process The Process type.
	 * @param array       $args {
	 *     Optional. An array of arguments.
	 *
	 *     @type bool   $require_login If only logged-in users can execute a
	 *                                 process. Default true.
	 *     @type string $capability    The user capability required to execute a
	 *                                 process.
	 */
	public function __construct( APP_Listing $listing, $process = 'upgrade', $args = array() ) {

		$defaults = array(
			'process_title' => sprintf( __( 'Upgrade Listing [%s]', APP_TD ), $listing->get_type() ),
		);

		$args = wp_parse_args( $args, $defaults );

		parent::__construct( $listing, $process, $args );

		// Remove inherited filter.
		remove_filter( 'get_edit_post_link', array( $this, 'get_edit_post_link' ), 10, 2 );
	}

	/**
	 * Checks post status transition and calls complete process method.
	 *
	 * @param string  $new_status New status.
	 * @param string  $old_status Old status.
	 * @param WP_Post $post       The listing item.
	 */
	public function maybe_complete_process( $new_status, $old_status, $post ) {}

	/**
	 * Checks accessibility and redirects user if access is not allowed
	 */
	protected function check_access() {

		parent::check_access();

		if ( $this->errors->get_error_codes() ) {
			return;
		}

		// listing_id guaranteed by the parent method.
		$item_id = absint( $_GET['listing_id'] ); // Input var okay.

		if ( 'publish' !== get_post_status( $item_id ) ) {
			$this->errors->add( 'cant_upgrade', __( 'You can upgrade only published listings.', APP_TD ) );
		}
	}

	/**
	 * Retrieves public process URL depending on context and user permissions.
	 *
	 * @param int $item_id An Item ID.
	 */
	public function get_process_url( $item_id = null ) {

		$cap  = $this->user_capability( 'edit_post' );
		$item = get_post( $item_id );

		if ( ! $item || $this->listing->get_type() !== $item->post_type ) {
			return;
		}

		if ( 'publish' !== $item->post_status ) {
			return;
		}

		if ( current_user_can( $cap, $item->ID ) ) {
			return $this->basic_url( $item->ID );
		}
	}

	/**
	 * Trigger final process actions until checkout will be removed.
	 *
	 * @param APP_Dynamic_Checkout $checkout The checkout object.
	 */
	public function complete_process( $checkout ) {}

}
