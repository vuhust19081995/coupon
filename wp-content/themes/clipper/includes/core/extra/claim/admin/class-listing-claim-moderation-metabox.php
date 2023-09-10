<?php
/**
 * Single Listing Claim Moderation metabox
 *
 * @package Listing\Modules\Claim\Metaboxes
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Pricing Addons class
 */
class APP_Listing_Claim_Moderation_Metabox extends APP_Meta_Box {

	/**
	 * Construct metabox
	 *
	 * @param string $ptype Listing module type.
	 * @param string $title Metabox title.
	 */
	public function __construct( $ptype, $title = '' ) {
		if ( ! $title ) {
			$title = __( 'Claim Request', APP_TD );
		}

		parent::__construct( "{$ptype}-claim-moderation", $title, $ptype, 'side', 'high' );

		if ( isset( $_GET['_reject_claim_nonce'] ) ) { // input var okay.
			add_action( 'admin_init', array( $this, 'maybe_reject_claim' ) );
		}
	}

	/**
	 * Additional checks before registering the metabox.
	 *
	 * @return bool
	 */
	function condition() {
		return ( get_post_status( $this->get_post_id() ) === 'pending-claimed' && get_post_meta( $this->get_post_id(), 'claimee', true ) );
	}

	/**
	 * Displays the metabox content.
	 *
	 * @param WP_Post $post Current post object.
	 *
	 * @return void
	 */
	public function display( $post ) {

		echo html( 'p', array(), __( 'Someone wants to claim this listing.', APP_TD ) );

		$claimee = get_userdata( get_post_meta( $post->ID, 'claimee', true ) );

		$request_date = get_post_meta( $post->ID, 'claimee_request_date', true );
		$request_date = appthemes_display_date( $request_date );

		$edit_url   = get_edit_post_link( $post->ID );
		$action     = "reject-claim-post_{$post->ID}";
		$nonce_name = '_reject_claim_nonce';
		$reject_url = wp_nonce_url( $edit_url, $action, $nonce_name );

		if ( $request_date ) {
			echo html( 'p', array(), sprintf( __( '<strong>Requested:</strong> %s', APP_TD ), $request_date ) );
		}

		echo html( 'p', array(), sprintf( __( '<strong>Name:</strong> %s', APP_TD ), html( 'a', array( 'href' => get_author_posts_url( $claimee->ID ), 'target' => '_blank' ), $claimee->display_name ) ) );

		echo html( 'p', array(), sprintf( __( '<strong>Email:</strong> %s', APP_TD ), html( 'a', array( 'href' => 'mailto:' . $claimee->user_email, 'target' => '_blank' ), $claimee->user_email ) ) );

		echo html( 'p', array( 'style' => 'margin-top: 20px; text-align: center;' ),
			html( 'input', array(
				'type'  => 'submit',
				'class' => 'button-primary',
				'value' => __( 'Accept', APP_TD ),
				'name'  => 'publish',
				'style' => 'width: 45%; margin-right: 15px;',
			) ),
			html( 'a', array(
				'class' => 'button',
				'style' => 'width: 45%;',
				'href'  => $reject_url,
			), __( 'Reject', APP_TD ) )
		);

		echo html( 'p', array(
				'class' => 'howto'
			), __( 'An email will be sent to the claimee with your decision.', APP_TD ) );

	}

	/**
	 * Displays admin notice when Claimed listing has been rejected.
	 */
	function rejected_claim_success_notice() {
		$ptype = get_post_type( $this->get_post_id() );

		// Don't affect other listing types.
		if ( "{$ptype}-claim-moderation" !== $this->box_id ) {
			return;
		}

		$msg = sprintf( __( 'You have rejected the claim so this listing has been reset to <a href="#%s-claimable">claimable</a>.', APP_TD ), esc_attr( $ptype ) );

		echo scb_admin_notice( $msg );
	}

	/**
	 * Verifies the url nonce and changes post status.
	 *
	 * Transition post status from pending-claimed to publish will trigger
	 * APP_View_Process_Claim::maybe_complete_process() action what continues
	 * Claim checkout from the Moderation step. If Claim was rejected, so user
	 * will stuck on the step until admin changes his decision, otherwise claim
	 * is approved and user will go further by steps.
	 */
	public function maybe_reject_claim() {

		$nonce  = $_GET['_reject_claim_nonce']; // input var okay.
		$action = "reject-claim-post_{$this->get_post_id()}";
		$item   = get_post( $this->get_post_id() );
		$ptype  = $item->post_type;

		// Don't affect other listing types.
		if ( "{$ptype}-claim-moderation" !== $this->box_id ) {
			return;
		}

		if ( 'pending-claimed' === $item->post_status && wp_verify_nonce( $nonce, $action ) ) {

			wp_update_post( array(
				'ID'          => $this->get_post_id(),
				'post_status' => 'publish',
			) );

			add_action( 'admin_notices', array( $this, 'rejected_claim_success_notice' ) );
		}
	}
}
