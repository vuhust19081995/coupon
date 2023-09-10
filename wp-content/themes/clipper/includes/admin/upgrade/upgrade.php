<?php
/**
 * Upgrade procedures.
 *
 * @package Clipper
 * @author  AppThemes
 * @since   2.0.0
 */

class CLPR_Upgrade_Post_Meta_Key extends APP_Progress_Upgrade_Step {

	/**
	 * Creates new Checkout Step instance
	 *
	 * @param string $step_id Step ID.
	 * @param array  $args    Step arguments.
	 */
	public function __construct( $step_id, $args = array() ) {

		$args = wp_parse_args( $args, array(
			'old_value' => '',
			'new_value' => '',
			'post_type' => '',
			'limit'     => 1000,
		) );

		parent::__construct( $step_id, $args );
	}

	/**
	 * Retrieves total number of items to be processed within current step.
	 *
	 * @return int
	 */
	public function get_total() {
		global $wpdb;

		$sql = "";
		$sql .= "SELECT COUNT(meta_id) pm ";
		$sql .= "FROM {$wpdb->postmeta} pm ";
		$sql .= ( $this->args['post_type'] ) ? "INNER JOIN {$wpdb->posts} ps ON ( pm.post_id = ps.ID ) " : "";
		$sql .= "WHERE ";
			$sql .= "pm.meta_key = '%s' ";
			$sql .= ( $this->args['post_type'] ) ? "AND ps.post_type = '%s'" : "";

		if ( $this->args['post_type'] ) {
			$sql = $wpdb->prepare( $sql, $this->args['old_value'], $this->args['post_type'] );
		} else {
			$sql = $wpdb->prepare( $sql, $this->args['old_value'] );
		}

		return intval( $wpdb->get_var( $sql ) );
	}

	/**
	 * Processes items.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @return int The number of actually processed items.
	 */
	public function progress( APP_Dynamic_Checkout $checkout ) {
		global $wpdb;

		$limit = $this->args['limit'];

		if ( ! $this->args['post_type'] ) {

			if ( ! $this->args['new_value'] ) {
				$sql   = "DELETE FROM {$wpdb->postmeta} WHERE meta_key = '%s' LIMIT %d";
				$count = $wpdb->query( $wpdb->prepare( $sql, $this->args['old_value'], $limit ) );
			} else {
				$sql   = "UPDATE {$wpdb->postmeta} SET meta_key = '%s' WHERE meta_key = '%s' LIMIT %d";
				$count = $wpdb->query( $wpdb->prepare( $sql, $this->args['new_value'], $this->args['old_value'], $limit ) );
			}

			return intval( $count );
		}

		$sql = "";
		$sql .= "SELECT meta_id ";
		$sql .= "FROM {$wpdb->postmeta} pm ";
		$sql .= "INNER JOIN {$wpdb->posts} ps ON ( pm.post_id = ps.ID ) ";
		$sql .= "WHERE ";
			$sql .= "pm.meta_key = '%s' AND ";
			$sql .= "ps.post_type = '%s'";
		$sql .= "LIMIT %d";

		$ids = $wpdb->get_results( $wpdb->prepare( $sql, $this->args['old_value'], $this->args['post_type'], $limit ) );
		$ids = implode( ', ', wp_list_pluck( $ids, 'meta_id' ) );

		if ( ! $ids ) {
			return 0;
		}

		if ( ! $this->args['new_value'] ) {
			$sql   = "DELETE FROM {$wpdb->postmeta} WHERE meta_id IN ( {$ids} ) ";
			$count = intval( $wpdb->query( $sql ) );
		} else {
			$sql   = "UPDATE {$wpdb->postmeta} SET meta_key = '%s' WHERE meta_id IN ( {$ids} ) ";
			$count = intval( $wpdb->query( $wpdb->prepare( $sql, $this->args['new_value'] ) ) );
		}

		return intval( $count );
	}
}

class CLPR_Add_Post_Meta extends APP_Progress_Upgrade_Step {

	/**
	 * Creates new Checkout Step instance
	 *
	 * @param string $step_id Step ID.
	 * @param array  $args    Step arguments.
	 */
	public function __construct( $step_id, $args = array() ) {

		$args = wp_parse_args( $args, array(
			'key'       => '',
			'value'     => '',
			'post_type' => '',
			'limit'     => 50,
		) );

		parent::__construct( $step_id, $args );
	}

	/**
	 * Retrieves total number of items to be processed within current step.
	 *
	 * @return int
	 */
	public function get_total() {
		global $wpdb;

		$sql = "";
		$sql .= "SELECT COUNT(ID) ps ";
		$sql .= "FROM $wpdb->posts ps ";
		$sql .= "LEFT JOIN $wpdb->postmeta pm ON (ps.ID = pm.post_id AND pm.meta_key = '%s') ";
		$sql .= "WHERE ";
			$sql .= "ps.post_type = '%s' AND ";
			$sql .= "( pm.post_id IS NULL )";

		$totalposts = intval( $wpdb->get_var( $wpdb->prepare( $sql, $this->args['key'], $this->args['post_type'] ) ) );
		return $totalposts;
	}

