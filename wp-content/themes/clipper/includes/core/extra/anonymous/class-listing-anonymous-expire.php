<?php
/**
 * Anonymous user expiration submodule
 *
 * @package Listing\Modules\Anonymous
 * @author  AppThemes
 * @since   Listing 2.0
 */

/**
 * Expire Anonymous processing class
 */
class APP_Listing_Anonymous_Expire {

	/**
	 * Current Listing module object.
	 *
	 * @var APP_Listing
	 */
	protected $listing;

	/**
	 * The main module object.
	 *
	 * @var APP_Listing_Anonymous
	 */
	protected $anonymous;

	/**
	 * The limit on an items number to be processed per single cron job.
	 *
	 * @var int
	 */
	protected $limit;

	/**
	 * Construct Expire Anonymous module
	 *
	 * @param APP_Listing $listing Listing module object.
	 * @param string      $module  The main module handle.
	 */
	public function __construct( $listing, $module = 'anonymous' ) {

		if ( $listing instanceof APP_Listing && $listing->{$module} instanceof APP_Listing_Anonymous ) {
			$this->listing   = $listing;
			$this->anonymous = $listing->{$module};
			$this->limit     = apply_filters( "appthemes_limit_expired_{$this->anonymous->get_nickname()}", 100 );
			$this->_schedule_listing_prune();

			add_action( "appthemes_prune_expired_{$this->anonymous->get_nickname()}", array( $this, '_prune_expired_items' ) );
		}
	}

	/**
	 * Schedules a prune expired listings event
	 */
	public function _schedule_listing_prune() {
		if ( ! wp_next_scheduled( "appthemes_prune_expired_{$this->anonymous->get_nickname()}" ) ) {
			wp_schedule_event( time(), 'hourly', "appthemes_prune_expired_{$this->anonymous->get_nickname()}" );
		}
	}

	/**
	 * Prunes expired listings
	 */
	public function _prune_expired_items() {
		$args = array(
			'number'     => $this->limit,
			'fields'     => array(
				'ID',
			),
			'meta_query' => array(
				array(
					'key'     => 'nickname',
					'value'   => $this->anonymous->get_nickname(),
					'compare' => '=',
				),
			),
			'date_query' => array(
				array(
					'before' => '2 weeks ago',
				),
			),
		);

		$users = get_users( $args );
		$users = wp_list_pluck( $users, 'ID' );

		foreach ( $users as $user_id ) {
			$this->anonymous->delete_user( $user_id );
		}
	}
}
