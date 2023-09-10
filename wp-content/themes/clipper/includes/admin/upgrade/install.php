<?php
/**
 * The theme setup and install scripts.
 *
 * @package Clipper
 *
 * @since 2.0.0
 */

class CLPR_Install_Menus extends APP_Progress_Upgrade_Step {

	private function _get_items() {
		return array(
			'primary'   => __( 'Header', APP_TD ),
			'secondary' => __( 'Footer', APP_TD ),
		);
	}

	public function get_total() {
		return count( $this->_get_items() );
	}

	/**
	 * Create the inital menus for the new site.
	 *
	 * @since  2.0.0
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @return int The number of actually processed items.
	 */
	public function progress( APP_Dynamic_Checkout $checkout ) {
		$menus     = $this->_get_items();
		$locations = get_theme_mod( 'nav_menu_locations' );

		foreach ( $menus as $location => $name ) {

			if ( has_nav_menu( $location ) ) {
				continue;
			}

			$menu_id = wp_create_nav_menu( $name );
			if ( is_wp_error( $menu_id ) ) {
				continue;
			}

			$locations[ $location ] = $menu_id;
		}

		set_theme_mod( 'nav_menu_locations', $locations );

		return count( $menus );
	}

}

class CLPR_Install_Widgets extends APP_Progress_Upgrade_Step {

	protected function get_widgets() {
		return array(
			// Homepage
			'sidebar_home' => array(
				'custom-coupons' => array(
					'title' => __( 'Top Coupons', APP_TD ),
					'count' => 10,
				),
				'appthemes_facebook' => array(
					'title' => __( 'Facebook Friends', APP_TD ),
					'fid' => '137589686255438',
					'connections' => 8,
					'width' => 268,
					'height' => 290,
				),
			),
			// Page
			'sidebar_page' => array(
				'appthemes_facebook' => array(
					'title' => __( 'Facebook Friends', APP_TD ),
					'fid' => '137589686255438',
					'connections' => 8,
					'width' => 268,
					'height' => 290,
				),
				'rss' => array(
					'title' => __( 'AppThemes Blog', APP_TD ),
					'url' => 'http://feeds.feedburner.com/appthemes',
					'show_date' => 1,
					'items' => 5,
				),
			),
			// Blog
			'sidebar_blog' => array(
				'archives' => array(
					'title' => __( 'Archives', APP_TD ),
				),
				'rss' => array(
					'title' => __( 'AppThemes Blog', APP_TD ),
					'url' => 'http://feeds.feedburner.com/appthemes',
					'show_date' => 1,
					'items' => 5,
				),
			),
			// Coupon
			'sidebar_coupon' => array(
				'coupon-cats' => array(
					'title' => __( 'Coupon Categories', APP_TD ),
				),
				'popular-searches' => array(
					'title' => __( 'Popular Searches', APP_TD ),
					'count' => 10,
				),
				'custom-coupons' => array(
					'title' => __( 'Popular Coupons', APP_TD ),
					'count' => 10,
				),
			),
			// Store
			'sidebar_store' => array(
				'appthemes_facebook' => array(
					'title' => __( 'Facebook Friends', APP_TD ),
					'fid' => '137589686255438',
					'connections' => 8,
					'width' => 268,
					'height' => 290,
				),
				'custom-stores' => array(
					'title' => __( 'Popular Stores', APP_TD ),
					'number' => 10,
				),
				'tag_cloud' => array(
					'title' => __( 'Tags', APP_TD ),
					'taxonomy' => 'coupon_tag',
				),
			),
			// Submit
			'sidebar_submit' => array(
				'coupon-cats' => array(
					'title' => __( 'Coupon Categories', APP_TD ),
				),
				'custom-stores' => array(
					'title' => __( 'Popular Stores', APP_TD ),
					'number' => 10,
				),
			),
			// Login
			'sidebar_login' => array(),
			// User
			'sidebar_user' => array(),
			// Footer
			'sidebar_footer' => array(
				'coupon-cats' => array(
					'title' => __( 'Categories', APP_TD ),
				),
				'custom-stores' => array(
					'title' => __( 'Stores', APP_TD ),
					'number' => 10,
				),
				'coupon_tag_cloud' => array(
					'title' => __( 'Tags', APP_TD ),
					'taxonomy' => 'coupon_tag',
				),
			),
		);
	}

