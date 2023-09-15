<?php
/**
 * Admin Settings.
 *
 * @package Clipper\Admin\Settings
 * @author  AppThemes
 * @since   Clipper 1.5
 */


/**
 * General Settings Page.
 */
class CLPR_Theme_Settings_General extends APP_Tabs_Page {

	/**
	 * Setups page.
	 *
	 * @return void
	 */
	function setup() {
		$this->textdomain = APP_TD;

		$this->args = array(
			'page_title' => __( 'Clipper Settings', APP_TD ),
			'menu_title' => __( 'Settings', APP_TD ),
			'page_slug' => 'app-settings',
			'parent' => 'app-dashboard',
			'screen_icon' => 'options-general',
			'admin_action_priority' => 10,
		);

		add_action( 'admin_notices', array( $this, 'admin_tools' ) );
	}

	/**
	 * Processes admin tools.
	 *
	 * @return void
	 */
	public function admin_tools() {
		global $clipper;

		if ( isset( $_GET['prune-coupons'] ) && $_GET['prune-coupons'] == 1 ) {
			$clipper->{APP_POST_TYPE}->expire->_prune_expired_listings();
			echo scb_admin_notice( __( 'Expired coupons have been pruned.', APP_TD ) );
		}

		if ( isset( $_GET['reset-votes'] ) && $_GET['reset-votes'] == 1 ) {
			clpr_reset_votes();
			echo scb_admin_notice( __( 'Votes have been reseted.', APP_TD ) );
		}

		if ( isset( $_GET['reset-stats'] ) && $_GET['reset-stats'] == 1 ) {
			appthemes_reset_stats();
			echo scb_admin_notice( __( 'Statistics have been reseted.', APP_TD ) );
		}

		if ( isset( $_GET['reset-search-stats'] ) && $_GET['reset-search-stats'] == 1 ) {
			clpr_reset_search_stats();
			echo scb_admin_notice( __( 'Search statistics have been reseted.', APP_TD ) );
		}

	}

