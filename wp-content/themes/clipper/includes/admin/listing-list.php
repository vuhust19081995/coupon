<?php
/**
 * Admin coupon listings lists.
 *
 * @package Clipper\Admin\Listings
 * @author  AppThemes
 * @since   Clipper 1.6.0
 */

// Coupon Listing
add_filter( 'manage_edit-' . APP_POST_TYPE . '_columns', 'clpr_edit_columns' );
add_filter( 'manage_edit-' . APP_POST_TYPE . '_sortable_columns', 'clpr_column_sortable' );
add_filter( 'request', 'clpr_column_orderby' );
add_action( 'manage_' . APP_POST_TYPE . '_posts_custom_column', 'clpr_custom_columns', 10, 2 );
// quick edit
add_action( 'quick_edit_custom_box', 'clpr_display_featured_option_quick_edit', 10, 2 );
add_action( 'save_post', 'clpr_save_featured_option_quick_edit', 10, 1 );
// filter default hidden columns
add_filter( 'get_user_option_manageedit-' . APP_POST_TYPE . 'columnshidden', 'clpr_coupon_default_hidden_columns', 10, 1 );

add_action( 'restrict_manage_posts', 'clpr_listing_list_add_filters' );
add_filter( 'parse_query', 'clpr_listing_list_filter_by_taxonomy' );


/**
 * Sets columns for coupon listing on edit.php page.
 *
 * @since 1.6.0
 *
 * @param array $columns
 *
 * @return array
 */
function clpr_edit_columns( $columns ) {

	// Remove to change order of columns.
	unset( $columns['comments'] );
	unset( $columns['date'] );

	$columns['title']                      = __( 'Title', APP_TD );
	$columns['author']                     = __( 'Author', APP_TD );
	$columns['taxonomy-' . APP_TAX_STORE ] = __( 'Store Name', APP_TD );
	$columns['taxonomy-' . APP_TAX_CAT ]   = __( 'Categories', APP_TD );
	$columns['taxonomy-' . APP_TAX_TYPE ]  = __( 'Coupon Type', APP_TD );
	$columns['coupon_code']                = __( 'Coupon', APP_TD );
	$columns['comments']                   = '<div class="vers"><img src="' . esc_url( admin_url( 'images/comment-grey-bubble.png' ) ) . '" /></div>';
	$columns['date']                       = __( 'Date', APP_TD );
	$columns['expire_date']                = __( 'Expires', APP_TD );
	$columns['votes']                      = __( 'Votes', APP_TD );
	$columns['clicks']                     = __( 'Clicks / Views', APP_TD );
	$columns['ctr']                        = __( 'CTR', APP_TD );

	return $columns;
}

/**
 * Registers custom columns as sortable for coupon listing.
 *
 * @since 1.6.0
 *
 * @param array $columns
 *
 * @return array
 */
function clpr_column_sortable( $columns ) {
	$columns['coupon_code'] = 'coupon_code';
	$columns['expire_date'] = 'expire_date';

	return $columns;
}

/**
 * Sets how the columns sorting should work.
 *
 * @since 1.6.0
 *
 * @param array $vars
 *
 * @return array
 */
function clpr_column_orderby( $vars ) {

	if ( isset( $vars['orderby'] ) ) {
		switch ( $vars['orderby'] ) {
			case 'coupon_code' :
				$vars = array_merge( $vars, array( 'meta_key' => 'clpr_coupon_code', 'orderby' => 'meta_value' ) );
				break;
			case 'expire_date' :
				$vars = array_merge( $vars, array( 'meta_key' => 'clpr_expire_date', 'orderby' => 'meta_value' ) );
				break;
		}
	}

	return $vars;
}

/**
 * Displays coupon listing custom columns data.
 *
 * @since 1.6.0
 *
 * @param string $column_index
 * @param int $post_id
 *
 * @return void
 */