	/**
	 * Processes items.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @return int The number of actually processed items.
	 */
	public function progress( APP_Dynamic_Checkout $checkout ) {
		$query = new WP_Query( array(
			'post_type'      => $this->args['post_type'],
			'posts_per_page' => $this->args['limit'],
			'fields'        => 'ids',
			'meta_query'    => array(
				array(
					'key'     => $this->args['key'],
					'compare' => 'NOT EXISTS',
				),
			),
		) );

		if ( $query->post_count > 0 ) {
			foreach ( $query->posts as $k => $post_id ) {
				update_post_meta( $post_id, $this->args['key'], $this->args['value'] );
			}
		}

		return $query->post_count;
	}
}

class CLPR_Upgrade_Printable_Coupon_Images extends APP_Progress_Upgrade_Step {

	public function get_total() {
		global $wpdb;

		$term = get_term_by( 'slug', 'printable-coupon', APP_TAX_IMAGE );

		$sql = "";
		$sql .= "SELECT COUNT(ID) ps ";
		$sql .= "FROM $wpdb->posts ps ";
		$sql .= "INNER JOIN $wpdb->term_relationships tr ON (ps.ID = tr.object_id) ";
		$sql .= "WHERE 1=1 AND ";
			$sql .= "( tr.term_taxonomy_id IN (%d) ) AND ";
			$sql .= "ps.post_type = '%s'";

		$totalposts = intval( $wpdb->get_var( $wpdb->prepare( $sql, $term->term_id, APP_POST_TYPE ) ) );
		return $totalposts;
	}

	public function progress( APP_Dynamic_Checkout $checkout ) {
		global $wpdb;

		$data  = (array) $this->checkout->get_data( 'progress_data' );
		$done  = absint( $data[ $this->step_id ]['done'] );
		$limit = ( $done ) ? "$done, 50" : 50;

		// update old printable coupon images
		$term = get_term_by( 'slug', 'printable-coupon', APP_TAX_IMAGE );

		$qryToString = "SELECT $wpdb->posts.ID FROM $wpdb->posts
			INNER JOIN $wpdb->term_relationships ON ($wpdb->posts.ID = $wpdb->term_relationships.object_id)
			WHERE 1=1 AND ( $wpdb->term_relationships.term_taxonomy_id IN ($term->term_id) )
			AND $wpdb->posts.post_type = '" . APP_POST_TYPE . "' LIMIT $limit";

		$postids = $wpdb->get_col( $qryToString );

		if ( $postids ) {
			foreach ( $postids as $id ) {

				$images = get_children( array( 'post_parent' => $id, 'post_status' => 'inherit', 'numberposts' => 1, 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'ID' ) );
				if ( $images ) {
					// move over bacon
					$image = array_shift( $images );
					wp_set_object_terms( $image->ID, 'printable-coupon', APP_TAX_IMAGE, false );
				}

			}
		}

		return count( $postids );
	}

}

class CLPR_Upgrade_Unreliable_Coupons extends APP_Progress_Upgrade_Step {

	/**
	 * Retrieves total number of items to be processed within current step.
	 *
	 * @return int
	 */
	public function get_total() {
		global $wpdb;
		$totalposts = intval( $wpdb->get_var( "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = '" . APP_POST_TYPE . "'" ) );
		return $totalposts;
	}

	/**
	 * Processes items.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @return int The number of actually processed items.
	 */
	public function progress( APP_Dynamic_Checkout $checkout ) {
		global $wpdb;

		$data  = (array) $this->checkout->get_data( 'progress_data' );
		$done  = absint( $data[ $this->step_id ]['done'] );
		$limit = ( $done ) ? "$done, 100" : 100;

		$sql = "SELECT ID FROM {$wpdb->posts} WHERE post_status = 'publish' AND post_type = '" . APP_POST_TYPE . "' LIMIT $limit";
		$ids = $wpdb->get_col( $sql );

		if ( ! $ids ) {
			return 0;
		}

		foreach ( $ids as $id ) {
			$t = time();
			$votes_down    = get_post_meta( $id, 'clpr_votes_down', true );
			$votes_percent = get_post_meta( $id, 'clpr_votes_percent', true );
			$expire_date   = get_post_meta( $id, 'clpr_expire_date', true );
			if ( $expire_date != '' ) {
				$expire_date_time = strtotime( str_replace( '-', '/', $expire_date ) );
			} else {
				$expire_date_time = 0;
			}

			if ( ( $votes_percent < 50 && $votes_down != 0 ) || ( $expire_date_time < $t && $expire_date != '' ) ) {
				$wpdb->update( $wpdb->posts, array( 'post_status' => 'unreliable' ), array( 'ID' => $id ) );
			}
		}

		return count( $ids );
	}
}

class CLPR_Upgrade_Custom_Tables_1_4 extends APP_Progress_Upgrade_Step {

	public function get_total() {
		return 3;
	}

	public function progress( \APP_Dynamic_Checkout $checkout ) {
		global $wpdb;

		// remove old table indexes
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		drop_index( $wpdb->clpr_pop_daily, 'id' );
		drop_index( $wpdb->clpr_pop_total, 'id' );

		// clean extra indexes
		add_clean_index( $wpdb->clpr_storesmeta, 'stores_id' );
		add_clean_index( $wpdb->clpr_storesmeta, 'meta_key' );

		return 3;
	}

}

