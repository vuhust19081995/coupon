<?php
/**
 * Listing Features Settings submodule
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
class APP_Listing_Features_Settings extends APP_Listing_Core_Settings {

	/**
	 * The list of enabled features to compare against updated. If the lists
	 * different so needs to reload page to enable/disable them.
	 *
	 * @var array
	 */
	protected $enabled_features;

	/**
	 * Initiates Listing Settings module
	 *
	 * @global array $admin_page_hooks Registered page hooks.
	 */
	public function init() {
		global $admin_page_hooks;

		parent::init();

		if ( isset( $admin_page_hooks['app-dashboard'] ) ) {
			add_action( "tabs_{$admin_page_hooks['app-dashboard']}_page_app-settings_form_handler", array( $this, 'features_handler' ), $this->priority );
		}
	}

	/**
	 * Registers Listing settings
	 *
	 * @param APP_Tabs_Page $page Current settings page object.
	 */
	public function register( $page ) {

		$options = $this->listing->options;
		$type    = $this->listing->get_type();
		$name    = $this->listing->meta->get_labels( $type )->name;

		$this->enabled_features = $this->listing->options->features;

		// Get all registered modules.
		$features = $this->listing->get_modules_raw();
		// Leave only optional modules.
		$features = wp_list_filter( $features, array( 'optional' => true ) );

		if ( ! empty( $features ) ) {
			$page->tab_sections[ $type ]['features'] = array(
				'title'  => __( 'Extra Features', APP_TD ),
				'fields' => array(
					array(
						'title'    => __( 'Activation', APP_TD ),
						'name'     => 'features',
						'type'     => 'checkbox',
						'choices'  => wp_list_pluck( $features, 'label' ),
						'tip'      => __( 'Enable Extra Features provided by custom addons for this Listing type.', APP_TD ),
					),
				),
				'options' => $options,
			);
		}
	}

	/**
	 * Reloads page if the features list changed.
	 *
	 * @param APP_Tabs_Page $page Current settings page object.
	 */
	function features_handler( $page ) {
		$updated_features = $this->listing->options->features;

		if ( $this->enabled_features !== $updated_features ) {
			// Otherwise update page to enable updated features.
			$url = add_query_arg( 'firstrun', 1 );
			$url = esc_url_raw( $url );
			wp_redirect( $url );
			appthemes_exit();
		}
	}
}
