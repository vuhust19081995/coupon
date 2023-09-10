<?php
/**
 * Clipper core theme functions.
 *
 * @package Clipper\Functions
 * @author  AppThemes
 * @since   Clipper 1.0.0
 */

/**
 * Filter short code [template-url].
 *
 * @since 1.0.0
 *
 * @param string $text
 * @return string
 */
function filter_template_url( $text ) {
	return str_replace( '[template-url]', get_template_directory_uri(), $text );
}
add_filter( 'the_content', 'filter_template_url' );
add_filter( 'get_the_content', 'filter_template_url' );
add_filter( 'widget_text', 'filter_template_url' );

/**
 * Replace Standard WP Menu Classes for cleaner CSS classes.
 *
 * @since 1.0.0
 *
 * @param string $css_classes
 * @param object $item
 * @return string
 */
function change_menu_classes( $css_classes, $item ) {
	$css_classes = str_replace( "current-menu-item", "active", $css_classes );
	$css_classes = str_replace( "current-menu-parent", "active", $css_classes );
	$css_classes = str_replace( "current-menu-ancestor", "active", $css_classes );

	return $css_classes;
}
add_filter( 'nav_menu_css_class', 'change_menu_classes', 10, 2 );

/**
 * Displays the login message in the header.
 *
 * @since 1.0.0
 *
 * @return void
 */
function clpr_login_head() {

	if ( is_user_logged_in() ) {
		echo html( 'li', html_link( clpr_get_dashboard_url(), '<i class="fa fa-tachometer" aria-hidden="true"></i> '.__( 'My Dashboard', APP_TD ) ) );
		echo html( 'li', html_link( clpr_logout_url( home_url() ), '<i class="fa fa-sign-out" aria-hidden="true"></i> '.__( 'Log out', APP_TD ) ) );
	} else {
		if ( get_option( 'users_can_register' ) ) {
			echo html( 'li', html_link( appthemes_get_registration_url(), '<i class="fa fa-user-plus" aria-hidden="true"></i> '.__( 'Register', APP_TD ) ) );
		}
		echo html( 'li', html_link( wp_login_url(), '<i class="fa fa-sign-in" aria-hidden="true"></i> '.__( 'Login', APP_TD ) ) );
	}
}

/**
 * Returns user name depend of account type.
 *
 * @since 1.0.0
 *
 * @param object|int $user (optional)
 * @return string
 */
function clpr_get_user_name( $user = false ) {

	if ( ! $user && is_user_logged_in() ) {
		$user = wp_get_current_user();
	} else if ( is_numeric( $user ) ) {
		$user = get_userdata( $user );
	}

	if ( is_object( $user ) ) {
		return $user->display_name;
	} else {
		return false;
	}
}

/**
 * Returns logout url depend of login type.
 *
 * @since 1.0.0
 *
 * @param string $url (optional)
 * @return string
 */
function clpr_logout_url( $url = '' ) {

	if ( ! $url ) {
		$url = home_url();
	}

	if ( is_user_logged_in() ) {
		return wp_logout_url( $url );
	} else {
		return false;
	}
}

/**
 * Corrects logout url in admin bar.
 *
 * @since 1.0.0
 *
 * @return void
 */
function clpr_admin_bar_render() {
  global $wp_admin_bar;

  if ( is_user_logged_in() ) {
		$wp_admin_bar->remove_menu( 'logout' );
		$wp_admin_bar->add_menu( array(
			'parent' => 'user-actions',
			'id'     => 'logout',
			'title'  => __( 'Log out', APP_TD ),
			'href'   => clpr_logout_url(),
		) );
	}
}
add_action( 'wp_before_admin_bar_render', 'clpr_admin_bar_render' );

/**
 * Returns url to user dashboard page.
 *
 * @since 1.0.0
 *
 * @param string $context (optional)
 * @return string
 */
function clpr_get_dashboard_url( $context = 'display' ) {

	if ( defined( 'CLPR_DASHBOARD_URL' ) ) {
		$url = CLPR_DASHBOARD_URL;
	} else {
		$url = get_permalink( CLPR_User_Dashboard::get_id() );
	}

	return esc_url( $url, null, $context );
}

/**
 * Returns url to edit profile page.
 *
 * @since 1.0.0
 *
 * @param string $context (optional)
 * @return string
 */
function clpr_get_profile_url( $context = 'display' ) {

	if ( defined( 'CLPR_PROFILE_URL' ) ) {
		$url = CLPR_PROFILE_URL;
	} else {
		$url = get_permalink( CLPR_User_Profile::get_id() );
	}

	return esc_url( $url, null, $context );
}

/**
 * Returns url to submit coupon page.
 *
 * @since 1.0.0
 *
 * @param string $context (optional)
 * @return string
 */
