<?php
/**
 * Listing Plan Decorator.
 *
 * @package Listing
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * A Plan object decorator.
 *
 * Allows to plugins or modules extend standard plan object methods by using
 * APP_Listing_Plan_Decorator classes. The decorator wraps given plan object by
 * itself and can be wrapped by other decorator. Both plan object and decorators
 * are using the same interface APP_Listing_Plan_I, therefore all original plan
 * methods will be inherited by the decorator.
 *
 * Use to apply to a plan object custom properties and behaviour dynamically.
 */
abstract class APP_Listing_Plan_Decorator implements APP_Listing_Plan_I {

	/**
	 * Decorated plan object.
	 *
	 * @var APP_Listing_Plan_I
	 */
	protected $plan;

	/**
	 * The reference to the current Listing module object.
	 *
	 * @var mixed
	 */
	protected $ref;

	/**
	 * Constructs decorator object.
	 *
	 * @param APP_Listing_Plan_I $plan Decorated object.
	 * @param mixed              $ref  The reference to Listing module object.
	 */
	public function __construct( APP_Listing_Plan_I $plan, $ref = null ) {
		$this->plan = $plan;
		$this->ref  = $ref;
	}

	/**
	 * Retrieves the referenced object.
	 *
	 * @return mixed
	 */
	protected function get_ref_object() {
		return APP_Listing_Director::get( $this->ref );
	}

	/**
	 * Adds Plan specific data to a given checkout object.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object for setup.
	 * @param array                $data     Stripslashed posted data.
	 */
	public function setup( APP_Dynamic_Checkout $checkout, $data = array() ) {
		return $this->plan->setup( $checkout, $data );
	}

	/**
	 * Applies plan attributes to an order item.
	 *
	 * @param int       $item_id Listing ID.
	 * @param APP_Order $order   An Order object.
	 */
	public function apply( $item_id, APP_Order $order ) {
		return $this->plan->apply( $item_id, $order );
	}

	/**
	 * Activates plan.
	 *
	 * @param int       $item_id Listing ID.
	 * @param APP_Order $order   An Order object.
	 */
	public function activate( $item_id, APP_Order $order ) {
		return $this->plan->activate( $item_id, $order );
	}

	/**
	 * Retrieves a plan type.
	 *
	 * @return string Plan type.
	 */
	public function get_type() {
		return $this->plan->get_type();
	}

	/**
	 * Retrieves a plan title.
	 *
	 * @return string Plan title.
	 */
	public function get_title() {
		return $this->plan->get_title();
	}

	/**
	 * Retrieves a plan description.
	 *
	 * @return string Plan description.
	 */
	public function get_description() {
		return $this->plan->get_description();
	}

	/**
	 * Retrieves plan duration in days.
	 *
	 * @return int
	 */
	public function get_duration() {
		return $this->plan->get_duration();
	}

	/**
	 * Retrieves plan duration meta key.
	 *
	 * @return int
	 */
	public function get_duration_key() {
		return $this->plan->get_duration_key();
	}

	/**
	 * Retrieves plan duration in other time units, like months or years.
	 *
	 * @return int
	 */
	public function get_period() {
		return $this->plan->get_period();
	}

	/**
	 * Generates period text.
	 */
	public function get_period_text() {
		return $this->plan->get_period_text();
	}

	/**
	 * Retrieves period type (D, W, M, Y).
	 *
	 * @var string
	 */
	public function get_period_type() {
		return $this->plan->get_period_type();
	}

	/**
	 * Retrieves plan price.
	 *
	 * @return string
	 */
	public function get_price() {
		return $this->plan->get_price();
	}

	/**
	 * Renders optional plan fields
	 */
	public function render() {
		return $this->plan->render();
	}

	/**
	 * Determines whether plan has optional items to be selected by user.
	 *
	 * @return bool True if plan has options, False otherwise.
	 */
	public function has_options() {
		return $this->plan->has_options();
	}

	/**
	 * Magic method: $plan->$method()
	 *
	 * @param string $name Method name.
	 * @param array  $args Numeric array of method args.
	 *
	 * @return mixed Result of method call.
	 */
	public function __call( $name, $args ) {
		return call_user_func_array( array( &$this->plan, $name ), $args );
	}

	/**
	 * Magic method: $plan->arg
	 *
	 * @param string $key The plan property.
	 *
	 * @return mixed
	 */
	public function __get( $key ) {
		return $this->plan->$key;
	}

	/**
	 * Magic setter
	 *
	 * Doesn't allow to set magic properties by default.
	 *
	 * @param string $name  The plan property.
	 * @param string $value New value.
	 */
	public function __set( $name, $value ) {}

}
