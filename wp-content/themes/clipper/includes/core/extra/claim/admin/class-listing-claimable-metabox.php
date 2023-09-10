<?php
/**
 * Single Listing Claimable metabox
 *
 * @package Listing\Modules\Claim\Metaboxes
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Pricing Addons class
 */
class APP_Listing_Claimable_Metabox extends APP_Meta_Box {

	/**
	 * Construct Listing Claimable metabox
	 *
	 * @param string $ptype Listing module type.
	 * @param string $title Metabox title.
	 */
	public function __construct( $ptype, $title = '' ) {
		if ( ! $title ) {
			$title = __( 'Claimable Listing', APP_TD );
		}

		parent::__construct( "{$ptype}-claimable", $title, $ptype, 'normal', 'high' );
	}


	/**
	 * Additional checks before registering the metabox.
	 *
	 * @return bool
	 */
	function condition() {
		return ( get_post_status( $this->get_post_id() ) !== 'pending-claimed' );
	}

	/**
	 * Returns an array of form fields.
	 *
	 * @return array
	 */
	public function form() {

		return array(
			array(
				'title' => __( 'Users can claim this listing', APP_TD ),
				'type' => 'checkbox',
				'name' => 'listing_claimable',
				'desc' => __( 'Yes', APP_TD ),
			),
		);

	}

	/**
	 * Display some extra HTML after the form.
	 *
	 * @param WP_Post $post Current Listing item object.
	 *
	 * @return void
	 */
	public function after_form( $post ) {
		echo html( 'p', array(
				'class' => 'howto',
		), __( 'Claimable listings will have a link that allows users to claim them. You can enable moderation on claimed listings in settings.', APP_TD ) );
	}

}
