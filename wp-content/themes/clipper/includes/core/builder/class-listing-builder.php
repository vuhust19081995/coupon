<?php
/**
 * Listing Abstract Builder
 *
 * @package Listing\Builder
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * An abstract class for Listing Builder.
 *
 * Constructs and puts together parts of the Listing by using the Builder
 * interface in communication with Director.
 *
 * Defines empty building methods to allows sub classes bypass them keeping
 * original interface.
 */
abstract class APP_Listing_Builder {

	/**
	 * A Listing instance.
	 * @var APP_Listing
	 */
	protected $listing;

	/**
	 * A Listing modules dependency object.
	 * @var APP_Listing_Dependencies
	 */
	protected $modules;

	/**
	 * Creates empty Listing instance.
	 *
	 * @param string $type The Listing type.
	 */
	abstract public function create( $type );

	/**
	 * Set up listing meta object.
	 */
	abstract public function set_meta_object();

	/**
	 * Registers listing stuff in the WordPress system (i.e. taxonomies, post
	 * types, comment types, etc.).
	 */
	public function register() {}

	/**
	 * Sets up basic listing options.
	 */
	public function set_options() {}

	/**
	 * Registers a module classes in a Listing's dependency object.
	 */
	public function add_modules() {}

	/**
	 * Enqueues a module classes in a Listing's dependency object.
	 */
	public function enqueue_modules() {}

	/**
	 * Instantiates a module classes.
	 */
	public function set_modules() {}

	/**
	 * Retrieves a Listing instance on any stage of the building.
	 *
	 * @return APP_Listing The Listing object.
	 */
	public final function get() {
		return $this->listing;
	}

}

