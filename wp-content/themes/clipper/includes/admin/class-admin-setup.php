<?php
/**
 * Admin setup page setup.
 *
 * @package Clipper\Admin\Setup
 * @author  AppThemes
 * @since   Clipper 2.0.0
 */

/**
 * Setup the admin pages class.
 */
class CLPR_Admin_Setup extends APP_Tabs_Page {

	/**
	 * Constructor
	 */
	public function setup() {

		new APP_Progress_Ajax_Upgrade_View( 'upgrade_theme', 'clpr_version' );
		new APP_Progress_Ajax_Upgrade_View( 'install_theme_samples', 'clpr_version' );

		add_action( 'appthemes_register_checkout_steps', array( $this, 'setup_upgrade' ) );

		$this->textdomain = APP_TD;

		$this->args = array(
			'page_title'            => __( 'Clipper Setup', APP_TD ),
			'menu_title'            => __( 'Setup Guide', APP_TD ),
			'page_slug'             => 'app-setup',
			'parent'                => 'app-dashboard',
			'screen_icon'           => 'options-general',
			'admin_action_priority' => 40,
		);

	}

	/**
	 *
	 * @param APP_Dynamic_Checkout $checkout
	 */
	public function setup_upgrade( APP_Dynamic_Checkout $checkout ) {
		$checkout_type = $checkout->get_checkout_type();

		if ( ! in_array( $checkout_type, array( 'upgrade_theme', 'install_theme_samples' ) ) ) {
			return;
		}

		require_once ( dirname( __FILE__ ) . '/upgrade/install.php' );
		require_once ( dirname( __FILE__ ) . '/upgrade/upgrade.php' );

		$old_version = $checkout->get_data( 'old_version' );
		$since       = '';

		if ( $old_version ) {
			$since = '2.0.0-dev1';
		}

		switch ( $checkout_type ) {
			case 'upgrade_theme':

				// Legacy upgrades.
				new CLPR_Add_Post_Meta( "upgrade_votes_up_1_2_1",    array( 'since' => '1.2.1', 'title' => "Add deafult clpr_votes_up meta field to each coupon",    'key' => 'clpr_votes_up',    'value' => 0,  'post_type' => APP_POST_TYPE, 'register_to' => $checkout_type, 'icon' => 'dashicons-before dashicons-cloud' ) );
				new CLPR_Add_Post_Meta( "upgrade_votes_down_1_2_1",  array( 'since' => '1.2.1', 'title' => "Add deafult clpr_votes_down meta field to each coupon",  'key' => 'clpr_votes_down',  'value' => 0,  'post_type' => APP_POST_TYPE, 'register_to' => $checkout_type, 'icon' => 'dashicons-before dashicons-cloud' ) );
				new CLPR_Add_Post_Meta( "upgrade_expire_date_1_2_1", array( 'since' => '1.2.1', 'title' => "Add deafult clpr_expire_date meta field to each coupon", 'key' => 'clpr_expire_date', 'value' => '', 'post_type' => APP_POST_TYPE, 'register_to' => $checkout_type, 'icon' => 'dashicons-before dashicons-cloud' ) );

				// First theme installation or upgrade from legacy version.
				new CLPR_Install_Menus(         'install_menus',         array( 'register_to' => $checkout_type, 'since' => '',     'title' => 'Setup Menus',         'icon' => 'dashicons-before dashicons-menu' ) );
				new CLPR_Install_Widgets(       'install_widgets',       array( 'register_to' => $checkout_type, 'since' => '',     'title' => 'Setup Widgets',       'icon' => 'dashicons-before dashicons-welcome-widgets-menus' ) );
				new CLPR_Install_Pages(         'install_pages',         array( 'register_to' => $checkout_type, 'since' => $since, 'title' => 'Setup Pages',         'icon' => 'dashicons-before dashicons-welcome-add-page' ) );
				new CLPR_Install_Menu_Items(    'install_menu_items',    array( 'register_to' => $checkout_type, 'since' => '',     'title' => 'Setup Menu Items',    'icon' => 'dashicons-before dashicons-editor-ul' ) );
				new CLPR_Install_Options(       'install_options',       array( 'register_to' => $checkout_type, 'since' => '',     'title' => 'Setup Options',       'icon' => 'dashicons-before dashicons-admin-settings' ) );
				new CLPR_Install_Terms(         'install_terms',         array( 'register_to' => $checkout_type, 'since' => '',     'title' => 'Install Taxonomy Terms', 'icon' => 'dashicons-before dashicons-category' ) );
				new CLPR_Install_Rewrite_Rules( 'install_rewrite_rules', array( 'register_to' => $checkout_type, 'since' => $since, 'title' => 'Flush Rewrite Rules',    'icon' => 'dashicons-before dashicons-update' ) );

				// Other legacy upgrades.
				new CLPR_Upgrade_Printable_Coupon_Images( 'upgrade_printable_coupon_images', array( 'register_to' => $checkout_type, 'since' => '1.2.2', 'title' => 'Upgrade Printable Coupon Images', 'icon' => 'dashicons-before dashicons-format-image' ) );
				new CLPR_Upgrade_Unreliable_Coupons(      'upgrade_unreliable_coupons',      array( 'register_to' => $checkout_type, 'since' => '1.2.3', 'title' => 'Set status to unreliable coupons', 'icon' => 'dashicons-before dashicons-cloud' ) );
				new CLPR_Upgrade_Custom_Tables_1_4(       'upgrade_custom_tables_1_4',       array( 'register_to' => $checkout_type, 'since' => '1.4',   'title' => 'Upgrade Custom Tables', 'icon' => 'dashicons-before dashicons-cloud' ) );
				new CLPR_Upgrade_Theme_Options_1_5(       'upgrade_theme_options_1_5',       array( 'register_to' => $checkout_type, 'since' => '1.5',   'title' => 'Upgrade Theme Options (since 1.5)', 'icon' => 'dashicons-before dashicons-admin-settings' ) );
				new CLPR_Upgrade_Expire_Date_1_5(         'upgrade_expire_date_1_5',         array( 'register_to' => $checkout_type, 'since' => '1.5',   'title' => 'Upgrade clpr_expire_date meta field format', 'icon' => 'dashicons-before dashicons-admin-cloud' ) );
				new CLPR_Remove_Pages_1_5(                'remove_old_blog_page_1_5',        array( 'register_to' => $checkout_type, 'since' => '1.5',   'title' => 'Remove old blog page', 'icon' => 'dashicons-before dashicons-admin-cloud' ) );
				new CLPR_Convert_Reports_1_5(             'convert_reportse_1_5',            array( 'register_to' => $checkout_type, 'since' => '1.5',   'title' => 'Convert old reports to custom comment type', 'icon' => 'dashicons-before dashicons-megaphone' ) );
				new CLPR_Remove_Pages_1_6(                'remove_pages_1_6',                array( 'register_to' => $checkout_type, 'since' => '1.6',   'title' => 'Remove old Submit Coupon and Edit Coupon pages', 'icon' => 'dashicons-before dashicons-admin-cloud' ) );
				new CLPR_Upgrade_Logo_1_6(                'upgrade_logo_1_6',                array( 'register_to' => $checkout_type, 'since' => '1.6',   'title' => 'Migrate logo options to "custom-header" theme support', 'icon' => 'dashicons-before dashicons-format-image' ) );
				new CLPR_Upgrade_Orders_1_6(              'upgrade_orders_1_6',              array( 'register_to' => $checkout_type, 'since' => '1.6',   'title' => 'Update orders to include urls, checkout type, and hash', 'icon' => 'dashicons-before dashicons-at-payments' ) );
				new CLPR_Upgrade_Recaptcha_Colors_1_6_5(  'upgrade_recaptcha_1_6_5',         array( 'register_to' => $checkout_type, 'since' => '1.6.5', 'title' => 'Migrate recaptcha color schemes', 'icon' => 'dashicons-before dashicons-admin-customizer' ) );

				// Regular upgrades might look like following:
				new CLPR_Upgrade_P2P_Order_Meta(          'upgrade_listing_item_p2p_2',  array( 'since' => '2.0.0-dev2', 'old_value' => 'coupon-listing', 'new_value' => 'coupon', 'post_type' => 'coupon', 'title' => "Upgrade Coupon Listing P2P Connection", 'register_to' => $checkout_type, 'icon' => 'dashicons-before dashicons-cloud' ) );
				new CLPR_Upgrade_Expired_Status(          'upgrade_expired_status_2_0',  array( 'since' => '2.0.0-dev3', 'title' => 'Upgrade Expired Coupons Status', 'register_to' => $checkout_type, 'icon' => 'dashicons-before dashicons-cloud' ) );
				new CLPR_Remove_Empty_Post_Meta(          'remove_empty_featured_flag',  array( 'since' => '2.0.0-dev4', 'old_value' => 'clpr_featured', 'post_type' => 'coupon', 'title' => 'Remove Empty Featured Flags', 'register_to' => $checkout_type, 'icon' => 'dashicons-before dashicons-cloud' ) );
				new CLPR_Upgrade_Stores_Options_2_0_0(    'upgrade_stores_options_2',    array( 'since' => '2.0.0-dev7', 'register_to' => $checkout_type, 'title' => 'Upgrade Stores Options v2.0.0', 'icon' => 'dashicons-before dashicons-admin-settings' ) );
				new CLPR_Upgrade_Options_2_0_0(           'upgrade_listing_options_2',   array( 'since' => '2.0.0-dev5', 'register_to' => $checkout_type, 'title' => 'Upgrade Coupon Options v2.0.0', 'icon' => 'dashicons-before dashicons-admin-settings' ) );
				new CLPR_Upgrade_Checkout_Data(           'upgrade_checkout_data',       array( 'since' => '2.0.0-dev6', 'register_to' => $checkout_type, 'title' => 'Upgrade Checkout Data', 'icon' => 'dashicons-before dashicons-cloud' ) );

				new CLPR_Migrate_Pages_2_0_1(             'migrate_pages_2_0_1',         array( 'since' => '2.0.1', 'register_to' => $checkout_type, 'title' => 'Remove duplicating Full Width page template', 'icon' => 'dashicons-before dashicons-cloud' ) );

				// Some regular upgrading procedures.
				new CLPR_Delete_Expired_Transients( 'delete_expired_transients', array( 'register_to' => $checkout_type, 'since' => CLPR_VERSION, 'title' => 'Delete Expired Transients', 'icon' => 'dashicons-before dashicons-cloud' ) );

				break;

			case 'install_theme_samples':
//				new CLPR_Install_Sample_Widgets(    'sample_widgets',    array( 'register_to' => $checkout_type, 'since' => $since, 'title' => 'Widgets',       'icon' => 'dashicons-before dashicons-welcome-widgets-menus' ) );
//				new CLPR_Install_Sample_Pages(      'sample_pages',      array( 'register_to' => $checkout_type, 'since' => $since, 'title' => 'Pages',         'icon' => 'dashicons-before dashicons-welcome-add-page' ) );
//				new CLPR_Install_Sample_Menu_Items( 'sample_menu_items', array( 'register_to' => $checkout_type, 'since' => $since, 'title' => 'Menu Items',    'icon' => 'dashicons-before dashicons-editor-ul' ) );
//				new CLPR_Install_Sample_Posts(      'sample_posts',      array( 'register_to' => $checkout_type, 'since' => $since, 'title' => 'Blog Posts',    'icon' => 'dashicons-before dashicons-welcome-write-blog' ) );
//				new CLPR_Install_Sample_Plans(      'sample_plans',      array( 'register_to' => $checkout_type, 'since' => $since, 'title' => 'Pricing Plans', 'icon' => 'dashicons-before dashicons-at-payments' ) );
				new CLPR_Install_Sample_Listings(   'sample_listings',   array( 'register_to' => $checkout_type, 'since' => $since, 'title' => 'Coupon',        'icon' => 'dashicons-before dashicons-tickets-alt' ) );
//				new CLPR_Install_Sample_Logo(       'sample_logo',       array( 'register_to' => $checkout_type, 'since' => $since, 'title' => 'Logo',          'icon' => 'dashicons-before dashicons-admin-customizer' ) );

				break;
		}

	}