function clpr_get_submit_coupon_url( $context = 'display' ) {

	$url = appthemes_get_process_url( APP_POST_TYPE, 'new' );

	return esc_url( $url, null, $context );
}

/**
 * Returns coupon listing edit url.
 *
 * @since 1.0.0
 *
 * @param int $listing_id
 * @param string $context (optional)
 * @return string
 */
function clpr_get_edit_coupon_url( $listing_id, $context = 'display' ) {
	$url = appthemes_get_process_url( APP_POST_TYPE, 'edit', $listing_id );
	return esc_url( $url, null, $context );
}

/**
 * Returns coupon listing renew url.
 *
 * @since 1.0.0
 *
 * @param int $listing_id
 * @param string $context (optional)
 * @return string
 */
function clpr_get_renew_coupon_url( $listing_id, $context = 'display' ) {
	$url = appthemes_get_process_url( APP_POST_TYPE, 'renew', $listing_id );
	return esc_url( $url, null, $context );
}

/**
 * Displays edit coupon link. Use only in loop.
 *
 * @since 1.0.0
 *
 * @return void
 */
function clpr_edit_coupon_link() {
	global $post, $current_user, $clipper;

	if ( ! is_user_logged_in() ) {
		return;
	}

	if ( current_user_can( 'manage_options' ) ) {
		edit_post_link( __( 'Edit Coupon', APP_TD ), '<p class="edit">', '</p>', $post->ID );
	} else if ( $clipper->{APP_POST_TYPE}->options->allow_edit && $post->post_author == $current_user->ID ) {
		$edit_url  = clpr_get_edit_coupon_url( $post->ID );
		$edit_link = html( 'a', array( 'class' => 'post-edit-link', 'href' => $edit_url, 'title' => __( 'Edit Coupon', APP_TD ) ), __( 'Edit Coupon', APP_TD ) );
		echo html( 'p', array( 'class' => 'edit' ), $edit_link );
	}
}

/**
 * Returns expire date of coupon.
 *
 * @since 1.0.0
 *
 * @param int $post_id
 * @param string $format (optional)
 * @return string|int
 */
function clpr_get_expire_date( $post_id, $format = 'raw' ) {
	global $clipper;

	$expire_time = $clipper->{APP_POST_TYPE}->plans->get_current_plan( $post_id )->get_expire_date();

	if ( empty( $expire_time ) ) {
		return '';
	}

	switch( $format ) {
		case 'display':
			$expire_date = date_i18n( get_option( 'date_format' ), $expire_time );
			break;

		case 'time':
			$expire_date = $expire_time;
			break;

		case 'raw':
			$expire_date = date( 'Y-m-d', $expire_time );
			break;

		default:
			$expire_date = date( $format, $expire_time );
			break;
	}

	return $expire_date;
}


/**
 * Checks if coupon listing is expired.
 *
 * @param int $listing_id (optional).
 *
 * @return bool
 */
function clpr_is_listing_expired( $listing_id = 0 ) {
	$listing_id = $listing_id ? $listing_id : get_the_ID();
	$listing = get_post( $listing_id );

	return $listing->post_status === APP_POST_STATUS_EXPIRED;
}


/**
 * Determines (Publish vs. Unreliable) and updates coupon status.
 *
 * @param int $post_id
 * @param string $post_status (optional)
 *
 * @return void
 */
function clpr_status_update( $post_id, $post_status = null ) {
	global $wpdb;

	$votes_down = get_post_meta( $post_id, 'clpr_votes_down', true );
	$votes_percent = get_post_meta( $post_id, 'clpr_votes_percent', true );

	if ( ! $post_status ) {
		$post_status = get_post_status( $post_id );
	}

	if ( ( $votes_percent < 50 && $votes_down != 0 ) ) {
		if ( $post_status == 'publish' ) {
			$wpdb->update( $wpdb->posts, array( 'post_status' => 'unreliable' ), array( 'ID' => $post_id ) );
		}
	} else {
		if ( $post_status == 'unreliable' ) {
			$wpdb->update( $wpdb->posts, array( 'post_status' => 'publish' ), array( 'ID' => $post_id ) );
		}
	}

}

/**
 * Returns the store image url with specified size.
 *
 * @param int $id
 * @param string $type (optional)
 * @param int $width (optional)
 *
 * @return string
 */
