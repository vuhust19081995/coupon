<?php

/**
 * Utility class to work with Listing Periods
 */
class APP_Listing_Period_Fields {

	/**
	 * Retrieves full array of periods and their data
	 *
	 * @return array An array of periods and their data.
	 */
	public static function get_periods_list() {
		return array(
			APP_Order::RECUR_PERIOD_TYPE_DAYS   => array(
				'title' => __( 'Days', APP_TD ),
				'days'  => 1,
				'max'   => 90,
			),
			APP_Order::RECUR_PERIOD_TYPE_MONTHS => array(
				'title' => __( 'Months', APP_TD ),
				'days'  => 30,
				'max'   => 24,
			),
			APP_Order::RECUR_PERIOD_TYPE_YEARS  => array(
				'title' => __( 'Years', APP_TD ),
				'days'  => 365,
				'max'   => 5,
			),
		);
	}

	/**
	 * Pluck a certain field out of each period item in a list.
	 *
	 * @param int|string $field Field from the period item to place instead of
	 *                           the entire item.
	 *
	 * @return array Array of found values.
	 */
	public static function pluck( $field ) {
		$periods = self::get_periods_list();

		return wp_list_pluck( self::get_periods_list(), $field );
	}

	/**
	 * Retrieves the certain field by given period type.
	 *
	 * @param string $type  Period type.
	 * @param string $field Field name.
	 *
	 * @return mixed Value of the period item's field.
	 */
	public static function get_period_field( $type, $field ) {
		$periods = self::get_periods_list();
		$result  = null;

		if ( isset( $periods[ $type ] ) && isset( $periods[ $type ][ $field ] ) ) {
			$result = $periods[ $type ][ $field ];
		}

		return $result;
	}

	/**
	 * Converts period value to days by given period type
	 *
	 * @param string|int $val  Period value.
	 * @param string     $type Period type.
	 *
	 * @return int Converted value in days.
	 */
	public static function to_days( $val, $type ) {
		$days = self::get_period_field( $type, 'days' );

		return absint( $val ) * absint( $days );
	}

	/**
	 * Cuts period value if it's greater than max allowed.
	 *
	 * @param string|int $val  Period value.
	 * @param string     $type Period type.
	 *
	 * @return int Cutted period value.
	 */
	public static function cut_period( $val, $type ) {
		$max = self::get_period_field( $type, 'max' );

		return min( $max, absint( $val ) );
	}

	/**
	 * Correctly sets listing Period values.
	 *
	 * @param string     $type     Period type.
	 * @param string|int $period   Duration in period type untits.
	 * @param string|int $duration Duration in days.
	 */
	public static function set_values( &$type, &$period, &$duration ) {
		$period   = self::cut_period( $period, $type );
		$duration = self::to_days( $period, $type );
		$type     = $duration ? $type : APP_Order::RECUR_PERIOD_TYPE_DAYS;
	}

	/**
	 * Registers utility scripts.
	 */
	public static function enqueue_scripts() {

		wp_enqueue_script(
			'pay-periods',
			APP_LISTING_URI . '/utils/admin/scripts/periods.js',
			array( 'jquery' ),
			APP_LISTING_VERSION,
			true
		);

		wp_localize_script( 'pay-periods', 'payPeriods', self::pluck( 'max' ) );

	}

}
