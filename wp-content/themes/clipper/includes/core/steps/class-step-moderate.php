<?php
/**
 * Moderate Listing Step
 *
 * @package Listing\Views\Checkouts
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Moderate Listing Step class
 */
class APP_Step_Moderate extends APP_Listing_Step {

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
					"{$this->listing->get_type()}-new" => array( 'before' => 'activate' ),
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
		$message   = html( 'p', sprintf( __( 'Listing %s was created and awaiting moderation.', APP_TD ), $permalink ) );

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

		$item_id = $checkout->get_data( 'listing_id' );
		$item    = get_post( $item_id );

		if ( ! $item ) {
			$this->errors->add( 'no-item', __( 'No listing item for moderation', APP_TD ) );
			return;
		}

		// Pass the step for already moderated item.
		if ( 'publish' === $item->post_status || ! $this->listing->options->moderate ) {
			$this->finish_step();
			return;
		}

		if ( ! $this->check_previous() ) {
			$this->cancel_step();
			return;
		}

		$checkout->add_data( 'title', __( 'Moderation', APP_TD ) );

		if ( 'pending' !== $item->post_status ) {

			// Make checkout unexpirable since we don't know when the admin will
			// deign to approve a listing.
			$checkout->set_expiration( 0 );

			// Stop the process and wait for admin's action.
			wp_update_post( array(
				'ID' => $item_id,
				'post_status' => 'pending',
			) );

			$emails = new APP_Listing_Process_Emails( $this->listing );
			$emails->notify_admin_pending_listing( $item );
			$emails->notify_user_pending_listing( $item );
		}

	}
}
