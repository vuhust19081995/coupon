<?php
/**
 * Listing Edit submodule
 *
 * @package Listing\Views\Processes
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Edit Listing processing class
 *
 * Requires theme supports:
 *	- app-framework
 *  - app-checkout
 */
class APP_View_Process_Edit extends APP_View_Process_New {

	/**
	 * Construct Edit Listing processing page view
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
	public function __construct( APP_Listing $listing, $process = 'edit', $args = array() ) {

		$defaults = array(
			'process_title' => sprintf( __( 'Edit Listing [%s]', APP_TD ), $listing->get_type() ),
		);

		$args = wp_parse_args( $args, $defaults );

		parent::__construct( $listing, $process, $args );

		add_filter( 'get_edit_post_link', array( $this, 'get_edit_post_link' ), 10, 2 );
	}

	/**
	 * Retrieves module's options default values to be registered in Listing
	 * options object.
	 *
	 * @return array The Form defaults.
	 */
	public function get_defaults() {
		$defaults = array();

		if ( 'edit' === $this->process ) {
			$defaults['allow_edit'] = true;
		}

		return $defaults;
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
	 * Adds listing ID to the current checkout object
	 *
	 * @param APP_Dynamic_Checkout $checkout The current checkout object.
	 */
	public function add_checkout_data( $checkout ) {

		if ( $this->errors->get_error_codes() ) {
			return;
		}

		$listing_id = $checkout->get_data( 'listing_id' );

		if ( $listing_id ) {
			return;
		} elseif ( isset( $_GET['listing_id'] ) ) { // Input var okay.
			$listing_id = absint( $_GET['listing_id'] );

			$checkout->add_data( 'listing_id', $listing_id );
			update_post_meta( $listing_id, $this->get_connection_type(), $checkout->get_hash() );
		}
	}

	/**
	 * Trigger final process actions until checkout will be removed.
	 *
	 * @param APP_Dynamic_Checkout $checkout The checkout object.
	 */
	public function complete_process( $checkout ) {}

	/**
	 * Checks user access to the.
	 */
	protected function check_access() {

		parent::check_access();

		$cap        = $this->user_capability( 'edit_post' );
		$allow_edit = $this->listing->options->allow_edit;
		$listing_id = null;

		if ( isset( $_GET['listing_id'] ) ) { // Input var okay.
			$listing_id = absint( $_GET['listing_id'] ); // Input var okay.
		}

		$listing_obj = get_post( $listing_id );

		if ( 'renew' !== $this->process ) {
			delete_post_meta( $listing_id, 'app_is_renewal' );
		}

		if ( $listing_obj && current_user_can( $cap, $listing_id ) && $this->listing->get_type() === $listing_obj->post_type ) {
			return;
		}

		if ( ! $listing_obj ) {
			$this->errors->add( 'no_item_id', __( 'There is no item ID to edit.', APP_TD ) );
		} elseif ( ! $allow_edit && 'publish' === $listing_obj->post_status && get_current_user_id() === (int) $listing_obj->post_author ) {
			$this->errors->add( 'cant_edit_published', __( 'You can\'t edit published items.', APP_TD ) );
		} else {
			$this->errors->add( 'cant_edit', __( 'You do not have sufficient permissions to edit this item.', APP_TD ) );
		}
	}

	/**
	 * Retrieves the process page URL
	 *
	 * @param int $listing_id Given listing ID.
	 *
	 * @return string Page URL
	 */
	protected function basic_url( $listing_id = null ) {
		$permalink = get_permalink( $this->get_page_id() );

		if ( ! $listing_id && isset( $_GET['listing_id'] ) ) { // Input var okay.
			$listing_id = (int) $_GET['listing_id']; // Input var okay.
		}

		if ( $listing_id ) {
			$permalink = add_query_arg( 'listing_id', $listing_id, $permalink );
		}

		return $permalink;
	}

	/**
	 * Retrieves public process URL depending on context and user permissions.
	 *
	 * @param int $item_id An Item ID.
	 */
	public function get_process_url( $item_id = null ) {

		$item = get_post( $item_id );

		if ( ! $item || $this->listing->get_type() !== $item->post_type ) {
			return;
		}

		$item_id = $item->ID;

		return $this->basic_url( $item_id );
	}

	/**
	 * Retrieves Edit Post link depending on the user capability and listing type
	 *
	 * Doesn't change link for admins.
	 *
	 * @param string $url        Original URL.
	 * @param int    $listing_id Listing ID.
	 *
	 * @return string Modified URL
	 */
	public function get_edit_post_link( $url, $listing_id ) {

		// Nothing to change if user have appropriate permissions.
		if ( current_user_can( 'edit_others_posts' ) ) {
			return $url;
		}

		$listing_obj  = get_post( $listing_id );
		$listing_type = $this->listing->get_type();

		// Nothing to change for another listing types.
		if ( $listing_type !== $listing_obj->post_type ) {
			return $url;
		}

		return $this->basic_url( $listing_id );
	}

}
