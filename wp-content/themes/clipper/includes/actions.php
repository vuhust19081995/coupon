<?php
/**
 * Action and filter hooks.
 *
 * @package Clipper\Actions
 * @author  AppThemes
 * @since   1.0.0
 */

/**
 * Adds version number in the header for troubleshooting.
 *
 * @since 1.0.0
 *
 * @return void
 */
function clpr_generator() {
	echo "\n\t" . '<meta name="generator" content="Clipper ' . CLPR_VERSION . '" />' . "\n";
}
add_action( 'wp_head', 'clpr_generator' );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 *
 * @since 2.0.0
 */
function clpr_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", get_bloginfo( 'pingback_url' ) );
	}
}
add_action( 'wp_head', 'clpr_pingback_header' );

/**
 * Add an alternate rss feed url if Feedburner is provided. Otherwise use default.
 *
 * @since 2.0.0
 */
function clpr_alternate_rss() {
	printf( '<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="%s">' . "\n", appthemes_get_feed_url() );
}
add_action( 'wp_head', 'clpr_alternate_rss' );

/**
 * Adds the google analytics tracking code in the footer.
 *
 * @since 1.0.0
 *
 * @return void
 */
function clpr_google_analytics_code() {
	global $clpr_options;

	if ( empty( $clpr_options->google_analytics ) ) {
		return;
	}

	echo stripslashes( $clpr_options->google_analytics );
}
add_action( 'wp_footer', 'clpr_google_analytics_code' );

/**
 * Adds the debug code to the footer.
 *
 * You must add following code to the wp-config.php file in order to see queries:
 * define( 'WP_DEBUG', true );
 * define( 'SAVEQUERIES', true );
 *
 * NOTE: This will have a performance impact on your site, so make sure to turn this off when you aren't debugging.
 *
 * @since 1.0.0
 *
 * @return void
 */
function clpr_add_after_footer() {
	global $wpdb, $wp_query, $clpr_options;

	if ( ! $clpr_options->debug_mode || ! current_user_can( 'manage_options' ) ) {
		return;
	}
?>
	<div class="clr"></div>
	<div class="debug">
		<h3><?php _e( 'Debug Mode On', APP_TD ); ?></h3>
		<br /><br />
		<h3>$wp_query->query_vars output</h3>
		<p><pre><?php print_r( $wp_query->query_vars ); ?></pre></p>
		<br /><br />
		<h3>$wpdb->queries output</h3>
		<p><pre><?php print_r( $wpdb->queries ); ?></pre></p>
	</div>
<?php
}
add_action( 'appthemes_after_footer', 'clpr_add_after_footer' );

/**
 * Sets custom favicon if specified in theme settings.
 *
 * @deprecated
 *
 * @since 1.0.0
 *
 * @param string $favicon
 * @return string
 */
function clpr_custom_favicon( $favicon ) {
	global $clpr_options;

	if ( ! empty( $clpr_options->favicon_url ) ) {
		$favicon = $clpr_options->favicon_url;
	}

	return $favicon;
}
add_filter( 'appthemes_favicon', 'clpr_custom_favicon', 10, 1 );

/**
 * Adds the colorbox to blog post galleries.
 *
 * @since 1.0.0
 *
 * @return void
 */
function clpr_colorbox_blog() {
?>
	<script type="text/javascript">
	// <![CDATA[
		jQuery(document).ready(function($){
			$(".gallery").each(function(index, obj){
				var galleryid = Math.floor(Math.random()*10000);
				$(obj).find("a").colorbox({rel:galleryid, maxWidth:"95%", maxHeight:"95%"});
			});
			$("a.lightbox").colorbox({maxWidth:"95%", maxHeight:"95%"});
		});
	// ]]>
	</script>
<?php
}
add_action( 'appthemes_before_blog_loop', 'clpr_colorbox_blog' );

/**
 * Adds the pagination to the coupons lists.
 *
 * @since 1.0.0
 *
 * @return void
 */
function clpr_coupon_pagination() {

	if ( is_singular( APP_POST_TYPE ) ) {
		return;
	}

	appthemes_pagination();
}
add_action( 'appthemes_after_endwhile', 'clpr_coupon_pagination' );
add_action( 'appthemes_after_search_endwhile', 'clpr_coupon_pagination' );