	/**
	 * Retrieves total number of items to be processed withing current step.
	 *
	 * @return int
	 */
	public function get_total() {
		$count   = 0;
		$widgets = $this->get_widgets();

		foreach ( $widgets as $group ) {
			$count += count( $group );
		}

		return $count;
	}

	/**
	 * Create the inital menus for the new site.
	 *
	 * @since  2.0.0
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @return int The number of actually processed items.
	 */
	public function progress( APP_Dynamic_Checkout $checkout ) {
		$sidebars_widgets = $this->get_widgets();
		appthemes_install_widgets( $sidebars_widgets );

		return $this->get_total();
	}

}

class CLPR_Install_Sample_Posts extends APP_Progress_Upgrade_Step {

	protected function _get_items() {
		return array();
	}

	public function get_total() {
		return count( $this->_get_items() );
	}

	/**
	 * Create the inital post content for the new site.
	 *
	 * @since  2.0.0
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @return int The number of actually processed items.
	 */
	public function progress( APP_Dynamic_Checkout $checkout ) {

		$items = $this->_get_items();

		foreach ( $items as $name => $args ) {
			$id = wp_insert_post( $args['postarr'] );

			if ( $id ) {
				$checkout->add_data( $name . '_page_id', $id );
				if ( isset( $args['meta'] ) ) {
					foreach ( (array) $args['meta'] as $metakey => $metavalue ) {
						update_post_meta( $id, $metakey, $metavalue );
					}
				}
				if ( isset( $args['image'] ) ) {
					$img_url = get_template_directory_uri() . '/assets/images/admin/install/' . $args['image'];
					CLPR_Install::upload_image( $id, $img_url, null, true );
				}
			}
		}

		return $this->get_total();
	}

}

class CLPR_Install_Pages extends CLPR_Install_Sample_Posts {

	protected function _get_items() {
		global $clipper;

		$items = array();

		$items_to_upgrade = array(
			'view_process_new'   => array(
				'template' => 'create-listing.php',
				'title'    => __( 'Share Coupon', APP_TD ),
			),
			'view_process_edit'  => array(
				'template' => 'edit-listing.php',
				'title'    => __( 'Edit Coupon', APP_TD ),
			),
			'view_process_renew' => array(
				'template' => 'renew-listing.php',
				'title'    => __( 'Renew Coupon', APP_TD ),
			),
		);

		foreach ( $items_to_upgrade as $module => $item ) {
			if ( ! $clipper->{APP_POST_TYPE}->{$module} ) {
				continue;
			}

			$proc        = $clipper->{APP_POST_TYPE}->{$module}->get_process_type();
			$new_page_id = $clipper->{APP_POST_TYPE}->{$module}->get_page_id();
			$page_q      = new WP_Query( array(
				'post_type'        => 'page',
				'meta_key'         => '_wp_page_template',
				'meta_value'       => $item['template'],
				'posts_per_page'   => 1,
				'suppress_filters' => true,
			) );

			if ( ! empty( $page_q->posts ) ) {
				$page_id = $page_q->posts[0]->ID;

				if ( $new_page_id ) {
					wp_delete_post( $new_page_id, true );
				}

			} else {
				$page_id = $new_page_id;
			}

			$items[ $proc ] = array(
				'postarr' => array(
					'ID'           => $page_id,
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => $item['title'],
				),
				'meta' => array(
					'_wp_page_template' => "process-coupon-{$proc}.php",
				),
			);
		}

		return $items;
	}

	public function get_total() {
		$count = parent::get_total();
		$count  += count( APP_View_Page::_get_templates() );
		return $count;
	}
}

class CLPR_Install_Menu_Items extends APP_Progress_Upgrade_Step {

	protected function _get_items() {
		global $clipper;

		// Get all registered menu locations so we can add page/posts to them.
		$locations = get_theme_mod( 'nav_menu_locations' );
		$items     = array();

		foreach( array( 'primary', 'secondary' ) as $location ) {
			$items[] = array(
				'menu_id' => $locations[ $location ],
				'data'    => array(
					'menu-item-type'   => 'custom',
					'menu-item-title'  => __( 'Home', APP_TD ),
					'menu-item-url'    => home_url( '/' ),
					'menu-item-status' => 'publish',
				),
			);

			$create_coupon_page_id = $clipper->{APP_POST_TYPE}->view_process_new->get_page_id();

			$page_ids = array(
				$create_coupon_page_id,
				CLPR_Coupon_Stores::get_id(),
				CLPR_Coupon_Categories::get_id(),
				CLPR_Blog_Archive::get_id(),
			);

			foreach ( $page_ids as $page_id ) {
				$page = get_post( $page_id );

				if ( ! $page ) {
					continue;
				}

				$items[] = array(
					'post_id' => $page_id,
					'menu_id' => $locations[ $location ],
					'data'    => array(
						'menu-item-type'      => 'post_type',
						'menu-item-object'    => 'page',
						'menu-item-object-id' => $page_id,
						'menu-item-title'     => $page->post_title,
						'menu-item-url'       => get_permalink( $page ),
						'menu-item-status'    => 'publish',
					),
				);
			}
		}

		return $items;
	}

