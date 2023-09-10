<?php
/**
 * Listing Process Renew Settings submodule
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
class APP_Listing_Process_Renew_Settings extends APP_Listing_Core_Settings {

	/**
	 * Registers Listing settings
	 *
	 * @param APP_Tabs_Page $page Current settings page object.
	 */
	public function register( $page ) {

		$options = $this->listing->options;
		$type    = $this->listing->get_type();
		$name    = $this->listing->meta->get_labels( $type )->name;

		$page->tabs->add( 'notifications', __( 'Notifications', APP_TD ) );

		if ( empty( $page->tab_sections[ $type ]['permissions'] ) ) {
			$page->tab_sections[ $type ]['permissions'] = array(
				'title'  => __( 'Permissions', APP_TD ),
				'fields' => array(),
				'options' => $options,
			);
		}

		if ( empty( $page->tab_sections['notifications'][ "{$type}_notifications" ] ) ) {
			$page->tab_sections['notifications'][ "{$type}_notifications" ] = array(
				'title'   => $name,
				'fields'  => array(),
				'options' => $options,
			);
		}

		$page->tab_sections[ $type ]['permissions']['fields'][] = array(
			'title' => __( 'Allow Renew Expired Listings', APP_TD ),
			'type'  => 'checkbox',
			'name'  => 'allow_renew',
			'desc'  => __( 'Yes', APP_TD ),
			'tip'   => __( 'Do you allow to users to renew their expired listings? Renew listing process allows users to re-publish their expired listings by passing through all publishing steps again.', APP_TD ),
		);

		$page->tab_sections['notifications'][ "{$type}_notifications" ]['fields'][] = array(
			'title' => __( 'Renew Items', APP_TD ),
			'type'  => 'checkbox',
			'name'  => "notify_renew_$type",
			'desc'  => __( 'Yes', APP_TD ),
			'tip'   => __( 'Notify admins when items are renewed', APP_TD ),
		);
	}

}
