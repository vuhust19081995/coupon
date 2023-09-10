<?php
/**
 * Admin Coupon Stores list.
 *
 * @package Clipper\Admin\Stores
 * @author  AppThemes
 * @since   Clipper 1.6
 */


// Stores list (right panel)
add_filter( 'manage_edit-' . APP_TAX_STORE . '_columns', 'clpr_stores_column_headers', 10, 1 );
add_filter( 'manage_edit-' . APP_TAX_STORE . '_sortable_columns', 'clpr_column_stores_sortable' );
add_filter( 'manage_' . APP_TAX_STORE . '_custom_column', 'clpr_stores_column_row', 10, 3 );

// Quick Edit Store (right panel)
add_action( 'quick_edit_custom_box', 'clpr_quick_edit_values', 10, 3 );
add_action( 'admin_enqueue_scripts', 'clpr_quick_edit_stores_scripts' );


/**
 * Sets the stores taxonomy headers.
 *
 * @param array $columns
 *
 * @return array
 */
function clpr_stores_column_headers( $columns ) {
	$columns = array(
		'cb' => '<input type="checkbox" />',
		'clpr_store_image' => __( 'Image', APP_TD ),
		'name' => __( 'Name', APP_TD ),
		'short_description' => __( 'Description', APP_TD ),
		'clpr_store_url' => __( 'Store URL', APP_TD ),
		'clpr_store_aff_url' => __( 'Destination URL', APP_TD ),
		'clpr_store_slug' => __( 'Slug', APP_TD ),
		'clpr_store_featured' => __( 'Featured', APP_TD ),
		'clpr_store_active' => __( 'Active', APP_TD ),
		'posts' => __( 'Coupons', APP_TD ),
		//'clpr_store_clicks' => __( 'Clicks', APP_TD ),
	);

	return $columns;
}


/**
 * Returns content for the custom store columns.
 *
 * @param string $row_content
 * @param string $column_name
 * @param int $term_id
 *
 * @return string
 */
function clpr_stores_column_row( $row_content, $column_name, $term_id ) {
	global $taxonomy;

	switch ( $column_name ) {

		case 'clpr_store_image':
			return '<img class="store-thumb" src="' . clpr_get_store_image_url( $term_id, 'term_id', 75 ) . '" />';
			break;

		case 'short_description':
			$string = strip_tags( term_description( $term_id, $taxonomy ) );
			if ( strlen( $string ) > 250 ) {
				$string = mb_substr( $string, 0, 250 ) . '...';
			}
			return $string;
			break;

		case 'clpr_store_url':
			return esc_url( clpr_get_store_meta( $term_id, 'clpr_store_url', true ) );
			break;

		case 'clpr_store_slug':
			$term=get_term($term_id);
			return esc_html($term->slug);
			break;

		case 'clpr_store_aff_url':
			return esc_url( clpr_get_store_meta( $term_id, 'clpr_store_aff_url', true ) );
			break;

		case 'clpr_store_active':
			$store_active = clpr_get_store_meta( $term_id, 'clpr_store_active', true );
			if ( $store_active == 'no' ) {
				return '<span class="active-no">' . __( 'No', APP_TD ) . '</span>';
			} else {
				return '<span class="active-yes">' . __( 'Yes', APP_TD ) . '</span>';
			}
			break;

		case 'clpr_store_featured':
			$store_featured = clpr_get_store_meta( $term_id, 'clpr_store_featured', true );
			if ( ! $store_featured ) {
				return '<span class="active-no">' . __( 'No', APP_TD ) . '</span>';
			} else {
				return '<span class="active-yes">' . __( 'Yes', APP_TD ) . '</span>';
			}
			break;

		case 'clpr_store_clicks':
			$clicks = clpr_get_store_meta( $term_id, 'clpr_aff_url_clicks', true );
			$clicks = $clicks ? $clicks : 0;
			return $clicks;
			break;

		default:
			break;

	}

}


/**
 * Registers the short_description column as sortable.
 *
 * @param array $columns
 *
 * @return array
 */
function clpr_column_stores_sortable( $columns ) {
	// Use WP built in handler for 'Order by Description'
	$columns['short_description'] = 'description';

	return $columns;
}


/**
 * Displays fields in quick edit store mode.
 *
 * @param string $column_name
 * @param object $screen
 * @param string $name (optional)
 *
 * @return void
 */
function clpr_quick_edit_values( $column_name, $screen, $name = null ) {

	if ( $name != APP_TAX_STORE ) {
		return;
	}

	if ( $column_name == 'clpr_store_url' ) {
?>
	<fieldset>
		<div class="inline-edit-col">

			<label>
				<span class="title"><?php _e( 'Store URL', APP_TD ); ?></span>
				<span class="input-text-wrap"><input type="url" name="clpr_store_url" class="ptitle" value="" placeholder="http://" /></span>
			</label>

		</div>
	</fieldset>
<?php
	}
	if ( $column_name == 'clpr_store_aff_url' ) {
?>
	<fieldset>
		<div class="inline-edit-col">

			<label>
				<span class="title"><?php _e( 'Destination URL', APP_TD ); ?></span>
				<span class="input-text-wrap"><input type="url" name="clpr_store_aff_url" class="ptitle" value="" placeholder="http://" /></span>
			</label>

		</div>
	</fieldset>
<?php
	}
	if ( $column_name == 'clpr_store_active' ) {
?>
	<fieldset>
		<div class="inline-edit-col">

			<label>
				<span class="title"><?php _e( 'Active', APP_TD ); ?></span>
				<span class="input-text-wrap">
					<select class="postform" id="clpr_store_active" name="clpr_store_active" style="min-width:125px;">
						<option value="yes"><?php _e( 'Yes', APP_TD ); ?></option>
						<option value="no"><?php _e( 'No', APP_TD ); ?></option>
					</select>
				</span>
			</label>

		</div>
	</fieldset>
<?php
	}
	if ( $column_name == 'clpr_store_featured' ) {
?>
	<fieldset>
		<div class="inline-edit-col">

			<label>
				<span class="title"><?php _e( 'Featured', APP_TD ); ?></span>
				<span class="input-text-wrap">
					<input type="checkbox" value="1" name="clpr_store_featured" > <?php _e( 'Yes', APP_TD ); ?>
				</span>
			</label>

		</div>
	</fieldset>
<?php
	}

}


/**
 * Enqueues admin quick edit stores scripts.
 *
 * @return void
 */
function clpr_quick_edit_stores_scripts() {
	global $pagenow;

	if ( $pagenow == 'edit-tags.php' && ( isset( $_GET['taxonomy'] ) && $_GET['taxonomy'] == APP_TAX_STORE ) && ! isset( $_GET['action'] ) ) {
		wp_enqueue_script( 'quick-edit-stores-js', get_template_directory_uri() . '/includes/js/quick-edit-stores.js', array( 'jquery' ), '1.5' );
	}

}