	/**
	 * Retrieves total number of items to be processed withing current step.
	 *
	 * @return int
	 */
	public function get_total() {
		return count( $this->_get_items() );
	}

	/**
	 * Add initial menu items.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @return int The number of actually processed items.
	 */
	public function progress( APP_Dynamic_Checkout $checkout ) {

		$items = $this->_get_items();

		foreach ( $items as $item ) {

			wp_parse_args( $item, array(
				'post_id' => '',
				'menu_id' => '',
				'data'    => array(),
			) );

			CLPR_Install::add_item_to_menu( $item['post_id'], $item['menu_id'], $item['data'] );
		}

		return count( $items );
	}

}


class CLPR_Install_Sample_Listings extends APP_Progress_Upgrade_Step {

	/**
	 * Retrieves total number of items to be processed within current step.
	 *
	 * @return int
	 */
	public function get_total() {
		return 1;
	}

	/**
	 * Create the initial listings content.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @return int The number of actually processed items.
	 */
	public function progress( APP_Dynamic_Checkout $checkout ) {
		global $clipper;

		$listing_mod = $clipper->{APP_POST_TYPE};
		/* @var $plan APP_Listing_Plan_I */
		$plans = $listing_mod->plans->get_plans();
		$plan = $plans[0];

		/* @var $importer CLPR_Admin_Importer */
		$importer = appthemes_get_instance( 'CLPR_Importer' );
		$sample   = $importer->get_sample_data();

		// Import listing from the generated sample.
		$listing_id = $importer->import_listing( $sample );

		// Set feature addons.
		appthemes_add_addon( $listing_id, CLPR_ITEM_FEATURED, 30 );

		// Create a transaction record.
		$order = appthemes_new_order();
		$order->add_item( $plan->get_type(), 5, $listing_id );
		$order->set_gateway( 'paypal' );
		$order->complete();
		$order->activate();

		return $this->get_total();
	}

}

class CLPR_Install_Options extends APP_Progress_Upgrade_Step {

	private function _get_items() {

		$options = array(
			'show_on_front'  => 'page',
			'page_on_front'  => CLPR_Coupons_Home::get_id(),
			'page_for_posts' => CLPR_Blog_Archive::get_id(),
		);

		// Setup pretty permalinks for the blog if something isn't already set.
		// Default core is now /%year%/%monthnum%/%day%/%postname%/
		if ( ! get_option( 'permalink_structure' ) ) {
			$options['permalink_structure'] = '/%postname%/';
		}

		// Set the default user role.
		if ( get_option( 'default_role' ) === 'subscriber' ) {
			$options['default_role'] = 'contributor';
		}

		// check the "membership" box to enable wordpress registration
		if ( ! get_option( 'users_can_register' ) ) {
			$options['users_can_register'] = 1;
		}

		return $options;
	}

	/**
	 * Retrieves total number of items to be processed withing current step.
	 *
	 * @return int
	 */
	public function get_total() {
		return count( $this->_get_items() );
	}

	/**
	 * Set the inital WordPress options.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @return int The number of actually processed items.
	 */
	public function progress( APP_Dynamic_Checkout $checkout ) {

		$items = $this->_get_items();

		foreach ( $items as $key => $value ) {
			update_option( $key, $value );
		}

		return count( $items );
	}

}

class CLPR_Install_Terms extends APP_Progress_Upgrade_Step {

	private function _get_items() {

		$items = array(
			APP_TAX_TYPE => array(
				'coupon-code'      => __( 'Coupon Code', APP_TD ),
				'printable-coupon' => __( 'Printable Coupon', APP_TD ),
				'promotion'        => __( 'Promotion', APP_TD ),
			),
			APP_TAX_IMAGE => array(
				'printable-coupon' => __( 'Printable Coupon', APP_TD ),
			),
		);

		return $items;
	}