/**
 * Adds the post tags, and stats after the blog post content.
 *
 * @since 1.0.0
 *
 * @return void
 */
function clpr_blog_post_tags() {
	global $post, $clpr_options;

	if ( is_page() ) {
		return;
	}
?>
	<div class="text-footer">

		<div class="row">

			<div class="small-6 columns">

				<div class="tags"><i class="fa fa-tags" aria-hidden="true"></i><?php _e( 'Tags:', APP_TD ); ?> <?php if ( get_the_tags() ) the_tags( ' ', ', ', '' ); else echo ' ' . __( 'None', APP_TD ); ?></div>

			</div><!-- .columns -->

			<div class="small-6 columns">

				<?php if ( $clpr_options->stats_all && current_theme_supports( 'app-stats' ) ) { ?>
					<div class="stats"><i class="fa fa-bar-chart" aria-hidden="true"></i><?php appthemes_stats_counter( $post->ID ); ?></div>
				<?php } ?>

			</div><!-- .columns -->

		</div><!-- .row -->

	</div><!-- .text-footer -->
<?php
}
add_action( 'appthemes_after_blog_post_content', 'clpr_blog_post_tags' );

/**
 * Adds the author box after the blog post content.
 *
 * @since 1.0.0
 *
 * @return void
 */
function clpr_author_box() {

	if ( ! is_singular( 'post' ) ) {
		return;
	}

	if ( ! get_the_author_meta( 'description' ) ) {
		return;
	}
?>
	<div class="author-wrap">

		<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'clpr_author_bio_avatar_size', 60 ) ); ?>

		<p class="author"><?php printf( esc_attr__( 'About %s', APP_TD ), get_the_author() ); ?></p>
		<p><?php the_author_meta( 'description' ); ?></p>

	</div>
<?php
}
add_action( 'appthemes_after_blog_post_content', 'clpr_author_box' );

/**
 * Modifies Social Connect plugin redirect to url.
 *
 * @since 1.3.1
 *
 * @param string
 * @return string
 */
function clpr_social_connect_redirect_to( $redirect_to ) {
	if ( preg_match( '#/wp-(admin|login)?(.*?)$#i', $redirect_to ) ) {
		$redirect_to = home_url();
	}

	if ( current_theme_supports( 'app-login' ) ) {
		if ( APP_Login::get_url( 'redirect' ) == $redirect_to || appthemes_get_registration_url( 'redirect' ) == $redirect_to ) {
			$redirect_to = home_url();
		}
	}

	return $redirect_to;
}
add_filter( 'social_connect_redirect_to', 'clpr_social_connect_redirect_to', 10, 1 );

/**
 * Processing Social Connect plugin request if App Login pages are enabled.
 *
 * @since 1.3.2
 *
 * @return void
 */
function clpr_social_connect_login() {

	if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'social_connect' ) {
		if ( current_theme_supports( 'app-login' ) && function_exists( 'sc_social_connect_process_login' ) ) {
			sc_social_connect_process_login( false );
		}
	}
}
add_action( 'init', 'clpr_social_connect_login' );

/**
 * Display reCatpcha if theme supports it.
 *
 * @since 2.0.0
 *
 * @return void
 */
function clpr_maybe_display_recaptcha() {
	global $clpr_options;

	if ( ! $clpr_options->captcha_enable ) {
		return;
	}

	appthemes_display_recaptcha();
}

/**
 * Adds reCaptcha theme support.
 *
 * @since 1.3.2
 *
 * @return void
 */
function clpr_recaptcha_support() {
	global $clpr_options;

	if ( ! $clpr_options->captcha_enable ) {
		return;
	}

	add_theme_support( 'app-recaptcha', array(
		'theme'       => $clpr_options->captcha_theme,
		'public_key'  => $clpr_options->captcha_public_key,
		'private_key' => $clpr_options->captcha_private_key,
	) );

	// Integrate recaptcha on the User Registration form.
	add_action( 'register_form', 'clpr_maybe_display_recaptcha' );
	add_action( 'appthemes_before_login_template', 'clpr_recaptcha_scripts_enqueue' );
	add_filter( 'registration_errors', 'clpr_recaptcha_verify' );

	// Integrate recaptcha in the Anonymous Coupon process.
	add_action( 'appthemes_listing_create_anonymous_form', 'clpr_maybe_display_recaptcha' );
	add_action( 'appthemes_listing_create_anonymous_scripts', 'appthemes_enqueue_recaptcha_scripts' );
	add_filter( 'appthemes_validate_create_anonymous_fields', 'clpr_recaptcha_verify' );
}
add_action( 'appthemes_init', 'clpr_recaptcha_support' );

