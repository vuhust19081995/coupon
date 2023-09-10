<?php
/**
 * Listing Pricing Addons submodule
 *
 * @package Listing\Modules\Addons
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Pricing Addons processing class
 *
 * Registers and activates payments addons related to given listing type
 *
 * register_addons() method is hooked the 'after_setup_theme' action and
 * registers addons in the Addons module and Payments module as well.
 * So, no need to register addons as the 'items' of Payments theme support.
 *
 * Use _get_addons_raw() method to assign Addons to the given listing type if
 * you're extending this class, or use filter 'appthemes_listing_{$type}_addons'
 * if you don't.
 *
 * Requires theme supports:
 *  - app-payments
 *  - app-addons
 */
class APP_Listing_Addons {

	/**
	 * Current Listing module object.
	 *
	 * @var APP_Listing
	 */
	protected $listing;

	/**
	 * Registered addons associated with current listing type.
	 *
	 * @var array
	 */
	protected $addons;

	/**
	 * The list of Payment Addons to be intiated by default.
	 *
	 * @var array
	 */
	protected $core_addons;

	/**
	 * Addons settings.
	 *
	 * @var type APP_Listing_Plans_Addons_Box
	 */
	public $settings;

	/**
	 * Multiple Plans Addons metabox.
	 *
	 * @var type APP_Listing_Plans_Addons_Box
	 */
	public $plan_addons;

	/**
	 * Listing Plan Addons metabox.
	 *
	 * @var type APP_Listing_Plan_Details_Addons_Box
	 */
	public $listing_addons;

	/**
	 * Construct Listing Addons module
	 *
	 * @param APP_Listing $listing Listing module object.
	 */
	public function __construct( APP_Listing $listing, $core_addons = array() ) {

		$this->listing     = $listing;
		$this->core_addons = $core_addons;
		$this->listing->options->set_defaults( $this->get_defaults() );

		$this->activate_modules();

		if ( did_action( 'init' ) ) {
			$this->register_addons();
		} else {
			add_action( 'init', array( $this, 'register_addons' ), 1000 );
		}

		add_filter( "appthemes_decorate_listing_plan_{$this->listing->get_type()}", array( $this, '_decorate_plan' ) );
		add_filter( "appthemes_decorate_current_listing_plan_{$this->listing->get_type()}", array( $this, '_decorate_current_plan' ) );
		add_filter( 'appthemes_validate_purchase_fields', array( $this, '_validate_addons' ), 10, 2 );
		add_action( 'appthemes_prune_post_addon', array( $this, '_handle_expired' ), 10, 2 );
	}

	/**
	 * Activates submodules
	 */
	protected function activate_modules() {
		if ( ! $this->settings && is_admin() ) {
			$this->settings = new APP_Listing_Addons_Settings( $this, $this->listing );
		}

		if ( ! $this->listing_addons && is_admin() ) {
			$listing_type = $this->listing->get_type();
			$this->listing_addons = new APP_Listing_Plan_Details_Addons_Box( $this, $listing_type );
		}

		if ( ! $this->plan_addons && is_admin() && $this->listing->multi_plans ) {
			$plan_ptype = $this->listing->multi_plans->get_plan_type();
			$this->plan_addons = new APP_Listing_Plans_Addons_Box( $this, $plan_ptype );
		}
	}

	/**
	 * Registers Addons in the Addons and Payments modules
	 */
	public function register_addons() {

		$this->addons = $this->_get_addons_raw();

		foreach ( $this->addons as $addon ) {
			$addon['meta'] = wp_parse_args( $addon['meta'], array( 'duration' => 0 ) );

			// Register in Addons module.
			appthemes_register_addon( $addon['type'], array(
				'title'    => $addon['title'],
				'duration' => $addon['meta']['duration'],
				'flag_key' => $addon['flag_key'],
			) );

			if ( ! $this->listing->options->addons[ $addon['type'] ]['enabled'] ) {
				continue;
			}

			// Register in Payments module.
			APP_Item_Registry::register( $addon['type'], $addon['title'], $addon['meta'] );
		}
	}

	/**
	 * Retrieves an array of all Addons associated with given post type
	 *
	 * @return array
	 */
	public function _get_addons_raw() {

		// Compare stored addons data against filtered defaults. Maybe some
		// addons have been added or removed.
		$_options = $this->listing->options->addons;
		$defaults = $this->get_defaults();
		$options  = array_intersect_key( $_options, $defaults['addons'] );
		$options  = array_merge( $defaults['addons'], $options );

		if ( $options !== $_options ) {
			$this->listing->options->addons = $options;
		}

		$raw = array();

		foreach ( $options as $key => $addon ) {
			$raw[] = array(
				'type'     => $key,
				'title'    => $addon['title'],
				'flag_key' => ! empty( $addon['flag_key'] ) ? $addon['flag_key'] : '_' . $key,
				'meta'     => array(
					'price'    => $addon['price'],
					'duration' => $addon['duration'],
				),
			);
		}

		return $raw;
	}

