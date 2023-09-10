<?php
/**
 * Admin Coupon Listings Metaboxes.
 *
 * @package Clipper\Admin\Metaboxes\Listings
 * @author  AppThemes
 * @since   Clipper 1.6
 */


add_action( 'add_meta_boxes_' . APP_POST_TYPE, 'clpr_setup_meta_box' );
add_filter( 'media_upload_tabs', 'clpr_remove_media_library_tab' );


/**
 * Adds and removes meta boxes on the coupon edit admin page.
 *
 * @return void
 */
function clpr_setup_meta_box() {

	//remove_meta_box( 'storesdiv', APP_POST_TYPE, 'side' );
	remove_meta_box( 'coupon_typediv', APP_POST_TYPE, 'side' );

	remove_meta_box( 'postimagediv', APP_POST_TYPE, 'side' );
	remove_meta_box( 'postexcerpt', APP_POST_TYPE, 'normal' );
	remove_meta_box( 'authordiv', APP_POST_TYPE, 'normal' );
	remove_meta_box( 'postcustom', APP_POST_TYPE, 'normal' );

	//custom post statuses
	//temporary hack until WP will fully support custom post statuses
	remove_meta_box( 'submitdiv', APP_POST_TYPE, 'side' );
	add_meta_box( 'submitdiv', __( 'Publish', APP_TD ), 'clpr_post_submit_meta_box', APP_POST_TYPE, 'side', 'high' );

}


/**
 * Removes media library tab to escape assignment second time the same printable coupon.
 *
 * @param array $tabs
 *
 * @return array
 */
function clpr_remove_media_library_tab( $tabs ) {
	if ( isset( $_REQUEST['post_id'] ) ) {
		$post_type = get_post_type( $_REQUEST['post_id'] );
		if ( APP_POST_TYPE == $post_type ) {
			unset( $tabs['library'] );
		}
	}

	return $tabs;
}


/**
 * Coupon Listing Info Metabox.
 * @since 1.6
 */
class CLPR_Listing_Info_Metabox extends APP_Meta_Box {

	/**
	 * Sets up metabox.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct( 'coupon-info', __( 'Coupon Info', APP_TD ), APP_POST_TYPE, 'normal', 'high' );
	}

	/**
	 * Returns an array of form fields.
	 *
	 * @return array
	 */
	public function form() {
		$form_fields = array(
			array(
				'title' => __( 'Reference ID', APP_TD ),
				'type' => 'text',
				'name' => 'clpr_id',
				'default' => clpr_generate_id(),
				'extra' => array( 'readonly' => 'readonly' ),
			),
			array(
				'title' => __( 'Display URL', APP_TD ),
				'type' => 'text',
				'name' => '_blank',
				'desc' => $this->get_display_url(),
				'extra' => array( 'style' => 'display: none;' ),
			),
			array(
				'title' => __( 'Views Today', APP_TD ),
				'type' => 'text',
				'name' => 'clpr_daily_count',
				'sanitize' => 'absint',
				'default' => '0',
				'extra' => array( 'readonly' => 'readonly' ),
			),
			array(
				'title' => __( 'Views Total', APP_TD ),
				'type' => 'text',
				'name' => 'clpr_total_count',
				'sanitize' => 'absint',
				'default' => '0',
				'extra' => array( 'readonly' => 'readonly' ),
			),
			array(
				'title' => __( 'Clicks', APP_TD ),
				'type' => 'text',
				'name' => 'clpr_coupon_aff_clicks',
				'sanitize' => 'absint',
				'default' => '0',
				'extra' => array( 'readonly' => 'readonly' ),
			),
			array(
				'title' => __( 'CTR', APP_TD ),
				'type' => 'text',
				'name' => '_blank',
				'desc' => clpr_get_coupon_ctr( $this->get_post_id() ),
				'extra' => array( 'style' => 'display: none;' ),
			),
			array(
				'title' => __( 'Votes Up', APP_TD ),
				'type' => 'text',
				'name' => 'clpr_votes_up',
				'sanitize' => 'absint',
				'default' => '0',
				'extra' => array( 'readonly' => 'readonly' ),
			),
			array(
				'title' => __( 'Votes Down', APP_TD ),
				'type' => 'text',
				'name' => 'clpr_votes_down',
				'sanitize' => 'absint',
				'default' => '0',
				'extra' => array( 'readonly' => 'readonly' ),
			),
			array(
				'title' => __( 'Votes Percent', APP_TD ),
				'type' => 'text',
				'name' => 'clpr_votes_percent',
				'sanitize' => 'absint',
				'default' => '100',
				'extra' => array( 'readonly' => 'readonly' ),
			),
			array(
				'title' => __( 'Votes Chart', APP_TD ),
				'type' => 'text',
				'name' => '_blank',
				'desc' => $this->get_votes_chart(),
				'extra' => array( 'style' => 'display: none;' ),
			),
			array(
				'title' => __( 'Submitted from IP', APP_TD ),
				'type' => 'text',
				'name' => 'clpr_sys_userIP',
				'default' => appthemes_get_ip(),
				'extra' => array( 'readonly' => 'readonly' ),
			),
		);

		return $form_fields;
	}

