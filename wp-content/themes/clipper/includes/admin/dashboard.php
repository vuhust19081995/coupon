<?php
/**
 * Admin Dashboard.
 *
 * @package Clipper\Admin\Dashboard
 * @author  AppThemes
 * @since   Clipper 1.5.0
 */

/**
 * Theme Dashboard page.
 *
 * @since 1.5.0
 */
class CLPR_Theme_Dashboard extends APP_DashBoard {

	const SUPPORT_FORUM = 'http://forums.appthemes.com/external.php?type=RSS2';

	/**
	 * Sets up page.
	 *
	 * @return void
	 */
	public function __construct() {

		parent::__construct( array(
			'page_title' => __( 'Dashboard', APP_TD ),
			'menu_title' => __( 'Clipper', APP_TD ),
			'icon_url'   => 'dashicons-index-card',
		) );

		$this->boxes[] = array( 'stats_30_days', $this->box_icon( 'at-chart-bar' ) . __( 'Last 30 Days', APP_TD ), 'side', 'high' );
		$this->boxes[] = array( 'support_forum', $this->box_icon( 'at-discussion' ) . __( 'Forums', APP_TD ), 'normal', 'low' );

		$stats_icon = $this->box_icon( 'at-chart-pie' );
		$stats      = array( 'stats', $stats_icon . __( 'Overview', APP_TD ), 'normal' );

		array_unshift( $this->boxes, $stats );
	}

	/**
	 * Displays stats box.
	 *
	 * @return void
	 */
	public function stats_box() {

		$users       = array();
		$users_stats = $this->get_user_counts();
?>
		<div class="stats_overview">
			<h3><?php _e( 'New Registrations', APP_TD ); ?></h3>
			<div class="overview_today">
				<p class="overview_day"><?php _e( 'Today', APP_TD ); ?></p>
				<p class="overview_count"><?php echo $users_stats['today']; ?></p>
				<p class="overview_type"><em><?php _e( 'Customers', APP_TD ); ?></em></p>
			</div>

			<div class="overview_previous">
				<p class="overview_day"><?php _e( 'Yesterday', APP_TD ); ?></p>
				<p class="overview_count"><?php echo $users_stats['yesterday']; ?></p>
				<p class="overview_type"><em><?php _e( 'Customers', APP_TD ); ?></em></p>
			</div>
		</div>

<?php
		$stats    = array();
		$listings = $this->get_listing_counts();

		if ( isset( $listings['publish'] ) ) {
			$stats[ __( 'Valid Coupons', APP_TD ) ] = array(
				'text' => $listings['publish'],
				'url'  => add_query_arg( array( 'post_type' => APP_POST_TYPE, 'post_status' => 'publish' ), admin_url( 'edit.php' ) ),
			);
		} else {
			$stats[ __( 'Valid Coupons', APP_TD ) ] = 0;
		}

		if ( isset( $listings['unreliable'] ) ) {
			// $stats[ __( 'Unreliable Coupons', APP_TD ) ] = array(
			// 	'text' => $listings['unreliable'],
			// 	'url'  => add_query_arg( array( 'post_type' => APP_POST_TYPE, 'post_status' => 'unreliable' ), admin_url( 'edit.php' ) ),
			// );
		} else {
			$stats[ __( 'Unreliable Coupons', APP_TD ) ] = 0;
		}

		if ( isset( $listings['pending'] ) ) {
			$stats[ __( 'Pending Coupons', APP_TD ) ] = array(
				'text' => $listings['pending'],
				'url'  => add_query_arg( array( 'post_type' => APP_POST_TYPE, 'post_status' => 'pending' ), admin_url( 'edit.php' ) ),
			);
		} else {
			$stats[ __( 'Pending Coupons', APP_TD ) ] = 0;
		}

		$stats[ __( 'Users', APP_TD ) ] = array(
			'text' => $users_stats['total_users'],
			'url'  => 'users.php',
		);

		if ( current_theme_supports( 'app-payments' ) ) {
			$revenue = $this->get_orders_revenue();
			$stats[ __( 'Last 7 Days', APP_TD ) ] = appthemes_get_price( $revenue['week'] );
			$stats[ __( 'Overall', APP_TD ) ]     = appthemes_get_price( $revenue['total'] );
		}

		$stats[ __( 'Version', APP_TD ) ]  = CLPR_VERSION;
		$stats[ __( 'Support', APP_TD ) ]  = html( 'a', array( 'href' => 'http://forums.appthemes.com' ), __( 'Forums', APP_TD ) );
		$stats[ __( 'Support', APP_TD ) ] .= ' | ' . html( 'a', array( 'href' => 'https://docs.appthemes.com/' ), __( 'Docs', APP_TD ) );

		$this->output_list( $stats );
	}

