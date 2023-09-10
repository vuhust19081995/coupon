<?php
/**
 * Listing Renew submodule
 *
 * @package Listing\Views\Processes
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Renew Listing processing class.
 *
 * Renew process intended to re-publish expired listings.
 *
 * This looks like a mix of New and Edit processes, since it repeats all "New"
 * process steps, but uses existing listing ID as well as "Edit" process.
 *
 * Expired listings shouldn't be editable by users, so Renew submodule filters
 * "edit_post_link()" and replaces standard link with process-related.
 *
 * Requires theme supports:
 *	- app-framework
 *  - app-checkout
 */
class APP_View_Process_Renew extends APP_View_Process_Edit {

	/**
	 * Construct Renew Listing processing page view.
	 *
	 * Depends on Listing option 'allow_renew'.
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
	public function __construct( APP_Listing $listing, $process = 'renew', $args = array() ) {

		$defaults = array(
			'process_title' => sprintf( __( 'Renew Listing [%s]', APP_TD ), $listing->get_type() ),
		);

		$args = wp_parse_args( $args, $defaults );

		parent::__construct( $listing, $process, $args );

		add_filter( 'post_row_actions', array( $this, 'post_row_actions' ), 10, 2 );

		add_action( 'appthemes_create_order', array( $this, '_add_order_item' ) );

		// Remove inherited filter.
		remove_filter( 'get_edit_post_link', array( $this, 'get_edit_post_link' ), 10, 2 );
	}

	/**
	 * Retrieves module's options default values to be registered in Listing
	 * options object.
	 *
	 * @return array The Form defaults.
	 */
	public function get_defaults() {
		$defaults = array();
		$type     = $this->listing->get_type();

		if ( 'renew' === $this->process ) {
			$defaults['allow_renew'] = true;
			$defaults[ "notify_renew_$type" ] = false;
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
	public function maybe_complete_process( $new_status, $old_status, $post ) {

		if ( $post->post_type !== $this->listing->get_type() ) {
			return;
		}

		if ( 'publish' !== $new_status || $new_status === $old_status ) {
			return;
		}

		$in_process = get_post_meta( $post->ID, $this->get_connection_type(), true );

		if ( ! $in_process ) {
			return;
		}

		$type = $this->get_checkout_type();

		// Disable Listing Plan metabox so it doesn't affect the activated plan.
		add_filter( "appthemes_{$this->listing->get_type()}-plan_before_save", '__return_empty_array', 999 );

		appthemes_process_background_checkout( $type, $in_process, 'activate' );
	}

	/**
	 * Checks accessibility and redirects user if access is not allowed
	 */
	protected function check_access() {

		parent::check_access();

		if ( $this->errors->get_error_codes() ) {
			return;
		}

		$item_id = ( isset( $_GET['listing_id'] ) ) ? absint( $_GET['listing_id'] ) : null; // Input var okay.
		$item    = get_post( $item_id );

		if ( APP_POST_STATUS_EXPIRED === $item->post_status ) {
			update_post_meta( $item_id, 'app_is_renewal', 1 );
		}

		if ( ! get_post_meta( $item_id, 'app_is_renewal', true )  ) {
			$this->errors->add( 'not_expired', __( 'You can renew only expired listings.', APP_TD ) );
		}
	}

	/**
	 * Controls the actions set on the item row.
	 *
	 * Changes "Edit" action title and description to "Renew".
	 *
	 * @param array   $actions An array of row action links.
	 * @param WP_Post $item    The item object.
	 *
	 * @return array Changed actions array.
	 */
	public function post_row_actions( $actions, $item ) {
		if ( $item->post_type !== $this->listing->get_type() ) {
			return $actions;
		}

		$url = $this->get_process_url( $item->ID );

		if ( $url ) {
			$actions['renew'] = html( 'a', array( 'href' => esc_url( $url ), 'title' => __( 'Renew this item', APP_TD ) ), __( 'Renew', APP_TD ) );
		}

		return $actions;
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

		if ( APP_POST_STATUS_EXPIRED !== $item->post_status ) {
			return;
		}

		$item_id = $item->ID;

		if ( current_user_can( $cap, $item_id ) ) {
			return $this->basic_url( $item_id );
		}
	}

	/**
	 * Retrieves the process page URL.
	 *
	 * Needs to be public to retrieve process link for email content regardless
	 * current user permissions.
	 *
	 * @param int $listing_id Given listing ID.
	 *
	 * @return string Page URL
	 */
	public function basic_url( $listing_id = null ) {
		return parent::basic_url( $listing_id );
	}

	/**
	 * Trigger final process actions until checkout will be removed.
	 *
	 * @param APP_Dynamic_Checkout $checkout The checkout object.
	 */
	public function complete_process( $checkout ) {
		$item_id = $checkout->get_data( 'listing_id' );
		$item    = get_post( $item_id );

		$emails = new APP_Listing_Process_Emails( $this->listing );

		$emails->notify_admin_renewed_listing( $item );
		$emails->notify_user_renewed_listing( $item );

	}

	/**
	 * Removes data from completed checkout object.
	 *
	 * @param APP_Dynamic_Checkout $checkout The checkout object.
	 */
	public function remove_checkout_data( $checkout ) {

		$listing_id = $checkout->get_data( 'listing_id' );

		if ( $listing_id ) {
			delete_post_meta( $listing_id, 'app_is_renewal' );
		}

		parent::remove_checkout_data( $checkout );
	}

	/**
	 * Adds Renew Listing Item to order.
	 *
	 * @param APP_Order $order Order object.
	 */
	public function _add_order_item( APP_Order $order ) {
		$checkout = appthemes_get_checkout();

		if ( ! $checkout || $checkout->get_checkout_type() !== $this->get_checkout_type() ) {
			return;
		}

		$item_id = $checkout->get_data( 'listing_id' );

		// maybe in future we could add surcharge or discount with this item.
		$order->add_item( $this->get_checkout_type(), 0, $item_id, true );
	}

}
