<?php
/**
 * User Listing Builder
 *
 * @package Clipper\Users
 * @author  AppThemes
 * @since   2.0.0
 */

/**
 * User Listing Builder class
 */
class CLPR_User_Listing_Builder extends APP_User_Listing_Builder {

	/**
	 * Sets up basic listing options.
	 */
	public function set_options() {

		$options  = new APP_Listing_Options( $this->listing );
		$defaults = array(
			'charge' => false,
		);

		$options->set_defaults( $defaults );
		$this->listing->set_options( $options );
	}

	/**
	 * Configure Listing instance with essential modules included in a
	 * listing package.
	 */
	public function add_modules() {

		$this->listing->add_module( 'form', 'CLPR_User_Listing_Form' );

		parent::add_modules();
	}
}
