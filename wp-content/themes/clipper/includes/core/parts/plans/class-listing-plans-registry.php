<?php
/**
 * Listing Plans Registry submodule.
 *
 * @package Listing
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Plans Registry class.
 */
class APP_Listing_Plans_Registry {

	/**
	 * An array of registered plan objects.
	 *
	 * @var array
	 */
	protected $plans = array();

	/**
	 * Current Listing module object
	 *
	 * @var APP_Listing
	 */
	protected $listing;

	/**
	 * Listing Plan metabox object.
	 *
	 * @var APP_Listing_Plan_Metabox
	 */
	protected $plan_metabox;

	/**
	 * Construct Listing Activation Plans Registry module
	 *
	 * @param APP_Listing $listing Listing module object.
	 */
	public function __construct( APP_Listing $listing ) {

		$this->listing = $listing;
		$this->listing->options->set_defaults( $this->get_defaults() );
		$this->activate_modules();
	}

	/**
	 * Retrieves module's options default values to be registered in Listing
	 * options object.
	 *
	 * @return array The Form defaults.
	 */
	public function get_defaults() {
		return array(
			'duration'     => 30,
			'duration_key' => '_listing_duration',
			'description'  => '',
			'bypass_plan'  => false,
		);
	}

	/**
	 * Activates submodules
	 */
	protected function activate_modules() {
		if ( ! $this->plan_metabox && is_admin() ) {
			$this->plan_metabox = new APP_Listing_Plan_Metabox( $this->listing );
		}
	}

	/**
	 * Decorates plan with custom behavioual objects.
	 *
	 * Allows to plugins or modules extend standard plan object methods by using
	 * APP_Listing_Plan_Decorator classes. The decorator wraps given plan object
	 * by itself and can be wrapped by other decorator. Both plan object and
	 * decorators are using the same interface APP_Listing_Plan_I, therefore all
	 * original plan methods will be inherited by the decorator.
	 *
	 * @see APP_Listing_Plan_Decorator
	 * @see APP_Listing_Plan_I
	 *
	 * @param APP_Listing_Plan_I $plan Plan object to be decorated.
	 *
	 * @return APP_Listing_Plan_I Decorated plan object.
	 */
	protected function decorate( APP_Listing_Plan_I $plan ) {

		// Decorate plan regular behaviour in a first level.
		$ref  = $this->listing->get_type();
		$plan = new APP_Listing_Regular_Plan_Decorator( $plan, $ref );

		/**
		 * Decorates a listing plan with custom behavioual objects (decorators).
		 *
		 * @see APP_Listing_Plan_Decorator
		 * @see APP_Listing_Regular_Plan_Decorator
		 *
		 * @since Listing 1.0
		 *
		 * @param APP_Listing_Plan_I $plan Decorated plan object.
		 */
		$plan = apply_filters( "appthemes_decorate_listing_plan_{$ref}", $plan );

		return $plan;
	}

	/**
	 * Register a new concrete plan object.
	 *
	 * @param APP_Listing_Plan_I $plan A plan object.
	 */
	public function add_plan( APP_Listing_Plan_I $plan ) {
		$this->plans[ $plan->get_type() ] = $plan;
	}

	/**
	 * Retrieves regular plan by given type.
	 *
	 * @param string $type The plan type.
	 *
	 * @return APP_Listing_Plan_I The plan object.
	 */
	public function get_plan( $type ) {

		if ( empty( $this->plans ) ) {
			// Add default plan if no plans registered.
			$plan = $this->_get_default_plan();

			$this->add_plan( $plan );
		}

		if ( ! isset( $this->plans[ $type ] ) ) {
			return;
		}

		return $this->decorate( $this->plans[ $type ] );
	}

