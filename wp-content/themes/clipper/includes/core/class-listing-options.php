<?php
/**
 * Listing Options submodule
 *
 * @package Listing\Options
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing options object
 *
 * Requires theme supports:
 *	- app-framework
 */
class APP_Listing_Options extends scbOptions {

	/**
	 * Create a new set of Listing options.
	 *
	 * @param APP_Listing $listing  Listing type.
	 * @param string      $file     Reference to main plugin file.
	 */
	public function __construct( APP_Listing $listing, $file = null ) {

		$ptype = $listing->get_type();
		$key   = "app_{$ptype}_options";

		$defaults = array(
			'charge' => true,
		);

		return parent::__construct( $key, $file, $defaults );
	}

	/**
	 * Allows to extend defaults array after object construction.
	 *
	 * @param array $defaults An associative array of custom default values.
	 */
	public function set_defaults( $defaults = array() ) {
		$this->defaults = wp_parse_args( $this->defaults, $defaults );
	}
}
