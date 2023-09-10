<?php
/**
 * Listing Process Views
 *
 * @package Listing\Views\Processes
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Basic Listing processing class
 *
 * Requires theme supports:
 *	- app-framework
 *  - app-checkout
 */
class APP_View_Process extends APP_View_Page {

	/**
	 * Current Listing module object
	 * @var APP_Listing
	 */
	protected $listing;

	/**
	 * The Process type
	 * @var string
	 */
	protected $process;

	/**
	 * If only logged-in users can execute a process
	 * @var bool
	 */
	protected $require_login;

	/**
	 * The user capability required to execute a process
	 * @var string
	 */
	protected $capability;

	/**
	 * The errors object
	 * @var WP_Error
	 */
	protected $errors;

	/**
	 * The processes registry.
	 * @var array
	 */
	private static $registry = array();

	/**
	 * Construct processing page view
	 *
	 * @param APP_Listing $listing Listing object to assign process with.
	 * @param string      $process The Process type.
	 * @param array       $args {
	 *     Optional. An array of arguments.
	 *
	 *     @type bool   $require_login If only logged-in users can execute a
	 *                                 process. Default true.
	 *     @type string $capability    The user capability required to execute a
	 *                                 process.
	 */
	public function __construct( APP_Listing $listing, $process, $args = array() ) {

		if ( isset( self::$registry[ $listing->get_type() ] ) ) {
			if ( isset( self::$registry[ $listing->get_type() ][ $process ] ) ) {
				return;
			}
		}

		$defaults = array(
			'require_login' => true,
			'capability'    => '',
			'process_title' => __( 'Process Listing', APP_TD ),
		);

		$templates = array(
			"process-{$listing->get_type()}-{$process}.php",
			"process-{$listing->get_type()}.php",
			'process.php',
		);

		$args = wp_parse_args( $args, $defaults );

		$this->listing       = $listing;
		$this->process       = $process;
		$this->require_login = $args['require_login'];
		$this->capability    = $args['capability'];
		$this->errors        = new WP_Error();

		add_action( "appthemes_checkout_{$this->get_checkout_type()}_created", array( $this, 'add_checkout_data' ) );
		add_action( "appthemes_checkout_{$this->get_checkout_type()}_completed", array( $this, 'complete_process' ) );
		add_action( "appthemes_checkout_{$this->get_checkout_type()}_completed", array( $this, 'remove_checkout_data' ), 11 );

		parent::__construct( $templates, $args['process_title'] );

		self::$registry[ $listing->get_type() ][ $process ] = $this;
	}

	/**
	 * Retrieves registered processes by listing type and process type.
	 *
	 * @param string $listing_type Listing type.
	 * @param string $process_type Process type.
	 *
	 * @return APP_View_Process|array If $process_type is set and registered,
	 *                                will return the Listing Process instance,
	 *                                otherwise will return an array of all
	 *                                registered processes by given listing type.
	 */
	public final static function get_process( $listing_type, $process_type = null ) {

		if ( isset( self::$registry[ $listing_type ] ) ) {
			if ( $process_type && isset( self::$registry[ $listing_type ][ $process_type ] ) ) {
				return self::$registry[ $listing_type ][ $process_type ];
			} else {
				return self::$registry[ $listing_type ];
			}
		}
	}

	/**
	 * Retrieves current process type.
	 *
	 * @return string The process type.
	 */
	public function get_process_type() {
		return $this->process;
	}

	/**
	 * Retrieves associated checkout type.
	 *
	 * @return string Checkout type.
	 */
	public function get_checkout_type() {
		$type = "{$this->listing->get_type()}-{$this->get_process_type()}";
		if ( strlen( $type ) > 25 ) {
			trigger_error( sprintf( 'Checkout type must not be more than 25 charachters length, "%s" given', $type ), E_USER_WARNING );
		}
		return $type;
	}

	/**
	 * Retrieves the name of connection between processed item and checkout type.
	 *
	 * @return string Connection type.
	 */
	public function get_connection_type() {
		return "_in_process_{$this->get_checkout_type()}";
	}

	/**
	 * Whether only logged-in users can execute a process
	 *
	 * @return bool
	 */
	protected function is_require_login() {
		return $this->require_login;
	}

