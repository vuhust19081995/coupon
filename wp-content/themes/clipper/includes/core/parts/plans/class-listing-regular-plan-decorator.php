<?php
/**
 * Regular Listing Plan Decorator.
 *
 * @package Listing
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * A Regular Plan object decorator class.
 *
 * This decorator makes plan object behave as ready for purchase and activation.
 * Specified plan options will be applied to listing item.
 */
class APP_Listing_Regular_Plan_Decorator extends APP_Listing_Plan_Decorator {

	/**
	 * Activates plan.
	 *
	 * @param int       $item_id Listing ID.
	 * @param APP_Order $order   An Order object.
	 */
	public function activate( $item_id, APP_Order $order ) {
		$this->plan->activate( $item_id, $order );

		$duration = absint( $this->get_duration() );
		$meta_key = $this->get_duration_key();

		wp_update_post( array(
			'ID'        => $item_id,
			'post_date' => current_time( 'mysql' ),
		) );

		update_post_meta( $item_id, $meta_key, $duration );
		update_post_meta( $item_id, '_app_plan_id', $this->get_type() );
	}

	/**
	 * Generates period text.
	 */
	public function get_period_text() {

		if ( $this->get_duration() ) {
			$period_disp = appthemes_get_recurring_period_type_display( $this->get_period_type(), $this->get_period() );
			$output = sprintf( __( ' / %1$s %2$s', APP_TD ), $this->get_period(), $period_disp );
		} else {
			$output = $this->plan->get_period_text();
		}

		return $output;
	}

}
