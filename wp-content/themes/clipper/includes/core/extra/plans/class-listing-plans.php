<?php
/**
 * Listing Plans submodule
 *
 * @package Listing\Modules\Plan
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Pricing Plan processing class
 *
 * Registers and activates pricing plan post type and payments item related to
 * given listing type.
 *
 * Registers Plan post type metaboxes
 *
 * Adds an admin menu items and pages.
 *
 * Requires theme supports:
 *  - app-payments
 */
class APP_Listing_Plans {

	/**
	 * Current Listing module object.
	 *
	 * @var APP_Listing
	 */
	protected $listing;

	/**
	 * Plan post type name.
	 *
	 * @var string
	 */
	protected $plan_type;

	/**
	 * Plan details metabox.
	 *
	 * @var type APP_Listing_Plans_Details_Box
	 */
	public $plan_details;

	/**
	 * Plan duration metabox.
	 *
	 * @var type APP_Listing_Plans_Duration_Box
	 */
	public $plan_duration;

	/**
	 * Construct Listing Plans module
	 *
	 * @param APP_Listing $listing Listing module object.
	 */
	public function __construct( APP_Listing $listing ) {

		$this->listing = $listing;

		if ( ! $this->plan_type ) {
			$this->plan_type = "{$this->listing->get_type()}-pricing-plan";
		}

		$this->activate_modules();

		if ( ! did_action( 'init' ) ) {
			add_action( 'init', array( $this, 'init' ), 1000 );
		} else {
			$this->init();
		}

		add_action( 'admin_menu', array( $this, '_add_menu' ), 11 );
		add_filter( 'parent_file', array( $this, '_edit_page_menu_workaround' ) );
	}

	/**
	 * Activates submodules
	 */
	protected function activate_modules() {
		if ( ! $this->plan_details && is_admin() ) {
			$this->plan_details = new APP_Listing_Plans_Details_Box( $this );
		}
		if ( ! $this->plan_duration && is_admin() ) {
			$this->plan_duration = new APP_Listing_Plans_Duration_Box( $this );
		}
	}

	/**
	 * Init actions
	 */
	public function init() {
		$this->register_plan_type();
		$this->register_payment_item();
	}

	/**
	 * Registers Pricing Plan post type
	 *
	 * @uses  register_post_type()
	 * @param array $args An array of arguments to be used in register_post_type().
	 */
	protected function register_plan_type( $args = array() ) {

		$labels = array(
			'name'               => __( 'Listing Plans', APP_TD ),
			'singular_name'      => __( 'Listing Plan', APP_TD ),
			'add_new'            => __( 'Add New', APP_TD ),
			'add_new_item'       => __( 'Add New Listing Plan', APP_TD ),
			'edit_item'          => __( 'Edit Plan', APP_TD ),
			'new_item'           => __( 'New Listing Plan', APP_TD ),
			'view_item'          => __( 'View Plan', APP_TD ),
			'search_items'       => __( 'Search Plans', APP_TD ),
			'not_found'          => __( 'No Plans found', APP_TD ),
			'not_found_in_trash' => __( 'No Plans found in Trash', APP_TD ),
			'parent_item_colon'  => __( 'Parent Plan:', APP_TD ),
			'menu_name'          => __( 'Listing Plans', APP_TD ),
		);

		$defaults = array(
			'labels'          => $labels,
			'hierarchical'    => false,
			'supports'        => array( 'title', 'page-attributes' ),
			'public'          => false,
			'capability_type' => 'page',
			'show_ui'         => true,
			'show_in_menu'    => false,
		);

		$args = wp_parse_args( $args, $defaults );

		register_post_type( $this->plan_type, $args );
	}

	/**
	 * Registers available Pricing Plans as Item in Payments module
	 */
	protected function register_payment_item() {

		$plans = new WP_Query( array( 'post_type' => $this->plan_type, 'nopaging' => 1, 'orderby' => 'menu_order', 'order' => 'ASC' ) );

		foreach ( $plans->posts as $plan ) {

			$plan_type = "{$this->listing->get_type()}-{$plan->ID}";
			$plan_obj  = new APP_Listing_Post_Plan( $plan_type, $plan->ID );

			$this->listing->plans->add_plan( $plan_obj );

			APP_Item_Registry::register( $plan_type, $plan->post_title );
		}
	}

	/**
	 * Retrieves the registered plan object by plan post id.
	 *
	 * @param int $plan_id
	 *
	 * @return APP_Listing_Post_Plan The plan object.
	 */
	public function get_plan( $plan_id ) {
		$plan_type = "{$this->listing->get_type()}-{$plan_id}";
		return $this->listing->plans->get_plan( $plan_type );
	}

	/**
	 * Registers Pricing menu in Admin area
	 *
	 * @global string $pagenow
	 * @global string $typenow
	 */
	public function _add_menu() {
		global $pagenow, $typenow;

		$ptype     = $this->get_plan_type();
		$ptype_obj = get_post_type_object( $ptype );

		add_submenu_page( 'app-payments', $ptype_obj->labels->menu_name, $ptype_obj->labels->menu_name, $ptype_obj->cap->edit_posts, "edit.php?post_type=$ptype" );

		if ( 'post-new.php' === $pagenow && $typenow === $ptype ) {
			add_submenu_page( 'app-payments', $ptype_obj->labels->new_item, $ptype_obj->labels->new_item, $ptype_obj->cap->edit_posts, "post-new.php?post_type=$ptype" );
		}
	}

	/**
	 * Edit pricing single page workaround
	 *
	 * Filter the parent file of an admin menu sub-menu item.
	 *
	 * @global string $pagenow Current page.
	 * @global string $typenow Current post type.
	 *
	 * @param string $parent_file The parent file.
	 *
	 * @return string Modified parent file
	 */
	public function _edit_page_menu_workaround( $parent_file ) {
		global $pagenow, $typenow;

		$ptype = $this->get_plan_type();

		if ( "edit.php?post_type=$ptype" === $parent_file && ( 'post.php' === $pagenow || 'post-new.php' === $pagenow ) && $typenow === $ptype ) {
			return 'app-payments';
		}

		return $parent_file;
	}

	/**
	 * Pricing Plan post type getter
	 *
	 * @return string Pricing Plan post type
	 */
	public function get_plan_type() {
		return $this->plan_type;
	}
}
