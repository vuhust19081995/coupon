<?php
/**
 * Single Listing Media Fields metabox
 *
 * @package Listing\Form\Metaboxes
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Media meta box.
 */
class APP_Listing_Media_Metabox extends APP_Media_Manager_Metabox {

	/**
	 * Current Listing module object.
	 *
	 * @var APP_Listing
	 */
	protected $listing;

	/**
	 * Listing Form object.
	 *
	 * @var APP_Listing_Form
	 */
	protected $form;

	/**
	 * Form fields array.
	 *
	 * @var array
	 */
	protected $media_fields = array();

	/**
	 * Constructs Listing Media Manager metabox
	 *
	 * @param APP_Listing $listing Listing module object.
	 * @param string      $title   Metabox title.
	 */
	public function __construct( APP_Listing $listing, $title = '' ) {

		$this->listing = $listing;

		if ( ! $title ) {
			$title = __( 'Listing Media', APP_TD );
		}

		$ptype = $this->listing->get_type();

		parent::__construct( "app-{$ptype}-media", $title, $ptype, 'normal', 'high' );
	}

	/**
	 * Additional checks before registering the meta box.
	 *
	 * @return bool
	 */
	protected function condition() {

		$this->form         = $this->listing->form;
		$this->media_fields = $this->form->get_form_fields( array(), array( 'type' => 'file' ), $this->get_post_id() );

		return ! empty( $this->media_fields );
	}

	/**
	 * Displays meta box content.
	 *
	 * @param WP_Post $post Displayed Listing object.
	 */
	function display( $post ) {
		echo $this->form->get_form( $post->ID, $this->media_fields );
	}
}
