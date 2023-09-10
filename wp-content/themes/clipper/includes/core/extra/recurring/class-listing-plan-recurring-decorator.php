<?php
/**
 * Listing Plan Recurring decorator
 *
 * @package Listing\Modules\Recurring
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Plan Recurring decorator class.
 */
class APP_Listing_Plan_Recurring_Decorator extends APP_Listing_Plan_Decorator {

	/**
	 * Adds Plan specific data to a given checkout object.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object for setup.
	 * @param array                $data     Stripslashed posted data.
	 */
	public function setup( APP_Dynamic_Checkout $checkout, $data = array() ) {
		// Call method for a decorated plan first!
		$this->plan->setup( $checkout, $data );

		$key = 'recurring_' . $this->get_type();

		switch ( $this->recurring ) {
			case 'optional_recurring':
				$recurring = ( isset( $data[ $key ] ) && 'recurring' === $data[ $key ] ) ? 'recurring' : 'non_recurring';
				break;

			case 'forced_recurring':
				$recurring = 'recurring';
				break;

			default:
				// non_recurring.
				$recurring = 'non_recurring';
				break;
		}

		if ( 'recurring' === $recurring ) {
			$checkout->add_data( 'recurring', true );
		} elseif ( $checkout->get_data( 'recurring' ) ) {
			$checkout->add_data( 'recurring', false );
		}
	}

	/**
	 * Applies recurring plan attributes to an order item.
	 *
	 * @param int       $item_id Listing ID.
	 * @param APP_Order $order   An Order object.
	 */
	public function apply( $item_id, APP_Order $order ) {
		// Call method for a decorated plan first!
		$this->plan->apply( $item_id, $order );

		$checkout = appthemes_get_checkout();

		if ( ! $checkout ) {
			return;
		}

		if ( $checkout->get_data( 'recurring' ) ) {
			$order->set_recurring_period( $this->get_period(), $this->get_period_type() );
		} elseif ( $order->get_recurring_period() ) {
			$order->clear_recurring_period();
		}
	}

	/**
	 * Generates period text.
	 */
	public function get_period_text() {

		if ( $this->get_duration() && 'forced_recurring' === $this->recurring ) {
			$period = sprintf( _n( 'every', 'every %d', $this->get_period(), APP_TD ), $this->get_period() );
			$period_type = appthemes_get_recurring_period_type_display( $this->get_period_type(), $this->get_period() );
			$output = sprintf( __( ' / %1$s %2$s', APP_TD ), $period, $period_type );
		} else {
			$output = $this->plan->get_period_text();
		}

		return $output;
	}

	/**
	 * Renders optional plan fields
	 */
	public function render() {
		// Call method for a decorated plan first!
		$this->plan->render();

		$recurring = $this->plan->recurring;

		if ( ! in_array( $recurring, array( 'forced_recurring', 'optional_recurring' ), true ) ) {
			return;
		}

		$name = 'recurring_' . $this->plan->get_type();
		$opts = '';

		$opts .= html( 'option', array( 'value' => 'recurring' ), __( 'Automatically', APP_TD ) );

		if ( 'optional_recurring' === $this->plan->recurring ) {
			$opts .= html( 'option', array( 'value' => 'non_recurring' ), __( 'Manually', APP_TD ) );
		}

		echo html( 'div class="addon-relist-wrap"',
			html( 'h6', __( 'Relist', APP_TD ) ),
			html( 'select', array( 'name' => $name ), $opts )
		);
	}

	/**
	 * Determines whether plan has optional items to be selected by user.
	 *
	 * @return bool True if plan has options, False otherwise.
	 */
	public function has_options() {
		$recurring = $this->plan->recurring;

		if ( $recurring === 'optional_recurring' ) {
			return true;
		}

		return $this->plan->has_options();
	}

}
