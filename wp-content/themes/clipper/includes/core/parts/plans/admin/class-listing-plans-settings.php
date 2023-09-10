<?php
/**
 * Listing Plans Settings submodule
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
class APP_Listing_Plans_Settings extends APP_Listing_Core_Settings {

	/**
	 * Registers Listing settings
	 *
	 * @param APP_Tabs_Page $page Current settings page object.
	 */
	public function register( $page ) {

		$options = $this->listing->options;
		$type    = $this->listing->get_type();

		if ( empty( $page->tab_sections[ $type ]['default_plan'] ) ) {
			$page->tab_sections[ $type ]['default_plan'] = array(
				'title'  => __( 'Default Listing Plan', APP_TD ),
				'fields' => array(),
				'options' => $options,
			);
		}

		$page->tab_sections[ $type ]['default_plan']['fields'][] = array(
			'title' => __( 'Items Duration', APP_TD ),
			'type'  => 'number',
			'name'  => 'duration',
			'tip'   => __( 'The duration in days to be applied to new items.', APP_TD ),
		);

		$page->tab_sections[ $type ]['default_plan']['fields'][] = array(
			'title'    => __( 'Plan Description', APP_TD ),
			'name'     => 'description',
			'type'     => 'custom',
			'render'   => array( 'APP_Editor_Field_Type', '_render' ),
			'sanitize' => 'wp_kses_post',
			'desc'     => '',
			'tip'      => __( 'This is a description of Default Listing Plan, which is available if there are no other plans.', APP_TD ),
			'props'    => array(
				'editor_type' => 'tmce',
			),
		);

		$page->tab_sections[ $type ]['default_plan']['fields'][] = array(
			'title' => __( 'Bypass Default Plan step', APP_TD ),
			'type'  => 'checkbox',
			'name'  => 'bypass_plan',
			'desc'  => __( 'Automatically apply Default Listing Plan if there is no other choice available', APP_TD ),
			'tip'   => __( 'Left unchecked, user will see the Default Listing Plan details on a first step. Such as description, price, duration, categories limit, etc.', APP_TD ),
		);

	}

}
