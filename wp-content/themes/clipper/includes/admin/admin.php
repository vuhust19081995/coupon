<?php
/**
 * Clipper core admin theme functions.
 *
 * @package Clipper\Admin\Functions
 * @author  AppThemes
 * @since   Clipper 1.0
 */


add_action( 'load-post-new.php', 'clpr_disable_admin_listing_creation' );
add_action( 'load-post.php', 'clpr_disable_admin_listing_editing' );


/**
 * Disables coupon listing creation via wp-admin, and redirects user to frontend form.
 *
 * @return void
 */
function clpr_disable_admin_listing_creation() {
	// don't disable for Editors and Admins 
	if ( current_user_can( 'edit_others_posts' ) ) {
		return;
	}

	if ( ! isset( $_GET['post_type'] ) || APP_POST_TYPE != $_GET['post_type'] ) {
		return;
	}

	wp_redirect( clpr_get_submit_coupon_url( 'redirect' ) );
	exit;
}


/**
 * Disables coupon listing editing via wp-admin, and redirects user to frontend form.
 *
 * @return void
 */
function clpr_disable_admin_listing_editing() {
	// don't disable for Editors and Admins 
	if ( current_user_can( 'edit_others_posts' ) ) {
		return;
	}

	if ( ! isset( $_GET['action'] ) || 'edit' != $_GET['action'] ) {
		return;
	}

	if ( ! isset( $_GET['post'] ) || ! $post_id = absint( $_GET['post'] ) ) {
		return;
	}

	if ( APP_POST_TYPE != get_post_type( $post_id ) ) {
		return;
	}

	wp_redirect( clpr_get_edit_coupon_url( $post_id, 'redirect' ) );
	exit;
}


/**
 * Displays the charts on admin dashboard.
 *
 * @return void
 */
function clpr_dashboard_charts() {
	global $wpdb;

	$sql = "SELECT COUNT(post_title) as total, post_date FROM $wpdb->posts WHERE post_type = %s AND post_date > %s GROUP BY DATE(post_date) DESC";
	$results = $wpdb->get_results( $wpdb->prepare( $sql, APP_POST_TYPE, appthemes_mysql_date( current_time( 'mysql' ), -30 ) ) );

	$listings = array();

	// put the days and total posts into an array
	foreach ( (array) $results as $result ) {
		$the_day = date( 'Y-m-d', strtotime( $result->post_date ) );
		$listings[ $the_day ] = $result->total;
	}

	// setup the last 30 days
	for ( $i = 0; $i < 30; $i++ ) {
		$each_day = date( 'Y-m-d', strtotime( '-' . $i . ' days' ) );
		// if there's no day with posts, insert a goose egg
		if ( ! in_array( $each_day, array_keys( $listings ) ) ) {
			$listings[ $each_day ] = 0;
		}
	}

	// sort the values by date
	ksort( $listings );

	// Get sales - completed orders with a cost
	$results = array();
	$currency_symbol = '$';
	if ( current_theme_supports( 'app-payments' ) ) {
		$sql = "SELECT sum( m.meta_value ) as total, p.post_date FROM $wpdb->postmeta m INNER JOIN $wpdb->posts p ON m.post_id = p.ID WHERE m.meta_key = 'total_price' AND p.post_status IN ( '" . APPTHEMES_ORDER_COMPLETED . "', '" . APPTHEMES_ORDER_ACTIVATED . "' ) AND p.post_date > %s GROUP BY DATE(p.post_date) DESC";
		$results = $wpdb->get_results( $wpdb->prepare( $sql, appthemes_mysql_date( current_time( 'mysql' ), -30 ) ) );
		$currency_symbol = APP_Currencies::get_current_symbol();
	}

	$sales = array();

	// put the days and total posts into an array
	foreach ( (array) $results as $result ) {
		$the_day = date( 'Y-m-d', strtotime( $result->post_date ) );
		$sales[ $the_day ] = $result->total;
	}

	// setup the last 30 days
	for ( $i = 0; $i < 30; $i++ ) {
		$each_day = date( 'Y-m-d', strtotime( '-' . $i . ' days' ) );
		// if there's no day with posts, insert a goose egg
		if ( ! in_array( $each_day, array_keys( $sales ) ) ) {
			$sales[ $each_day ] = 0;
		}
	}

	// sort the values by date
	ksort( $sales );
?>

<div id="placeholder"></div>

<script type="text/javascript">
// <![CDATA[
jQuery(function () {

	var posts = [
		<?php
		foreach ( $listings as $day => $value ) {
			$sdate = strtotime( $day );
			$sdate = $sdate * 1000; // js timestamps measure milliseconds vs seconds
			$newoutput = "[$sdate, $value],\n";
			echo $newoutput;
		}
		?>
	];

	var sales = [
		<?php
		foreach ( $sales as $day => $value ) {
			$sdate = strtotime( $day );
			$sdate = $sdate * 1000; // js timestamps measure milliseconds vs seconds
			$newoutput = "[$sdate, $value],\n";
			echo $newoutput;
		}
		?>
	];


	var placeholder = jQuery("#placeholder");

	var output = [
		{
			data: posts,
			label: "<?php _e( 'New Coupons', APP_TD ); ?>",
			symbol: ''
		},
		{
			data: sales,
			label: "<?php _e( 'Total Sales', APP_TD ); ?>",
			symbol: '<?php echo $currency_symbol; ?>',
			yaxis: 2
		}
	];

	var options = {
		series: {
			lines: { show: true },
			points: { show: true }
		},
		grid: {
			tickColor:'#f4f4f4',
			hoverable: true,
			clickable: true,
			borderColor: '#f4f4f4',
			backgroundColor:'#FFFFFF'
		},
		xaxis: {
			mode: 'time',
			timeformat: "%m/%d"
		},
		yaxis: {
			min: 0
		},
		y2axis: {
			min: 0,
			tickFormatter: function(v, axis) {
				return "<?php echo $currency_symbol; ?>" + v.toFixed(axis.tickDecimals)
			}
		},
		legend: {
			position: 'nw'
		}
	};

	jQuery.plot(placeholder, output, options);

	// reload the plot when browser window gets resized
	jQuery(window).resize(function() {
		jQuery.plot(placeholder, output, options);
	});

	function showChartTooltip(x, y, contents) {
		jQuery('<div id="charttooltip">' + contents + '</div>').css( {
			position: 'absolute',
			display: 'none',
			top: y + 5,
			left: x + 5,
			opacity: 1
		} ).appendTo("body").fadeIn(200);
	}

	var previousPoint = null;
	jQuery("#placeholder").bind("plothover", function (event, pos, item) {
		jQuery("#x").text(pos.x.toFixed(2));
		jQuery("#y").text(pos.y.toFixed(2));
		if (item) {
			if (previousPoint != item.datapoint) {
				previousPoint = item.datapoint;

				jQuery("#charttooltip").remove();
				var x = new Date(item.datapoint[0]), y = item.datapoint[1];
				var xday = x.getDate(), xmonth = x.getMonth()+1; // jan = 0 so we need to offset month
				showChartTooltip(item.pageX, item.pageY, xmonth + "/" + xday + " - <b>" + item.series.symbol + y + "</b> " + item.series.label);
			}
		} else {
			jQuery("#charttooltip").remove();
			previousPoint = null;
		}
	});
});
// ]]>
</script>

<?php
}

