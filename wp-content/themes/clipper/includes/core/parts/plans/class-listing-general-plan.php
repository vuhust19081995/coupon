<?php
/**
 * Listing General Plan.
 *
 * @package Listing
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * A Basic Listing Plan class.
 *
 * Uses Listing module type as reference to object. Retrieves Plan properties
 * from the Listing Options object.
 */
class APP_Listing_General_Plan implements APP_Listing_Plan_I {

	/**
	 * The plan type.
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * The reference to the object that contains the meta data for the plan.
	 *
	 * @var mixed
	 */
	protected $ref;

	/**
	 * Construct Listing Plan object
	 *
	 * @param string $type The plan type (or ID).
	 * @param mixed  $ref  The reference to the object that contains the meta
	 *                      data for the plan.
	 */
	public function __construct( $type, $ref = null ) {

		if ( ! $ref ) {
			$ref = $type;
		}

		$this->type = $type;
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
	 * Retrieves a plan type.
	 *
	 * @return string Plan type.
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Retrieves a plan title.
	 *
	 * @return string Plan title.
	 */
	public function get_title() {
		$listing   = $this->get_ref_object();
		$post_type = get_post_type_object( $listing->get_type() );

		return $post_type->labels->singular_name;
	}

	/**
	 * Retrieves a plan description.
	 *
	 * @return string Plan description.
	 */
	public function get_description() {
		$content = apply_filters( 'the_content', $this->description );
		return str_replace( ']]>', ']]&gt;', $content );
	}

	/**
	 * Retrieves plan duration in days.
	 *
	 * @return int
	 */
	public function get_duration() {
		return $this->duration;
	}

	/**
	 * Retrieves plan duration meta key.
	 *
	 * @return int
	 */
	public function get_duration_key() {

		$key = $this->duration_key;

		if ( ! $key ) {
			$key = '_listing_duration';
		}

		return $key;
	}

	/**
	 * Retrieves plan duration in other time units, like months or years.
	 *
	 * @return int
	 */
	public function get_period() {

		$period = $this->period;

		if ( ! $period ) {
			$period = $this->get_duration();
		}

		return $period;
	}

	/**
	 * Generates period text.
	 */
	public function get_period_text() {
		return __( 'Unlimited', APP_TD );
	}

	/**
	 * Retrieves period type (D, W, M, Y).
	 *
	 * @var string
	 */
	public function get_period_type() {

		$period_type = $this->period_type;

		if ( ! $period_type ) {
			$period_type = 'D';
		}

		return $period_type;
	}

	/**
	 * Retrieves plan price.
	 *
	 * @return string
	 */
	public function get_price() {
		return 0;
	}

	/**
	 * Adds Plan specific data to a given checkout object.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object for setup.
	 * @param array                $data     Stripslashed posted data.
	 */
	public function setup( APP_Dynamic_Checkout $checkout, $data = array() ) {
		$checkout->add_data( 'plan', $this->get_type() );
	}

	/**
	 * Applies plan attributes to an order item.
	 *
	 * @param int       $item_id Listing ID.
	 * @param APP_Order $order   An Order object.
	 */
	public function apply( $item_id, APP_Order $order ) {}

	/**
	 * Activates plan.
	 *
	 * @param int       $item_id Listing ID.
	 * @param APP_Order $order   An Order object.
	 */
	public function activate( $item_id, APP_Order $order ) {}

	/**
	 * Renders optional plan fields
	 */
	public function render() {}

	/**
	 * Determines whether plan has optional items to be selected by user.
	 *
	 * @return bool True if plan has options, False otherwise.
	 */
	public function has_options() {
		return false;
	}

	/**
	 * Magic method: $plan->arg
	 *
	 * @param string $key The plan property.
	 *
	 * @return mixed
	 */
	public function __get( $key ) {
		$listing = $this->get_ref_object();
		return $listing->options->get( $key );
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
