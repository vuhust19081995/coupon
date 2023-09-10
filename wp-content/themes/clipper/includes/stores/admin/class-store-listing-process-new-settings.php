<?php
/**
 * Listing Process New Settings submodule
 *
 * @package Clipper\Stores
 * @author  AppThemes
 * @since   2.0.0
 */

/**
 * Listing Settings class
 *
 * Requires theme supports:
 *	- app-framework
 *  - app-dashboard
 */
class CLPR_Store_Listing_Process_New_Settings extends APP_Listing_Core_Settings {

	/**
	 * Registers Listing settings
	 *
	 * @param APP_Tabs_Page $page Current settings page object.
	 */
	public function register( $page ) {

		$options = $this->listing->options;
		$type    = $this->listing->get_type();

		if ( empty( $page->tab_sections[ $type ]['moderation'] ) ) {
			$page->tab_sections[ $type ]['moderation'] = array(
				'title'  => __( 'Moderation', APP_TD ),
				'fields' => array(),
				'options' => $options,
			);
		}

		$page->tab_sections[ $type ]['moderation']['fields'][] = array(
			'title' => __( 'Moderate Stores', APP_TD ),
			'type'  => 'checkbox',
			'name'  => 'moderate',
			'desc'  => __( 'Manually approve and publish each new store', APP_TD ),
			'tip'   => __( 'Left unchecked, new stores submitted with coupons will go live immediately without being moderated. Note: the moderate new coupons option above must be checked for this to work.', APP_TD ),
		);

	}
}