function clpr_custom_columns( $column_index, $post_id ) {

	$post = get_post( $post_id );

	if ( ! $post ) {
		return;
	}

	switch ( $column_index ) {

		case 'coupon_code':
			$coupon_type = clpr_get_coupon_type( $post->ID );
			if ( $coupon_type == 'coupon-code' ) {
				echo esc_html( get_post_meta( $post->ID, 'clpr_coupon_code', true ) );
			} else if ( $coupon_type == 'printable-coupon' ) {
				clpr_get_coupon_image( 'thumb-med' );
			} else {
				_e( 'No code', APP_TD );
			}
			break;

		case 'expire_date':
			echo clpr_get_expire_date( $post->ID, 'display' );
			break;

		case 'votes':
			clpr_votes_chart();
			break;

		case 'clicks':
			$clicks = (int) get_post_meta( $post->ID, 'clpr_coupon_aff_clicks', true );
			$views = (int) get_post_meta( $post->ID, 'clpr_total_count', true );
			echo number_format_i18n( $clicks ) . ' / <strong>' . number_format_i18n( $views ) . '</strong>';
			break;

		case 'ctr':
			echo clpr_get_coupon_ctr( $post->ID );
			// add data for inline edit
			echo '<div class="clpr_featured hidden">' . (int) get_post_meta( $post->ID, CLPR_ITEM_FEATURED, true ) . '</div>';
			echo '<div class="clpr_featured_duration hidden">' . (int) get_post_meta( $post->ID, '_clpr_featured_duration', true ) . '</div>';
			break;
	}
}

/**
 * Sets some coupon columns as hidden when user have no preferences saved.
 *
 * @since 1.6.0
 *
 * @param array|bool $hidden_columns An array of user hidden columns. Boolean false if no preferences saved.
 *
 * @return array
 */
function clpr_coupon_default_hidden_columns( $hidden_columns ) {

	$default_hidden_columns = array(
		'author',
		'comments',
		'date',
	);

	// return defaults when user have no preferences saved
	if ( $hidden_columns === false ) {
		return $default_hidden_columns;
	}

	return $hidden_columns;
}

/**
 * Adds the drop-down filter on edit.php page.
 *
 * @since 1.6.0
 *
 * @return void
 */
function clpr_listing_list_add_filters() {
	global $typenow, $wp_query;

	if ( $typenow != APP_POST_TYPE ) {
		return;
	}

	$taxonomies = array( APP_TAX_CAT, APP_TAX_TYPE );

	foreach ( $taxonomies as $taxonomy ) {

		$selected     = isset( $_GET[ $taxonomy ] ) ? $_GET[ $taxonomy ] : '';
		$taxonomy_obj = get_taxonomy( $taxonomy );

		$args = array(
			'show_option_all' => sprintf( __( 'All %s', APP_TD ), $taxonomy_obj->labels->menu_name ),
			'taxonomy'        => $taxonomy,
			'name'            => $taxonomy,
			'orderby'         => 'name',
			'selected'        => $selected,
			'hierarchical'    => true,
			'depth'           => 3,
			'show_count'      => false,
			'hide_empty'      => true,
		);

		$filter_text = sprintf( __( 'Filter by %s', APP_TD ), $taxonomy_obj->labels->singular_name );
		echo html( 'label', array( 'class' => 'screen-reader-text', 'for' => $taxonomy ), $filter_text );
		wp_dropdown_categories( $args );
	}
}

/**
 * Handles the filtering by taxonomy on edit.php page.
 *
 * @since 1.6.0
 *
 * @param object $query
 *
 * @return object
 */
function clpr_listing_list_filter_by_taxonomy( $query ) {
	global $pagenow, $typenow;

	if ( ! is_admin() || $pagenow != 'edit.php' || $typenow != APP_POST_TYPE ) {
		return $query;
	}

	$taxonomies = get_object_taxonomies( $typenow, 'names' );

	foreach ( $taxonomies as $tax_name ) {
		if ( ! empty( $query->query_vars[ $tax_name ] ) && is_numeric( $query->query_vars[ $tax_name ] ) ) {
			$term = get_term_by( 'id', $query->query_vars[ $tax_name ], $tax_name );
			if ( $term ) {
				$query->query_vars[ $tax_name ] = $term->slug;
			}
		}
	}

	return $query;
}

