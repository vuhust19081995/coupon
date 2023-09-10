<?php
/**
 * Listing Admin Security submodule.
 *
 * Provides methods to protect listing related admin stuff from unauthorized
 * access.
 *
 * @package Listing\Admin\Security
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Admin Security class
 */
class APP_Listing_Admin_Security {

	/**
	 * Current Listing module object
	 * @var APP_Listing
	 */
	protected $listing;

	/**
	 * Construct Activate Listing module
	 *
	 * @param APP_Listing $listing Listing module object.
	 */
	public function __construct( APP_Listing $listing ) {

		$this->listing = $listing;

		add_action( 'load-post.php', array( $this, 'protect_edit_item_form' ) );
		add_action( 'load-post-new.php', array( $this, 'protect_new_item_form' ) );
		add_filter( 'post_row_actions', array( $this, 'post_row_actions' ), 10, 2 );
	}

	/**
	 * Redirects user from admin's Edit Post form to front-end form unless user
	 * has appropriate permissions.
	 *
	 * @global string $typenow Current post type
	 */
	public function protect_edit_item_form() {
		global $typenow;

		if ( $typenow !== $this->listing->get_type() || current_user_can( 'edit_others_posts' ) ) {
			return;
		}

		if ( isset( $_GET['post'] ) ) {
			$item_id = (int) $_GET['post'];
		} elseif ( isset( $_POST['post_ID'] ) ) {
			$item_id = (int) $_POST['post_ID'];
		} else {
			$item_id = 0;
		}

		if ( $item_id && ! empty( $_GET['action'] ) && 'edit' === $_GET['action'] ) {

			$url = esc_url_raw( get_edit_post_link( $item_id, 'url' ) );

			wp_redirect( $url );
			appthemes_exit( 'redirect_to_edit_listing_page' );
		}

	}

	/**
	 * Redirects user from admin's New Post form to front-end form unless user
	 * has appropriate permissions.
	 *
	 * @global string $typenow Current post type
	 */
	public function protect_new_item_form() {
		global $typenow;

		if ( $typenow !== $this->listing->get_type() || current_user_can( 'edit_others_posts' ) ) {
			return;
		}

		$url = esc_url_raw( get_permalink( $this->listing->view_process_new->get_page_id() ) );

		wp_redirect( $url );
		appthemes_exit( 'redirect_to_new_listing_page' );

	}

	/**
	 * Controls the actions set on the item row.
	 *
	 * @param array   $actions An array of row action links.
	 * @param WP_Post $item    The item object.
	 *
	 * @return array Changed actions array.
	 */
	public function post_row_actions( $actions, $item ) {
		if ( $item->post_type !== $this->listing->get_type() ) {
			return $actions;
		}

		if ( isset( $actions['inline hide-if-no-js'] ) && ! current_user_can( 'edit_others_posts' ) ) {
			unset( $actions['inline hide-if-no-js'] );
		}

		return $actions;
	}

}