function clpr_get_store_image_url( $id, $type = 'post_id', $width = 110 ) {
	$store_url = false;
	$store_image_id = false;

	$sizes = array( 75 => 'thumb-med', 110 => 'post-thumbnail', 150 => 'thumb-store', 160 => 'thumb-featured', 250 => 'thumb-large-preview', 768 => 'medium_large' );
	$sizes = apply_filters( 'clpr_store_image_sizes', $sizes );

	if ( ! array_key_exists( $width, $sizes ) ) {
		$width = 110;
	}

	if ( ! isset( $sizes[ $width ] ) ) {
		$sizes[ $width ] = 'post-thumbnail';
	}

	if ( $type == 'term_id' && $id ) {
		$store_url = clpr_get_store_meta( $id, 'clpr_store_url', true );
		$store_image_id = (array) clpr_get_store_meta( $id, 'clpr_store_image_id', true );
	}

	if ( $type == 'post_id' && $id ) {
		$term_id = appthemes_get_custom_taxonomy( $id, APP_TAX_STORE, 'term_id' );
		$store_url = clpr_get_store_meta( $term_id, 'clpr_store_url', true );
		$store_image_id = (array) clpr_get_store_meta( $term_id, 'clpr_store_image_id', true );
	}

	$store_image_id = array_shift( $store_image_id );

	if ( is_numeric( $store_image_id ) ) {
		$store_image_src = wp_get_attachment_image_src( $store_image_id, $sizes[ $width ] );
		// Check if we need to upgrade the legacy values.
		$media_manager_id = get_post_meta( $store_image_id, '_app_media_manager_parent', true );
		if ( ! $media_manager_id ) {
			update_post_meta( $store_image_id, '_app_media_manager_parent', 'clpr_store_image_id' );
			update_post_meta( $store_image_id, '_app_attachment_parent_type', APP_TAX_STORE );
			update_post_meta( $store_image_id, '_app_attachment_type', APP_ATTACHMENT_FILE );
		}
		if ( $store_image_src ) {
			return apply_filters( 'clpr_store_image', $store_image_src[0], $width, $id, $type );
		}
	}

	if ( ! empty( $store_url ) ) {
		$store_image_url = 'https://s.wordpress.com/mshots/v1/' . urlencode( $store_url ) . "?w=" . $width;
		return apply_filters( 'clpr_store_image', $store_image_url, $width, $id, $type );
	} else {
		$store_image_url = apply_filters( 'clpr_store_default_image', appthemes_locate_template_uri( 'images/clpr_default.jpg' ), $width );
		return apply_filters( 'clpr_store_image', $store_image_url, $width, $id, $type );
	}

}


/**
 * Validates coupon expiration date.
 *
 * @param string $date
 *
 * @return bool
 */
function clpr_is_valid_expiration_date( $date ) {
	global $clpr_options;

	if ( empty( $date ) ) {
		return false;
	}

	// Year, month, day.
	if ( ! preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $date, $date_parts ) ) {
		return false;
	}

	// Month, day, year.
	if ( ! checkdate( $date_parts[2], $date_parts[3], $date_parts[1] ) ) {
		return false;
	}

	// + 24h, coupons expire in the end of day
	$timestamp = strtotime( $date ) + ( 24 * 3600 );
	if ( $clpr_options->prune_coupons && current_time( 'timestamp' ) > $timestamp ) {
		return false;
	}

	return true;
}

/**
 * Validate date fields.
 *
 * @param mixed          $value  Posted field value.
 * @param scbCustomField $inst   Field object.
 * @param WP_Error       $errors Errors object.
 *
 * @return mixed Validated value.
 */
function clpr_expiration_date_validator( $value, $inst, $errors ) {
	if ( $value && ! clpr_is_valid_expiration_date( $value ) ) {
		$title = ( isset( $inst->title ) ) ? $inst->title : $inst->desc ;
		$errors->add(
			'invalid_date',
			sprintf( __( 'ERROR: Field %s contains an invalid date!', APP_TD ), $title )
		);
	}
	return $value;
}

/**
 * Displays the printable coupon image associated to the coupon listing. Use only in loop.
 *
 * @param string $size (optional)
 * @param string $return (optional)
 *
 * @return void
 */
function clpr_get_coupon_image( $size = 'thumb-large', $return = 'html' ) {
	global $post;

	echo clpr_get_printable_coupon( $post->ID, $size, $return );
}


/**
 * Provides joins for expired coupon filters.
 *
 * @param string $join
 * @param object $wp_query
 *
 * @return string
 */
function clpr_expired_coupons_joins( $join, $wp_query ) {
	global $wpdb;

	if ( $wp_query->get( 'not_expired_coupons' ) || $wp_query->get( 'filter_unreliable' ) ) {
		$join .= " INNER JOIN $wpdb->postmeta AS exp1 ON ($wpdb->posts.ID = exp1.post_id) ";
		$join .= " INNER JOIN $wpdb->postmeta AS exp2 ON ($wpdb->posts.ID = exp2.post_id) ";

		// Only provide second join to queries that need it
		$join .= " INNER JOIN $wpdb->postmeta AS exp3 ON ($wpdb->posts.ID = exp3.post_id) ";
	}

	return $join;
}
add_filter( 'posts_join', 'clpr_expired_coupons_joins', 10, 2 );


/**
 * Filters out anything that isn't unreliable or expired.
 *
 * @param string $where
 * @param object $wp_query
 *
 * @return string
 */
