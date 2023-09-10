<?php
/**
 * Listing Recurring Payments submodule settings
 *
 * @package Listing\Modules\Recurring\Settings
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Recurring Payments submodule class.
 */
class APP_Listing_Recurring_Settings extends APP_Listing_Payments_Settings {

	/**
	 * Current module object.
	 *
	 * @var APP_Listing_Recurring
	 */
	protected $module;

	/**
	 * Construct Listing Recurring Settings module.
	 *
	 * @param APP_Listing_Recurring $module  Recurring module object.
	 * @param APP_Listing           $listing Listing module object.
	 */
	public function __construct( APP_Listing_Recurring $module, APP_Listing $listing ) {

		$this->module = $module;

		parent::__construct( $listing );
	}

	/**
	 * Registers settings
	 *
	 * @param APP_Payments_Settings_Admin $page Current settings page object.
	 */
	public function register( $page ) {
		$options = $this->listing->options;
		$type    = $this->listing->get_type();

		$page->tab_sections[ $type ]['recurring'] = array(
			'title'    => __( 'Recurring Payments', APP_TD ),
			'fields'   => array(
				array(
					'title'  => __( 'Recurring', APP_TD ),
					'type'   => 'select',
					'name'   => 'recurring',
					'values' => array(
						'non_recurring'      => __( 'Non Recurring', APP_TD ),
						'optional_recurring' => __( 'Optional Recurring', APP_TD ),
						'forced_recurring'   => __( 'Forced Recurring', APP_TD ),
					),
				),
			),
			'options'  => $options,
		);

	}
}
