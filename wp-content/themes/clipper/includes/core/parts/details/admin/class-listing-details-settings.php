<?php
/**
 * Listing Details Settings submodule
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
class APP_Listing_Details_Settings extends APP_Listing_Core_Settings {

	/**
	 * Registers Listing settings
	 *
	 * @param APP_Tabs_Page $page Current settings page object.
	 */
	public function register( $page ) {

		$options = $this->listing->options;
		$type    = $this->listing->get_type();

		if ( empty( $page->tab_sections[ $type ]['templates'] ) ) {
			$page->tab_sections[ $type ]['templates'] = array(
				'title'  => __( 'Templates', APP_TD ),
				'fields' => array(),
				'options' => $options,
			);
		}

		$page->tab_sections[ $type ]['templates']['fields'][] = array(
			'title'    => __( 'Content Template', APP_TD ),
			'name'     => 'content_template',
			'type'     => 'custom',
			'render'   => array( 'APP_Editor_Field_Type', '_render' ),
			'sanitize' => 'wp_kses_post',
			'desc'     => '',
			'tip'      => sprintf( __( 'This is a content template with default markup. "%s" shortcode indicates where the actuall listing content will placed in relation to other elements.', APP_TD ), '[appthemes_listing_content]' ),
			'props'    => array(
				'editor_type' => 'tmce',
			),
		);
	}
}
