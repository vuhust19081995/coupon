<?php
/**
 * Affiliate links redirects.
 *
 * @package Clipper\Links
 * @author  AppThemes
 * @since   Clipper 1.1
 */


/**
 * Creates the rewrite rules for custom urls.
 *
 * @return void
 */
function clpr_rewrite_tag() {
	add_rewrite_tag( '%' . CLPR_STORE_REDIRECT_BASE_URL . '%', '([^&]+)' );
	add_rewrite_rule( '^' . CLPR_STORE_REDIRECT_BASE_URL . '([^/]*)', 'index.php?store_slug=$matches[1]', 'top' );

	add_rewrite_tag( '%' . CLPR_COUPON_REDIRECT_BASE_URL . '%', '([^&]+)' );
	add_rewrite_rule( '^' . CLPR_COUPON_REDIRECT_BASE_URL . '([^/]*)/([^/]*)', 'index.php?coupon_slug=$matches[1]&coupon_id=$matches[2]', 'top' );
}
add_action( 'init', 'clpr_rewrite_tag' );


/**
 * Redirects custom coupon/store outgoing urls.
 *
 * @param object $wp
 *
 * @return void
 */
function clpr_redirect_links( $wp ) {
	global $wp_rewrite;

	$target = null;

	if ( $wp_rewrite->using_permalinks() ) {
		if ( isset( $wp->query_vars['store_slug'] ) ) {
			$store_slug = sanitize_title( $wp->query_vars['store_slug'] );
			$target = clpr_redirect_store( $store_slug );
		}

		if ( isset( $wp->query_vars['coupon_id'] ) ) {
			$coupon_id = absint( $wp->query_vars['coupon_id'] );
			$target = clpr_redirect_coupon( $coupon_id );
		}
	} else {
		if ( ! empty( $_GET['redirect_store'] ) ) {
			$store_slug = sanitize_title( $_GET['redirect_store'] );
			$target = clpr_redirect_store( $store_slug );
		}

		if ( ! empty( $_GET['redirect_coupon'] ) ) {
			$coupon_id = absint( $_GET['redirect_coupon'] );
			$target = clpr_redirect_coupon( $coupon_id );
		}
	}

	// no redirect detected
	if ( is_null( $target ) ) {
		return;
	}

	// missing target url, go home
	if ( empty( $target ) ) {
		$target = home_url();
	}

	clpr_redirect( esc_url_raw( wp_specialchars_decode( $target ) ) );
	die();
}
add_action( 'parse_request', 'clpr_redirect_links' );


/**
 * Registers public query vars for custom urls.
 *
 * @param array $public_query_vars
 *
 * @return array
 */
function clpr_add_query_vars( $public_query_vars ) {
	$public_query_vars[] = 'store_slug';
	$public_query_vars[] = 'coupon_slug';
	$public_query_vars[] = 'coupon_id';

	return $public_query_vars;
}
add_filter( 'query_vars', 'clpr_add_query_vars' );


/**
 * Increments store clicks on affiliate link, and returns target url.
 *
 * @param string $slug
 *
 * @return string|bool
 */
function clpr_redirect_store( $slug ) {
	$term = get_term_by( 'slug', $slug, APP_TAX_STORE );
	if ( ! $term ) {
		return false;
	}

	$target = clpr_get_store_meta( $term->term_id, 'clpr_store_aff_url', true );
	if ( ! $target ) {
		$target = clpr_get_store_meta( $term->term_id, 'clpr_store_url', true );
	}

	if ( ! $target ) {
		return false;
	}

	$count = clpr_get_store_meta( $term->term_id, 'clpr_aff_url_clicks', true );
	if ( $count ) {
		$count++;
	} else {
		$count = 1;
	}

	clpr_update_store_meta( $term->term_id, 'clpr_aff_url_clicks', $count );

	return $target;
}


/**
 * Increments coupon clicks on affiliate link, and returns target url.
 *
 * @param int $post_id
 *
 * @return string|bool
 */
function clpr_redirect_coupon( $post_id ) {
	$post = get_post( $post_id );
	if ( ! $post || APP_POST_TYPE != $post->post_type ) {
		return false;
	}

	$coupon_type = clpr_get_coupon_type( $post->ID );
	if ( 'printable-coupon' === $coupon_type ) {
		$target = clpr_get_printable_coupon( $post->ID, 'large', 'url' );
	} else {
		$target = get_post_meta( $post->ID, 'clpr_coupon_aff_url', true );
	}

	if ( ! $target ) {
		$store = clpr_get_first_term( $post->ID, APP_TAX_STORE );
		if ( $store ) {
			return clpr_redirect_store( $store->slug );
		}
		return false;
	}

	$count = get_post_meta( $post->ID, 'clpr_coupon_aff_clicks', true );
	if ( $count ) {
		$count++;
	} else {
		$count = 1;
	}

	update_post_meta( $post->ID, 'clpr_coupon_aff_clicks', $count );

	return $target;
}