class CLPR_Upgrade_Theme_Options_1_5 extends APP_Progress_Upgrade_Step {

	/**
	 * Retrieves total number of items to be processed within current step.
	 *
	 * @return int
	 */
	public function get_total() {
		global $wpdb;
		$legacy_options = intval( $wpdb->get_var( "SELECT COUNT(ID) FROM $wpdb->options WHERE option_name LIKE 'clpr_%'" ) );
		return count( $legacy_options );
	}

	/**
	 * Processes items.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @return int The number of actually processed items.
	 */
	public function progress( APP_Dynamic_Checkout $checkout ) {
		global $wpdb, $clpr_options;

		$new_options = array();
		$options_to_delete = array();
		$report_options = array();

		// fields to convert from select 'yes/no' to checkbox
		$select_fields = array(
			'use_logo',
			'search_stats',
			'search_ex_pages',
			'search_ex_blog',
			'coupons_require_moderation',
			'stores_require_moderation',
			'prune_coupons',
			'reg_required',
			'coupon_edit',
			'coupon_code_hide',
			'allow_html',
			'stats_all',
			'charge_coupons',
			'captcha_enable',
			'adcode_336x280_enable',
			'disable_stylesheet',
			'debug_mode',
			'disable_wp_login',
			'remove_wp_generator',
			'remove_admin_bar',
			'new_ad_email',
			'prune_coupons_email',
			'nu_custom_email',
			'nc_custom_email',
		);

		// legacy settings
		$legacy_options = $wpdb->get_results( "SELECT * FROM $wpdb->options WHERE option_name LIKE 'clpr_%'" );

		if ( ! $legacy_options ) {
			return;
		}

		foreach ( $legacy_options as $option ) {
			$new_option_name = substr( $option->option_name, 5 );

			// skip not used options and membership entries
			if ( is_null( $clpr_options->$new_option_name ) || $new_option_name == 'options' ) {
				continue;
			}

			// convert select 'yes/no' to checkbox
			if ( in_array( $new_option_name, $select_fields ) ) {
				$option->option_value = ( $option->option_value == 'yes' ) ? 1 : 0;
			}

			// convert report options field
			if ( $new_option_name == 'rp_options' ) {
				$option->option_value = str_replace( "|", "\n", $option->option_value );
			}

			// collect report options
			if ( in_array( $new_option_name, array( 'rp_options', 'rp_registeronly', 'rp_send_email' ) ) ) {
				$report_option_name = substr( $new_option_name, 3 );
				$report_option_name = ( $report_option_name == 'options' ) ? 'post_options' : $report_option_name;
				$report_options[ $report_option_name ] = maybe_unserialize( $option->option_value );
			}

			$new_options[ $new_option_name ] = maybe_unserialize( $option->option_value );
			$options_to_delete[] = $option->option_name;
		}

		// migrate payments settings
		$new_options = array_merge( $new_options, get_option( 'payments', array() ) );
		$options_to_delete[] = 'payments';

		// migrate reports settings
		$new_options = array_merge( $new_options, array( 'reports' => $report_options ) );

		// save new options
		$new_options = array_merge( get_option( 'clpr_options', array() ), $new_options );
		update_option( 'clpr_options', $new_options );

		// delete old options
		foreach ( $options_to_delete as $option_name ) {
			delete_option( $option_name );
		}

		return count( $legacy_options );
	}

}

class CLPR_Upgrade_Expire_Date_1_5 extends APP_Progress_Upgrade_Step {

	/**
	 * Retrieves total number of items to be processed within current step.
	 *
	 * @return int
	 */
	public function get_total() {
		global $wpdb;
		$count = intval( $wpdb->get_var( "SELECT COUNT(post_id) FROM $wpdb->postmeta WHERE meta_key = 'clpr_expire_date' AND meta_value != ''" ) );
		return $count;
	}

	/**
	 * Processes items.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @return int The number of actually processed items.
	 */
	public function progress( APP_Dynamic_Checkout $checkout ) {
		global $wpdb;

		$data  = (array) $this->checkout->get_data( 'progress_data' );
		$done  = absint( $data[ $this->step_id ]['done'] );
		$limit = ( $done ) ? "$done, 100" : 100;

		$results = $wpdb->get_results( "SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_key = 'clpr_expire_date' AND meta_value != '' GROUP BY post_id LIMIT $limit" );
		if ( ! $results ) {
			return 0;
		}

		foreach ( $results as $meta ) {
			// month, day, year
			if ( ! preg_match( "/^(\d{2})-(\d{2})-(\d{4})$/", $meta->meta_value, $date_parts ) ) {
				continue;
			}

			$time = strtotime( str_replace( '-', '/', $meta->meta_value ) );
			$date = date( 'Y-m-d', $time );
			update_post_meta( $meta->post_id, 'clpr_expire_date', $date );
		}

		return count( $results );
	}
}

class CLPR_Remove_Pages_1_5 extends APP_Progress_Upgrade_Step {