	/**
	 * Retrieves module's options default values to be registered in Listing
	 * options object.
	 */
	public function get_defaults() {

		if ( empty( $this->core_addons ) ) {
			$this->core_addons = array(
				$this->listing->get_type() . '-featured-home' => array(
					'title' => __( 'Feature on Homepage', APP_TD ),
				),
				$this->listing->get_type() . '-featured-cat'  => array(
					'title' => __( 'Feature on Category', APP_TD ),
				),
			);
		}

		$addons = apply_filters( "appthemes_listing_{$this->listing->get_type()}_addons", $this->core_addons );

		foreach ( $addons as &$addon ) {
			$addon = wp_parse_args( $addon, array(
				'title'       => '',
				'enabled'     => 'yes',
				'price'       => 0,
				'duration'    => 30,
				'period'      => 30,
				'period_type' => 'D',
			) );
		}

		$defaults = array(
			'addons' => $addons,
		);

		return $defaults;
	}

	/**
	 * Creates new Addon object depending on given referenced object.
	 *
	 * @param string $type    Addon type.
	 * @param mixed  $ref_obj Referenced object.
	 *
	 * @return APP_Listing_Addon_I Addon object.
	 */
	public function get_addon( $type, $ref_obj = null ) {

		$options = $this->listing->options->addons;

		if ( ! isset( $options[ $type ] ) || ! $options[ $type ]['enabled'] ) {
			return;
		}

		if ( $ref_obj instanceof APP_Listing_Current_Plan_I ) {

			if ( appthemes_has_addon( $ref_obj->get_item_id(), $type ) ) {
				$addon = new APP_Listing_Purchased_Addon( $type, $this->listing->get_type() );
			} else {
				$addon = new APP_Listing_General_Addon( $type, $this->listing->get_type() );
			}
		} elseif ( $ref_obj instanceof APP_Listing_Plan_I && $ref_obj->$type ) {
			$addon = new APP_Listing_Plan_Addon( $type, $ref_obj );
		} else {
			$addon = new APP_Listing_General_Addon( $type, $this->listing->get_type() );
		}

		if ( $ref_obj instanceof APP_Listing_Plan_I ) {
			$addon->connect( $ref_obj );
		}

		return $addon;
	}

	/**
	 * Retrieves array of Addons object associated with given post type.
	 *
	 * @param mixed $ref_obj Referenced object.
	 *
	 * @return array Addons objects array.
	 */
	public function get_addons( $ref_obj = null ) {

		$addons = array();

		foreach ( $this->addons as $addon_info ) {
			$addon = $this->get_addon( $addon_info['type'], $ref_obj );

			if ( $addon instanceof APP_Listing_Addon_I ) {
				$addons[] = $addon;
			}
		}

		return $addons;
	}

	/**
	 * Retrievs an array of addons types
	 *
	 * @return array
	 */
	public function get_addons_types() {
		$addons = $this->addons;
		return wp_list_pluck( (array) $addons, 'type' );
	}

	/**
	 * Decorates given plan.
	 *
	 * @param APP_Listing_Plan_I $plan Given listing plan object.
	 *
	 * @return \APP_Listing_Plan_Addons_Decorator Decorated plan object.
	 */
	public function _decorate_plan( APP_Listing_Plan_I $plan ) {
		return new APP_Listing_Plan_Addons_Decorator( $plan, $this->listing->get_type() );
	}

	/**
	 * Decorates given plan.
	 *
	 * @param APP_Listing_Plan_I $plan Given listing plan object.
	 *
	 * @return \APP_Listing_Current_Plan_Addons_Decorator Decorated plan object.
	 */
	public function _decorate_current_plan( APP_Listing_Plan_I $plan ) {
		return new APP_Listing_Current_Plan_Addons_Decorator( $plan, $this->listing->get_type() );
	}

	/**
	 * Validates posted addons duration.
	 *
	 * @param WP_Error $errors Errors object.
	 * @param array    $data   Stripslashed posted data.
	 */
	public function _validate_addons( $errors, $data = array() ) {

		// Nothing to check if no plan posted or plan duration is unlimited.
		if ( ! isset( $data['plan'] ) ) {
			return $errors;
		}

		$plan = $this->listing->plans->get_plan( $data['plan'] );

		if ( ! $plan || ! $plan->get_duration() ) {
			return $errors;
		}

		foreach ( $this->get_addons() as $addon ) {
			$addon_key = "{$addon->get_type()}_{$data['plan']}";

			if ( empty( $data[ $addon_key ] ) ) {
				continue;
			}

			$duration = (int) $addon->get_duration();

			if ( $duration > $plan->get_duration() ) {
				$message = sprintf( __( 'Duration of the Add-On %s more than allowed by the chosen plan.', APP_TD ), $addon->get_title() );
				$errors->add( 'redundant-addon-duration', $message );
				return $errors;
			}
		}

		return $errors;
	}

	/**
	 * Triggers on addon expire.
	 *
	 * @param WP_Post $item       Listing Item.
	 * @param string  $addon_type Addon type.
	 */
	public function _handle_expired( $item, $addon_type ) {
		if ( $item->post_type !== $this->listing->get_type() ) {
			return;
		}

		$addon = $this->get_addon( $addon_type );

		if ( $addon instanceof APP_Listing_Addon_I ) {
			$addon_title = $addon->get_title();
		} else {
			$addon_title = $addon_type;
		}

		$emails = new APP_Listing_Addons_Emails( $this->listing );
		$emails->notify_user_expired_addon( $item, $addon_title );
	}

}