function clpr_filter_unreliable_coupons( $where, $wp_query ) {

	if ( ! $wp_query->get( 'filter_unreliable' ) ) {
		return $where;
	}

	$not_zero = " ( exp1.meta_key = 'clpr_votes_down' AND CAST( exp1.meta_value AS SIGNED) NOT BETWEEN '0' AND '0' ) ";

	$low_percent = " ( exp2.meta_key = 'clpr_votes_percent' AND CAST( exp2.meta_value AS SIGNED ) BETWEEN '0' AND '50' ) ";

	$votes_match = " ( $low_percent AND $not_zero ) ";

	$expired = " ( exp3.meta_key = 'clpr_expire_date' AND exp3.meta_value < CURRENT_DATE() ) ";

	$not_empty = " ( exp3.meta_key = 'clpr_expire_date' AND exp3.meta_value != '' ) ";

	$expired_match = " ( $expired AND $not_empty ) ";

	$meta_matches = " ( $votes_match OR $expired_match )";

	$where .= " AND ( $meta_matches ) ";

	return $where;
}
add_filter( 'posts_where', 'clpr_filter_unreliable_coupons', 10, 2 );


/**
 * Filters out expired coupons.
 *
 * @param string $where
 * @param object $wp_query
 *
 * @return string
 */
function clpr_not_expired_coupons_filter( $where, $wp_query ) {

	if ( $wp_query->get( 'not_expired_coupons' ) ) {
		$where .= " AND ( (exp1.meta_key = 'clpr_expire_date' AND exp1.meta_value >= CURRENT_DATE()) OR ( exp1.meta_key = 'clpr_expire_date' AND exp1.meta_value = '') )";
	}

	return $where;
}
add_filter( 'posts_where', 'clpr_not_expired_coupons_filter', 10, 2 );


/**
 * Displays coupon type/code box.
 *
 * @since 1.6.0
 *
 * @param string $coupon_type (optional)
 * @return void
 */
if ( ! function_exists( 'clpr_coupon_code_box' ) ) :
	function clpr_coupon_code_box( $coupon_type = null ) {
		global $post;

		if ( ! $post || $post->post_type != APP_POST_TYPE ) {
			return;
		}

		if ( ! $coupon_type ) {
			$coupon_type = clpr_get_coupon_type( $post->ID );
		}

		// display additional info if coupon is expired
		clpr_display_expired_info( $post->ID );

		get_template_part( 'parts/content-codebox', $coupon_type );
	}
endif;

/**
 * Loads all page templates, setups cache, limits db queries.
 *
 * @since 1.0.0
 *
 * @return void
 */
function clpr_load_all_page_templates() {

	$pages = get_posts( array(
		'post_type'      => 'page',
		'meta_key'       => '_wp_page_template',
		'posts_per_page' => -1,
		'no_found_rows'  => true,
	) );
}

/**
 * Deletes coupon listing.
 *
 * @since 1.0.0
 *
 * @param int $post_id
 * @return bool
 */