	/**
	 * Retrieves total number of items to be processed within current step.
	 *
	 * @return int
	 */
	public function get_total() {
		global $wpdb;
		$count = intval( $wpdb->get_var( "SELECT COUNT(post_id) FROM $wpdb->postmeta WHERE meta_key = '_wp_page_template' AND meta_value = 'tpl-blog.php'" ) );
		return $count;
	}

	/**
	 * Processes items.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @return int The number of actually processed items.
	 */
	public function progress( APP_Dynamic_Checkout $checkout ) {
		global $wpdb;

		$results = $wpdb->get_results( "SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_key = '_wp_page_template' AND meta_value = 'tpl-blog.php' GROUP BY post_id LIMIT 100" );
		if ( ! $results ) {
			return 0;
		}

		foreach ( $results as $meta ) {
			wp_delete_post( $meta->post_id, true );
		}

		return count( $results );
	}
}

class CLPR_Convert_Reports_1_5 extends APP_Progress_Upgrade_Step {

	/**
	 * Retrieves total number of items to be processed within current step.
	 *
	 * @return int
	 */
	public function get_total() {
		global $wpdb;
		$count = intval( $wpdb->get_var( "SELECT COUNT(postID) FROM $wpdb->clpr_report" ) );
		return $count;
	}

	/**
	 * Processes items.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @return int The number of actually processed items.
	 */
	public function progress( APP_Dynamic_Checkout $checkout ) {
		global $wpdb;

		$query = "SELECT postID FROM $wpdb->clpr_report LIMIT 500";
		$post_ids = $wpdb->get_col( $query );

		if ( ! $post_ids ) {
			return 0;
		}

		foreach ( $post_ids as $post_id ) {

			$query = $wpdb->prepare( "SELECT id FROM $wpdb->clpr_report WHERE postID = %d", $post_id );
			$report_id = $wpdb->get_var( $query );
			if ( ! $report_id ) {
				continue;
			}

			$query = $wpdb->prepare( "SELECT * FROM $wpdb->clpr_report_comments WHERE reportID = %d", $report_id );
			$reports = $wpdb->get_results( $query );
			if ( ! $reports ) {
				continue;
			}

			foreach ( $reports as $report ) {
				$comment = array(
					'comment_post_ID' => $post_id,
					'comment_content' => $report->type,
					'comment_date' => date( 'Y-m-d H:i:s', $report->stamp ),
					'comment_author_IP' => $report->ip,
					'comment_author' => '',
					'comment_author_email' => '',
					'comment_author_url' => '',
					'user_id' => 0,
				);
				appthemes_create_report( $comment );
			}

			// remove records from old table
			$wpdb->delete( $wpdb->clpr_report, array( 'postID' => $post_id ), array( '%d' ) );
			$wpdb->delete( $wpdb->clpr_report_comments, array( 'reportID' => $report_id ), array( '%d' ) );
		}

		return count( $post_ids );
	}
}

class CLPR_Remove_Pages_1_6 extends APP_Progress_Upgrade_Step {

	/**
	 * Retrieves total number of items to be processed within current step.
	 *
	 * @return int
	 */
	public function get_total() {
		global $wpdb;
		$count = intval( $wpdb->get_var( "SELECT COUNT(post_id) FROM $wpdb->postmeta WHERE meta_key = '_wp_page_template' AND ( meta_value = 'tpl-submit-coupon.php' OR meta_value = 'tpl-edit-item.php' )" ) );
		return $count;
	}

	/**
	 * Processes items.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @return int The number of actually processed items.
	 */
	public function progress( APP_Dynamic_Checkout $checkout ) {
		global $wpdb;

		$results = $wpdb->get_results( "SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_key = '_wp_page_template' AND ( meta_value = 'tpl-submit-coupon.php' OR meta_value = 'tpl-edit-item.php' ) GROUP BY post_id LIMIT 100" );
		if ( ! $results ) {
			return 0;
		}

		foreach ( $results as $meta ) {
			wp_delete_post( $meta->post_id, true );
		}

		return count( $results );
	}
}

class CLPR_Upgrade_Logo_1_6 extends APP_Progress_Upgrade_Step {

	/**
	 * Retrieves total number of items to be processed within current step.
	 *
	 * @return int
	 */
	public function get_total() {
		return 1;
	}

	/**
	 * Processes items.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @return int The number of actually processed items.
	 */
	public function progress( APP_Dynamic_Checkout $checkout ) {
		global $clpr_options;

		// migrate logo options to 'custom-header' theme support
		if ( ! $clpr_options->use_logo ) {
			// logo wasn't used, use the default one
			set_theme_mod( 'header_image', 'remove-header' );
			remove_theme_mod( 'header_image_data' );
		} else if ( $clpr_options->logo_url && $importer = appthemes_get_instance( 'CLPR_Importer' ) ) {
			// create new attachment from old logo
			$attachment_id = $importer->process_attachment( $clpr_options->logo_url, 0 );
			if ( ! is_wp_error( $attachment_id ) && $attachment_attr = wp_get_attachment_image_src( $attachment_id, 'full' ) ) {
				$data = array();
				$data['url'] = esc_url_raw( $attachment_attr[0] );

				$header_image_data = (object) array(
					'attachment_id' => $attachment_id,
					'url'           => $data['url'],
					'thumbnail_url' => $data['url'],
					'height'        => $attachment_attr[2],
					'width'         => $attachment_attr[1],
				);

				update_post_meta( $attachment_id, '_wp_attachment_is_custom_header', get_stylesheet() );
				set_theme_mod( 'header_image', $data['url'] );
				set_theme_mod( 'header_image_data', $header_image_data );
				set_theme_mod( 'header_textcolor', 'blank' );
			}
		}

		return 1;
	}
}

