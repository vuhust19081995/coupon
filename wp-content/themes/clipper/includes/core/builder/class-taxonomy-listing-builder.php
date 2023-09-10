<?php
/**
 * Taxonomy Type Listing Builder
 *
 * @package Listing\Builder
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * A generic Listing Builder to construct a Listing object associated with
 * taxonomy type.
 */
class APP_Taxonomy_Listing_Builder extends APP_Listing_Builder {

	/**
	 * Set up listing meta object.
	 */
	public function set_meta_object() {
		$meta_object = new APP_Listing_Taxonomy_Meta_Object();
		$this->listing->set_meta_object( $meta_object );
	}

	/**
	 * Creates empty Listing instance.
	 *
	 * @param string $type The Listing type.
	 */
	public function create( $type = '' ) {

		if ( ! $type ) {
			$type = 'category';
		}

		$this->modules = new APP_Listing_Dependencies();
		$this->listing = new APP_Listing( $type, $this->modules );
	}

	/**
	 * Registers listing stuff in the WordPress system (i.e. taxonomies, post
	 * types, comment types, etc.).
	 */
	public function register() {
		$this->_register_taxonomies();
	}

	/**
	 * Sets up basic listing options.
	 */
	public function set_options() {
		$options = new APP_Listing_Options( $this->listing );
		$this->listing->set_options( $options );
	}

	/**
	 * Registers taxonomies associated with current listing type
	 */
	protected function _register_taxonomies() {}

	/**
	 * Configure Listing instance with essential modules included in a
	 * listing package.
	 */
	public function add_modules() {
		// Add default is_admin() dependency.
		if ( is_admin() ) {
			$this->modules->add( '__admin__', '' );
		}

		$this->listing->add_module( 'form',              'APP_Listing_Form' );
		$this->listing->add_module( 'form_settings',     'APP_Listing_Form_Settings',  array( 'form', '__admin__' ) );
		$this->listing->add_module( 'form_fields_box',   'APP_Listing_Taxonomy_Metabox', array( 'form', '__admin__' ) );

		$this->listing->add_module( 'details',           'APP_Listing_Details' );
		$this->listing->add_module( 'details_settings',  'APP_Listing_Details_Settings',  array( 'details', '__admin__' ) );

		$this->listing->add_module( 'settings',          'APP_Listing_Settings',  array( '__admin__' ) );

		$this->listing->add_module( 'features',          'APP_Listing_Features' );
		$this->listing->add_module( 'features_settings', 'APP_Listing_Features_Settings',  array( 'features', '__admin__' ) );
	}

	/**
	 * Enqueues a module classes in a Listing's dependency object.
	 *
	 * Filters disabled modules. Only enqueued modules will be instantiated.
	 *
	 * @param mixed $handles Item handle and argument (string) or item handles
	 *                        and arguments (array of strings). If argument is
	 *                        false, all registered items will be enqueued.
	 */
	public function enqueue_modules( $handles = false ) {

		if ( false === $handles ) {
			$handles = array_keys( $this->modules->registered );
		}

		$allowed = (array) $this->listing->options->features;

		foreach ( (array) $handles as $handle ) {
			if ( ! $this->modules->get_data( $handle, 'optional' ) || in_array( $handle, $allowed ) ) {
				$this->modules->enqueue( $handle );
			}
		}
	}

	/**
	 * Instantiates registered submodules.
	 *
	 * Processes the items passed to it or the queue, and their dependencies.
	 *
	 * @param mixed $handles Optional. Items to be processed:
	 *                        Process queue (false),
	 *                        process item (string),
	 *                        process items (array of strings).
	 */
	public function set_modules( $handles = false ) {
		$this->modules->do_items( $handles );
	}

}
