<?php
/**
 * Listing features submodule
 *
 * @package Listing\Features
 * @author  AppThemes
 * @since   Listing 2.0
 */

/**
 * Listing features object
 */
class APP_Listing_Features {

	/**
	 * Create a new set of Listing options.
	 *
	 * @param APP_Listing $listing Listing object.
	 */
	public function __construct( APP_Listing $listing ) {

		$listing->options->set_defaults( $this->get_defaults() );

	}

	/**
	 * Retrieves module's options default values to be registered in Listing
	 * options object.
	 *
	 * @return array The Form defaults.
	 */
	public function get_defaults() {
		return array(
			'features' => array(),
		);
	}
}