class CLPR_Upgrade_Orders_1_6 extends APP_Progress_Upgrade_Step {

	/**
	 * Retrieves total number of items to be processed within current step.
	 *
	 * @return int
	 */
	public function get_total() {
		global $wpdb;
		$totalposts = intval( $wpdb->get_var( "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_type = 'transaction'" ) );
		return $totalposts;
	}

	/**
	 * Processes items.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @return int The number of actually processed items.
	 */
	public function progress( APP_Dynamic_Checkout $checkout ) {
		global $wpdb;

		$data  = (array) $this->checkout->get_data( 'progress_data' );
		$done  = absint( $data[ $this->step_id ]['done'] );
		$limit = ( $done ) ? "$done, 100" : 100;

		$sql = "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'transaction' LIMIT $limit";
		$ids = $wpdb->get_col( $sql );

		if ( ! $ids ) {
			return 0;
		}

		foreach ( $ids as $order_id ) {

			// updated order check
			if ( $checkout_hash = get_post_meta( $order_id, 'checkout_hash', true ) ) {
				continue;
			}

			// retrieve order object
			$order = appthemes_get_order( $order_id );
			if ( ! $order ) {
				continue;
			}

			// determine checkout type and url
			if ( $item = $order->get_item( 'coupon-listing' /*CLPR_COUPON_LISTING_TYPE*/ ) ) {
				$listing_orders_args = array(
					'connected_type' => 'order-connection' /*APPTHEMES_ORDER_CONNECTION*/,
					'connected_query' => array( 'post_status' => 'any' ),
					'connected_to' => $item['post_id'],
					'post_status' => 'any',
					'fields' => 'ids',
					'nopaging' => true,
				);
				$listing_orders = new WP_Query( $listing_orders_args );

				if ( empty( $listing_orders->posts ) || $order_id == min( $listing_orders->posts ) ) {
					$checkout_type = 'create-listing';
					$checkout_url = clpr_get_submit_coupon_url( 'raw' );
				} else {
					$checkout_type = 'renew-listing';
					$checkout_url = clpr_get_renew_coupon_url( $item['post_id'], 'raw' );
				}
			} else {
				// unknown/invalid order
				continue;
			}

			// generate new checkout hash
			$hash = substr( sha1( time() . mt_rand( 0, 1000 ) ), 0, 20 );

			// if url set, get the hash
			if ( $complete_url = get_post_meta( $order_id, 'complete_url', true ) ) {
				$parsed_url = parse_url( $complete_url );
				parse_str( $parsed_url['query'], $url_args );
				if ( ! empty( $url_args['hash'] ) ) {
					$hash = $url_args['hash'];
				}
			}

			$complete_url = add_query_arg( array( 'step' => 'order-summary', 'hash' => $hash ), $checkout_url );
			$cancel_url = add_query_arg( array( 'step' => 'gateway-select', 'hash' => $hash ), $checkout_url );

			update_post_meta( $order_id, 'complete_url', $complete_url );
			update_post_meta( $order_id, 'cancel_url', $cancel_url );
			update_post_meta( $order_id, 'checkout_type', $checkout_type );
			update_post_meta( $order_id, 'checkout_hash', $hash );

		}

		return count( $ids );
	}
}

class CLPR_Upgrade_Recaptcha_Colors_1_6_5 extends APP_Progress_Upgrade_Step {

	/**
	 * Retrieves total number of items to be processed within current step.
	 *
	 * @return int
	 */
	public function get_total() {
		return 1;
	}

	/**
	 * Processes items.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @return int The number of actually processed items.
	 */
	public function progress( APP_Dynamic_Checkout $checkout ) {
		global $clpr_options;

		// migrate recaptcha color schemes
		$light_theme = array( 'clean', 'white' );
		$dark_theme	 = array( 'blackglass', 'red' );

		if ( in_array( $clpr_options->captcha_theme, $light_theme ) ) {
			$clpr_options->captcha_theme = 'light';
		} elseif ( in_array( $clpr_options->captcha_theme, $dark_theme ) ) {
			$clpr_options->captcha_theme = 'dark';
		}

		return 1;
	}
}

class CLPR_Upgrade_Expired_Status extends APP_Progress_Upgrade_Step {

	public function get_total() {
		global $clipper;

		$expire_mod = $clipper->{APP_POST_TYPE}->expire;

		add_filter( 'posts_clauses', array( $expire_mod, '_expired_listing_sql' ), 10, 2 );

		$expired_posts = new WP_Query( array(
			'post_type'        => APP_POST_TYPE,
			'post_status'      => array( 'draft', 'unreliable', 'publish' ),
			'expired_listings' => true,
			'nopaging'         => true,
		) );

		remove_filter( 'posts_clauses', array( $expire_mod, '_expired_listing_sql' ), 10, 2 );

		return $expired_posts->post_count;
	}