	/**
	 * Retrieves all registered plan objects.
	 *
	 * @return APP_Listing_Plan_I[] The list of plan objects.
	 */
	public function get_plans() {

		if ( empty( $this->plans ) ) {
			// Add default plan if no plans registered.
			$plan = $this->_get_default_plan();

			$this->add_plan( $plan );
		}

		$types = array_keys( $this->plans );
		$plans = array_map( array( $this, 'get_plan' ), $types );

		return $plans;
	}

	/**
	 * Retrieves current plan for a given listing item.
	 *
	 * @param int $item_id Listing item ID.
	 *
	 * @return APP_Listing_Current_Plan_I Current item plan object.
	 */
	public function get_current_plan( $item_id ) {
		$type = $this->_get_last_plan_type( $item_id );

		if ( isset( $this->plans[ $type ] ) ) {
			$plan = $this->plans[ $type ];
		} else {
			$plan = $this->_get_default_plan();
		}

		$ref  = $this->listing->get_type();
		$plan = new APP_Listing_Current_Plan_Decorator( $item_id, $plan, $ref );

		/**
		 * Decorates a current listing plan with custom behavioural objects.
		 *
		 * @see APP_Listing_Plan_Decorator
		 * @see APP_Listing_Current_Plan_Decorator
		 *
		 * @since Listing 1.0
		 *
		 * @param APP_Listing_Current_Plan_I $plan Decorated plan object.
		 */
		$plan = apply_filters( "appthemes_decorate_current_listing_plan_{$ref}", $plan );

		return $plan;
	}

	/**
	 * Retrieves regular plan object associated with a listing item or order.
	 *
	 * @param int       $item_id Listing Item ID.
	 * @param APP_Order $order   Optional. Order object. Helps to find plan
	 *                            added to order as item.
	 *
	 * @return APP_Listing_Plan_I|null The plan object if it exists.
	 */
	public function get_last_plan( $item_id, APP_Order $order = null ) {
		$type = $this->_get_last_plan_type( $item_id, $order );
		$plan = $this->get_plan( $type );

		if ( ! $plan ) {
			$plan = $this->decorate( $this->_get_default_plan() );
		}

		return $plan;
	}

	/**
	 * Retrieves a default plan.
	 *
	 * Needs in cases when there are no other plans registered or listing plan
	 * no longer exists.
	 *
	 * @return APP_Listing_Plan_I The default plan object.
	 */
	protected function _get_default_plan() {
		$type = $this->listing->get_type();
		return new APP_Listing_General_Plan( $type );
	}

	/**
	 * Retrieves plan type associated with a listing item or order.
	 *
	 * @param int       $item_id Listing Item ID.
	 * @param APP_Order $order   Optional. Order object. Helps to find plan
	 *                            added to order as item.
	 *
	 * @return string The plan object type if it exists.
	 */
	protected function _get_last_plan_type( $item_id, APP_Order $order = null ) {
		// Simply get current plan from the listing item meta.
		$plan_id = get_post_meta( $item_id, '_app_plan_id', true );

		if ( $plan_id ) {
			return $plan_id;
		}

		$plans = array_keys( $this->plans );

		// Otherwise try to retrieve plan from connected order.
		// What a bad luck! There is no order. So retrieve it from connections.
		if ( ! $order ) {
			$order = appthemes_get_order_connected_to( $item_id, array(
				'connected_meta' => array(
					array(
						'key'     => 'type',
						'value'   => $plans,
						'compare' => 'IN',
					),
				),
				'post_status' => array(
					APPTHEMES_ORDER_COMPLETED,
					APPTHEMES_ORDER_ACTIVATED,
				),
			) );
		}

		if ( $order ) {

			$item_types  = wp_list_pluck( $order->get_items(), 'type' );
			$valid_plans = array_intersect( $plans, $item_types );

			if ( ! empty( $valid_plans ) ) {
				$plan_id = array_shift( $valid_plans );
				// Maybe next time will do faster.
				update_post_meta( $item_id, '_app_plan_id', $plan_id );
				return $plan_id;
			}
		}
	}
}
