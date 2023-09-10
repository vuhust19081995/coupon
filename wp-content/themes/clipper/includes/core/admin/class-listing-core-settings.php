<?php
/**
 * Listing Settings submodule
 *
 * @package Listing\Admin\Settings
 * @author  AppThemes
 * @since   Listing 2.0
 */

/**
 * Abstract Listing Settings class
 *
 * Requires theme supports:
 *	- app-framework
 *  - app-dashboard
 */
abstract class APP_Listing_Core_Settings {

	/**
	 * Current Listing module object
	 *
	 * @var APP_Listing
	 */
	protected $listing;

	/**
	 * The settings registration priority.
	 *
	 * @var int
	 */
	protected $priority = 10;

	/**
	 * Construct Listing Settings module
	 *
	 * @param APP_Listing $listing Listing module object.
	 */
	public function __construct( APP_Listing $listing ) {

		$this->listing = $listing;

		add_action( 'admin_init', array( $this, 'init' ) );
	}

	/**
	 * Initiates Listing Settings module
	 *
	 * @global array $admin_page_hooks Registered page hooks.
	 */
	public function init() {
		global $admin_page_hooks;

		if ( isset( $admin_page_hooks['app-dashboard'] ) ) {
			add_action( "tabs_{$admin_page_hooks['app-dashboard']}_page_app-settings", array( $this, 'register' ), $this->priority );
		}
	}

	/**
	 * Registers Listing settings
	 *
	 * @param APP_Tabs_Page $page Current settings page object.
	 */
	abstract function register( $page );
}
