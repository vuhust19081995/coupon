<?php
/**
 * Admin Coupon Stores Metaboxes.
 *
 * @package Clipper\Admin\Metaboxes\Stores
 * @author  AppThemes
 * @since   Clipper 1.6
 */


add_action( APP_TAX_STORE . '_edit_form_fields', 'clpr_edit_stores', 10, 2 );
add_action( 'edited_' . APP_TAX_STORE, 'clpr_save_stores', 10, 2 );
add_filter( 'media_upload_tabs', 'clpr_stores_media_remove_from_url_tab' );


/**
 * Displays the custom url meta field for the stores taxonomy.
 *
 * @param object $tag
 * @param string $taxonomy
 *
 * @return void
 */
function clpr_edit_stores( $tag, $taxonomy ) {
	$the_store_url = clpr_get_store_meta( $tag->term_id, 'clpr_store_url', true );
	$the_store_aff_url_clicks = clpr_get_store_meta( $tag->term_id, 'clpr_aff_url_clicks', true );
?>

	<tr class="form-field">
		<th scope="row" valign="top"><label for="clpr_store_aff_url_cloaked"><?php _e( 'Display URL', APP_TD ); ?></label></th>
		<td><?php echo clpr_get_store_out_url( $tag ); ?></td>
	</tr>

	<tr class="form-field">
		<th scope="row" valign="top"><label for="clpr_aff_url_clicks"><?php _e( 'Clicks', APP_TD ); ?></label></th>
		<td><?php echo esc_html( $the_store_aff_url_clicks ); ?></td>
	</tr>

	<tr class="form-field">
		<th scope="row" valign="top"><label for=""><?php _e( 'Store Screenshot', APP_TD ); ?></label></th>
		<td>
			<span class="thumb-wrap">
				<a href="<?php echo esc_url( $the_store_url ); ?>" target="_blank"><img class="store-thumb" src="<?php echo esc_url( clpr_get_store_image_url( $tag->term_id, 'term_id', 250 ) ); ?>" alt="" /></a>
			</span>
		</td>
	</tr>
<?php
}


/**
 * Saves the store url custom meta field.
 *
 * @param int $term_id
 * @param int $tt_id
 *
 * @return void
 */
function clpr_save_stores( $term_id, $tt_id ) {
	if ( ! $term_id ) {
		return;
	}

	if ( wp_verify_nonce( $_REQUEST['_inline_edit'], 'taxinlineeditnonce' ) ) {
		// Store URL.
		if ( isset( $_POST['clpr_store_url'] ) ) {
			$url = wp_http_validate_url( $_POST['clpr_store_url'] );
			if ( $url ) {
				clpr_update_store_meta( $term_id, 'clpr_store_url', $url );
			} else {
				clpr_update_store_meta( $term_id, 'clpr_store_url', '' );
			}
		}

		// Store Affiliate URL.
		if ( isset( $_POST['clpr_store_aff_url'] ) ) {
			$url = wp_http_validate_url( $_POST['clpr_store_aff_url'] );
			if ( $url ) {
				clpr_update_store_meta( $term_id, 'clpr_store_aff_url', $url );
			} else {
				clpr_update_store_meta( $term_id, 'clpr_store_aff_url', '' );
			}
		}

		// Store Active.
		if ( isset( $_POST['clpr_store_active'] ) && in_array( $_POST['clpr_store_active'], array( 'yes', 'no' ) ) ) {
			clpr_update_store_meta( $term_id, 'clpr_store_active', $_POST['clpr_store_active'] );
		}

		// Store Featured.
		if ( isset( $_POST['clpr_store_featured'] ) ) {
			clpr_update_store_meta( $term_id, 'clpr_store_featured', $_POST['clpr_store_featured'] );
		} else {
			clpr_update_store_meta( $term_id, 'clpr_store_featured', '' );
		}
	}

	delete_transient( 'clpr_hidden_stores_ids' );
	delete_transient( 'clpr_featured_stores_ids' );
}


/**
 * Removes 'From URL' tab in media uploader, need local image for stores.
 *
 * @param array $tabs
 *
 * @return array
 */
function clpr_stores_media_remove_from_url_tab( $tabs ) {

	if ( isset( $_GET['taxonomy'] ) && $_GET['taxonomy'] == APP_TAX_STORE ) {
		unset( $tabs['type_url'] );
	}

	return $tabs;
}


/**
 * Admin HTML Store Description.
 * @since 1.6
 */
class CLPR_Store_Term_Description extends CLPR_Term_Description {

	/**
	 * Setups editor.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct( APP_TAX_STORE );
	}

}