	public function progress( APP_Dynamic_Checkout $checkout ) {
		global $clipper;

		$expire_mod = $clipper->{APP_POST_TYPE}->expire;

		add_filter( 'posts_clauses', array( $expire_mod, '_expired_listing_sql' ), 10, 2 );

		$expired_posts = new WP_Query( array(
			'post_type'        => APP_POST_TYPE,
			'post_status'      => array( 'draft', 'unreliable', 'publish' ),
			'expired_listings' => true,
			'posts_per_page'   => 100,
		) );

		remove_filter( 'posts_clauses', array( $expire_mod, '_expired_listing_sql' ), 10, 2 );

		foreach ( $expired_posts->posts as $post ) {
			wp_update_post( array(
				'ID'          => $post->ID,
				'post_status' => APP_POST_STATUS_EXPIRED,
			) );
		}

		return $expired_posts->post_count;
	}
}

class CLPR_Upgrade_Options_2_0_0 extends APP_Progress_Upgrade_Step {

	/**
	 * Retrieves the items array to be processed.
	 *
	 * @global scbOptions $clpr_options
	 *
	 * @return array
	 */
	protected function _get_items() {
		global $clpr_options;

		// Already cleaned, nothing to do.
		if ( ! $clpr_options->coupon_price ) {
			return array();
		}

		$items = array(
			'price'                      => $clpr_options->coupon_price,
			'charge'                     => $clpr_options->charge_coupons,
			'moderate'                   => $clpr_options->coupons_require_moderation,
			'allow_edit'                 => $clpr_options->coupon_edit,
			'allow_renew'                => $clpr_options->coupon_edit,
			'notify_new_coupon'          => $clpr_options->new_ad_email,
			'notify_user_pending_coupon' => $clpr_options->nc_custom_email,
		);

		if ( ! $clpr_options->reg_required ) {
			$items['features'][] = 'anonymous';
		}

		return $items;
	}

	/**
	 * Retrieves total number of items to be processed within current step.
	 *
	 * @return int
	 */
	public function get_total() {
		return count( $this->_get_items() );
	}

	/**
	 * Processes items.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @return int The number of actually processed items.
	 */
	public function progress( APP_Dynamic_Checkout $checkout ) {
		global $clpr_options, $clipper;

		$items = $this->_get_items();

		if ( ! empty( $items ) ) {
			/* @var $listings APP_Listing */
			$listings = $clipper->{APP_POST_TYPE};
			$listings->options->update( $items );
			// cleanup migrated options.
			$clpr_options->cleanup();
		}

		return count( $items );
	}

}

class CLPR_Upgrade_Stores_Options_2_0_0 extends CLPR_Upgrade_Options_2_0_0 {

	/**
	 * Retrieves the items array to be processed.
	 *
	 * @global scbOptions $clpr_options
	 *
	 * @return array
	 */
	protected function _get_items() {
		global $clpr_options;

		return array(
			'moderate' => $clpr_options->stores_require_moderation,
		);
	}

	/**
	 * Processes items.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @return int The number of actually processed items.
	 */
	public function progress( APP_Dynamic_Checkout $checkout ) {
		global $clipper;

		$items = $this->_get_items();

		if ( ! empty( $items ) ) {
			/* @var $listings APP_Listing */
			$listings = $clipper->{APP_TAX_STORE};
			$listings->options->update( $items );
		}

		return count( $items );
	}

}

class CLPR_Upgrade_P2P_Order_Meta extends CLPR_Upgrade_Post_Meta_Key {

	/**
	 * Retrieves total number of items to be processed within current step.
	 *
	 * @return int
	 */
	public function get_total() {
		global $wpdb;

		$sql = "";
		$sql .= "SELECT COUNT(meta_id) p2pm ";
		$sql .= "FROM {$wpdb->p2pmeta} p2pm ";
		$sql .= "INNER JOIN {$wpdb->p2p} p2p ON ( p2p.p2p_id = p2pm.p2p_id ) ";
		$sql .= "INNER JOIN {$wpdb->posts} ps ON ( p2p.p2p_to = ps.ID ) ";
		$sql .= "WHERE ";
			$sql .= "p2pm.meta_value = '%s' AND ";
			$sql .= "ps.post_type = '%s' AND ";
			$sql .= "p2p.p2p_type = '%s'";

		$total = intval( $wpdb->get_var( $wpdb->prepare( $sql, $this->args['old_value'], $this->args['post_type'], APPTHEMES_ORDER_CONNECTION ) ) );
		return $total;
	}

