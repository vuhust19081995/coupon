<?php
/**
 * Listing expiration submodule
 *
 * @package Listing\Expire
 * @author  AppThemes
 * @since   Listing 1.0
 */

define( 'APP_POST_STATUS_EXPIRED', 'expired' );

/**
 * Expire Listing processing class
 */
class APP_Listing_Expire {

	/**
	 * Current Listing module object.
	 *
	 * @var APP_Listing
	 */
	protected $listing;

	/**
	 * The limit on an items number to be processed per single cron job.
	 *
	 * @var int
	 */
	protected $limit;

	/**
	 * Construct Expire Listing module
	 *
	 * @param APP_Listing $listing Listing module object.
	 */
	public function __construct( APP_Listing $listing ) {

		$this->listing = $listing;
		$this->limit   = apply_filters( "appthemes_limit_expired_{$this->listing->get_type()}s", 100 );

		$this->init();
		add_action( "appthemes_prune_expired_{$this->listing->get_type()}s", array( $this, '_prune_expired_listings' ) );
		add_filter( "appthemes_decorate_current_listing_plan_{$this->listing->get_type()}", array( $this, '_decorate_current_plan' ), 9 );
	}

	/**
	 * Init action
	 *
	 * Called on the 'init' hook.
	 */
	public function init() {
		$this->register_post_statuses();
		$this->_schedule_listing_prune();
	}


	/**
	 * Registers extra post statuses
	 */
	protected function register_post_statuses() {

		register_post_status( APP_POST_STATUS_EXPIRED, array(
			'label'                     => _x( 'Expired', 'listing', APP_TD ),
			'public'                    => false,
			'protected'                 => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>', APP_TD ),
		) );
	}

	/**
	 * Schedules a prune expired listings event
	 */
	public function _schedule_listing_prune() {
		if ( ! wp_next_scheduled( "appthemes_prune_expired_{$this->listing->get_type()}s" ) ) {
			wp_schedule_event( time(), 'hourly', "appthemes_prune_expired_{$this->listing->get_type()}s" );
		}
	}

	/**
	 * Prunes expired listings
	 */
	public function _prune_expired_listings() {

		add_filter( 'posts_clauses', array( $this, '_expired_listing_sql' ), 10, 2 );

		$expired_posts = new WP_Query( array(
			'post_type'        => $this->listing->get_type(),
			'post_status'      => 'publish',
			'expired_listings' => true,
			'showposts'        => intval( $this->limit ),
		) );

		remove_filter( 'posts_clauses', array( $this, '_expired_listing_sql' ), 10, 2 );

		/* @var $plans_module APP_Listing_Plans_Registry */
		/* @var $plan         APP_Listing_Current_Plan_I */
		$plans_module = $this->listing->plans;
		$plan         = null;

		foreach ( $expired_posts->posts as $post ) {

			if ( $plans_module ) {
				$plan = $plans_module->get_current_plan( $post->ID );
			}

			if ( $plan ) {
				$plan->deactivate();
			}
		}

	}

	/**
	 * Retrieves SQL query clauses for expired listings
	 *
	 * @global wpdb $wpdb
	 *
	 * @param array    $clauses  The list of clauses for the query.
	 * @param WP_Query $wp_query The WP_Query instance (passed by reference).
	 *
	 * @return array Modified clauses
	 */
	public function _expired_listing_sql( $clauses, $wp_query ) {
		global $wpdb;

		if ( $wp_query->get( 'expired_listings' ) ) {
			$clauses['join'] .= ' INNER JOIN ' . $wpdb->postmeta . ' AS exp1 ON (' . $wpdb->posts . '.ID = exp1.post_id)' ;

			$clauses['where'] .= " AND ( exp1.meta_key = '" . $this->listing->options->duration_key . "' AND DATE_ADD(post_date, INTERVAL exp1.meta_value DAY) < '" . current_time( 'mysql' ) . "' AND exp1.meta_value > 0 )";
		}

		return $clauses;
	}

	/**
	 * Decorates given current plan with a expire behaviour.
	 *
	 * @param APP_Listing_Plan_I $plan Given listing plan object.
	 *
	 * @return \APP_Listing_Plan_Recurring_Decorator Decorated plan object.
	 */
	public function _decorate_current_plan( APP_Listing_Plan_I $plan ) {
		return new APP_Listing_Current_Plan_Expire_Decorator( $plan, $this->listing->get_type() );
	}
}
