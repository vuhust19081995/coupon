<?php
/**
 * Search engine.
 *
 * @package Clipper\Search
 * @author  AppThemes
 * @since   Clipper 1.0
 */


// search suggest
add_action( 'wp_ajax_nopriv_ajax-tag-search-front', 'clpr_store_suggest' );
add_action( 'wp_ajax_ajax-tag-search-front', 'clpr_store_suggest' );
// update Search Index
add_action( 'clpr_update_listing', 'appthemes_update_search_index' );


// load search filters only on frontend
if ( ! is_admin() ) {
	add_filter( 'posts_join', 'clpr_search_join' );
	add_filter( 'posts_where', 'clpr_search_where' );
	add_filter( 'posts_groupby', 'clpr_search_groupby' );
}


/**
 * AJAX auto-complete search for store names.
 *
 * @return void
 */
function clpr_store_suggest() {

	if ( 'GET' != $_SERVER['REQUEST_METHOD'] ) {
		die( json_encode( array( 'success' => false, 'message' => __( 'Sorry, only get method allowed.', APP_TD ) ) ) );
	}

	if ( ! isset( $_GET['term'] ) || ( strlen( $_GET['term'] ) < 2 ) ) {
		die( json_encode( array( 'success' => false, 'message' => __( 'Sorry, need at least two characters.', APP_TD ) ) ) );
	}

	$s = htmlentities( $_GET['term'] );

	if ( false !== strpos( $s, ',' ) ) {
		$s = explode( ',', $s );
		$s = end( $s );
	}

	$s = trim( $s );
	if ( strlen( $s ) < 2 ) {
		die( json_encode( array( 'success' => false, 'message' => __( 'Sorry, need at least two characters.', APP_TD ) ) ) );
	}

	$args = array(
		'search'     => $s,
		'number'     => 5,
		'hide_empty' => 1,
		'exclude'    => clpr_hidden_stores(),
	);
	$stores = get_terms( APP_TAX_STORE, $args );

	if ( ! $stores || ! is_array( $stores ) ) {
		die( json_encode( array( 'success' => false, 'message' => __( 'Sorry, no stores found.', APP_TD ) ) ) );
	}

	$results = array();

	foreach ( $stores as $store ) {
		$store->clpr_store_url = esc_url( clpr_get_store_meta( $store->term_id, 'clpr_store_url', true ) );
		$store->clpr_store_image_url = esc_url( clpr_get_store_image_url( $store->term_id, 'term_id', 110 ) );

		$results[] = $store;
	}

	die( json_encode( array( 'success' => true, 'items' => $results ) ) );
}


/**
 * Joins additional tables in search queries.
 *
 * @param string $join
 *
 * @return string
 */
function clpr_search_join( $join ) {
	global $wpdb, $wp_query;

	if ( is_search() && isset( $_GET['s'] ) ) {

		// Only run filter on coupon searches.
		if ( isset( $wp_query->query_vars['post_type'] ) && $wp_query->query_vars['post_type'] != APP_POST_TYPE ) {
			return $join;
		}

		if ( ! clpr_search_index_enabled() ) {
			$join  = " INNER JOIN $wpdb->term_relationships AS r ON ($wpdb->posts.ID = r.object_id) ";
			$join .= " INNER JOIN $wpdb->term_taxonomy AS x ON (r.term_taxonomy_id = x.term_taxonomy_id) ";
			$join .= " AND (x.taxonomy = '" . APP_TAX_TAG . "' OR x.taxonomy = '" . APP_TAX_CAT . "' OR x.taxonomy = '" . APP_TAX_STORE . "' OR 1=1) "; // the custom taxonomies
		}

		// if a single category is selected, limit results to that cat only
		$catid = $wp_query->query_vars['cat'];

		if ( ! empty( $catid ) ) {

			// put the catid into an array
			(array) $include_cats[] = $catid;

			// get all sub cats of catid and put them into the array
			$descendants = get_term_children( (int) $catid, $tax_cat );

			foreach( $descendants as $key => $value ) {
				$include_cats[] = $value;
			}

			// take catids out of the array and separate with commas
			$include_cats = "'" . implode( "', '", $include_cats ) . "'";

			// add the category filter to show anything within this cat or it's children
			$join .= " INNER JOIN $wpdb->term_relationships AS tr2 ON ($wpdb->posts.ID = tr2.object_id) ";
			$join .= " INNER JOIN $wpdb->term_taxonomy AS tt2 ON (tr2.term_taxonomy_id = tt2.term_taxonomy_id) ";
			$join .= " AND tt2.term_id IN ($include_cats) ";

		}


		if ( ! clpr_search_index_enabled() ) {
			$join .= " INNER JOIN $wpdb->postmeta AS m ON ($wpdb->posts.ID = m.post_id) ";
			$join .= " INNER JOIN $wpdb->terms AS t ON x.term_id = t.term_id ";
		}

		remove_filter( 'posts_join', 'clpr_search_join' );
	}

	return $join;
}