	/**
	 * Processes items.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @return int The number of actually processed items.
	 */
	public function progress( APP_Dynamic_Checkout $checkout ) {
		global $wpdb;

		$limit = $this->args['limit'];

		$sql = "";
		$sql .= "SELECT meta_id FROM {$wpdb->p2pmeta} p2pm ";
		$sql .= "INNER JOIN {$wpdb->p2p} p2p ON ( p2p.p2p_id = p2pm.p2p_id ) ";
		$sql .= "INNER JOIN {$wpdb->posts} ps ON ( p2p.p2p_to = ps.ID ) ";
		$sql .= "WHERE ";
			$sql .= "p2pm.meta_value = '%s' AND ";
			$sql .= "ps.post_type = '%s' AND ";
			$sql .= "p2p.p2p_type = '%s' ";
		$sql .= "LIMIT %d";

		$ids = $wpdb->get_results( $wpdb->prepare( $sql, $this->args['old_value'], $this->args['post_type'], APPTHEMES_ORDER_CONNECTION, $limit ) );
		$ids = implode( ', ', wp_list_pluck( $ids, 'meta_id' ) );

		if ( ! $ids ) {
			return 0;
		}

		$sql = "";
		$sql .= "UPDATE {$wpdb->p2pmeta} ";
		$sql .= "SET meta_value = '%s' ";
		$sql .= "WHERE meta_id IN ( {$ids} ) ";

		$count = intval( $wpdb->query( $wpdb->prepare( $sql, $this->args['new_value'] ) ) );

		return $count;
	}
}

class CLPR_Remove_Empty_Post_Meta extends CLPR_Upgrade_Post_Meta_Key {

	/**
	 * Retrieves total number of items to be processed within current step.
	 *
	 * @return int
	 */
	public function get_total() {
		global $wpdb;

		$sql = "";
		$sql .= "SELECT COUNT(meta_id) pm ";
		$sql .= "FROM {$wpdb->postmeta} pm ";
		$sql .= "INNER JOIN {$wpdb->posts} ps ON ( pm.post_id = ps.ID ) ";
		$sql .= "WHERE ";
			$sql .= "pm.meta_key = '%s' AND ";
			$sql .= "pm.meta_value = 0 AND ";
			$sql .= "ps.post_type = '%s'";

		$total = intval( $wpdb->get_var( $wpdb->prepare( $sql, $this->args['old_value'], $this->args['post_type'] ) ) );
		return $total;
	}

	/**
	 * Processes items.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @return int The number of actually processed items.
	 */
	public function progress( APP_Dynamic_Checkout $checkout ) {
		global $wpdb;

		$limit = $this->args['limit'];

		$sql = "";
		$sql .= "SELECT meta_id ";
		$sql .= "FROM {$wpdb->postmeta} pm ";
		$sql .= "INNER JOIN {$wpdb->posts} ps ON ( pm.post_id = ps.ID ) ";
		$sql .= "WHERE ";
			$sql .= "pm.meta_key = '%s' AND ";
			$sql .= "pm.meta_value = 0 AND ";
			$sql .= "ps.post_type = '%s'";
		$sql .= "LIMIT %d";

		$ids = $wpdb->get_results( $wpdb->prepare( $sql, $this->args['old_value'], $this->args['post_type'], $limit ) );
		$ids = implode( ', ', wp_list_pluck( $ids, 'meta_id' ) );

		if ( ! $ids ) {
			return 0;
		}

		$sql = "DELETE FROM {$wpdb->postmeta} WHERE meta_id IN ( {$ids} ) ";

		$count = intval( $wpdb->query( $sql ) );

		return $count;
	}
}


class CLPR_Upgrade_Checkout_Data extends APP_Progress_Upgrade_Step {

	/**
	 * Retrieves total number of items to be processed within current step.
	 *
	 * @return int
	 */
	public function get_total() {
		global $wpdb;

		$sql = "";
		$sql .= "SELECT COUNT(ID) ps ";
		$sql .= "FROM {$wpdb->posts} ps ";
		$sql .= "INNER JOIN {$wpdb->postmeta} pm ON ( pm.post_id = ps.ID ) ";
		$sql .= "WHERE ";
			$sql .= "pm.meta_key = 'checkout_type' AND ";
			$sql .= "pm.meta_value IN ('create-listing', 'renew-listing') AND ";
			$sql .= "ps.post_type = 'transaction' AND ";
			$sql .= "ps.post_status IN ('tr_pending', 'tr_completed')";

		$total = intval( $wpdb->get_var( $sql ) );
		return $total;
	}