	/**
	 * Initializes page tabs.
	 *
	 * @return void
	 */
	protected function init_tabs() {
		// Remove unwanted query args from urls
		$_SERVER['REQUEST_URI'] = remove_query_arg( array( 'firstrun', 'prune-coupons', 'reset-votes', 'reset-stats', 'reset-search-stats' ), $_SERVER['REQUEST_URI'] );

		$this->tabs->add( 'general', __( 'General', APP_TD ) );
		$this->tabs->add( 'coupon', __( 'Coupons', APP_TD ) );
		$this->tabs->add( 'security', __( 'Security', APP_TD ) );
		$this->tabs->add( 'advertise', __( 'Advertising', APP_TD ) );
		$this->tabs->add( 'advanced', __( 'Advanced', APP_TD ) );

		$this->tab_sections['general']['configuration'] = array(
			'title'  => __( 'Appearance', APP_TD ),
			'desc'   => sprintf( __( 'Further customize the look and feel by visiting the <a href="%1$s">WordPress customizer</a>.', APP_TD ), 'customize.php' ),
			'fields' => array(
				array(
					'title'  => __( 'Scheme', APP_TD ),
					'type'   => 'select',
					'name'   => 'stylesheet',
					'values' => array(
						'primary.css'    => __( 'Primary Theme', APP_TD ),
						'red.css'    => __( 'Red Theme', APP_TD ),
						'blue.css'   => __( 'Blue Theme', APP_TD ),
						'orange.css' => __( 'Orange Theme', APP_TD ),
						'green.css'  => __( 'Green Theme', APP_TD ),
						'gray.css'   => __( 'Gray Theme', APP_TD ),
					),
					'tip' => '',
				),
				array(
					'title' => __( 'Slider', APP_TD ),
					'name'  => 'featured_slider',
					'type'  => 'checkbox',
					'desc'  => __( 'Show the featured slider on the home page', APP_TD ),
					'tip'   => __( 'To make a coupon appear, check the "Featured Coupon" box on the edit post page under "Coupon Meta Fields".', APP_TD ),
				),
				array(
					'title' => __( 'Feedburner URL', APP_TD ),
					'desc'  => sprintf( __( '%1$s Sign up for a free <a target="_blank" href="%2$s">Feedburner account</a>.', APP_TD ), '<i class="social-ico dashicons-before feedburnerico"></i>', 'https://feedburner.google.com' ),
					'type'  => 'text',
					'name'  => 'feedburner_url',
					'tip'   => __( 'Automatically redirect your default RSS feed to Feedburner.', APP_TD ),
				),
				array(
					'title' => __( 'Twitter ID', APP_TD ),
					'desc'  => sprintf( __( '%1$s Sign up for a free <a target="_blank" href="%2$s">Twitter account</a>.', APP_TD ), '<i class="social-ico dashicons-before twitterico"></i>', 'https://twitter.com' ),
					'type'  => 'text',
					'name'  => 'twitter_id',
					'tip'   => __( 'Automatically redirect your Twitter link to your Twitter page.', APP_TD ),
				),
				array(
					'title' => __( 'Facebook ID', APP_TD ),
					'desc'  => sprintf( __( '%1$s Sign up for a free <a target="_blank" href="%2$s">Facebook account</a>.', APP_TD ), '<i class="social-ico dashicons-before facebookico"></i>', 'https://www.facebook.com' ),
					'type'  => 'text',
					'name'  => 'facebook_id',
					'tip'   => __( 'Display a Facebook icon in the header that links to your page.', APP_TD ),
				),
				array(
					'title'    => __( 'Analytics Code', APP_TD ),
					'desc'     => sprintf( __( 'Sign up for a free <a target="_blank" href="%s">Google Analytics account</a>.', APP_TD ), 'https://www.google.com/analytics/' ),
					'type'     => 'textarea',
					'sanitize' => 'appthemes_clean',
					'name'     => 'google_analytics',
					'extra'    => array(
						'rows'  => 10,
						'cols'  => 50,
						'class' => 'large-text code',
					),
					'tip'      => __( 'You can use Google Analytics or other providers as well.', APP_TD ),
				),
			),
		);

		$this->tab_sections['general']['search_settings'] = array(
			'title'  => __( 'Search', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Pages', APP_TD ),
					'name'  => 'search_ex_pages',
					'type'  => 'checkbox',
					'desc'  => __( 'Exclude from blog search results', APP_TD ),
					'tip'   => '',
				),
				array(
					'title' => __( 'Search Stats', APP_TD ),
					'name'  => 'search_stats',
					'type'  => 'checkbox',
					'desc'  => __( 'Save all search queries', APP_TD ),
					'tip'   => '',
				),
			),
		);


