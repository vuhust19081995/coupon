<?php
/**
 * Listing Director
 *
 * @package Listing\Builder
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * A Listing director class.
 *
 * It knows the interface of the builder and builds a complex object with the
 * help of the concrete builder provided by the client.
 */
class APP_Listing_Director {

	/**
	 *
	 * @var array
	 */
	private static $instances = array();

	/**
	 * Builds the Listing object.
	 *
	 * @param string              $type    The Listing type to be built.
	 * @param APP_Listing_Builder $builder The concrete Listing Builder object.
	 *
	 * @return APP_Listing Resulting Listing object.
	 */
	public function build( $type, APP_Listing_Builder $builder ) {

		// Try to get already created instance.
		$listing = self::get( $type );

		if ( $listing ) {
			trigger_error( sprintf( "Listing type '%s' has been already registered.", $listing->get_type() ), E_USER_WARNING );
			return $listing;
		}

		// The beginning of construction -->

		// Create new draft listing.
		$builder->create( $type );

		// Register listing stuff in WordPress system.
		$builder->register();

		// Set up listing meta object.
		$builder->set_meta_object();

		// Set up listing options object.
		$builder->set_options();

		// Allow to plugins add custom modules before standard ones.
		do_action( 'appthemes_draft_listing_object_created', $builder->get() );

		// Register modules.
		$builder->add_modules();

		// Enqueue modules to be instatiated.
		$builder->enqueue_modules();

		// Instatiate modules.
		$builder->set_modules();

		// <-- The end of construction.

		$listing = $builder->get();

		self::set( $type, $listing );

		do_action( 'appthemes_listing_object_building_completed', $listing );

		return $listing;
	}

	/**
	 * Retrieves the Listing object from the list of already built by given type.
	 *
	 * @param string $type The Listing type.
	 *
	 * @return APP_Listing The Listing object.
	 */
	public final static function get( $type ) {
		if ( isset( self::$instances[ $type ] ) ) {
			return self::$instances[ $type ];
		}
	}

	/**
	 * Adds created Listings to a list for future use.
	 *
	 * @param string      $type    The Listing type.
	 * @param APP_Listing $listing The Listing object.
	 */
	protected final static function set( $type, APP_Listing $listing ) {
		self::$instances[ $type ] = $listing;
	}

}