	/**
	 * Retrieves total number of items to be processed withing current step.
	 *
	 * @return int
	 */
	public function get_total() {
		$c = 0;
		foreach ( $this->_get_items() as $tax ) {
			$c += count( $tax );
		}
		return $c;
	}

	/**
	 * Create a terms.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @return int The number of actually processed items.
	 */
	public function progress( APP_Dynamic_Checkout $checkout ) {

		// create coupon types
		$coupon_types = $this->_get_items();

		foreach ( $coupon_types as $tax => $terms ) {
			foreach ( $terms as $name => $term ) {
				appthemes_maybe_insert_term( $term, $tax, array( 'slug' => $name ) );
			}
		}

		return $this->get_total();
	}

}

class CLPR_Install_Rewrite_Rules extends APP_Progress_Upgrade_Step {

	/**
	 * Retrieves total number of items to be processed withing current step.
	 *
	 * @return int
	 */
	public function get_total() {
		return 1;
	}

	/**
	 * Set the sample logo.
	 *
	 * @param APP_Dynamic_Checkout $checkout Checkout object.
	 *
	 * @return int The number of actually processed items.
	 */
	public function progress( APP_Dynamic_Checkout $checkout ) {
		flush_rewrite_rules();
		return $this->get_total();
	}

}

/**
 * Install/Upgrade helper utils.
 *
 * @since 2.0.0
 */
final class CLPR_Install {

	/**
	 * Adds the page/post to an existing menu.
	 *
	 * @since 2.0.0
	 *
	 * @param int    $post_id         The post ID to add.
	 * @param string $menu_id         The menu ID to add to.
	 * @param array  $menu_item_data  The menu item's data.
	 * @return int|WP_Error Menu ID on success, WP_Error object on failure.
	 */
	public static function add_item_to_menu( $post_id, $menu_id, $menu_item_data = array() ) {

		$defaults = array(
			'menu-item-object-id'   => $post_id,
			'menu-item-object'      => 'page',
			'menu-item-parent-id'   => 0,
			'menu-item-position'    => 0,
			'menu-item-type'        => 'post_type',
			'menu-item-title'       => '', // The post_title.
			'menu-item-url'         => '', // The url to go to if custom page.
			'menu-item-description' => '', // The post_content.
			'menu-item-target'      => '', // When clicked on. Options: _blank or empty.
			'menu-item-classes'     => '', // CSS classes separated by spaces.
			'menu-item-xfn'         => '',
			'menu-item-status'      => 'publish', // The post status.
		);

		$args = wp_parse_args( $menu_item_data, $defaults );

		return wp_update_nav_menu_item( $menu_id, 0, $args );
	}

	/**
	 * Upload an image to the media library.
	 *
	 * @since 2.0.0
	 *
	 * @param int    $post_id   The ID of the post to which the image is attached.
	 * @param string $file      The full path of the image (http url or local absolute path).
	 * @param string $desc      The image description (optional).
	 * @param bool   $featured  Set the featured image. Default false.
	 * @param array  $post_data Optional. Post data to override. Default empty array.
	 * @return (int|false)      The image post ID on success.
	 */
	public static function upload_image( $post_id, $file, $desc = null, $featured = false, $post_data = array() ) {

		// WordPress API for image uploads.
		require_once( ABSPATH . 'wp-admin/includes/admin.php' );

		// Get the actual file name (xyz.jpg).
		$filename = basename( $file );

		// Handle locally stored files differently.
		if ( ! preg_match( '!^(http|https)://!i', $file ) ) {

			// Grab the upload dir values.
			$r = wp_upload_dir();

			// Copy it to the uploads dir.
			@copy( $file, trailingslashit( $r['path'] ) . $filename );

			// Get the new media url.
			$file = trailingslashit( $r['url'] ) . $filename;
		}

		// Download the file to a temp location.
		$url = download_url( $file );

		$file_array = array(
			'name'     => $filename,
			'tmp_name' => $url,
		);

		if ( is_wp_error( $file_array['tmp_name'] ) ) {
			error_log( 'Error trying to store the file temporarily.' );
			return;
		}

		// Do the validation and put it in the media library.
		$id = media_handle_sideload( $file_array, $post_id, $desc, $post_data );

		// If error storing permanently, unlink.
		if ( is_wp_error( $id ) ) {
			@unlink( $file_array['tmp_name'] );
			return $id;
		}

		// Set as featured image if param passed.
		if ( true === $featured ) {
			set_post_thumbnail( $post_id, $id );
		}

		return $id;
	}

}
