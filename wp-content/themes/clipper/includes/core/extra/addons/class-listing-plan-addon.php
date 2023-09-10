<?php
/**
 * Listing Addon class.
 *
 * @package Listing\Modules\Addons
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * A Listing Plan Addon class.
 *
 * Unlike APP_Listing_General_Addon it uses Plan object as reference.
 * Retrieves Addon properties from the Plan meta fields.
 */
class APP_Listing_Plan_Addon extends APP_Listing_General_Addon {

	/**
	 * Construct Listing Addon object
	 *
	 * @param string             $type The addon type.
	 * @param APP_Listing_Plan_I $ref  The reference to the object that
	 *                                  contains the meta  data for the addon.
	 */
	public function __construct( $type, APP_Listing_Plan_I $ref ) {
		parent::__construct( $type, $ref );
	}

	/**
	 * Retrieves the referenced object.
	 *
	 * @return mixed
	 */
	protected function get_ref_object() {
		return $this->ref;
	}

	/**
	 * Retrieves an addon title.
	 *
	 * @return string Addon title.
	 */
	public function get_title() {
		$info = appthemes_get_addon_info( $this->get_type() );

		if ( isset( $info['title'] ) ) {
			return $info['title'];
		}
	}

	/**
	 * Retrieves addon price.
	 *
	 * @return string
	 */
	public function get_price() {
		return 0;
	}

	/**
	 * Generates purchase addon option HTML.
	 *
	 * @return string Generated HTML.
	 */
	public function get_purchase_option() {
		$checkbox = '';
		$text     = $this->get_period_text();

		$plan = $this->get_ref_object();
		$name = $this->get_type() . '_' . $plan->get_type();

		$checkbox = html( 'input', array(
			'name'     => $name,
			'type'     => 'checkbox',
			'disabled' => true,
			'checked'  => true,
		) );

		return $checkbox . $text;
	}

	/**
	 * Generates period text.
	 */
	public function get_period_text() {

		$price = appthemes_get_price( APP_Item_Registry::get_meta( $this->get_type(), 'price' ) );

		if ( ! $this->get_duration() ) {
			$output = sprintf( __( '%s is included in this plan for unlimited days.', APP_TD ), $this->get_title(), $price );
		} else {
			$period_unit = appthemes_get_recurring_period_type_display( $this->get_period_type(), $this->get_period() );
			$output = sprintf( __( '%1$s is included in this plan for %2$s %3$s.', APP_TD ),
				$this->get_title(),
				$this->get_period(),
				$period_unit
			);
		}

		return $output;
	}

	/**
	 * Adds addon to an order item.
	 *
	 * @param int       $item_id Listing ID.
	 * @param APP_Order $order   An Order object.
	 */
	public function apply( $item_id, APP_Order $order ) {
		$order->add_item( $this->get_type(), $this->get_price(), $item_id );
	}

	/**
	 * Magic method: $addon->arg
	 *
	 * @param string $key The addon property.
	 *
	 * @return mixed
	 */
	public function __get( $key ) {
		$plan = $this->get_ref_object();
		$meta_key = "{$this->get_type()}_{$key}";

		return $plan->$meta_key;
	}
}
