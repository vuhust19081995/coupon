<?php
/**
 * Listing Addon class.
 *
 * @package Listing\Modules\Addons
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * A Listing Addon class that used in context of APP_Listing_Current_Plan_I plan
 * type.
 *
 * Uses Current Listing module type as reference to object. Retrieves Addon
 * properties from the Listing Options object.
 */
class APP_Listing_Purchased_Addon extends APP_Listing_General_Addon {

	/**
	 * Related plan object if exists.
	 *
	 * @var APP_Listing_Current_Plan_I
	 */
	protected $connected;

	/**
	 * Retrieves addon duration in days.
	 *
	 * @return int
	 */
	public function get_duration() {
		$item_id = $this->connected->get_item_id();
		return  appthemes_get_addon_duration( $item_id, $this->get_type() );
	}

	/**
	 * Retrieves addon duration in other time units, like months or years.
	 *
	 * @return int
	 */
	public function get_period() {
		return $this->get_duration();
	}


	/**
	 * Retrieves period type (D, W, M, Y).
	 *
	 * @var string
	 */
	public function get_period_type() {
		return 'D';
	}

	/**
	 * Generates period text.
	 */
	public function get_period_text() {

		$expiration_date = appthemes_get_addon_end_date( $this->connected->get_item_id(), $this->get_type(), true );

		if ( $expiration_date ) {
			$expiration_date = appthemes_display_date( $expiration_date, 'date' );
			$output = sprintf( __( ' %1$s until %2$s', APP_TD ), $this->get_title(), $expiration_date );
		} else {
			$output = sprintf( __( ' %s for Unlimited days', APP_TD ), $this->get_title() );
		}

		return $output;
	}

	/**
	 * Generates purchase addon option HTML.
	 *
	 * @return string Generated HTML.
	 */
	public function get_purchase_option() {

		$text     = $this->get_period_text();
		$name     = $this->get_type() . '_' . $this->connected->get_type();
		$checkbox = html( 'input', array(
			'name'     => $name,
			'type'     => 'checkbox',
			'disabled' => true,
			'checked'  => true,
		) );

		return $checkbox . $text;
	}

	/**
	 * Adds addon to an order item.
	 *
	 * @param int       $item_id Listing ID.
	 * @param APP_Order $order   An Order object.
	 */
	public function apply( $item_id, APP_Order $order ) {}

	/**
	 * Activates addon.
	 *
	 * @param int $item_id Listing ID.
	 */
	public function activate( $item_id ) {}
}