/**
 * Verify reCaptcha user response.
 *
 * @since 1.3.2
 *
 * @param object $errors
 * @return object
 */
function clpr_recaptcha_verify( $errors ) {

	$response = appthemes_recaptcha_verify();
	if ( is_wp_error( $response ) ) {

		foreach ( $response->get_error_codes() as $code ) {
			$errors->add( $code, $response->get_error_message( $code ) );
		}

	}

	return $errors;
}

/**
 * Enqueue reCaptcha scripts on registration page.
 *
 * @since 2.0.0
 *
 * @param string $action
 * @return void
 */
function clpr_recaptcha_scripts_enqueue( $action ) {
	if ( 'register' !== $action ) {
		return;
	}

	appthemes_enqueue_recaptcha_scripts();
}

/**
 * Displays 336 x 280 Ad box.
 *
 * @since 1.4.0
 *
 * @return void
 */
function clpr_adbox_336x280() {
	global $clpr_options;

	if ( ! $clpr_options->adcode_336x280_enable ) {
		return;
	}

	if ( ! empty( $clpr_options->adcode_336x280 ) ) {
		echo stripslashes( $clpr_options->adcode_336x280 );
	} else {
		if ( $clpr_options->adcode_336x280_url ) {
			$img = html( 'img', array( 'src' => $clpr_options->adcode_336x280_url, 'alt' => '' ) );
			echo html( 'a', array( 'href' => $clpr_options->adcode_336x280_dest, 'target' => '_blank' ), $img );
		}
	}
}

/**
 * Adds advertise to single blog page.
 *
 * @since 1.4.0
 *
 * @return void
 */
function clpr_adbox_single_page() {
	global $clpr_options;

	if ( ! is_singular( array( 'post' ) ) ) {
		return;
	}

	if ( ! $clpr_options->adcode_336x280_enable ) {
		return;
	}
?>
	<div class="content-box">

		<div class="box-holder">

			<div class="post-box">

				<div class="head">

					<h3><?php _e( 'Sponsored Ads', APP_TD ); ?></h3>

				</div>

				<div class="text-box">

					<?php clpr_adbox_336x280(); ?>

				</div>

			</div>

		</div>

	</div>
<?php
}
add_action( 'appthemes_advertise_content', 'clpr_adbox_single_page' );

/**
 * Checks and updates coupon status, unreliable vs. publish.
 *
 * @since 1.5.0
 *
 * @return void
 */
function clpr_maybe_update_coupon_status() {
	global $post;

	if ( ! in_the_loop() || $post->post_type != APP_POST_TYPE ) {
		return;
	}

	clpr_status_update( $post->ID, $post->post_status );
}
add_action( 'appthemes_before_post', 'clpr_maybe_update_coupon_status' );

/**
 * Pings 'update services' while publish coupon.
 *
 * @since 1.5.0
 */
add_action( 'publish_' . APP_POST_TYPE, '_publish_post_hook', 5, 1 );

/**
 * Moves social URLs into custom fields on user registration.
 *
 * @since 1.5.0
 *
 * @return void
 */
function clpr_move_social_url_on_user_registration( $user_id ) {

	$user_info = get_userdata( $user_id );

	if ( empty( $user_info->user_url ) ) {
		return;
	}

	if ( preg_match( '#facebook.com#i', $user_info->user_url ) ) {
		wp_update_user( array ( 'ID' => $user_id, 'user_url' => '' ) );
		update_user_meta( $user_id, 'facebook_id', $user_info->user_url );
	}
}
add_action( 'user_register', 'clpr_move_social_url_on_user_registration' );

/**
 * Make the options object instantly available in templates.
 * @since 1.5.0
 *
 * @return void
 */
