<?php
/**
 * Listing Claim Process submodule
 *
 * @package Listing\Views\Processes
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Claim Listing processing class.
 *
 * If listing is marked as "Claimable", any user can initiate "Claim" process to
 * make this listing belong to him.
 *
 * With that, listing doesn't changes during the process, but only when process
 * had been completed and claim was approved.
 */
class APP_View_Process_Claim extends APP_View_Process_New {

	/**
	 * Construct Listing processing page view.
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
	public function __construct( APP_Listing $listing, $process = 'claim', $args = array() ) {

		$defaults = array(
			'process_title' => sprintf( __( 'Claim Listing [%s]', APP_TD ), $listing->get_type() ),
		);

		$args = wp_parse_args( $args, $defaults );

		parent::__construct( $listing, $process, $args );

		$this->setup_steps();

		add_action( 'appthemes_create_order', array( $this, '_add_order_item' ) );
	}

	/**
	 * Retrieves the name of connection between processed item and checkout type.
	 *
	 * @return string Connection type.
	 */
	public function get_connection_type() {
		$claimee = get_current_user_id();
		return "_in_process_{$this->get_checkout_type()}_by_{$claimee}";
	}

	/**
	 * Set up process steps.
	 */
	protected function setup_steps() {

		// Register custom steps.
		//
		// 8. Activate items, publish listing, set claimee as author.
		new APP_Step_Activate_Claim( $this->listing );
		// 6. Set 'claimee' field, remove 'claimable' field, maybe set status
		// 'pending-claimed'.
		new APP_Step_Moderate_Claim( $this->listing );
		// 7. Activate order.
		new APP_Step_Activate_Order( $this->listing, 'activate-order', array(
			'priority'    => 999,
			'register_to' => array(
				"{$this->listing->get_type()}-claim" => array( 'before' => 'activate' ),
			),
		) );

		// Re-register standard steps.
		$steps = array(
			'step_select_plan',        // 1. select plan
			'step_edit_info_optional', // 2. maybe change categories
			'step_create_order',       // 3. create order
			'step_gateway_select',     // 4. select payment gateway
			'step_gateway_process',    // 5. process gateway
		);

		foreach ( $steps as $step ) {
			$this->register_step( $this->listing->$step );
		}
	}

	/**
	 * Register given step object in a process.
	 *
	 * @param APP_Listing_Step $step The listing step object.
	 */
	protected function register_step( $step ) {
		if ( ! $step instanceof APP_Listing_Step ) {
			return;
		}

		add_action( 'appthemes_register_checkout_steps_' . $this->get_checkout_type(), array( $step, 'register' ), $step->args['priority'] );
	}

	/**
	 * Checks accessibility and redirects user if access is not allowed
	 */
	protected function check_access() {

		parent::check_access();

		if ( $this->errors->get_error_codes() ) {
			return;
		}

		$item_id = null;

		if ( isset( $_GET['listing_id'] ) ) { // Input var okay.
			$item_id = absint( $_GET['listing_id'] ); // Input var okay.
		}

		if ( ! $item_id ) {
			$this->errors->add( 'no_item_id', __( 'There is no item ID to claim.', APP_TD ) );
			return;
		}

		if ( $this->listing->get_type() !== get_post_type( $item_id ) ) {
			$this->errors->add( 'incorrect_listing_type', __( 'You can not claim item of such type in this process.', APP_TD ) );
			return;
		}

		$claimable = get_post_meta( $item_id, 'listing_claimable', true );
		$claimee   = (int) get_post_meta( $item_id, 'claimee', true );
		$own_claim = $claimee && get_current_user_id() === $claimee;

		if ( ! $claimable && ! $own_claim ) {
			$this->errors->add( 'not_claimable', __( 'This  listing is not claimable.', APP_TD ) );
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

		if ( 'publish' !== $item->post_status ) {
			return;
		}

		if ( ! get_post_meta( $item_id, 'listing_claimable', true ) ) {
			return;
		}

		return $this->basic_url( $item->ID );
	}

	/**
	 * Setup Checkout Object specifically for current process.
	 */
	protected function setup_checkout() {
		$hash = '';

		if ( isset( $_GET['listing_id'] ) ) { // input var okay.
			$item_id = absint( $_GET['listing_id'] ); // Input var okay.
			$hash = get_post_meta( $item_id, $this->get_connection_type(), true );
		}

		// Doesn't allow setup new Claim checkout if there is incompleted one.
		// What should prevent from repeating already rejected claims.
		appthemes_setup_checkout( $this->get_checkout_type(), $this->basic_url(), $hash );
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

		$item_id = $checkout->get_data( 'listing_id' );

		if ( $item_id ) {
			return;
		} elseif ( isset( $_GET['listing_id'] ) ) { // Input var okay.
			$item_id = absint( $_GET['listing_id'] ); // Input var okay.

			$checkout->add_data( 'listing_id', $item_id );
			$checkout->add_data( 'initiator', get_current_user_id() );
			update_post_meta( $item_id, $this->get_connection_type(), $checkout->get_hash() );
		}
	}

	/**
	 * Trigger final process actions until checkout will be removed.
	 *
	 * @param APP_Dynamic_Checkout $checkout The checkout object.
	 */
	public function complete_process( $checkout ) {}

	/**
	 * Removes data from completed checkout object.
	 *
	 * @param APP_Dynamic_Checkout $checkout The checkout object.
	 */
	public function remove_checkout_data( $checkout ) {

		$item_id    = $checkout->get_data( 'listing_id' );
		$initiator  = $checkout->get_data( 'initiator' );
		$connection = "_in_process_{$this->get_checkout_type()}_by_{$initiator}";

		if ( $item_id ) {
			delete_post_meta( $item_id, $connection );
		}
	}

	/**
	 * Checks post status transition and calls complete process method.
	 *
	 * Triggers when admin is approving claimed listing.
	 *
	 * @param string  $new_status New status.
	 * @param string  $old_status Old status.
	 * @param WP_Post $post       The listing item.
	 */
	public function maybe_complete_process( $new_status, $old_status, $post ) {

		if ( $post->post_type !== $this->listing->get_type() ) {
			return;
		}

		if ( 'publish' !== $new_status || 'pending-claimed' !== $old_status ) {
			return;
		}

		$claimee    = get_post_meta( $post->ID, 'claimee', true );
		$type       = $this->get_checkout_type();
		$connection = "_in_process_{$type}_by_{$claimee}";
		$in_process = get_post_meta( $post->ID, $connection, true );

		if ( ! $in_process || ! $claimee ) {
			return;
		}

		// Disable Listing Plan metabox so it doesn't affect the activated plan.
		add_filter( "appthemes_{$this->listing->get_type()}-plan_before_save", '__return_empty_array', 999 );

		// Move to Moderation step and decide what to do next: move next or
		// stuck if claim was rejected.
		// Since checkout doesn't expires on moderation step we can return here
		// anytime and complete checkout later.
		appthemes_process_background_checkout( $type, $in_process, 'moderate' );
	}


	/**
	 * Adds Claim Listing Item to order.
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
