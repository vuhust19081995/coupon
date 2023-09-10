<?php
/**
 * Listing Payments Settings
 *
 * @package Listing\Admin\Settings\Payments
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Payments Settings class.
 *
 * Requires theme supports:
 *	- app-framework
 *  - app-dashboard
 */
class APP_Listing_Payments_Settings extends APP_Listing_Core_Settings {

	/**
	 * Initiates Listing Payments Settings module
	 *
	 * @global array $admin_page_hooks Registered page hooks.
	 */
	public function init() {
		global $admin_page_hooks;

		$this->priority = ( 'APP_Listing_Payments_Settings' === get_class( $this ) ) ? 10 : 11 ;

		if ( isset( $admin_page_hooks['app-payments'] ) ) {
			add_action( 'tabs_' . $admin_page_hooks['app-payments'] . '_page_app-payments-settings', array( $this, 'register' ), $this->priority );
		}
	}

	/**
	 * Registers Listing Payments settings
	 *
	 * @param APP_Tabs_Page $page Current settings page object.
	 */
	public function register( $page ) {

		$options = $this->listing->options;
		$type    = $this->listing->get_type();
		$name    = get_post_type_object( $type )->labels->name;

		$page->tabs->add_after( 'general', $type, $name );

		if ( $this->listing->options->charge ) {
			$page->tab_sections[ $type ]['pricing'] = array(
				'title' => __( 'Pricing', APP_TD ),
				'fields' => array(
					array(
						'title'    => __( 'Price', APP_TD ),
						'type'     => 'text',
						'name'     => 'price',
						'sanitize' => 'appthemes_absfloat',
						'desc'     => __( 'Price to list an item', APP_TD ),
						'tip'      => '',
						'extra' => array(
							'class' => 'small-text',
						),
					),
				),
				'options' => $options,
			);

		} else {
			$page->tab_sections[ $type ]['charge'] = array(
				'title' => __( 'Pricing', APP_TD ),
				'desc' => sprintf( __( 'You need to enable the <a href="%s">"Charge for Items"</a> option before setting up price models.', APP_TD ), "admin.php?page=app-settings&tab=$type" ),
				'fields' => array(),
			);
		}
	}
}
