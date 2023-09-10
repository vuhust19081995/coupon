<?php
/**
 * Views.
 *
 * @package Clipper\Views
 * @author  AppThemes
 * @since   Clipper 1.3.1
 */


/**
 * Blog Archive page view.
 */
class CLPR_Blog_Archive extends APP_View_Page {

	/**
	 * Setups page view.
	 *
	 * @return void
	 */
	function __construct() {
		parent::__construct( 'index.php', __( 'Blog', APP_TD ) );
	}

	/**
	 * Returns page ID.
	 *
	 * @return int
	 */
	static function get_id() {
		return parent::_get_page_id( 'index.php' );
	}

}


/**
 * Coupons Home page view.
 */
class CLPR_Coupons_Home extends APP_View_Page {

	/**
	 * Setups page view.
	 *
	 * @return void
	 */
	function __construct() {
		parent::__construct( 'front-page.php', __( 'Coupon Listings', APP_TD ) );
	}

	/**
	 * Returns page ID.
	 *
	 * @return int
	 */
	static function get_id() {
		return parent::_get_page_id( 'front-page.php' );
	}

	/**
	 * Fires before determining which template to load.
	 *
	 * @return void
	 */
	function template_redirect() {
		global $wp_query;

		// if page on front, set back paged parameter
		if ( self::get_id() == get_option( 'page_on_front' ) ) {
			$paged = get_query_var( 'page' );
			$wp_query->set( 'paged', $paged );
		}

	}

}


/**
 * Coupons Categories page view.
 */
class CLPR_Coupon_Categories extends APP_View_Page {

	/**
	 * Setups page view.
	 *
	 * @return void
	 */
	function __construct() {
		parent::__construct( 'tpl-coupon-cats.php', __( 'Categories', APP_TD ) );
	}

	/**
	 * Returns page ID.
	 *
	 * @return int
	 */
	static function get_id() {
		return parent::_get_page_id( 'tpl-coupon-cats.php' );
	}

}


/**
 * Coupons Stores page view.
 */
class CLPR_Coupon_Stores extends APP_View_Page {

	/**
	 * Setups page view.
	 *
	 * @return void
	 */
	function __construct() {
		parent::__construct( 'tpl-stores.php', __( 'Stores', APP_TD ) );
	}

	/**
	 * Returns page ID.
	 *
	 * @return int
	 */
	static function get_id() {
		return parent::_get_page_id( 'tpl-stores.php' );
	}

}


/**
 * User Dashboard page view.
 */
class CLPR_User_Dashboard extends APP_View_Page {

	/**
	 * Setups page view.
	 *
	 * @return void
	 */
	function __construct() {
		parent::__construct( 'tpl-dashboard.php', __( 'Dashboard', APP_TD ) );
	}

	/**
	 * Returns page ID.
	 *
	 * @return int
	 */
	static function get_id() {
		return parent::_get_page_id( 'tpl-dashboard.php' );
	}

	/**
	 * Fires before determining which template to load.
	 *
	 * @return void
	 */
	function template_redirect() {
		global $wpdb, $current_user;
		appthemes_auth_redirect_login(); // if not logged in, redirect to login page
		nocache_headers();

		$allowed_actions = array( 'pause', 'restart', 'delete' );

		if ( ! isset( $_GET['action'] ) || ! in_array( $_GET['action'], $allowed_actions ) ) {
			return;
		}

		if ( ! isset( $_GET['aid'] ) || ! is_numeric( $_GET['aid'] ) ) {
			return;
		}

		$d = trim( $_GET['action'] );
		$aid = appthemes_numbers_only( $_GET['aid'] );

		// make sure author matches coupon, and coupon exist
		$sql = $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE ID = %d AND post_author = %d AND post_type = %s", $aid, $current_user->ID, APP_POST_TYPE );
		$post = $wpdb->get_row( $sql );
		if ( $post == null ) {
			return;
		}

		if ( $d == 'pause' ) {
			wp_update_post( array( 'ID' => $aid, 'post_status' => 'draft' ) );
			appthemes_add_notice( 'paused', __( 'Your coupon has been paused.', APP_TD ), 'success' );
			wp_redirect( clpr_get_dashboard_url( 'redirect' ) );
			exit();

		} elseif ( $d == 'restart' ) {
			wp_update_post( array( 'ID' => $aid, 'post_status' => 'publish' ) );
			appthemes_add_notice( 'restarted', __( 'Your coupon has been restarted.', APP_TD ), 'success' );
			wp_redirect( clpr_get_dashboard_url( 'redirect' ) );
			exit();

		} elseif ( $d == 'delete' ) {
			clpr_delete_coupon( $aid );
			appthemes_add_notice( 'deleted', __( 'Your coupon has been deleted.', APP_TD ), 'success' );
			wp_redirect( clpr_get_dashboard_url( 'redirect' ) );
			exit();

		}

	}

}