/**
 * Redirects to another page. wp_redirect() breaks affiliate links.
 *
 * @since 1.1
 *
 * @param string $location The path to redirect to.
 * @param int $status (optional) Status code to use.
 *
 * @return bool False if $location is not provided.
 */
function clpr_redirect( $location, $status = 301 ) {
	global $is_IIS;

	$location = apply_filters( 'wp_redirect', $location, $status );
	$status = apply_filters( 'wp_redirect_status', $status, $location );

	if ( ! $location ) {
		return false;
	}

	$location = preg_replace( '|[^a-z0-9-~+_.?#=&;,/:%!\(\)\{\}\*]|i', '', $location );

	$location = wp_kses_no_null( $location );

	$strip = array( '%0d', '%0a', '%0D', '%0A' );
	$location = _deep_replace( $strip, $location );

	if ( ! $is_IIS && php_sapi_name() != 'cgi-fcgi' ) {
		status_header( $status );
	}

	header( "Location: $location", true, $status );
}


/**
 * Returns store outgoing url.
 *
 * @since 1.4
 *
 * @param object $term A Stores Term object.
 * @param string $context (optional) How to escape url.
 *
 * @return string
 */
function clpr_get_store_out_url( $term, $context = 'display' ) {
	global $clpr_options;

	if ( ! is_object( $term ) ) {
		return;
	}

	if ( $clpr_options->cloak_links ) {
		$url = clpr_get_store_out_cloak_url( $term );
	} else {
		$url = clpr_get_store_meta( $term->term_id, 'clpr_store_aff_url', true );
		if ( empty( $url ) ) {
			$url = clpr_get_store_meta( $term->term_id, 'clpr_store_url', true );
		}
	}

	$url = apply_filters( 'clpr_store_out_url', $url, $term );

	return esc_url( $url, null, $context );
}


/**
 * Returns store outgoing cloak url.
 *
 * @param object $term A Stores Term object.
 *
 * @return string
 */
function clpr_get_store_out_cloak_url( $term ) {

	if ( get_option( 'permalink_structure' ) != '' ) {
		$url = home_url( CLPR_STORE_REDIRECT_BASE_URL . $term->slug );
	} else {
		$url = add_query_arg( array( 'redirect_store' => $term->slug ), home_url( '/' ) );
	}

	return $url;
}


/**
 * Returns coupon outgoing url.
 *
 * @since 1.4
 *
 * @param object $post A Post object.
 * @param string $context (optional) How to escape url.
 *
 * @return string
 */
function clpr_get_coupon_out_url( $post, $context = 'display' ) {
	global $clpr_options;

	if ( ! is_object( $post ) ) {
		return;
	}

	$url = '';

	if ( $clpr_options->cloak_links ) {
		$url = clpr_get_coupon_out_cloak_url( $post );
	} else {
		$coupon_type = clpr_get_coupon_type( $post->ID );
		if ( 'printable-coupon' === $coupon_type ) {
			$url = clpr_get_printable_coupon( $post->ID, 'large', 'url' );
		} else {
			$url = get_post_meta( $post->ID, 'clpr_coupon_aff_url', true );
		}
	}

	// fallback to store out url
	if ( empty( $url ) ) {
		$store = clpr_get_first_term( $post->ID, APP_TAX_STORE );
		if ( $store ) {
			return clpr_get_store_out_url( $store, $context );
		}
	}

	$url = apply_filters( 'clpr_coupon_out_url', $url, $post );

	return esc_url( $url, null, $context );
}


/**
 * Returns coupon outgoing cloak url.
 *
 * @param object $post A Post object.
 *
 * @return string
 */
function clpr_get_coupon_out_cloak_url( $post ) {

	if ( get_option( 'permalink_structure' ) != '' ) {
		if ( ! empty( $post->post_name ) ) {
			$slug = $post->post_name;
		} else if ( $post->post_title && $post->post_title != __( 'Auto Draft' ) ) {
			$slug = sanitize_title( $post->post_title, $post->ID );
		} else {
			$slug = 'coupon-name';
		}

		$url = home_url( CLPR_COUPON_REDIRECT_BASE_URL . $slug . '/' . $post->ID );
	} else {
		$url = add_query_arg( array( 'redirect_coupon' => $post->ID ), home_url( '/' ) );
	}

	return $url;
}

