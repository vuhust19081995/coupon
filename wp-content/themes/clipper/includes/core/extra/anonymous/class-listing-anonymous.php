<?php
/**
 * Listing Anonymous submodule
 *
 * @package Listing\Modules\Anonymous
 * @author  AppThemes
 * @since   Listing 2.0
 */

/**
 * Listing Anonymous class
 */
class APP_Listing_Anonymous extends APP_View {

	/**
	 * Current Listing module object.
	 *
	 * @var APP_Listing
	 */
	protected $listing;

	/**
	 * The process object.
	 *
	 * @var APP_View_Process
	 */
	protected $process;

	/**
	 * Construct Listing Anonymous module
	 *
	 * @param APP_Listing $listing Listing module object.
	 * @param string      $process The listing process module handle for which
	 *                             anonymous to be created.
	 */
	public function __construct( $listing, $process = 'view_process_new' ) {

		if ( ! $listing instanceof APP_Listing || ! $listing->{$process} instanceof APP_View_Process ) {
			return;
		}

		$this->listing = $listing;
		$this->process = $this->listing->{$process};

		parent::__construct();

		// Make sure we use hooks after view_process_new process did it.
		remove_action( 'template_redirect', array( $this, '_template_redirect' ), 9 );
		add_action( 'template_redirect', array( $this, '_template_redirect' ), 99 );

		add_action( "appthemes_checkout_{$this->process->get_checkout_type()}_completed", array( $this, 'complete_process' ), 99 );
	}

	/**
	 * Test if this class should handle the current view.
	 *
	 * Use is_*() conditional tags and get_query_var()
	 *
	 * @return bool
	 */
	public function condition() {
		if ( is_page_template( $this->process->get_template() ) ) {
			return true;
		}
	}

	/**
	 * Allows to create anonymous only if original process fails with
	 * 'require_login' error.
	 *
	 * No other errors allowed.
	 *
	 * @return bool.
	 */
	public function check_access() {
		$errors = $this->process->get_errors();
		$codes  = $errors->get_error_codes();

		return in_array( 'require_login', $codes ) && 1 === count( $codes );
	}

	/**
	 * Calls actions on the template_redirect hook.
	 */
	public function template_redirect() {
		$errors = $this->process->get_errors();

		// Means there is only one error and this error is 'require_login'.
		if ( $this->check_access() ) {
			$errors->add( 'require_login', __( 'Alternatively you can continue using an anonymous account.' ) );
			do_action( 'appthemes_listing_create_anonymous_scripts' );
		}
	}

	/**
	 * Only being on the process page we can create and authenticate anonymous
	 * user.
	 *
	 * @param string $path Path to the page template.
	 *
	 * @return string
	 */
	public function template_include( $path ) {
		if ( $this->check_access() ) {
			$this->setup_checkout();

			$step_found = appthemes_process_checkout();

			if ( ! $step_found ) {
				$path = locate_template( '404.php' );
			}
		}

		return $path;
	}

	/**
	 * Trigger final process actions until checkout will be removed.
	 *
	 * Change listing author from anonymous to administrator.
	 *
	 * @param APP_Dynamic_Checkout $checkout The checkout object.
	 */
	public function complete_process( $checkout ) {

		$item_id = $checkout->get_data( 'listing_id' );
		$user_id = (int) get_post_field( 'post_author', $item_id );
		$author  = get_userdata( $user_id );

		if ( $author && $author->nickname === $this->get_nickname() ) {

			if ( get_current_user_id() === $user_id ) {
				wp_logout();
				wp_set_current_user( 0 );
			}

			$this->delete_user( $user_id );
		}
	}

	/**
	 * Removes anonymous user from the db and reassign his stuff to admin.
	 *
	 * @param int $user_id User ID.
	 */
	function delete_user( $user_id ) {
		global $wpdb;

		require_once ABSPATH . '/wp-admin/includes/user.php';
		// Delete anonymous and reassign his stuff to administrator.
		wp_delete_user( $user_id, 1 );
		// Make sure user completely deleted even on the multisite.
		$wpdb->delete( $wpdb->users, array( 'ID' => $user_id ) );
	}

	/**
	 * Setup Checkout Object specifically for current process.
	 */
	protected function setup_checkout() {
		appthemes_setup_checkout( $this->get_checkout_type(), $this->process->get_process_url() );
	}

	/**
	 * Retrieves associated checkout type.
	 *
	 * @return string Checkout type.
	 */
	public function get_checkout_type() {
		return "{$this->listing->get_type()}-anonym";
	}

	/**
	 * Retrieves the anonymous user login associated with concrete listing
	 * process.
	 *
	 * @param APP_Dynamic_Checkout $checkout The checkout object.
	 *
	 * @return string
	 */
	public function get_login( $checkout ) {
		return "anonymous_{$checkout->get_hash()}";
	}

	/**
	 * Retrieves the anonymous user nickname associated with listing process.
	 *
	 * @return string
	 */
	public function get_nickname() {
		return "anonymous_{$this->process->get_process_type()}_{$this->listing->get_type()}_" . get_current_blog_id();
	}

	/**
	 * Retrieves listing process related to anonymous.
	 *
	 * @return APP_View_Process
	 */
	public function get_process() {
		return $this->process;
	}

}