/**
 * Helper function to trim search terms.
 *
 * @param string $a
 *
 * @return string;
 */
function _clpr_trim_search_terms( $a ) {
	return trim( $a, "\"'\n\r " );
}


/**
 * Builds the WHERE part in search queries.
 *
 * @param string $where
 *
 * @return string
 */
function clpr_search_where( $where ) {
	global $wpdb, $wp_query, $clpr_options, $app_custom_fields;

	$old_where = $where; // intercept the old where statement

	if ( is_search() && isset( $_GET['s'] ) ) {

		// Only run filter on coupon searches.
		if ( isset( $wp_query->query_vars['post_type'] ) && $wp_query->query_vars['post_type'] != APP_POST_TYPE ) {
			return $where;
		}

		// put the custom fields into an array
		$customs = array();
		$customs = $app_custom_fields;

		$query = '';

		$var_q = stripslashes( $_GET['s'] );

		if ( isset( $_GET['sentence'] ) || $var_q == '' ) {
			$search_terms = array( $var_q );
		} else {
			preg_match_all( '/".*?("|$)|((?<=[\\s",+])|^)[^\\s",+]+/', $var_q, $matches );
			$search_terms = array_map( '_clpr_trim_search_terms', $matches[0] );
		}

		$n = isset( $_GET['exact'] ) ? '' : '%';
		$searchand = '';

		foreach ( (array) $search_terms as $term ) {

			$term = addslashes_gpc( $term );

			$query .= "{$searchand}(";

			if ( ! clpr_search_index_enabled() ) {
				$query .= "($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
				$query .= " OR ($wpdb->posts.post_content LIKE '{$n}{$term}{$n}')";
				$query .= " OR ((t.name LIKE '{$n}{$term}{$n}')) OR ((t.slug LIKE '{$n}{$term}{$n}'))";

// disable meta search as we will not find anything usefull there
//			foreach($customs as $custom) {
//				$query .= " OR (";
//				$query .= "(m.meta_key = '$custom')";
//				$query .= " AND (m.meta_value  LIKE '{$n}{$term}{$n}')";
//				$query .= ")";
//			}

			} else {
				$query .= "($wpdb->posts.post_content_filtered LIKE '{$n}{$term}{$n}')";
			}

			$query    .= ")";
			$searchand = ' AND ';
		}

		$term = esc_sql( $var_q );
		if ( ! isset( $_GET['sentence'] ) && count( $search_terms ) > 1 && $search_terms[0] != $var_q ) {
			if ( ! clpr_search_index_enabled() ) {
				$query .= " OR ($wpdb->posts.post_title LIKE '{$n}{$term}{$n}')";
				$query .= " OR ($wpdb->posts.post_content LIKE '{$n}{$term}{$n}')";
			} else {
				$query .= " OR ($wpdb->posts.post_content_filtered LIKE '{$n}{$term}{$n}')";
			}
		}

		if ( ! empty( $query ) ) {

			$where = " AND ({$query}) AND ($wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'unreliable') ";

			// setup the array for post types
			$post_type_array = array();

			// always include the custom post type
			$post_type_array[] = APP_POST_TYPE;

			// build the post type filter sql from the array values
			$post_type_filter = "'" . implode( "','", $post_type_array ) . "'";

			// return the post type sql to complete the where clause
			$where .= " AND ($wpdb->posts.post_type IN ($post_type_filter)) ";

		}

		remove_filter( 'posts_where', 'clpr_search_where' );
	}

	return $where;
}

/**
 * Group posts by ID on the search pages.
 *
 * @param string $groupby
 *
 * @return string
 */
function clpr_search_groupby( $groupby ) {
	global $wpdb, $wp_query;

	if ( is_search() && isset( $_GET['s'] ) ) {

		// Only run filter on coupon searches.
		if ( isset( $wp_query->query_vars['post_type'] ) && $wp_query->query_vars['post_type'] != APP_POST_TYPE ) {
			return $groupby;
		}

		$groupby = "$wpdb->posts.ID";

		remove_filter( 'posts_groupby', 'clpr_search_groupby' );
	}

	return $groupby;
}

/**
 * Only search blog posts on the front-end
 *
 * @since 2.0.0
 *
 * @param string $query The actual sql query string
 * @return string
 */
function clpr_exclude_search_types( $query ) {
	global $clpr_options;

	if ( ! $query->is_search ) {
		return $query;
	}

	// Only run filter on coupons.
	if ( isset( $query->query_vars['post_type'] ) && $query->query_vars['post_type'] == APP_POST_TYPE ) {
		return $query;
	}

	if ( is_admin() ) {
		return $query;
	}

	$post_type_array = array( 'post' );

	// Check to see if we include pages.
	if ( ! $clpr_options->search_ex_pages ) {
		$post_type_array[] = 'page';
	}

	$query->set( 'post_type', $post_type_array );

	return $query;
}
add_filter( 'pre_get_posts', 'clpr_exclude_search_types', 10, 1 );
