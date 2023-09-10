<?php
/**
 * Listing Charge Settings
 *
 * @package Listing\Admin\Settings
 * @author  AppThemes
 * @since   Listing 2.0
 */

/**
 * Listing Charge Settings class.
 *
 * Requires theme supports:
 *	- app-framework
 *  - app-dashboard
 */
class APP_Listing_Charge_Settings extends APP_Listing_Core_Settings {

	/**
	 * Registers Listing Charge settings
	 *
	 * @param APP_Tabs_Page $page Current settings page object.
	 */
	public function register( $page ) {

		$options = $this->listing->options;
		$type    = $this->listing->get_type();

		if ( empty( $page->tab_sections[ $type ]['pricing'] ) ) {
			$page->tab_sections[ $type ]['pricing'] = array(
				'title'  => __( 'Pricing', APP_TD ),
				'fields' => array(),
				'options' => $options,
			);
		}

		$page->tab_sections[ $type ]['pricing']['fields'][] = array(
			'title' => __( 'Charge for Items', APP_TD ),
			'name'  => 'charge',
			'type'  => 'checkbox',
			'desc'  => __( 'Start accepting payments', APP_TD ),
			'tip'   => sprintf( __( 'Do you want to charge for creating an item on your site? You can manage your <a href="%s">Payments Settings</a> in the Payments Menu.', APP_TD ), "admin.php?page=app-payments-settings&tab={$type}" ),
		);
	}
}