	/**
	 * Filter data before save.
	 *
	 * @param array $post_data
	 * @param int $post_id
	 *
	 * @return array
	 */
	protected function before_save( $post_data, $post_id ) {
		$post_keys = get_post_custom_keys( $post_id );

		// do not update fields if already exist
		foreach ( $post_keys as $post_key ) {
			if ( isset( $post_data[ $post_key ] ) ) {
				unset( $post_data[ $post_key ] );
			}
		}

		return $post_data;
	}

	/**
	 * Returns coupon outgoing URL.
	 *
	 * @return string
	 */
	public function get_display_url() {

		$post = get_post( $this->get_post_id() );
		$url = clpr_get_coupon_out_cloak_url( $post );

		$output = html( 'code', $url ) . ' ';
		if ( 'auto-draft' != $post->post_status ) {
			$output .= html( 'a', array( 'href' => $url, 'target' => '_blank' ), __( 'Visit link', APP_TD ) );
		}

		return $output;
	}

	/**
	 * Returns votes chart.
	 *
	 * @return string
	 */
	public function get_votes_chart() {
		ob_start();
		clpr_votes_chart( $this->get_post_id() );
		$chart = ob_get_clean();

		if ( empty( $chart ) ) {
			$chart = __( 'No votes yet', APP_TD );
		}

		return $chart;
	}

}

/**
 * Coupon Listing Author Metabox.
 * @since 1.6
 */
class CLPR_Listing_Author_Metabox extends APP_Meta_Box {

	/**
	 * Sets up metabox.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct( 'listingauthordiv', __( 'Author', APP_TD ), APP_POST_TYPE, 'side', 'low' );
	}

	/**
	 * Checks if metabox should be registered.
	 *
	 * @return bool
	 */
	protected function condition() {
		return current_user_can( 'edit_others_posts' );
	}

	/**
	 * Displays content.
	 *
	 * @param object $post
	 *
	 * @return void
	 */
	public function display( $post ) {
		$author_id = empty( $post->post_author ) ? get_current_user_id() : $post->post_author;
		$author = get_userdata( $author_id );
		// display current author avatar and edit link
		echo html( 'div', array( 'class' => 'avatar' ), get_avatar( $author_id, '96', '' ) );
		$edit_url = get_edit_user_link( $author_id );
		if ( $edit_url ) {
			echo html( 'p', html_link( $edit_url, sprintf( __( 'Edit: %s', APP_TD ), $author->display_name ) ) );
		}
		?>
		<label class="screen-reader-text" for="post_author_override"><?php _e( 'Author', APP_TD ); ?></label>
		<?php
		wp_dropdown_users( array(
			/* 'who' => 'authors', */
			'name' => 'post_author_override',
			'selected' => $author_id,
			'include_selected' => true,
		) );
	}

}


/**
 * Coupon Listing Moderation Metabox.
 * @since 1.4
 */
class CLPR_Listing_Publish_Moderation extends APP_Meta_Box {

	/**
	 * Sets up metabox.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct( 'listing-publish-moderation', __( 'Moderation Queue', APP_TD ), APP_POST_TYPE, 'side', 'high' );
	}

	/**
	 * Checks if metabox should be registered.
	 *
	 * @return bool
	 */
	protected function condition() {
		if ( ! isset( $_GET['post'] ) || get_post_status( $_GET['post'] ) != 'pending' ) {
			return false;
		}

		return current_user_can( 'edit_others_posts' );
	}

	/**
	 * Displays content.
	 *
	 * @param object $post
	 *
	 * @return void
	 */
	public function display( $post ) {

		echo html( 'p', array(), __( 'You must approve this coupon before it can be published.', APP_TD ) );

		echo html( 'input', array(
			'type' => 'submit',
			'class' => 'button-primary',
			'value' => __( 'Accept', APP_TD ),
			'name' => 'publish',
			'style' => 'padding-left: 30px; padding-right: 30px; margin-right: 20px; margin-left: 15px;',
		) );

		echo html( 'a', array(
			'class' => 'button',
			'style' => 'padding-left: 30px; padding-right: 30px;',
			'href' => get_delete_post_link( $post->ID ),
		), __( 'Reject', APP_TD ) );

		echo html( 'p', array(
			'class' => 'howto'
		), __( 'Rejecting a Coupon sends it to the trash.', APP_TD ) );

	}

}