function clpr_delete_coupon( $post_id ) {

	// delete post and it's revisions, comments, metadata, tax relations, etc.
	if ( wp_delete_post( $post_id, true ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Deletes all attachments associated to coupon listing.
 *
 * @since 1.0.0
 *
 * @param int $post_id
 * @return bool
 */
function clpr_delete_coupon_attachments( $post_id ) {
	global $wpdb;

	if ( ! $post_id = absint( $post_id ) ) {
		return false;
	}

	if ( APP_POST_TYPE != get_post_type( $post_id ) ) {
		return false;
	}

	$attachment_ids = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_parent = %d AND post_type = 'attachment'", $post_id ) );

	// delete all associated attachments
	foreach ( $attachment_ids as $attachment_id ) {
		wp_delete_attachment( $attachment_id, true );
	}

	return true;
}
add_action( 'before_delete_post', 'clpr_delete_coupon_attachments' );

/**
 * Returns coupons which are marked as featured for slider.
 *
 * @since 1.5.0
 *
 * @return object|bool A boolean False if no coupons found.
 */
function clpr_get_featured_slider_coupons() {
	global $clpr_options;

	$args = array(
		'post_type'        => APP_POST_TYPE,
		'post_status'      => array( 'publish', 'unreliable' ),
		'addon'            => CLPR_ITEM_FEATURED,
		'posts_per_page'   => 15,
		'orderby'          => 'rand',
		'no_found_rows'    => true,
		'suppress_filters' => false,
	);

	if ( $clpr_options->exclude_unreliable_featured ) {
		$args['post_status'] = array( 'publish' );
	}

	$args = apply_filters( 'clpr_featured_slider_args', $args );

	$featured = new WP_Query( $args );

	if ( ! $featured->have_posts() ) {
		return false;
	}

	return $featured;
}

/**
 * Returns localized status if available.
 *
 * @since 1.0.0
 *
 * @param string $status
 * @return string
 */
function clpr_get_status_i18n( $status ) {

	$statuses = array(
		'draft'              => __( 'Draft', APP_TD ),
		'ended'              => __( 'Ended', APP_TD ),
		'expired'            => __( 'Expired', APP_TD ),
		'future'             => __( 'Scheduled', APP_TD ),
		'live'               => __( 'Live', APP_TD ),
		'live_expired'       => __( 'Live-Expired', APP_TD ),
		'live_unreliable'    => __( 'Live-Unreliable', APP_TD ),
		'offline'            => __( 'Offline', APP_TD ),
		'pending'            => __( 'Pending', APP_TD ),
		'pending_moderation' => __( 'Awaiting approval', APP_TD ),
		'pending_payment'    => __( 'Awaiting payment', APP_TD ),
		'private'            => __( 'Private', APP_TD ),
		'publish'            => __( 'Published', APP_TD ),
		'trash'              => __( 'Trash', APP_TD ),
		'unreliable'         => __( 'Unreliable', APP_TD ),
	);

	$status = strtolower( $status );

	if ( array_key_exists( $status, $statuses ) ) {
		return $statuses[ $status ];
	} else {
		return ucfirst( $status );
	}
}

/**
 * Returns coupon listing status name.
 *
 * @since 1.0.0
 *
 * @param int $listing_id (optional)
 * @return string
 */
function clpr_get_listing_status_name( $listing_id = 0 ) {
	global $clpr_options;

	$listing_id = $listing_id ? $listing_id : get_the_ID();
	$listing    = get_post( $listing_id );

	if ( clpr_is_listing_expired( $listing->ID ) ) {
		if ( ! $clpr_options->prune_coupons ) {
			return 'live_expired';
		} else {
			return 'ended';
		}
	} else if ( $listing->post_status == 'draft' ) {
		return 'offline';
	} else if ( $listing->post_status == 'pending' ) {
		if ( clpr_have_pending_payment( $listing->ID ) ) {
			return 'pending_payment';
		} else {
			return 'pending_moderation';
		}
	} else if ( $listing->post_status == 'unreliable' ) {
		return 'live_unreliable';
	}

	return 'live';
}

/**
 * Creates terms list.
 *
 * @since 1.5.0
 *
 * @param array $args (optional)
 * @return string
 */
function clpr_terms_list( $args = array() ) {

	$defaults = array(
		'taxonomy' => 'category',
		'exclude'  => array(),
		'menu'     => true,
		'count'    => true,
		'top_link' => true,
		'class'    => 'terms',
	);

	$options = wp_parse_args( (array) $args, $defaults );
	$options = apply_filters( 'clpr_terms_list_args', $options );

	$terms = get_terms( $options['taxonomy'], array(
		'hide_empty'     => ! empty( $options['hide_empty'] ) ? $options['hide_empty'] : 0,
		'child_of'       => 0,
		'pad_counts'     => 0,
		'app_pad_counts' => 1,
	) );

	$navigation = '';
	$list       = '';
	$groups     = array();

	if ( empty( $terms ) || ! is_array( $terms ) ) {
		return html( 'p', __( 'Sorry, but no terms were found.', APP_TD ) );
	}

	// unset child terms
	foreach ( $terms as $key => $value ) {
		if ( $value->parent != 0 ) {
			unset( $terms[ $key ] );
		}
	}

	foreach ( $terms as $term ) {
		$letter = mb_strtoupper( mb_substr( $term->name, 0, 1 ) );
		if ( is_numeric( $letter ) ) {
			$letter = '#';
		}

		if ( ! empty( $letter ) ) {
			$groups[ $letter ][] = $term;
		}
	}

	if ( empty( $groups ) ) {
		return;
	}

	foreach ( $groups as $letter => $terms ) {

		$old_list       = $list;
		$old_navigation = $navigation;
		$letter_items   = false;

		$letter      = apply_filters( 'the_title', $letter );
		$letter_id   = ( preg_match( '/\p{L}/', $letter ) ) ? $letter : substr( md5( $letter ), 0, 5 ); // hash special chars
		$navigation .= html_link( '#' . $options['class'] . '-' . $letter_id, $letter );

		$list .= '<h2 class="' . $options['class'] . '" id="' . $options['class'] . '-' . $letter_id . '">' . $letter . '</h2>';
		$list .= '<ul class="' . $options['class'] . '">';

		foreach ( $terms as $term ) {

			if ( in_array( $term->term_id, $options['exclude'] ) ) {
				continue;
			}

			$letter_items = true;
			$name         = apply_filters( 'the_title', $term->name );
			$link         = html_link( get_term_link( $term, $options['taxonomy'] ), $name );
			$count        = ( $options['count'] ) ? ' (' . intval( $term->count ) . ')' : '';

			$list .= html( 'li', $link . $count );
		}

		$list .= '</ul>';

		if ( ! $letter_items ) {
			$list       = $old_list;
			$navigation = $old_navigation;
		}
	}

	$navigation = html( 'div class="grouplinks"', $navigation );

	if ( $options['menu'] ) {
		$list = $navigation . $list;
	}

	return $list;
}

/**
 * Creates categories list.
 *
 * @since 1.5.0
 *
 * @return string
 */
function clpr_categories_list() {

	$args = array(
		'taxonomy' => APP_TAX_CAT,
		'class'    => 'categories',
	);

	return clpr_terms_list( $args );
}

/**
 * Creates stores list.
 *
 * @since 1.5.0
 *
 * @return string
 */
function clpr_stores_list() {

	$hidden_stores = clpr_hidden_stores();

	$args = array(
		'taxonomy' => APP_TAX_STORE,
		'exclude'  => $hidden_stores,
		'class'    => 'stores',
	);

	return clpr_terms_list( $args );
}

/**
 * Displays report coupon form.
 *
 * @since 1.0.0
 *
 * @param bool $echo (optional)
 * @return string
 */
function clpr_report_coupon( $echo = false ) {
	global $post;

	$form = appthemes_get_reports_form( $post->ID, 'post' );

	if ( ! $form ) {
		return;
	}

	$content  = '<li><div class="reports_wrapper"><div class="reports_form_link">';
	$content .= '<a href="#" class="problem"><i class="fa fa-question-circle" aria-hidden="true"></i>' . __( 'Report a Problem', APP_TD ) . '</a>';
	$content .= '</div></div></li>';
	$content .= '<li class="report">' . $form . '</li>';

	if ( $echo ) {
		echo $content;
	}

	return $content;
}

/**
 * Displays additional information if coupon is expired.
 *
 * @since 1.5.0
 *
 * @param int $post_id Post ID.
 * @return void
 */
function clpr_display_expired_info( $post_id ) {

	// do not show on taxonomy pages, there is Unreliable section
	if ( is_tax() ) {
		return;
	}

	if ( ! clpr_is_listing_expired( $post_id ) ) {
		return;
	}

	echo html( 'div class="expired-coupon-info"', __( 'This offer has expired.', APP_TD ) );
}

/**
 * Displays coupon title or link - depend on settings.
 *
 * @since 1.5
 * @since 2.0.0 Added argument $item_id to use outside the loop.
 *
 * @param int $item_id The Coupon ID.
 *
 * @return void
 */
function clpr_coupon_title( $item_id = 0 ) {
	global $clpr_options;

	if ( $clpr_options->link_single_page ) {
		$title = ( mb_strlen( get_the_title( $item_id ) ) >= 87 ) ? mb_substr( get_the_title( $item_id ), 0, 87 ) . '...' : get_the_title( $item_id );
		/* translators: %s: coupon title */
		$title_attr = sprintf( esc_attr__( 'View the "%s" coupon page', APP_TD ), the_title_attribute( array(
			'echo' => false,
			'post' => $item_id,
		) ) );
		echo html( 'a', array( 'href' => get_permalink( $item_id ), 'title' => $title_attr, 'rel' => 'bookmark' ), $title );
	} else {
		echo get_the_title( $item_id );
	}
}

/**
 * Displays coupon content or content preview - depend on settings.
 *
 * @since 1.5
 *
 * @return void
 */
function clpr_coupon_content() {
	global $post, $clpr_options;

	if ( ! in_the_loop() ) {
		return;
	}

	if ( $clpr_options->link_single_page ) {
		$content = mb_substr( strip_tags( $post->post_content ), 0, 200 ) . '... ';
		$title_attr = sprintf( esc_attr__( 'View the "%s" coupon page', APP_TD ), the_title_attribute( 'echo=0' ) );
		echo $content . html( 'a', array( 'href' => get_permalink(), 'class' => 'more', 'title' => $title_attr ), __( 'more &rsaquo;&rsaquo;', APP_TD ) );
	} else {
		the_content();
	}
}

/**
 * Adds Open Graph meta tags.
 *
 * @since 1.5.1
 */
class CLPR_Open_Graph extends APP_Open_Graph {

	/**
	 * Setups meta tags.
	 *
	 * @param array $args (optional)
	 *
	 * @return void
	 */
	public function __construct( $args = array() ) {

		if ( get_header_image() ) {
			$args['default_image'] = get_header_image();
		} else {
			$args['default_image'] = appthemes_locate_template_uri( 'images/logo.png' );
		}

		parent::__construct( $args );
	}

	/**
	 * Returns image url.
	 *
	 * @return string
	 */
	public function get_image_url() {

		$image_url = '';
		$queried_object = get_queried_object();

		if ( is_singular( APP_POST_TYPE ) ) {
			$image_url = clpr_get_store_image_url( $queried_object->ID, 'post_id', 250 );
		} else if ( is_tax( APP_TAX_STORE ) ) {
			$image_url = clpr_get_store_image_url( $queried_object->term_id, 'term_id', 250 );
		} else {
			$image_url = parent::get_image_url();
		}

		if ( empty( $image_url ) ) {
			$image_url = $this->args['default_image'];
		}

		return $image_url;
	}
}

/**
 * Generates unique ID for coupons.
 *
 * @since 1.5.1
 *
 * @return string
 */
function clpr_generate_id() {

	$id = uniqid( rand( 10, 1000 ), false );

	if ( clpr_get_listing_by_ref( $id ) ) {
		return clpr_generate_id();
	}

	return $id;
}

/**
 * Retrieves listing data by given reference ID.
 *
 * @since 1.5.1
 *
 * @param string $reference_id An listing reference ID.
 * @return object|bool A listing object, boolean False otherwise.
 */
function clpr_get_listing_by_ref( $reference_id ) {

	if ( empty( $reference_id ) || ! is_string( $reference_id ) ) {
		return false;
	}

	$reference_id = appthemes_numbers_letters_only( $reference_id );

	$listing_q = new WP_Query( array(
		'post_type'        => APP_POST_TYPE,
		'post_status'      => 'any',
		'meta_key'         => 'clpr_id',
		'meta_value'       => $reference_id,
		'posts_per_page'   => 1,
		'suppress_filters' => true,
		'no_found_rows'    => true,
	) );

	if ( empty( $listing_q->posts ) ) {
		return false;
	}

	return $listing_q->posts[0];
}

/**
 * Displays coupon code popup.
 *
 * @since 1.5.1
 *
 * @return void
 */
function clpr_coupon_code_popup() {

	if ( 'GET' != $_SERVER['REQUEST_METHOD'] ) {
		appthemes_display_notice( 'error', __( 'Sorry, only get method allowed.', APP_TD ) );
		die;
	}

	$post_id = isset( $_GET['id'] ) ? (int) appthemes_numbers_only( $_GET['id'] ) : 0;

	if ( $post_id < 1 ) {
		appthemes_display_notice( 'error', __( 'Sorry, item does not exist.', APP_TD ) );
		die;
	}

	$post = get_post( $post_id );

	if ( ! $post ) {
		appthemes_display_notice( 'error', __( 'Sorry, item does not exist.', APP_TD ) );
		die;
	}

	if ( $post->post_type != APP_POST_TYPE ) {
		appthemes_display_notice( 'error', __( 'Sorry, wrong item type.', APP_TD ) );
		die;
	}

	$coupon_type = clpr_get_coupon_type( $post->ID );

	if ( $coupon_type != 'coupon-code' ) {
		appthemes_display_notice( 'error', __( 'Sorry, wrong item type.', APP_TD ) );
		die;
	}

	$coupon_code = wptexturize( get_post_meta( $post->ID, 'clpr_coupon_code', true ) );

	$template_name = 'form-popup-coupon-code.php';
	$template_path = locate_template( $template_name );

	if ( ! $template_path ) {
		appthemes_display_notice( 'error', sprintf( __( 'Error: template "%s" not found.', APP_TD ), $template_name ) );
		die;
	}

	include $template_path;
	die;
}

/**
 * Returns coupon listing CTR.
 *
 * @since 1.0.0
 *
 * @param int $listing_id (optional)
 * @return string
 */
function clpr_get_coupon_ctr( $listing_id = 0 ) {

	$listing_id = $listing_id ? $listing_id : get_the_ID();

	$views  = (int) get_post_meta( $listing_id, 'clpr_total_count', true );
	$clicks = (int) get_post_meta( $listing_id, 'clpr_coupon_aff_clicks', true );

	if ( $views > 0 ) {
		$ctr = ( $clicks / $views * 100 );
	} else {
		$ctr = 0;
	}

	return number_format_i18n( $ctr, 2 ) . '%';
}

/**
 * Returns coupon listing type.
 *
 * @since 1.0.0
 *
 * @param int $listing_id (optional)
 * @return string
 */
function clpr_get_coupon_type( $listing_id = 0 ) {

	$listing_id = $listing_id ? $listing_id : get_the_ID();
	$type = clpr_get_first_term( $listing_id, APP_TAX_TYPE );

	if ( ! $type ) {
		return '';
	}

	return $type->slug;
}

/**
 * Returns coupon listing store name.
 *
 * @since 1.0.0
 *
 * @param int $listing_id (optional)
 * @return string
 */
function clpr_get_coupon_store_name( $listing_id = 0 ) {

	$listing_id = $listing_id ? $listing_id : get_the_ID();
	$store      = clpr_get_first_term( $listing_id, APP_TAX_STORE );

	if ( ! $store ) {
		return '';
	}

	return $store->name;
}

/**
 * Returns coupon listing category name.
 *
 * @since 1.0.0
 *
 * @param int $listing_id (optional)
 * @return string
 */
function clpr_get_coupon_category_name( $listing_id = 0 ) {

	$listing_id = $listing_id ? $listing_id : get_the_ID();
	$category   = clpr_get_first_term( $listing_id, APP_TAX_CAT );

	if ( ! $category ) {
		return '';
	}

	return $category->name;
}

/**
 * Returns the first taxonomy term for an coupon listing.
 *
 * @since 1.0.0
 *
 * @param int $listing_id
 * @param string $taxonomy
 * @return object|bool
 */
function clpr_get_first_term( $listing_id, $taxonomy ) {

	$terms = get_the_terms( $listing_id, $taxonomy );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return false;
	}

	return reset( $terms );
}

/**
 * Returns the first taxonomy term link for an coupon listing.
 *
 * @since 1.0.0
 *
 * @param int $listing_id
 * @param string $taxonomy
 * @return object|bool
 */
function clpr_get_first_term_link( $listing_id, $taxonomy ) {

	$terms = get_the_terms( $listing_id, $taxonomy );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return false;
	}
	$term = reset( $terms );

	return get_term_link( $term, $taxonomy );
}

/**
 * Prints HTML with meta information for attachments.
 *
 * @since 1.0.0
 *
 * @return void
 */
function clpr_attachment_entry_meta() {

	if ( ! is_attachment() ) {
		return;
	}

	$parts     = array();
	$separator = '<span class="meta-sep"> | </span>';

	if ( get_the_author() ) {
		$parts['author'] = sprintf( '<span class="byline">%1$s <span class="author vcard"><a class="url fn n" href="%2$s" title="%3$s" rel="author">%4$s</a></span></span>',
			__( 'By:', APP_TD ),
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			sprintf( esc_attr__( 'View all coupons by %s', APP_TD ), get_the_author() ),
			get_the_author()
		);
	}

	$time_string = sprintf( '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="entry-date updated" datetime="%3$s">%4$s</time>',
		esc_attr( get_the_date( 'c' ) ),
		get_the_date(),
		esc_attr( get_the_modified_date( 'c' ) ),
		get_the_modified_date()
	);

	$parts['date'] = sprintf( __( '<span class="posted-on">%1$s %2$s</span>', APP_TD ),
		__( 'Uploaded:', APP_TD ),
		$time_string
	);

	if ( wp_attachment_is_image() ) {
		// Retrieve attachment metadata.
		$metadata = wp_get_attachment_metadata();

		$parts['image-size'] = sprintf( '<span class="full-size-link">%1$s <a href="%2$s">%3$s &times; %4$s</a></span>',
			__( 'Full size:', APP_TD ),
			esc_url( wp_get_attachment_url() ),
			$metadata['width'],
			$metadata['height']
		);
	} else {
		$attached_file = get_attached_file( get_the_ID() );
		if ( file_exists( $attached_file ) ) {
			$file_size = size_format( filesize( $attached_file ) );
			$file_type = wp_check_filetype( wp_basename( get_the_guid() ) );

			$parts['file-type'] = sprintf( '<span class="file-type">%1$s %2$s</span>',
				__( 'File type:', APP_TD ),
				strtoupper( $file_type['ext'] )
			);

			$parts['file-size'] = sprintf( '<span class="file-size">%1$s %2$s</span>',
				__( 'File size:', APP_TD ),
				$file_size
			);
		}
	}

	if ( current_user_can( 'edit_others_posts' ) ) {
		$parts['edit-link'] = sprintf( '<span class="edit-link"><a href="%1$s">%2$s</a></span>',
			get_edit_post_link( get_the_ID() ),
			__( 'Edit', APP_TD )
		);
	}

	echo implode( $separator, $parts );
}

/**
 * Count the number of footer sidebars to enable dynamic classes for the footer.
 *
 * @since 2.0.0
 */
function clpr_footer_sidebar_class() {
	$count = 0;

	if ( is_active_sidebar( 'sidebar_footer' ) ) {
		$count++;
	}

	if ( is_active_sidebar( 'sidebar_footer_2' ) ) {
		$count++;
	}

	if ( is_active_sidebar( 'sidebar_footer_3' ) ) {
		$count++;
	}

	if ( is_active_sidebar( 'sidebar_footer_4' ) ) {
		$count++;
	}

	$class = '';

	switch ( $count ) {
		case '1':
			$class = 'large-12';
			break;
		case '2':
			$class = 'medium-6 large-6';
			break;
		case '3':
			$class = 'medium-6 large-4';
			break;
		case '4':
			$class = 'medium-6 large-3';
			break;
	}

	if ( $class ) {
		return $class;
	}
}
