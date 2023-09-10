<?php
/**
 * AppThemes Listing
 *
 * @package Listing
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Basic Listing class
 *
 * @property-read APP_Listing_Options     $options Listing options object.
 * @property-read APP_Listing_Meta_Object $meta    Listing meta object.
 */
class APP_Listing {

	/**
	 * The Listing type slug.
	 *
	 * @access private
	 * @var string $type An unique listing type identificator
	 */
	private $type;

	/**
	 * Listing modules dependency object.
	 *
	 * Contains the modules objects.
	 *
	 * @access protected
	 * @var APP_Listing_Dependencies
	 */
	protected $modules;

	/**
	 * Listing options object.
	 *
	 * @access protected
	 * @var APP_Listing_Options
	 */
	protected $options;

	/**
	 * Listing meta object.
	 *
	 * Provides a methods for data manipulation of a listing item.
	 *
	 * @access protected
	 * @var APP_Listing_Meta_Object
	 */
	protected $meta;

	/**
	 * Listing type constructor
	 *
	 * @param string                   $type    An unique listing type id.
	 * @param APP_Listing_Dependencies $modules Listing dependency object.
	 */
	public function __construct( $type, APP_Listing_Dependencies $modules ) {
		$this->type    = $type;
		$this->modules = $modules;
	}

	/**
	 * Sets up an options object.
	 *
	 * Checks the type of options object to ensure unified interface.
	 *
	 * @param APP_Listing_Options $options Options object.
	 */
	public final function set_options( APP_Listing_Options $options ) {

		if ( isset( $this->options ) ) {
			return;
		}

		$this->options = $options;
	}

	/**
	 * Sets up a meta object.
	 *
	 * Checks the type of meta object to ensure unified interface.
	 *
	 * @param APP_Listing_Meta_Object $object Meta object.
	 */
	public final function set_meta_object( APP_Listing_Meta_Object $object ) {

		if ( isset( $this->meta ) ) {
			return;
		}

		$this->meta = $object;
	}

	/**
	 * Retrieves all registered modules with optional parameters.
	 *
	 * @return array Raw modules array.
	 */
	public final function get_modules_raw() {
		return wp_list_pluck( $this->modules->registered, 'extra' );
	}

	/**
	 * Retrieves all instantiated modules.
	 *
	 * @return array Listing modules.
	 */
	public final function get_modules() {
		$handles = $this->modules->done;
		$modules = array();

		foreach ( $handles as $handle ) {
			$modules[ $handle ] = $this->modules->get_data( $handle, 'instance' );
		}

		return $modules;
	}

	/**
	 * Register an item if no item of that name already registered.
	 *
	 * All modules should be registered before enqueued and instantiated.
	 *
	 * @param string $handle Unique item name.
	 * @param string $class  The item object class name.
	 * @param array  $deps   Optional. An array of registered item handles
	 *                        this item depends on. Set to false if there are
	 *                        no dependencies. Default empty array.
	 * @param mixed  $args   Optional. An array of parameters to be passed in
	 *                        module constructor. Listing object will always
	 *                        be added as a first argument.
	 * @param array  $extra  Optional. An array of module parameters to be used
	 *                        before class instantiated.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function add_module( $handle, $class, $deps = array(), $args = array(), $extra = array() ) {
		array_unshift( $args, $this );

		if ( ! $this->modules->add( $handle, $class, $deps, false, $args ) ) {
			return false;
		}

		$defaults = array(
			// Whether module can be disabled by Admin via Listing settings.
			'optional' => false,
			// The Feature label to be used in the Listing settings.
			'label'    => $handle,
		);

		$extra = array_merge( $defaults, $extra );

		foreach ( $extra as $key => $value ) {
			$this->modules->add_data( $handle, $key, $value );
		}

		return true;
	}

	/**
	 * Retrieves a current listing type
	 *
	 * @return string A listing type associated with current object
	 */
	public final function get_type() {
		return $this->type;
	}

	/**
	 * Magic method: $listing->arg
	 *
	 * @param string $key The listing property or module name.
	 *
	 * @return mixed
	 */
	public function __get( $key ) {

		if ( 'options' === $key ) {
			return $this->options;
		} elseif ( 'meta' === $key ) {
			return $this->meta;
		}

		return $this->modules->get_data( $key, 'instance' );
	}

	/**
	 * Magic setter
	 *
	 * Doesn't allow to set magic properties by default.
	 *
	 * @param string $name  The listing property or module name.
	 * @param string $value New value.
	 */
	public function __set( $name, $value ) {}

	/**
	 * Magic method: isset( $listing->arg )
	 *
	 * @param string $key The listing property or module name.
	 *
	 * @return bool
	 */
	public function __isset( $key ) {
		return isset( $this->modules->registered[$key]->extra['instance'] );
	}

}