/**
 * Adds the featured option to the quick edit area.
 *
 * @since 1.6.0
 *
 * @param string $column_name
 * @param string $post_type
 *
 * @return void
 */
function clpr_display_featured_option_quick_edit( $column_name, $post_type ) {

	// if post is a coupon and only during the first execution of the action quick_edit_custom_box
	if ( $post_type != APP_POST_TYPE || ! current_user_can( 'edit_others_posts' ) || did_action( 'quick_edit_custom_box' ) !== 1 ) {
		return;
	}
?>
	<fieldset class="inline-edit-col-right">
		<div class="inline-edit-col">
			<label class="alignleft">
				<input type="checkbox" name="clpr_featured" value="1" />
				<span class="checkbox-title"><?php _e( 'Featured Coupon', APP_TD ); ?></span>
			</label>
		    <div class="wp-clearfix"></div>
			<label>
				<span class="title"><?php _e( 'Duration', APP_TD ); ?></span>
				<input class="small-text" id="clpr_featured_duration" name="clpr_featured_duration" value="0" type="number" />
				<span class="description"><?php _e( 'days (0 = Infinite)', APP_TD );?></span>
			</label>
		</div>
	</fieldset>

	<script type="text/javascript">
		jQuery(document).ready(function($) {
			jQuery('#the-list').on('click', 'a.editinline', function() {
				post_id = jQuery(this).closest('tr').attr('id');
				if ( jQuery( '.clpr_featured', '#' + post_id ).text() === '1' ) {
					jQuery( 'input[name="clpr_featured"]', '.inline-edit-row' ).prop( 'checked', true );
				} else {
					jQuery( 'input[name="clpr_featured"]', '.inline-edit-row' ).prop( 'checked', false );
				}
				feat_duration = jQuery( '.clpr_featured_duration', '#' + post_id ).text();
				jQuery( 'input[name="clpr_featured_duration"]', '.inline-edit-row' ).val( feat_duration );
			});
		});
	</script>
<?php
}

/**
 * Saves the featured option from the quick edit area.
 *
 * @since 1.6.0
 *
 * @param int $post_id
 *
 * @return void
 */
function clpr_save_featured_option_quick_edit( $post_id ) {

	// check ajax action and permission
	if ( ! check_ajax_referer( 'inlineeditnonce', '_inline_edit', false ) || ! current_user_can( 'edit_others_posts' ) ) {
		return;
	}

	// check post type
	$post = get_post( $post_id );
	if ( $post->post_type != APP_POST_TYPE ) {
		return;
	}

	// set/unset coupon as featured
	if ( isset( $_POST[CLPR_ITEM_FEATURED] ) ) {
		update_post_meta( $post_id, CLPR_ITEM_FEATURED, '1' );
		$featured = true;
	} else {
		update_post_meta( $post_id, CLPR_ITEM_FEATURED, '0' );
		$featured = false;
	}

	if ( isset( $_POST[ 'clpr_featured_duration' ] ) ) {

		if (!$featured) {
			update_post_meta( $post_id, '_clpr_featured_duration', '' );
			update_post_meta( $post_id, '_clpr_featured_start_date', '' );
		} else {

			$duration = absint( $_POST[ 'clpr_featured_duration' ] );

			if (!is_numeric( $duration ) ) {
				return;
			}

			update_post_meta( $post_id, '_clpr_featured_duration', $duration );

			$start_date = get_post_meta( $post_id, '_clpr_featured_start_date', true);

			if ( empty ( $start_date ) ) {
				update_post_meta( $post_id, '_clpr_featured_start_date', date( 'Y-m-d 00:00:00') );
			}
		}
	}
}
