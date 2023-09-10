<?php
/**
 * Listing Addon class.
 *
 * @package Listing\Modules\Addons
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * A Basic Listing Addon class.
 *
 * Uses Listing module type as reference to object. Retrieves Addon properties
 * from the Listing Options object.
 */
class APP_Listing_General_Addon implements APP_Listing_Addon_I {

	/**
	 * The Addon type.
	 *
	 * @var string
	 */
	protected $type;

	/**
	 * The reference to the object that contains the meta data for the addon.
	 *
	 * @var mixed
	 */
	protected $ref;

	/**
	 * Related plan object if exists.
	 *
	 * @var APP_Listing_Plan_I
	 */
	protected $connected;

	/**
	 * Construct Listing Addon object
	 *
	 * @param string $type The addon type.
	 * @param mixed  $ref  The reference to the object that contains the meta
	 *                      data for the addon.
	 */
	public function __construct( $type, $ref ) {
		$this->type = $type;
		$this->ref  = $ref;
	}

	/**
	 * Connect Plan object to Addon.
	 *
	 * @param APP_Listing_Plan_I $plan The Plan object.
	 */
	public function connect( APP_Listing_Plan_I $plan ) {
		$this->connected = $plan;
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
	 * Retrieves an addon type.
	 *
	 * @return string Addon type.
	 */
	public function get_type() {
		return $this->type;
	}

	/**
	 * Retrieves an addon title.
	 *
	 * @return string Addon title.
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Retrieves addon duration in days.
	 *
	 * @return int
	 */
	public function get_duration() {
		return $this->duration;
	}

	/**
	 * Retrieves addon duration in other time units, like months or years.
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

		$price = appthemes_get_price( APP_Item_Registry::get_meta( $this->get_type(), 'price' ) );

		if ( ! $this->get_duration() ) {
			$period_disp = __( 'unlimited days', APP_TD );
		} else {
			$period_unit = appthemes_get_recurring_period_type_display( $this->get_period_type(), $this->get_period() );
			$period_disp = sprintf( __( '%1$s %2$s', APP_TD ), $this->get_period(), $period_unit );
		}

		return sprintf( __( '%1$s - %2$s / %3$s', APP_TD ), $this->get_title(), $price, $period_disp );
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
	 * Retrieves addon price.
	 *
	 * @return string
	 */
	public function get_price() {
		return $this->price;
	}

	/**
	 * Generates purchase addon option HTML.
	 *
	 * @return string Generated HTML.
	 */
	public function get_purchase_option() {
		$checkbox = '';
		$text     = $this->get_period_text();
		$name     = $this->get_type();

		if ( ! empty( $this->connected ) ) {
			$name = $this->get_type() . '_' . $this->connected->get_type();
		}

		$checkbox = html( 'input', array(
			'name'     => $name,
			'type'     => 'checkbox',
		) );

		return $checkbox . $text;
	}

	/**
	 * Adds addon to an order item.
	 *
	 * @param int       $item_id Listing ID.
	 * @param APP_Order $order   An Order object.
	 */
	public function apply( $item_id, APP_Order $order ) {

		$checkout = appthemes_get_checkout();
		$addons   = (array) $checkout->get_data( 'addons' );

		// Check if addon was added by user.
		if ( in_array( $this->get_type(), $addons, true ) ) {
			$order->add_item( $this->get_type(), $this->get_price(), $item_id, true );
		}
	}

	/**
	 * Activates addon.
	 *
	 * @param int $item_id Listing ID.
	 */
	public function activate( $item_id ) {
		appthemes_remove_addon( $item_id, $this->get_type() );
		appthemes_add_addon( $item_id, $this->get_type(), $this->get_duration() );
	}

	/**
	 * Magic method: $addon->arg
	 *
	 * @param string $key The addon property.
	 *
	 * @return mixed
	 */
	public function __get( $key ) {
		$listing = $this->get_ref_object();

		if ( ! $listing->options ) {
			return null;
		}

		$options = $listing->options->addons;
		$addon   = $this->get_type();

		if ( ! $options || ! isset( $options[ $addon ] ) ) {
			return null;
		}

		if ( isset( $options[ $addon ][ $key ] ) ) {
			return $options[ $addon ][ $key ];
		}

	}

	/**
	 * Magic setter
	 *
	 * Doesn't allow to set magic properties by default.
	 *
	 * @param string $name  The addon property.
	 * @param string $value New value.
	 */
	public function __set( $name, $value ) {}

}
