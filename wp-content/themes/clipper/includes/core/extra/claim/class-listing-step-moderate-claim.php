<?php
/**
 * Moderate Claimed Listing Step
 *
 * @package Listing\Views\Checkouts
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Moderate Listing Step class
 */
class APP_Step_Moderate_Claim extends APP_Listing_Step {

	/**
	 * Construct Moderate Listing step
	 *
	 * @param APP_Listing $listing Listing object to assign step with.
	 * @param string      $step_id Step ID.
	 * @param array       $args    Optional. An array of arguments.
	 */
	public function __construct( APP_Listing $listing, $step_id = 'moderate', $args = array() ) {

		$this->listing = $listing;

		if ( empty( $args ) ) {
			$args = array(
				'priority'    => 999,
				'register_to' => array(
					"{$this->listing->get_type()}-claim" => array( 'before' => 'activate' ),
				),
			);
		}

		parent::__construct( $listing, $step_id, $args );
	}

	/**
	 * Displays step
	 *
	 * @param APP_Order            $order    Order object.
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 */
	public function display( $order, $checkout ) {

		$item_id   = $checkout->get_data( 'listing_id' );
		$item      = get_post( $item_id );
		$permalink = html_link( get_permalink( $item ), $item->post_title );
		$rejected  = $checkout->get_data( 'rejected' );

		if ( $rejected ) {
			$message = sprintf( __( 'Your claim on %s had rejected by administrator.', APP_TD ), $permalink );
		} else {
			$message = sprintf( __( 'Listing %s was claimed and awaiting moderation.', APP_TD ), $permalink );
		}

		$message = html( 'p', $message );

		appthemes_add_template_var( array(
			'step_content' => $message,
		) );

		parent::display( $order, $checkout );
	}

	/**
	 * Processes step
	 *
	 * @param APP_Order            $order    Order object.
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 */
	public function process( $order, $checkout ) {

		if ( ! $this->check_previous() ) {
			$this->cancel_step();
			return;
		}

		$item_id = $checkout->get_data( 'listing_id' );
		$item    = get_post( $item_id );

		if ( ! $item ) {
			$this->errors->add( 'no-item', __( 'No listing item for moderation', APP_TD ) );
			return;
		}

		$checkout->add_data( 'title', __( 'Moderation', APP_TD ) );

		// Still Pending or Rejected.
		if ( 'pending-claimed' === $item->post_status || $checkout->get_data( 'rejected' ) ) {
			return;
		}

		$emails    = new APP_Listing_Claim_Emails( $this->listing );
		$initiator = (int) $checkout->get_data( 'initiator' );
		$claimee   = (int) get_post_meta( $item_id, 'claimee', true );

		$claiminitiator = $claimee && $initiator === $claimee;

		// Moderated or Completed.
		if ( 'publish' === $item->post_status && $claiminitiator ) {

			// Admin has made decision, set expiration then.
			$checkout->set_expiration( DAY_IN_SECONDS );

			$nonce  = isset( $_GET['_reject_claim_nonce'] ) ? $_GET['_reject_claim_nonce'] : false; // input var okay.
			$action = "reject-claim-post_{$item_id}";

			// Well, claim was rejected...
			if ( wp_verify_nonce( $nonce, $action ) ) {

				$emails->notify_user_rejected_claim( $item );

				update_post_meta( $item_id, 'listing_claimable', 1 );
				delete_post_meta( $item_id, 'claimee' );
				delete_post_meta( $item_id, 'claimee_request_date' );
				$checkout->add_data( 'rejected', true );
				return;
			}

			$checkout->add_data( 'rejected', false );

			if ( $this->listing->options->moderate_claimed_listings ) {
				$emails->notify_user_approved_claim( $item );
			}

			// Pass the step if it already completed.
			$this->finish_step();
			return;
		}

		// Regular actions.
		if ( $this->listing->options->moderate_claimed_listings ) {
			// Make checkout unexpirable since we don't know when the admin will
			// deign to approve or reject a listing.
			$checkout->set_expiration( 0 );

			// Stop the process and wait for admin's action.
			wp_update_post( array(
				'ID' => $item_id,
				'post_status' => 'pending-claimed',
			) );
		}

		$date = gmdate( 'Y-m-d H:i:s' );
		$checkout->add_data( 'claimee_request_date', $date );

		delete_post_meta( $item_id, 'listing_claimable' );
		update_post_meta( $item_id, 'claimee', $initiator );
		update_post_meta( $item_id, 'claimee_request_date', $date );

		$emails->notify_admin_pending_claimed_listing( $item );
		$emails->notify_user_pending_claimed_listing( $item );
	}
}
