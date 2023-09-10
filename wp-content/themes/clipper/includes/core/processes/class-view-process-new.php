<?php
/**
 * Listing New submodule
 *
 * @package Listing\Views\Processes
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * New Listing processing class
 *
 * Requires theme supports:
 *	- app-framework
 *  - app-checkout
 */
class APP_View_Process_New extends APP_View_Process {

	/**
	 * Create New listing process
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
	public function __construct( APP_Listing $listing, $process = 'new', $args = array() ) {

		$defaults = array(
			'process_title' => sprintf( __( 'New Listing [%s]', APP_TD ), $listing->get_type() ),
			'capability'    => 'edit_posts',
		);

		$args = wp_parse_args( $args, $defaults );

		add_action( 'transition_post_status', array( $this, 'maybe_complete_process' ), 10, 3 );
		add_action( 'before_delete_post', array( $this, 'cleanup_process' ), 10, 3 );

		parent::__construct( $listing, $process, $args );

		$this->listing->options->set_defaults( $this->get_defaults() );
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

		if ( 'new' === $this->process ) {
			$defaults['moderate']                    = false;
			$defaults[ "notify_new_$type" ]          = false;
			$defaults[ "notify_pending_$type" ]      = false;
			$defaults[ "notify_user_pending_$type" ] = false;
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

		if ( 'pending' === $old_status ) {
			$emails = new APP_Listing_Process_Emails( $this->listing );
			$emails->notify_user_approved_listing( $post );
		}

		$type = $this->get_checkout_type();

		// Disable Listing Plan metabox so it doesn't affect the activated plan.
		add_filter( "appthemes_{$this->listing->get_type()}-plan_before_save", '__return_empty_array', 999 );

		appthemes_process_background_checkout( $type, $in_process, 'activate' );
	}

	/**
	 * Removes checkout data stored in DB before listing is deleted.
	 *
	 * @param int $item_id Listing Item ID.
	 */
	public function cleanup_process( $item_id ) {

		$item = get_post( $item_id );

		if ( ! $item || $item->post_type !== $this->listing->get_type() ) {
			return;
		}

		$hash = get_post_meta( $item_id, $this->get_connection_type(), true );

		if ( ! $hash ) {
			return;
		}

		$checkout = new APP_Dynamic_Checkout( $this->get_checkout_type(), $hash );

		// Set expiration 1 sec and delegate data removing to transient cleaner.
		$checkout->set_expiration( 1 );
	}

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

		if ( ! $listing_id ) {
			require_once ABSPATH . '/wp-admin/includes/post.php';
			$listing = get_default_post_to_edit( $this->listing->get_type(), true );
			$checkout->add_data( 'listing_id', $listing->ID );
			$listing_id = $listing->ID;
			update_post_meta( $listing_id, $this->get_connection_type(), $checkout->get_hash() );
		}
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

		$emails->notify_admin_publish_listing( $item );
		$emails->notify_user_publish_listing( $item );
	}

	/**
	 * Removes data from completed checkout object.
	 *
	 * @param APP_Dynamic_Checkout $checkout The checkout object.
	 */
	public function remove_checkout_data( $checkout ) {

		$listing_id = $checkout->get_data( 'listing_id' );

		if ( $listing_id ) {
			delete_post_meta( $listing_id, $this->get_connection_type() );
		}
	}

	/**
	 * Enqueues scripts
	 */
	public function enqueue_scripts() {
		$checkout   = appthemes_get_checkout();
		$listing_id = 0;

		if ( $checkout ) {
			$listing_id = $checkout->get_data( 'listing_id' );
		}

		appthemes_enqueue_media_manager( array(
			'post_id' => $listing_id,
		) );
	}

}