		$this->tab_sections['coupon']['general'] = array(
			'title'  => __( 'General', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Prune Coupons', APP_TD ),
					'name'  => 'prune_coupons',
					'type'  => 'checkbox',
					'desc'  => __( 'Automatically remove expired coupons', APP_TD ),
					'tip'   => __( 'Left unchecked, coupons will remain live, be marked as expired, and moved under the "unreliable coupons" section. It changes the post status to draft (not delete coupons).', APP_TD ),
				),
				array(
					'title' => __( 'Registration', APP_TD ),
					'name'  => 'reg_required',
					'type'  => 'checkbox',
					'desc'  => __( 'Require a user account before submitting coupons', APP_TD ),
					'tip'   => __( 'If "Charge for Coupons" is enabled, all users are required to have an account regardless so this option will be ignored.', APP_TD ),
				),
			),
		);

		$this->tab_sections['coupon']['coupon_page'] = array(
			'title' => __( 'Coupons Page', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Coupon Code', APP_TD ),
					'name'  => 'coupon_code_hide',
					'type'  => 'checkbox',
					'desc'  => __( "Don't show the coupon code until after they click on it", APP_TD ),
					'tip'   => '',
				),
				// array(
				// 	'title' => __( 'Unreliable Coupons', APP_TD ),
				// 	'name'  => 'exclude_unreliable',
				// 	'type'  => 'checkbox',
				// 	'desc'  => __( 'Exclude unreliable coupons from the home page', APP_TD ),
				// 	'tip'   => '',
				// ),
				array(
					'title' => __( 'Unreliable Featured Coupons', APP_TD ),
					'name'  => 'exclude_unreliable_featured',
					'type'  => 'checkbox',
					'desc'  => __( 'Exclude unreliable coupons from the featured coupons slider', APP_TD ),
					'tip'   => '',
				),
				array(
					'title' => __( 'Views Counter', APP_TD ),
					'name'  => 'stats_all',
					'type'  => 'checkbox',
					'desc'  => __( 'Show a daily and total views counter', APP_TD ),
					'tip'   => __( "This will appear at the bottom of each coupon page and blog post.", APP_TD ),
				),
				array(
					'title' => __( 'Link to Page', APP_TD ),
					'name'  => 'link_single_page',
					'type'  => 'checkbox',
					'desc'  => __( 'Display links to single coupon pages', APP_TD ),
					'tip'   => __( 'Leave empty and there will be no direct link to these pages.', APP_TD ),
				),
				array(
					'title' => __( 'Cloak Links', APP_TD ),
					'name'  => 'cloak_links',
					'type'  => 'checkbox',
					'desc'  => __( "Hide coupon and store outgoing URLs", APP_TD ),
					'tip'   => __( 'Click-thrus will be counted for each link.', APP_TD ),
				),
				array(
					'title' => __( 'Direct Links', APP_TD ),
					'name'  => 'direct_links',
					'type'  => 'checkbox',
					'desc'  => __( 'Open outbound coupon links in a new browser tab', APP_TD ),
					'tip'   => __( "The default is to use a popup window. NOTE: This option will be ignored in case that Flash object is disabled in a user's browser or if option 'Coupon Code' is enabled.", APP_TD ),
				),
			),
		);

		$this->tab_sections['security']['settings'] = array(
			'title' => __( 'General', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'WP-Admin', APP_TD ),
					'desc' => sprintf( __( "Restrict access by <a target='_blank' href='%s'>specific role</a>.", APP_TD ), 'http://codex.wordpress.org/Roles_and_Capabilities' ),
					'type' => 'select',
					'name' => 'admin_security',
					'values' => array(
						'manage_options' => __( 'Admins Only', APP_TD ),
						'edit_others_posts' => __( 'Admins, Editors', APP_TD ),
						'publish_posts' => __( 'Admins, Editors, Authors', APP_TD ),
						'edit_posts' => __( 'Admins, Editors, Authors, Contributors', APP_TD ),
						'read' => __( 'All Access', APP_TD ),
						'disable' => __( 'Disable', APP_TD ),
					),
					'tip' => '',
				),
			),
		);

		$this->tab_sections['security']['recaptcha'] = array(
			'title' => __( 'reCaptcha', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Enable', APP_TD ),
					'name'  => 'captcha_enable',
					'type'  => 'checkbox',
					'desc'  => sprintf( __( "A free <a target='_blank' href='%s'>anti-spam service</a> provided by Google", APP_TD ), 'https://www.google.com/recaptcha/' ),
					'tip'   => __( 'Displays a verification box on your registration page to prevent your website from spam and abuse.', APP_TD ),
				),
				array(
					'title' => __( 'Site Key', APP_TD ),
					'desc'  => '',
					'type'  => 'text',
					'name'  => 'captcha_public_key',
					'desc'  => '',
				),
				array(
					'title' => __( 'Secret Key', APP_TD ),
					'desc'  => '',
					'type'  => 'text',
					'name'  => 'captcha_private_key',
					'tip'   => '',
				),
				array(
					'title'  => __( 'Theme', APP_TD ),
					'type'   => 'select',
					'name'   => 'captcha_theme',
					'tip'    => '',
					'values' => array(
						'light' => __( 'Light', APP_TD ),
						'dark'  => __( 'Dark', APP_TD ),
					),
				),
			),
		);


		$this->tab_sections['advertise']['content'] = array(
			'title' => __( 'Content Ad (336x280)', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Enable', APP_TD ),
					'name' => 'adcode_336x280_enable',
					'type' => 'checkbox',
					'desc' => __( 'Displayed on single coupon, category, and search result pages', APP_TD ),
					'tip' => '',
				),
				array(
					'title' => __( 'Code', APP_TD ),
					'desc' => sprintf( __( 'Supports many popular providers such as <a target="_blank" href="%s">Google AdSense</a> and <a target="_blank" href="%s">BuySellAds</a>.', APP_TD ), 'https://www.google.com/adsense/', 'https://www.buysellads.com/' ),
					'type' => 'textarea',
					'sanitize' => 'appthemes_clean',
					'name' => 'adcode_336x280',
					'extra' => array(
						'rows' => 10,
						'cols' => 50,
						'class' => 'large-text code'
					),
					'tip' => '',
				),
				array(
					'title' => __( 'Image', APP_TD ),
					'desc' => $this->wrap_upload( 'adcode_336x280_url', '<br />' . __( 'Enter the URL to your ad creative.', APP_TD ) ),
					'type' => 'text',
					'name' => 'adcode_336x280_url',
					'tip' => __( 'If you would rather use an image ad instead of code provided by your advertiser, use this field.', APP_TD ),
				),
				array(
					'title' => __( 'Destination', APP_TD ),
					'desc' => __( 'The URL of your landing page.', APP_TD ),
					'type' => 'text',
					'name' => 'adcode_336x280_dest',
					'tip' => __( 'When a visitor clicks on your ad image, this is the destination they will be sent to.', APP_TD ),
				),
			),
		);


		$this->tab_sections['advanced']['settings'] = array(
			'title' => __( 'Maintenance', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Prune Coupons', APP_TD ),
					'name' => '_blank',
					'type' => '',
					'desc' => sprintf( __( 'Prune <a href="%s">expired coupons</a>', APP_TD ), 'admin.php?page=app-settings&prune-coupons=1' ),
					'extra' => array(
						'style' => 'display: none;'
					),
					'tip' => __( 'Click the link to manually run the function that checks all coupons expiration and prunes any coupons that are expired. This event will run only one time.', APP_TD ),
				),
				array(
					'title' => __( 'Reset Votes', APP_TD ),
					'name' => '_blank',
					'type' => '',
					'desc' => sprintf( __( 'Reset <a href="%s">vote counters</a>', APP_TD ), 'admin.php?page=app-settings&reset-votes=1' ),
					'extra' => array(
						'style' => 'display: none;'
					),
					'tip' => __( 'Resets the vote counters for all coupons.', APP_TD ),
				),
				array(
					'title' => __( 'Reset Stats', APP_TD ),
					'name' => '_blank',
					'type' => '',
					'desc' => sprintf( __( 'Reset <a href="%s">stats counters</a>', APP_TD ), 'admin.php?page=app-settings&reset-stats=1' ),
					'extra' => array(
						'style' => 'display: none;'
					),
					'tip' => __( 'Resets the stat counters for all coupons and posts.', APP_TD ),
				),
				array(
					'title' => __( 'Reset Search', APP_TD ),
					'name' => '_blank',
					'type' => '',
					'desc' => sprintf( __( 'Reset <a href="%s">search counters</a>', APP_TD ), 'admin.php?page=app-settings&reset-search-stats=1' ),
					'extra' => array(
						'style' => 'display: none;'
					),
					'tip' => __( 'Resets the search engine stats.', APP_TD ),
				),
			),
		);


		$this->tab_sections['advanced']['user'] = array(
			'title' => __( 'User', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Disable Login Page', APP_TD ),
					'name' => 'disable_wp_login',
					'type' => 'checkbox',
					'desc' => __( 'Prevents users from accessing <code>wp-login.php</code> directly', APP_TD ),
					'tip' => '',
				),
			),
		);

		$this->tab_sections['advanced']['developer'] = array(
			'title' => __( 'Developer', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Enable Debug Mode', APP_TD ),
					'name' => 'debug_mode',
					'type' => 'checkbox',
					'desc' => __( 'Print out the <code>$wp_query->query_vars</code> array at the top of your website', APP_TD ),
					'tip' => '',
				),
				array(
					'title' => __( 'Hide Version', APP_TD ),
					'name' => 'remove_wp_generator',
					'type' => 'checkbox',
					'desc' => __( "Remove the WordPress version meta tag from your website", APP_TD ),
					'tip' => __( "An added security measure so snoopers won't be able to tell what version of WordPress you're running.", APP_TD ),
				),
			),
		);

		$this->tab_sections['advanced']['permalinks'] = array(
			'title' => __( 'Custom URLs', APP_TD ),
			'desc' => sprintf( __( 'This controls the base names of your website urls. Do not change this once your site is established. You must resave your <a target="_blank" href="%s">permalinks</a> for any changes to take effect.', APP_TD ), 'options-permalink.php' ),
			'fields' => array(
				array(
					'title' => __( 'Coupon Listing', APP_TD ),
					'desc' => '',
					'type' => 'text',
					'name' => 'coupon_permalink',
					'tip' => __( "Base name of your coupon listing urls. The default is 'coupon' (http://www.yoursite.com/coupons/coupon-title-here/). Only use alpha and/or numeric values (no slashes).", APP_TD ),
				),
				array(
					'title' => __( 'Coupon Category', APP_TD ),
					'desc' => '',
					'type' => 'text',
					'name' => 'coupon_cat_tax_permalink',
					'tip' => __( "Base name of your coupon category urls. The default is 'coupon-category' (http://www.yoursite.com/coupon-category/category-name/). Only use alpha and/or numeric values (no slashes).", APP_TD ),
				),
				array(
					'title' => __( 'Coupon Store', APP_TD ),
					'desc' => '',
					'type' => 'text',
					'name' => 'coupon_store_tax_permalink',
					'tip' => __( "Base name of your coupon store urls. The default is 'store' (http://www.yoursite.com/store/store-name/). Only use alpha and/or numeric values (no slashes).", APP_TD ),
				),
				array(
					'title' => __( 'Coupon Type', APP_TD ),
					'desc' => '',
					'type' => 'text',
					'name' => 'coupon_type_tax_permalink',
					'tip' => __( "Base name of your coupon type urls. The default is 'coupon-type' (http://www.yoursite.com/coupon-type/type-name/). Only use alpha and/or numeric values (no slashes).", APP_TD ),
				),
				array(
					'title' => __( 'Coupon Tag', APP_TD ),
					'desc' => '',
					'type' => 'text',
					'name' => 'coupon_tag_tax_permalink',
					'tip' => __( "Base name of your coupon tag urls. The default is 'coupon-tag' (http://www.yoursite.com/coupon-tag/tag-name/). Only use alpha and/or numeric values (no slashes).", APP_TD ),
				),
				array(
					'title' => __( 'Coupon Redirect', APP_TD ),
					'desc' => '',
					'type' => 'text',
					'name' => 'coupon_redirect_base_url',
					'tip' => __( "Base name of your display url. If you have a destination url setup for a coupon, it will look something like this when you hover over the link: http://www.yoursite.com/go/coupon-name/23. The '/go/' is what is controlled here. Only use alpha and/or numeric values (no slashes).", APP_TD ),
				),
				array(
					'title' => __( 'Store Redirect', APP_TD ),
					'desc' => '',
					'type' => 'text',
					'name' => 'store_redirect_base_url',
					'tip' => __( "Base name of your display url. If you have a destination url setup for a store, it will look something like this when you hover over the link: http://www.yoursite.com/go-store/store-name/23. The '/go-store/' is what is controlled here. Only use alpha and/or numeric values (no slashes).", APP_TD ),
				),
			),
		);

	}

	/**
	 * Returns an upload image buttons, and image preview.
	 *
	 * @param string $field_name
	 * @param string $desc
	 *
	 * @return string
	 */
	private function wrap_upload( $field_name, $desc ) {
		$upload_button = html( 'input', array( 'class' => 'upload_button button', 'rel' => $field_name, 'type' => 'button', 'value' => __( 'Upload Image', APP_TD ) ) );
		$clear_button = html( 'input', array( 'class' => 'delete_button button', 'rel' => $field_name, 'type' => 'button', 'value' => __( 'Clear Image', APP_TD ) ) );
		$preview = html( 'div', array( 'id' => $field_name . '_image', 'class' => 'upload_image_preview' ), html( 'img', array( 'src' => scbForms::get_value( $field_name, $this->options->get() ) ) ) );

		return $upload_button . ' ' . $clear_button . $desc . $preview;
	}

	/**
	 * Prints page footer.
	 *
	 * @return void
	 */
	function page_footer() {
		parent::page_footer();
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	/* upload logo and images */
	jQuery('.upload_button').click(function() {
		formfield = jQuery(this).attr('rel');
		tb_show('', 'media-upload.php?type=image&amp;post_id=0&amp;TB_iframe=true');
		return false;
	});

	/* send the uploaded image url to the field */
	window.send_to_editor = function(html) {
		imgurl = jQuery('img',html).attr('src'); // get the image url
		imgoutput = '<img src="' + imgurl + '" />'; //get the html to output for the image preview
		jQuery('#' + formfield).val(imgurl);
		jQuery('#' + formfield + '_image').html(imgoutput);
		tb_remove();
	}
});
</script>
<?php
	}


}