function clpr_set_default_template_vars() {
	global $clpr_options;

	appthemes_add_template_var( 'clpr_options', $clpr_options );
}
add_action( 'template_redirect', 'clpr_set_default_template_vars' );

/**
 * Disables some WordPress features.
 *
 * @since 1.5.0
 *
 * @return void
 */
function clpr_disable_wp_features() {
	global $clpr_options;

	// remove the WordPress version meta tag
	if ( $clpr_options->remove_wp_generator ) {
		remove_action( 'wp_head', 'wp_generator' );
	}

}
add_action( 'init', 'clpr_disable_wp_features' );

/**
 * Display a noindex meta tag for single coupon pages if linking is disabled.
 *
 * @since 1.5.0
 *
 * @return void
 */
function clpr_noindex_single_coupon_page() {
	global $clpr_options;

	// if the blog is not public, meta tag is already there.
	if ( '0' == get_option( 'blog_public' ) ) {
		return;
	}

	if ( ! $clpr_options->link_single_page && is_singular( APP_POST_TYPE ) ) {
		wp_no_robots();
	}
}
add_action( 'wp_head', 'clpr_noindex_single_coupon_page' );

/**
 * Modify available buttons in html editor.
 *
 * @since 1.5.1
 *
 * @param array $buttons
 * @param string $editor_id
 *
 * @return array
 */
function clpr_editor_modify_buttons( $buttons, $editor_id ) {

	if ( is_admin() || ! is_array( $buttons ) ) {
		return $buttons;
	}

	$remove = array( 'wp_more', 'spellchecker' );

	return array_diff( $buttons, $remove );
}
add_filter( 'mce_buttons', 'clpr_editor_modify_buttons', 10, 2 );

/**
 * Add coupon type to the post class.
 *
 * @since 1.5.1
 *
 * @param array $classes
 * @param string $class
 * @param int $post_id
 *
 * @return array
 */
function clpr_add_coupon_type_to_post_class( $classes, $class, $post_id ) {

	$post = get_post( $post_id );

	if ( is_object_in_taxonomy( $post->post_type, APP_TAX_TYPE ) ) {
		foreach ( (array) get_the_terms( $post->ID, APP_TAX_TYPE ) as $term ) {
			if ( empty( $term->slug ) ) {
				continue;
			}

			$classes[] = APP_TAX_TYPE . '-' . sanitize_html_class( $term->slug, $term->term_id );
		}
	}

	return $classes;
}
add_filter( 'post_class', 'clpr_add_coupon_type_to_post_class', 10, 3 );

/**
 * Temporary fix for invalid reset password URL.
 *
 * @todo: remove after framework update.
 */
function _cp_fix_password_reset_url( $message, $key ) {
	return html_entity_decode( $message);
}
add_filter( 'retrieve_password_message', '_cp_fix_password_reset_url', 10, 2 );


/**
 * Set's up initial coupon data.
 *
 * @todo migrate to appropriate modules.
 *
 * @param APP_Dynamic_Checkout $checkout The checkout object.
 */
function clpr_set_initial_coupon_data( $checkout ) {
	$item_id = $checkout->get_data( 'listing_id' );

	clpr_set_initial_votes_data( $item_id );

	// set listing unique id
	if ( ! $unique_id = get_post_meta( $item_id, 'clpr_id', true ) ) {
		$unique_id = clpr_generate_id();
		update_post_meta( $item_id, 'clpr_id', $unique_id, true );
	}

	// set user IP
	update_post_meta( $item_id, 'clpr_sys_userIP', appthemes_get_ip() );

	// set meta with zero as default (stats and votes)
	$meta_names = array(
		'clpr_daily_count',
		'clpr_total_count',
		'clpr_coupon_aff_clicks',
	);
	foreach ( $meta_names as $meta_name ) {
		if ( ! $meta_value = get_post_meta( $item_id, $meta_name, true ) ) {
			update_post_meta( $item_id, $meta_name, '0', true );
		}
	}
}
add_action( 'appthemes_checkout_coupon-new_completed', 'clpr_set_initial_coupon_data' );
add_action( 'appthemes_checkout_coupon-renew_completed', 'clpr_set_initial_coupon_data' );