	/**
	 * Processes items.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @global wpdb $wpdb
	 *
	 * @return int The number of actually processed items.
	 */
	public function progress( APP_Dynamic_Checkout $checkout ) {
		global $wpdb, $clipper;

		$limit = 50;
		/* @var $types_map APP_View_Process[] */
		$types_map = array(
			'create-listing' => $clipper->{APP_POST_TYPE}->view_process_new,
			'renew-listing'  => $clipper->{APP_POST_TYPE}->view_process_renew,
		);

		$sql = "";
		$sql .= "SELECT post_id, meta_value ";
		$sql .= "FROM {$wpdb->postmeta} pm ";
		$sql .= "INNER JOIN {$wpdb->posts} ps ON ( pm.post_id = ps.ID ) ";
		$sql .= "WHERE ";
			$sql .= "pm.meta_key = 'checkout_type' AND ";
			$sql .= "pm.meta_value IN ('create-listing', 'renew-listing') AND ";
			$sql .= "ps.post_type = 'transaction' AND ";
			$sql .= "ps.post_status IN ('tr_pending', 'tr_completed') ";
		$sql .= "LIMIT %d";

		$orders = $wpdb->get_results( $wpdb->prepare( $sql, $limit ) );

		foreach ( $orders as $order_info ) {
			$type  = $order_info->meta_value;
			$order = appthemes_get_order( $order_info->post_id );
			$hash  = get_post_meta( $order->get_id(), 'checkout_hash', true );
			$item  = $this->_get_order_listing_item( $order );
			$plan  = $item->post_type;
			$proc  = $types_map[ $type ];

			$checkout = new APP_Dynamic_Checkout( $proc->get_checkout_type(), $hash );

			update_post_meta( $order->get_id(), 'checkout_type', $proc->get_checkout_type() );

			if ( 'tr_pending' === $order->get_status() ) {
				$expire = 14 * DAY_IN_SECONDS;
				$steps_completed = array( 'create-order' );

				$query_args = array(
					'step' => 'gateway-process',
					'hash' => $hash,
				);

				$url = add_query_arg( $query_args, $proc->get_process_url( $item->ID ) );
				update_post_meta( $order->get_id(), 'complete_url', $url );

				$query_args = array(
					'step' => 'gateway-select',
					'hash' => $hash,
				);

				$url = add_query_arg( $query_args, $proc->get_process_url( $item->ID ) );
				update_post_meta( $order->get_id(), 'cancel_url', $url );
			} else {
				$expire = 0;
				$steps_completed = array( 'gateway-process' );

				$query_args = array(
					'step' => 'activate-order',
					'hash' => $hash,
				);

				$url = add_query_arg( $query_args, $proc->get_process_url( $item->ID ) );
				update_post_meta( $order->get_id(), 'activate_url', $url );
			}

			$checkout->set_expiration( $expire );
			$checkout->add_data( 'checkout_type', $proc->get_checkout_type() );
			$checkout->add_data( 'listing_id', $item->ID );
			$checkout->add_data( 'order_id', $order->get_id() );
			$checkout->add_data( 'plan', $plan );
			$checkout->add_data( 'step-completed', $steps_completed );

			$connection = $proc->get_connection_type();

			update_post_meta( $item->ID, $connection, $hash );
		}

		return count( $orders );
	}

	/**
	 * Retrieves a listing item connected to given order.
	 *
	 * @param APP_Order $order
	 * @return WP_Post
	 */
	private function _get_order_listing_item( $order ) {
		foreach ( $order->get_items() as $item ) {
			if ( isset( $item['post'] ) && 'coupon' === $item['post']->post_type ) {
				return $item['post'];
			}
		}
	}
}

class CLPR_Delete_Expired_Transients extends APP_Progress_Upgrade_Step {

	/**
	 * Retrieves total number of items to be processed within current step.
	 *
	 * @return int
	 */
	public function get_total() {
		global $wpdb;
		$time = time();
		$sql = "SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE '\_transient\_timeout\_%' and option_value < '{$time}'";

		$totalposts = intval( $wpdb->get_var( $sql ) );
		return $totalposts;
	}

	/**
	 * Processes items.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @global wpdb $wpdb
	 *
	 * @return int The number of actually processed items.
	 */
	public function progress( APP_Dynamic_Checkout $checkout ) {
		global $wpdb;

		$limit = 1000;
		$time  = time();

		$sql = "";
		$sql .= "SELECT option_id ";
		$sql .= "FROM {$wpdb->options} ";
		$sql .= "WHERE ";
			$sql .= "option_name LIKE '\_transient\_timeout\_%' AND ";
			$sql .= "option_value < '{$time}' ";
		$sql .= "LIMIT {$limit}";

		$results = $wpdb->get_results( $sql );
		$ids     = implode( ', ', wp_list_pluck( $results, 'option_id' ) );
		if ( ! $ids ) {
			return 0;
		}

		$sql = "";
		$sql .= "DELETE FROM t1, t2 ";
		$sql .= "USING {$wpdb->options} t1 ";
		$sql .= "LEFT JOIN {$wpdb->options} t2 ON t2.option_name = REPLACE(t1.option_name, '_timeout', '') ";
		$sql .= "WHERE t1.option_id IN ( {$ids} ) ";

		$wpdb->query( $sql );

		return count( $results );
	}
}

class CLPR_Migrate_Pages_2_0_1 extends APP_Progress_Upgrade_Step {

	/**
	 * Retrieves total number of items to be processed within current step.
	 *
	 * @return int
	 */
	public function get_total() {
		global $wpdb;
		$count = intval( $wpdb->get_var( "SELECT COUNT(post_id) FROM $wpdb->postmeta WHERE meta_key = '_wp_page_template' AND meta_value = 'page-templates/template-full-width.php'" ) );
		return $count;
	}

	/**
	 * Processes items.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @return int The number of actually processed items.
	 */
	public function progress( APP_Dynamic_Checkout $checkout ) {
		global $wpdb;

		$results = $wpdb->get_results( "SELECT post_id, meta_value FROM $wpdb->postmeta WHERE meta_key = '_wp_page_template' AND meta_value = 'page-templates/template-full-width.php' GROUP BY post_id LIMIT 100" );
		if ( ! $results ) {
			return 0;
		}

		foreach ( $results as $meta ) {
			update_post_meta( $meta->post_id, '_wp_page_template', 'tpl-full-width.php' );
		}

		return count( $results );
	}
}