/**
 * Emails Settings Page.
 */
class CLPR_Theme_Settings_Emails extends APP_Tabs_Page {

	/**
	 * Setups page.
	 *
	 * @return void
	 */
	function setup() {
		$this->textdomain = APP_TD;

		$this->args = array(
			'page_title' => __( 'Clipper Emails', APP_TD ),
			'menu_title' => __( 'Emails', APP_TD ),
			'page_slug' => 'app-emails',
			'parent' => 'app-dashboard',
			'screen_icon' => 'options-general',
			'admin_action_priority' => 10,
		);

	}

	/**
	 * Initializes page tabs.
	 *
	 * @return void
	 */
	protected function init_tabs() {
		$this->tabs->add( 'general', __( 'General', APP_TD ) );
		$this->tabs->add( 'new_user', __( 'New User', APP_TD ) );
		$this->tabs->add( 'new_coupon', __( 'New Coupon', APP_TD ) );

		$this->tab_sections['general']['notifications'] = array(
			'title' => __( 'Admin', APP_TD ),
			'fields' => array(
				array(
					'title' => __( 'Recipient', APP_TD ),
					'name' => '_blank',
					'type' => '',
					'desc' => sprintf( __( '%1$s (<a href="%2$s">change</a>)', APP_TD ), get_option('admin_email'), 'options-general.php' ),
					'extra' => array(
						'style' => 'display: none;'
					),
				),
				array(
					'title' => __( 'Pruned Coupons', APP_TD ),
					'name' => 'prune_coupons_email',
					'type' => 'checkbox',
					'desc' => __( 'Send an email every time the system prunes expired coupons', APP_TD ),
					'tip' => '',
				),
				array(
					'title' => __( 'New User', APP_TD ),
					'name' => 'nu_admin_email',
					'type' => 'checkbox',
					'desc' => __( 'Send an email when a user registers on my site', APP_TD ),
					'tip' => '',
				),
			),
		);


		$this->tab_sections['new_user']['settings'] = array(
			'title' => '',
			'fields' => array(
				array(
					'title' => __( 'Enable', APP_TD ),
					'name' => 'nu_custom_email',
					'type' => 'checkbox',
					'desc' => __( 'Send a custom new user notification email instead of the WordPress default one', APP_TD ),
					'tip' => '',
				),
				array(
					'title' => __( 'Name', APP_TD ),
					'type' => 'text',
					'name' => 'nu_from_name',
					'tip' => __( 'This is what your users will see as the &quot;from&quot; name.', APP_TD ),
				),
				array(
					'title' => __( 'Email', APP_TD ),
					'type' => 'text',
					'name' => 'nu_from_email',
					'tip' => __( 'This is what your users will see as the &quot;from&quot; email address.', APP_TD ),
				),
				array(
					'title' => __( 'Subject', APP_TD ),
					'type' => 'text',
					'name' => 'nu_email_subject',
					'tip' => '',
				),
				array(
					'title' => __( 'Allow HTML', APP_TD ),
					'name' => 'nu_email_type',
					'type' => 'select',
					'values' => array(
						'text/HTML' => __( 'Yes', APP_TD ),
						'text/plain' => __( 'No', APP_TD ),
					),
					'tip' => __( 'Allow html markup in the email body below. If you have delivery problems, keep this option disabled.', APP_TD ),
				),
				array(
					'title' => __( 'Body', APP_TD ),
					'desc' => __( 'You may use the following variables within the email body and/or subject line.', APP_TD )
						. '<br />' . sprintf( __( '%s - prints out the username', APP_TD ), '<code>%username%</code>' )
						. '<br />' . sprintf( __( '%s - prints out the users email address', APP_TD ), '<code>%useremail%</code>' )
						. '<br />' . sprintf( __( '%s - prints out the users text password', APP_TD ), '<code>%password%</code>' )
						. '<br />' . sprintf( __( '%s - prints out your website url', APP_TD ), '<code>%siteurl%</code>' )
						. '<br />' . sprintf( __( '%s - prints out your site name', APP_TD ), '<code>%blogname%</code>' )
						. '<br />' . sprintf( __( '%s - prints out your sites login url', APP_TD ), '<code>%loginurl%</code>' )
						. '<br /><br />' . __( 'Each variable MUST have the percentage signs wrapped around it with no spaces.', APP_TD )
						. '<br />' . __( 'Always test your new email after making any changes (register) to make sure it is working and formatted correctly. If you do not receive an email, chances are something is wrong with your email body.', APP_TD ),
					'type' => 'textarea',
					'sanitize' => 'appthemes_clean',
					'name' => 'nu_email_body',
					'extra' => array(
						'rows' => 20,
						'cols' => 50,
						'class' => 'large-text code'
					),
					'tip' => '',
				),
			),
		);


		$this->tab_sections['new_coupon']['settings'] = array(
			'title' => '',
			'fields' => array(
				array(
					'title' => __( 'Name', APP_TD ),
					'type' => 'text',
					'name' => 'nc_from_name',
					'tip' => __( 'This is what your users will see as the &quot;from&quot; name.', APP_TD ),
				),
				array(
					'title' => __( 'Email', APP_TD ),
					'type' => 'text',
					'name' => 'nc_from_email',
					'tip' => __( 'This is what your users will see as the &quot;from&quot; email address.', APP_TD ),
				),
				array(
					'title' => __( 'Subject', APP_TD ),
					'type' => 'text',
					'name' => 'nc_email_subject',
					'tip' => '',
				),
				array(
					'title' => __( 'Allow HTML', APP_TD ),
					'name' => 'nc_email_type',
					'type' => 'select',
					'values' => array(
						'text/HTML' => __( 'Yes', APP_TD ),
						'text/plain' => __( 'No', APP_TD ),
					),
					'tip' => __( 'Allow html markup in the email body below. If you have delivery problems, keep this option disabled.', APP_TD ),
				),
				array(
					'title' => __( 'Body', APP_TD ),
					'desc' => __( 'You may use the following variables within the email body and/or subject line.', APP_TD )
						. '<br />' . sprintf( __( '%s - prints out the username', APP_TD ), '<code>%username%</code>' )
						. '<br />' . sprintf( __( '%s - prints out the users email address', APP_TD ), '<code>%useremail%</code>' )
						. '<br />' . sprintf( __( '%s - prints out your website url', APP_TD ), '<code>%siteurl%</code>' )
						. '<br />' . sprintf( __( '%s - prints out your site name', APP_TD ), '<code>%blogname%</code>' )
						. '<br />' . sprintf( __( '%s - prints out your login url', APP_TD ), '<code>%loginurl%</code>' )
						. '<br />' . sprintf( __( '%s - prints out the coupon title', APP_TD ), '<code>%title%</code>' )
						. '<br />' . sprintf( __( '%s - prints out the coupon code', APP_TD ), '<code>%code%</code>' )
						. '<br />' . sprintf( __( '%s - prints out the coupon category', APP_TD ), '<code>%category%</code>' )
						. '<br />' . sprintf( __( '%s - prints out the coupon store name', APP_TD ), '<code>%store%</code>' )
						. '<br />' . sprintf( __( '%s - prints out the coupon description', APP_TD ), '<code>%description%</code>' )
						. '<br />' . sprintf( __( '%s - prints out the dashboard url', APP_TD ), '<code>%dashurl%</code>' )
						. '<br /><br />' . __( 'Each variable MUST have the percentage signs wrapped around it with no spaces.', APP_TD )
						. '<br />' . __( 'Always test your new email after making any changes (register) to make sure it is working and formatted correctly. If you do not receive an email, chances are something is wrong with your email body.', APP_TD ),
					'type' => 'textarea',
					'sanitize' => 'appthemes_clean',
					'name' => 'nc_email_body',
					'extra' => array(
						'rows' => 20,
						'cols' => 50,
						'class' => 'large-text code'
					),
					'tip' => '',
				),
			),
		);

	}

}
