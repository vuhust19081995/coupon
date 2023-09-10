<?php
/**
 * Listing Settings submodule
 *
 * @package Listing\Admin\Settings
 * @author  AppThemes
 * @since   Listing 1.0
 * @since   Listing 2.0 extends APP_Listing_Core_Settings
 */

/**
 * Listing Settings class
 *
 * Requires theme supports:
 *	- app-framework
 *  - app-dashboard
 */
class APP_Listing_Settings extends APP_Listing_Core_Settings {

	/**
	 * The settings registration priority.
	 *
	 * @var int
	 */
	protected $priority = 9;

	/**
	 * Registers Listing settings
	 *
	 * @param APP_Tabs_Page $page Current settings page object.
	 */
	public function register( $page ) {

		$options = $this->listing->options;
		$type    = $this->listing->get_type();
		$name    = $this->listing->meta->get_labels( $type )->name;

		$page->tabs->add_after( 'general', $type, $name );

		if ( ! isset( $page->tab_sections[ $type ]['general'] ) ) {
			$page->tab_sections[ $type ]['general'] = array(
				'title'  => __( 'General', APP_TD ),
				'fields' => array(),
				'options' => $options,
			);
		}
	}
}
