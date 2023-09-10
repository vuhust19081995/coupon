<?php
/**
 * Listing Process Edit Settings submodule
 *
 * @package Listing\Admin\Settings
 * @author  AppThemes
 * @since   Listing 2.0
 */

/**
 * Listing Settings class
 *
 * Requires theme supports:
 *	- app-framework
 *  - app-dashboard
 */
class APP_Listing_Process_Edit_Settings extends APP_Listing_Core_Settings {

	/**
	 * Registers Listing settings
	 *
	 * @param APP_Tabs_Page $page Current settings page object.
	 */
	public function register( $page ) {

		$options = $this->listing->options;
		$type    = $this->listing->get_type();

		if ( empty( $page->tab_sections[ $type ]['permissions'] ) ) {
			$page->tab_sections[ $type ]['permissions'] = array(
				'title'  => __( 'Permissions', APP_TD ),
				'fields' => array(),
				'options' => $options,
			);
		}

		$page->tab_sections[ $type ]['permissions']['fields'][] = array(
			'title' => __( 'Allow Edit Published Listings', APP_TD ),
			'type'  => 'checkbox',
			'name'  => 'allow_edit',
			'desc'  => __( 'Yes', APP_TD ),
			'tip'   => __( 'Do you allow to users edit their published listings? Note: this option has effect only if user is in Contributor role and lower, users in other roles can edit their listings anyway.', APP_TD ),
		);

	}

}
