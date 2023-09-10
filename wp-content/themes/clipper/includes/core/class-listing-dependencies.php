<?php
/**
 * Listing Dependency builder
 *
 * @package Listing\Builder
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * An objects collection container class.
 *
 * Allows to register and instatiate classes considering their dependencies.
 */
class APP_Listing_Dependencies extends WP_Dependencies {

	/**
	 * Register an item if no item of that name already registered.
	 *
	 * @param string $handle Unique item name.
	 * @param string $class  The item object class name.
	 * @param array  $deps   Optional. An array of registered item handles
	 *                        this item depends on. Set to false if there are
	 *                        no dependencies. Default empty array.
	 * @param bool   $ver    Not used.
	 * @param mixed  $args   Optional. An array of parameters to be passed in
	 *                        module constructor.
	 *
	 * @return bool True on success, false on failure.
	 */
	public function add( $handle, $class, $deps = array(), $ver = false, $args = array() ) {
		return parent::add( $handle, $class, $deps, false, $args );
	}

	/**
	 * Process a dependency.
	 *
	 * Instatiates a module object.
	 *
	 * @param string $handle Name of the item. Should be unique.
	 * @return bool True on success, false if not set.
	 */
	public function do_item( $handle, $group = false ) {
		if ( ! parent::do_item( $handle ) ) {
			return false;
		}

		$class = $this->registered[ $handle ]->src;
		$args  = $this->registered[ $handle ]->args;

		if ( ! class_exists( $class ) ) {
			return false;
		}

		$item  = appthemes_instantiate_class( $class, $args );

		$this->add_data( $handle, 'instance', $item );

		return true;
	}

}
