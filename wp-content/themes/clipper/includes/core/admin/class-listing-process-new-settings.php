<?php
/**
 * Listing Process New Settings submodule
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
class APP_Listing_Process_New_Settings extends APP_Listing_Core_Settings {

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

		if ( empty( $page->tab_sections[ $type ]['moderation'] ) ) {
			$page->tab_sections[ $type ]['moderation'] = array(
				'title'  => __( 'Moderation', APP_TD ),
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

		$page->tab_sections[ $type ]['moderation']['fields'][] = array(
			'title' => __( 'Moderate Items', APP_TD ),
			'type'  => 'checkbox',
			'name'  => 'moderate',
			'desc'  => __( 'Manually approve and publish each new listing', APP_TD ),
			'tip'   => __( 'Left unchecked, listings go live immediately without being moderated (unless it has not been paid for).', APP_TD ),
		);

		$page->tab_sections['notifications'][ "{$type}_notifications" ]['fields'][] = array(
			'title' => __( 'New Items', APP_TD ),
			'type'  => 'checkbox',
			'name'  => "notify_new_$type",
			'desc'  => __( 'Yes', APP_TD ),
			'tip'   => __( 'Notify admins when new items are posted', APP_TD ),
		);

		$page->tab_sections['notifications'][ "{$type}_notifications" ]['fields'][] = array(
			'title' => __( 'Pending Items', APP_TD ),
			'type'  => 'checkbox',
			'name'  => "notify_pending_$type",
			'desc'  => __( 'Yes', APP_TD ),
			'tip'   => __( 'Notify admins when new items are waiting for moderation', APP_TD ),
		);

		$page->tab_sections['notifications'][ "{$type}_notifications" ]['fields'][] = array(
			'title' => __( 'Notify User About Pending Item', APP_TD ),
			'type'  => 'checkbox',
			'name'  => "notify_user_pending_$type",
			'desc'  => __( 'Yes', APP_TD ),
			'tip'   => __( 'Notify user when new item is pending moderation', APP_TD ),
		);
	}
}
