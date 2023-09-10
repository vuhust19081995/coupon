<?php
/**
 * Tracking page views.
 *
 * @package Clipper\Stats
 * @author  AppThemes
 * @since   Clipper 1.0
 */


add_action( 'appthemes_after_post_content', 'clpr_add_reset_stats_link' );
add_action( 'appthemes_after_blog_post_content', 'clpr_add_reset_stats_link', 9 );

add_action( 'wp', 'clpr_cache_stats' );


/**
 * Query popular coupons & posts.
 *
 * @since 1.5
 */
class CLPR_Popular_Posts_Query extends WP_Query {

	public $stats;
	public $stats_table;
	public $today_date;

	public function __construct( $args = array(), $stats = 'total' ) {
		global $wpdb;

		$this->stats = $stats;
		$this->stats_table = ( $stats == 'today' ) ? $wpdb->clpr_pop_daily : $wpdb->clpr_pop_total;
		$this->today_date = date( 'Y-m-d', current_time( 'timestamp' ) );

		$defaults = array(
			'post_type' => APP_POST_TYPE,
			'post_status' => 'publish',
			'paged' => ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1,
			'suppress_filters' => false,
		);
		$args = wp_parse_args( $args, $defaults );

		$args = apply_filters( 'clpr_popular_posts_args', $args );

		add_filter( 'posts_join', array( $this, 'posts_join' ) );
		add_filter( 'posts_where', array( $this, 'posts_where' ) );
		add_filter( 'posts_orderby', array( $this, 'posts_orderby' ) );

		parent::__construct( $args );

		// remove filters to don't affect any other queries
		remove_filter( 'posts_join', array( $this, 'posts_join' ) );
		remove_filter( 'posts_where', array( $this, 'posts_where' ) );
		remove_filter( 'posts_orderby', array( $this, 'posts_orderby' ) );
	}

	/**
	 * Modifies JOIN part of query.
	 *
	 * @param string $sql
	 *
	 * @return string
	 */
	public function posts_join( $sql ) {
		global $wpdb;

		return $sql . " INNER JOIN $this->stats_table ON ($wpdb->posts.ID = $this->stats_table.postnum) ";
	}

	/**
	 * Modifies WHERE part of query.
	 *
	 * @param string $sql
	 *
	 * @return string
	 */
	public function posts_where( $sql ) {
		global $wpdb;

		$sql = $sql . " AND $this->stats_table.postcount > 0 ";

		if ( $this->stats == 'today' ) {
			$sql .= " AND $this->stats_table.time = '$this->today_date' ";
		}

		if ( $this->get( 'date_start' ) ) {
			$sql .= " AND $wpdb->posts.post_date > '" . $this->get( 'date_start' ) . "' ";
		}

		return $sql;
	}

	/**
	 * Modifies ORDER BY part of query.
	 *
	 * @param string $sql
	 *
	 * @return string
	 */
	public function posts_orderby( $sql ) {
		return "$this->stats_table.postcount DESC";
	}

}


/**
 * Inserts link for admin to reset stats of an coupon or post.
 * @since 1.5
 *
 * @return void
 */
function clpr_add_reset_stats_link() {
	global $clpr_options;

	if ( ! is_singular( array( APP_POST_TYPE, 'post' ) ) ) {
		return;
	}

	if ( ! $clpr_options->stats_all || ! current_theme_supports( 'app-stats' ) ) {
		return;
	}

	appthemes_reset_stats_link();
}


/**
 * Collects stats if are enabled, limits db queries.
 *
 * @return void
 */
function clpr_cache_stats() {
	global $clpr_options;

	if ( is_singular( array( APP_POST_TYPE, 'post' ) ) ) {
		return;
	}

	if ( ! $clpr_options->stats_all || ! current_theme_supports( 'app-stats' ) ) {
		return;
	}

	add_action( 'appthemes_before_loop', 'appthemes_collect_stats' );
	add_action( 'appthemes_before_search_loop', 'appthemes_collect_stats' );
	add_action( 'appthemes_before_blog_loop', 'appthemes_collect_stats' );
}


/**
 * Deletes all search stats.
 *
 * @return void
 */
function clpr_reset_search_stats() {
	global $wpdb;

	// empty both search tables
	$wpdb->query( "TRUNCATE $wpdb->clpr_search_recent ;" );
	$wpdb->query( "TRUNCATE $wpdb->clpr_search_total ;" );

}