	/**
	 * Get the theme name.
	 *
	 * @since 4.0.0
	 *
	 * @return string The theme name value.
	 */
	public function theme_name() {
		return get_option( 'current_theme' );
	}

	/**
	 * Load the inital tabs
	 *
	 * It's required so we leave it empty.
	 *
	 * @since 4.0.0
	 */
	protected function init_tabs() {
		$_SERVER['REQUEST_URI'] = esc_url_raw( remove_query_arg( array( 'firstrun' ), $_SERVER['REQUEST_URI'] ) );
		add_action( 'admin_enqueue_scripts', array( APP_Progress_View::get_instance( 'upgrade_theme' ), 'enqueue_scripts' ) );
	}

	/**
	 * Load the page content.
	 *
	 * @since 4.0.0
	 */
	public function page_content() {

		if ( isset( $_GET['firstrun'] ) ) {
			do_action( 'appthemes_first_run' );
		} ?>

	<div class="wrap about-wrap app-setup">

		<h1><?php printf( __( 'Welcome to %s&nbsp;%s' ), $this->theme_name(), CLPR_VERSION ); ?></h1>

		<p class="about-text"><?php echo sprintf( __( 'Thank you for updating to the latest version. %s %s is a huge change visually and under the hood&mdash;all which makes your overall experience even better!' ), $this->theme_name(), CLPR_VERSION ); ?></p>
		<div class="wp-badge"><?php printf( __( 'Version %s' ), CLPR_VERSION ); ?></div>

		<p class="getting-started-links">
			<a href="https://docs.appthemes.com" class="button button-primary" target="_blank"><?php _e( 'View Documentation &rarr;', APP_TD ); ?></a>
		</p>

		<!-- <h2 class="nav-tab-wrapper wp-clearfix">
			<a href="?page=app-setup" class="nav-tab nav-tab-active"><?php _e( 'What&#8217;s New', APP_TD ); ?></a>
		</h2>

		<div class="changelog point-releases">
			<h3><?php _e( 'Major Redesign and Code Overhaul' ); ?></h3>
			<p><?php printf( _n( '<strong>Version %1$s</strong> is a complete rebuild with and fixed %2$s bug.',
				'<strong>Version %1$s</strong> addressed some security issues and fixed %2$s issues.', 15 ), CLPR_VERSION, number_format_i18n( 15 ) ); ?>
				<?php printf( __( 'For more information, see <a href="%s">the release notes</a>.' ), 'https://docs.appthemes.com' ); ?>
			</p>
		</div> -->

		<?php if ( APP_Progress_View::get_instance( 'upgrade_theme' )->condition() ) { ?>
			<hr />

			<h3><?php _e( 'Summary' ); ?></h3>
			<p><?php _e( 'Please wait until the installation process is complete.', APP_TD ); ?></p>

			<?php APP_Progress_View::get_instance( 'upgrade_theme' )->display(); ?>

			<script type="text/javascript">
			jQuery( function($) {
				$('#app_progress_form_upgrade_theme' ).find( '.app-progress-button' ).hide().click();
			});
			</script>

		<?php } ?>

		<?php if ( APP_Progress_View::get_instance( 'install_theme_samples' )->condition() ) { ?>

			<hr />

			<h3><?php _e( 'Sample Data' ); ?></h3>
			<p><?php _e( "To help get you started, we've setup some initial pages, posts, options, and pricing plans. You can of course edit and/or delete whatever you like.", APP_TD ); ?></p>

			<?php APP_Progress_View::get_instance( 'install_theme_samples' )->display(); ?>
			<br />
		<?php } ?>

		<?php do_action( 'appthemes_upgrade_section' ); ?>

		<hr />

		<div class="va-setup-getting-started feature-section">

				<h2><?php _e( 'Getting Started', APP_TD ); ?></h2>


				<div class="under-the-hood three-col">
					<div class="col">
						<h3><span class="dashicons dashicons-admin-generic"></span> <?php _e( 'General Settings', APP_TD ); ?></h3>
						<p><?php _e( 'Setup and configure the Clipper options and get your business ready to launch.', APP_TD ); ?></p>
						<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=app-settings' ) ); ?>"><?php _e( 'Configure Theme Settings &rarr;', APP_TD ); ?></a></p>
					</div>
					<div class="col">
						<h3><span class="dashicons dashicons-admin-appearance"></span> <?php _e( 'Look and Feel', APP_TD ); ?></h3>
						<p><?php _e( 'Manage the front-end design of your website using the native WordPress live customizer. Content blocks are widgets for easy setup.', APP_TD ); ?></p>
						<p><a href="<?php echo esc_url( admin_url( 'customize.php' ) ); ?>"><?php _e( 'Launch Customizer &rarr;', APP_TD ); ?></a></p>
					</div>
					<div class="col">
						<h3><span class="dashicons dashicons-at-payments"></span> <?php _e( 'Payment Settings', APP_TD ); ?></h3>
						<p><?php _e( 'If you plan on monetizing your website, make sure you setup and configure the payment settings.', APP_TD ); ?></p>
						<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=app-payments-settings' ) ); ?>"><?php _e( 'Configure Payment Settings &rarr;', APP_TD ); ?></a></p>
					</div>
				</div>

				<div class="under-the-hood three-col">
					<div class="col">
						<h3><span class="dashicons dashicons-update"></span> <?php _e( 'Automatic Updates', APP_TD ); ?></h3>
						<p><?php _e( "Make sure you've got the latest and greatest version of Clipper. Keeping up-to-date protects you from bugs and security issues.", APP_TD ); ?></p>
						<p><a href="<?php echo esc_url( 'https://my.appthemes.com/' ); ?>" target="_blank"><?php _e( 'Download Updater Plugin &rarr;', APP_TD ); ?></a></p>
					</div>
					<div class="col">
						<h3><span class="dashicons dashicons-admin-plugins"></span> <?php _e( 'Marketplace Add-ons', APP_TD ); ?></h3>
						<p><?php _e( "We've got a thriving 3rd-party Marketplace which sells child themes and plugins for your theme.", APP_TD ); ?></p>
						<p><a href="<?php echo esc_url( 'https://marketplace.appthemes.com/' ) ?>" target="_blank"><?php _e( 'View Add-ons &rarr;', APP_TD ); ?></a></p>
					</div>
					<div class="col">
						<h3><span class="dashicons dashicons-sos"></span> <?php _e( 'Get Support', APP_TD ); ?></h3>
						<p><?php _e( "If you've gone through the setup and documentation and still have questions, our support team is here to help.", APP_TD ); ?></p>
						<p><a href="http://forums.appthemes.com/" target="_blank"><?php _e( 'Visit Forums &rarr;', APP_TD ); ?></a></p>
					</div>
				</div>

		</div>

		<hr />

		<h2><?php _e( 'Get Involved', APP_TD ); ?></h2>
		<div class="feature-section two-col">

			<div class="col">
				<img src="//cdn.appthemes.com/wp-content/uploads/2016/11/appthemes-developers-splash-page.png" alt="">
				<h3><span class="dashicons dashicons-hammer"></span> <?php _e( 'Developers', APP_TD ); ?></h3>
				<p>
					<?php
						printf(
							__( 'Build plugins and child themes on top of our products. Check out our Developers Center for tutorials and documentation. We also accept <a href="%s">Github pull requests</a>. If you would like access, please <a href="%s">contact us</a>.', APP_TD ),
							'https://github.com/AppThemes', 'https://www.appthemes.com/about/contact-form/'
						);
					?>
				</p>
				<p><a href="https://docs.appthemes.com/developers/" target="_blank"><?php _e( 'Developers Center &rarr;', APP_TD ); ?></a></p>
			</div>
			<div class="col">
				<img src="//cdn.appthemes.com/wp-content/uploads/2015/03/translate-wordpress-illustration.png" alt="">
				<h3><span class="dashicons dashicons-location-alt"></span> <?php _e( 'Translators', APP_TD ); ?></h3>
				<p><?php _e( "English not your native language? We could use your help then! Become an official AppThemes translator and reap some nice benefits. All text strings can be translated using tools like poedit.", APP_TD ); ?></p>
				<p><a href="https://www.appthemes.com/support/languages/" target="_blank"><?php _e( 'Learn More &rarr;', APP_TD ); ?></a></p>
			</div>

		</div>

	</div> <!-- .wrap about-wrap -->
<?php
	}

}