	/**
	 * Retrieves user capability required to execute a process
	 *
	 * @param string $cap_type Capability type.
	 *
	 * @return type
	 */
	public function user_capability( $cap_type = null ) {

		$ptype_obj = get_post_type_object( $this->listing->get_type() );

		if ( is_null( $cap_type ) ) {
			$cap_type = $this->capability;
		}

		if ( isset( $ptype_obj->cap->$cap_type ) ) {
			$cap = $ptype_obj->cap->$cap_type;
		} else {
			$cap = $cap_type;
		}

		return $cap;
	}

	/**
	 * Checks accessibility and redirects user if access is not allowed
	 */
	protected function check_access() {

		if ( $this->is_require_login() && ! is_user_logged_in() ) {

			if ( get_option( 'users_can_register' ) ) {
				$message = __( 'You must first <a href="%1$s">login</a> or <a href="%2$s">register</a> before you can submit anything.', APP_TD );
			} else {
				$message = __( 'You must first <a href="%1$s">login</a> before you can submit anything.', APP_TD );
			}

			$page_url     = scbUtil::get_current_url();
			$login_url    = appthemes_get_login_url( 'display', $page_url );
			$register_url = add_query_arg( 'redirect_to', urlencode( $page_url ), appthemes_get_registration_url() );
			$message      = sprintf( $message, $login_url, $register_url );

			$this->errors->add( 'require_login', $message );

			return;
		}

		if ( $this->user_capability() && ! current_user_can( $this->user_capability() ) ) {
			$this->errors->add( 'cant_edit', __( 'You do not have sufficient permissions to start this process.', APP_TD ) );
		}
	}

	/**
	 * Setup Checkout Object specifically for current process.
	 */
	protected function setup_checkout() {
		appthemes_setup_checkout( $this->get_checkout_type(), $this->basic_url() );
	}

	/**
	 * Filters the current template file path and change it if need
	 *
	 * @param string $path Path to the page template.
	 *
	 * @return string
	 */
	public function template_include( $path ) {

		$errors = $this->errors->get_error_codes();
		if ( empty( $errors ) ) {
			$this->setup_checkout();

			$step_found = appthemes_process_checkout();

			if ( ! $step_found ) {
				$path = locate_template( '404.php' );
			}
		} else {
			add_action( 'appthemes_notices', array( $this, 'notices' ) );
		}

		return $path;
	}

	/**
	 * Displays notices on process failure.
	 */
	public function notices() {
		if ( $this->errors->get_error_codes() ) {
			appthemes_display_notice( 'process-error', $this->errors );
		}
	}

	/**
	 * Retrieves the process basic URL.
	 *
	 * @param int $item_id An Item ID.
	 *
	 * @return string Page URL
	 */
	protected function basic_url( $item_id = null ) {
		return get_permalink( $this->get_page_id() );
	}

	/**
	 * Retrieves public process URL depending on context and user permissions.
	 *
	 * @param int $item_id An Item ID.
	 */
	public function get_process_url( $item_id = null ) {
		return $this->basic_url( $item_id );
	}

	/**
	 * Adds necessary data to a new created checkout object.
	 *
	 * @param APP_Dynamic_Checkout $checkout The checkout object.
	 */
	public function add_checkout_data( $checkout ) {}

	/**
	 * Trigger final process actions until checkout will be removed.
	 *
	 * @param APP_Dynamic_Checkout $checkout The checkout object.
	 */
	public function complete_process( $checkout ) {}

	/**
	 * Removes data from completed checkout object.
	 *
	 * @param APP_Dynamic_Checkout $checkout The checkout object.
	 */
	public function remove_checkout_data( $checkout ) {}

	/**
	 * Calls actions on the template_redirect hook
	 */
	public function template_redirect() {

		$this->check_access();

		if ( method_exists( $this, 'enqueue_scripts' ) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		add_filter( 'body_class', array( $this, 'body_class' ), 99 );

		do_action( strtolower( get_class( $this ) . '_' . __FUNCTION__ ) );
	}

	/**
	 * Adds extra CSS classes to the page body tag
	 *
	 * @param array $classes Post classes.
	 *
	 * @return array Modified classes array
	 */
	public function body_class( $classes ) {
		$classes[] = "process-{$this->listing->get_type()}-{$this->process}";
		$classes[] = "process-{$this->listing->get_type()}";
		$classes[] = "process-{$this->process}";
		$classes[] = 'process';
		return $classes;
	}

	/**
	 * Retrieves error object.
	 *
	 * @return WP_Error The error object.
	 */
	public function get_errors() {
		return $this->errors;
	}

}