	/**
	 * Displays charts box with stats for last 30 days.
	 *
	 * @return void
	 */
	public function stats_30_days_box() {
		echo '<div class="charts-widget">';
		clpr_dashboard_charts();
		echo '</div>';
	}

	/**
	 * Displays recent forum posts box.
	 *
	 * @return void
	 */
	public function support_forum_box() {
		echo '<div class="rss-widget">';
		wp_widget_rss_output( self::SUPPORT_FORUM, array( 'items' => 5, 'show_author' => 0, 'show_date' => 1, 'show_summary' => 1 ) );
		echo '</div>';
	}

	/**
	 * Returns/Outputs html list.
	 *
	 * @param array $items
	 * @param string $begin (optional)
	 * @param string $end (optional)
	 * @param bool $echo (optional)
	 *
	 * @return string|void
	 */
	private function output_list( $items, $begin = '<ul>', $end = '</ul>', $echo = true ) {

		$html = '';

		foreach ( $items as $title => $value ) {

			if ( is_array( $value ) ) {
				$html .= '<li>' . $title . ': <a href="' . esc_url( $value['url'] ) . '">' . $value['text'] . '</a></li>';
			} else {
				$html .= '<li>' . $title . ': ' . $value . '</li>';
			}
		}

		$html = $begin . $html . $end;
		$html = html( 'div', array( 'class' => 'stats-info' ), $html );

		if ( $echo ) {
			echo $html;
		} else {
			return $html;
		}
	}

	/**
	 * Returns an array of user counts.
	 *
	 * @return array
	 */
	private function get_user_counts() {
		global $wpdb;

		$users = (array) count_users();

		$capabilities_meta = $wpdb->prefix . 'capabilities';
		$date_today        = date( 'Y-m-d', current_time( 'timestamp' ) );
		$date_yesterday    = date( 'Y-m-d', strtotime( '-1 days', current_time( 'timestamp' ) ) );

		$users['today']          = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->users INNER JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id WHERE $wpdb->usermeta.meta_key = %s AND ($wpdb->usermeta.meta_value NOT LIKE %s) AND $wpdb->users.user_registered >= %s", $capabilities_meta, '%administrator%', $date_today ) );
		$users['yesterday']      = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM $wpdb->users INNER JOIN $wpdb->usermeta ON $wpdb->users.ID = $wpdb->usermeta.user_id WHERE $wpdb->usermeta.meta_key = %s AND ($wpdb->usermeta.meta_value NOT LIKE %s) AND $wpdb->users.user_registered BETWEEN %s AND %s", $capabilities_meta, '%administrator%', $date_yesterday, $date_today ) );

		return $users;
	}

	/**
	 * Returns an array of listing counts.
	 *
	 * @return array
	 */
	private function get_listing_counts() {

		$listings = (array) wp_count_posts( APP_POST_TYPE );
		$all      = 0;

		foreach ( (array) $listings as $type => $count ) {
			$all += $count;
		}

		$listings['all'] = $all;

		return $listings;
	}

	/**
	 * Returns an array of orders revenue.
	 *
	 * @return array
	 */
	private function get_orders_revenue() {
		global $wpdb;

		$last_week     = date( 'Y-m-d', strtotime( '-7 days', current_time( 'timestamp' ) ) );
		$revenue_total = $wpdb->get_var( "SELECT sum( m.meta_value ) FROM $wpdb->postmeta m INNER JOIN $wpdb->posts p ON m.post_id = p.ID WHERE m.meta_key = 'total_price' AND p.post_status IN ( '" . APPTHEMES_ORDER_COMPLETED . "', '" . APPTHEMES_ORDER_ACTIVATED . "' )" );
		$revenue_week  = $wpdb->get_var( $wpdb->prepare( "SELECT sum( m.meta_value ) FROM $wpdb->postmeta m INNER JOIN $wpdb->posts p ON m.post_id = p.ID WHERE m.meta_key = 'total_price' AND p.post_date >= %s AND p.post_status IN ( '" . APPTHEMES_ORDER_COMPLETED . "', '" . APPTHEMES_ORDER_ACTIVATED . "' )", $last_week ) );

		$revenue['total'] = $revenue_total;
		$revenue['week']  = $revenue_week;

		return $revenue;
	}
}