/**
 * User Orders page view.
 */
class CLPR_User_Orders extends APP_View_Page {

	/**
	 * Setups page view.
	 *
	 * @return void
	 */
	function __construct() {
		parent::__construct( 'tpl-user-orders.php', __( 'My Orders', APP_TD ) );
	}

	/**
	 * Returns page ID.
	 *
	 * @return int
	 */
	static function get_id() {
		return parent::_get_page_id( 'tpl-user-orders.php' );
	}

	/**
	 * Fires before determining which template to load.
	 *
	 * @return void
	 */
	function template_redirect() {
		// if not logged in, redirect to login page
		appthemes_auth_redirect_login();

		// if payments disabled, redirect to dashboard
		if ( ! clpr_payments_is_enabled() ) {
			appthemes_add_notice( 'payments-disabled', __( 'Payments are currently disabled. You cannot purchase anything.', APP_TD ), 'error' );
			wp_redirect( clpr_get_dashboard_url( 'redirect' ) );
			exit();
		}
	}

}


/**
 * Edit Profile page view.
 */
class CLPR_User_Profile extends APP_User_Profile {

	/**
	 * Setups page view.
	 *
	 * @return void
	 */
	function __construct() {
		APP_View_Page::__construct( 'tpl-profile.php', __( 'Edit Profile', APP_TD ) );
		add_action( 'init', array( $this, 'update' ) );
		add_action( 'user_profile_update_errors', array( $this, 'user_profile_update_errors' ), 10, 3 );
	}

	/**
	 * Returns page ID.
	 *
	 * @return int
	 */
	static function get_id() {
		return parent::_get_page_id( 'tpl-profile.php' );
	}


	/**
	 * Enqueues scripts
	 */
	public function enqueue_scripts() {
		parent::enqueue_scripts();

		appthemes_enqueue_media_manager();
	}

	/**
	 * Processes custom user fields.
	 *
	 * @param WP_Error $errors WP_Error object, passed by reference.
	 * @param bool     $update Whether this is a user update.
	 * @param stdClass $user   User object, passed by reference.
	 */
	public function user_profile_update_errors( $errors, $update, $user ) {

		$verify = isset( $_REQUEST['_wpnonce'] ) ? wp_verify_nonce( $_REQUEST['_wpnonce'], 'app-edit-profile' ) : false;

		if ( ! $verify ) {
			return;
		}

		$listing    = APP_Listing_Director::get( 'user' );
		$core_names = $listing->meta->get_core_fields();
		$fields     = $listing->form->get_form_fields( array(
			'name' => $core_names,
		), array(), $user->ID );

		if ( empty( $fields ) ) {
			return;
		}

		$user_id = $listing->form->process_form( $user->ID, 'app-edit-profile', '_wpnonce', $fields );

		if ( $user_id instanceof WP_Error && $user_id->get_error_codes() ) {
			foreach ( $user_id->get_error_codes() as $error_code ) {
				$errors->add( $error_code, $user_id->get_error_message( $error_code ) );
			}
		}
	}

}


/**
 * Single Coupon Listing view.
 */
class CLPR_Coupon_Single extends APP_View {

	/**
	 * Checks if class should handle current view.
	 *
	 * @return bool
	 */
	function condition() {
		return is_singular( APP_POST_TYPE );
	}

	/**
	 * Displays notices.
	 *
	 * @return void
	 */
	function notices() {
		$status = get_post_status( get_queried_object() );

		if ( $status == 'pending' ) {
			appthemes_display_notice( 'success', __( 'This coupon listing is currently pending and must be approved by an administrator.', APP_TD ) );
		}
	}

}

