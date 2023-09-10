<?php
/**
 * Listing Pricing Addons submodule settings
 *
 * @package Listing\Modules\Addons\Settings
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Pricing Addons submodule class.
 */
class APP_Listing_Addons_Settings extends APP_Listing_Payments_Settings {

	/**
	 * Current Addons module object.
	 *
	 * @var APP_Listing_Addons
	 */
	protected $addons;

	/**
	 * Current settings page.
	 *
	 * @var APP_Payments_Settings_Admin
	 */
	protected $page;

	/**
	 * Construct Listing Addons Settings module.
	 *
	 * @param APP_Listing_Addons $addons  Addons module object.
	 * @param APP_Listing        $listing Listing module object.
	 */
	public function __construct( APP_Listing_Addons $addons, APP_Listing $listing ) {

		$this->addons = $addons;

		parent::__construct( $listing );
	}

	/**
	 * Registers Listing Addons settings
	 *
	 * @param APP_Payments_Settings_Admin $page Current settings page object.
	 */
	public function register( $page ) {
		global $admin_page_hooks;

		$options = $this->listing->options;
		$type    = $this->listing->get_type();
		$name    = get_post_type_object( $type )->labels->name;
		$fields  = array();

		$this->page = $page;

		$page->tabs->add_after( 'general', $type, $name );

		foreach ( $this->addons->get_addons_types() as $addon ) {
			$fields = array_merge( $fields, self::generate_fields( $addon ) );
		}

		$page->tab_sections[ $type ]['featured'] = array(
			'title'    => __( 'Listing Add-ons', APP_TD ),
			'renderer' => array( $this, 'render' ),
			'fields'   => $fields,
			'options'  => $options,
		);

		if ( isset( $_GET['tab'] ) && $type === $_GET['tab'] ) { // input var okay.
			add_action( 'admin_enqueue_scripts', array( 'APP_Listing_Period_Fields', 'enqueue_scripts' ) );
			add_action( 'tabs_' . $admin_page_hooks['app-payments'] . '_page_app-payments-settings_form_handler', array( $this, 'form_handler' ) );
		}
	}

	/**
	 * Section renderer.
	 *
	 * @param array $section Section parameters.
	 */
	public function render( $section ) {

		$columns = array(
			'type'        => __( 'Type', APP_TD ),
			'enabled'     => __( 'Enabled', APP_TD ),
			'price'       => __( 'Price', APP_TD ),
			'period'      => __( 'Duration', APP_TD ),
			'period_type' => __( 'Period Type', APP_TD ),
			'duration'    => '',
		);

		$header = '';
		foreach ( $columns as $label ) {
			$header .= html( 'th', $label );
		}

		$rows = '';
		foreach ( $this->addons->get_addons_types() as $addon ) {
			$info = appthemes_get_addon_info( $addon );
			$row  = '';

			foreach ( self::generate_fields( $addon ) as $field ) {
				$row .= html( 'td', $this->page->input( $field, $section['options']->get() ) );
			}

			$rows .= html( 'tr', $row );
		}

		echo html( 'table id="featured-pricing" class="widefat"', html( 'tr', $header ), html( 'tbody', $rows ) );
	}

	/**
	 * Form handler.
	 *
	 * @param APP_Payments_Settings_Admin $page Current settings page object.
	 */
	public function form_handler( $page ) {

		if ( ! isset( $_POST['addons'] ) ) { // input var okay. nonce verified.
			return;
		}

		$data = $this->listing->options->addons;

		foreach ( $this->addons->get_addons_types() as $addon ) {
			$addon_data =& $data[ $addon ];

			if ( empty( $_POST['addons'][ $addon ] ) || ! $addon_data['enabled'] ) { // input var okay.
				continue;
			}

			APP_Listing_Period_Fields::set_values(
				$addon_data['period_type'],
				$addon_data['period'],
				$addon_data['duration']
			);
		}
		$this->listing->options->addons = $data;
	}

	/**
	 * Generates Addon fields set.
	 *
	 * @param string $addon Addon type.
	 *
	 * @return array Generated fields array.
	 */
	protected static function generate_fields( $addon ) {

		return array(
			array(
				'type' => 'text',
				'name' => array( 'addons', $addon, 'title' ),
			),
			array(
				'type' => 'checkbox',
				'name' => array( 'addons', $addon, 'enabled' ),
				'desc' => __( 'Yes', APP_TD ),
			),
			array(
				'type'     => 'text',
				'name'     => array( 'addons', $addon, 'price' ),
				'sanitize' => 'appthemes_absfloat',
				'extra'    => array( 'size' => 3 ),
			),
			array(
				'title'    => __( 'Duration', APP_TD ),
				'type'     => 'number',
				'name'     => array( 'addons', $addon, 'period' ),
				'sanitize' => 'absint',
				'extra'    => array(
					'id'    => $addon . '_period',
					'class' => 'small-text',
					'min'   => 0,
					'max'   => 90,
				),
			),
			array(
				'title'  => __( 'Period Type', APP_TD ),
				'type'   => 'select',
				'name'   => array( 'addons', $addon, 'period_type' ),
				'values' => APP_Listing_Period_Fields::pluck( 'title' ),
				'extra'  => array(
					'class'            => 'period_type',
					'data-period-item' => $addon,
				),
			),
			array(
				'type'     => 'hidden',
				'name'     => array( 'addons', $addon, 'duration' ),
				'sanitize' => 'absint',
				'extra' => array(
					'id' => $addon . '_duration',
				),
			),
		);
	}

}
