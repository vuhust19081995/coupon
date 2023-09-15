<?php
/**
 * User Sidebar template.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 1.0.0
 */
global $current_user;
?>

<div id="sidebar" class="medium-4 columns" role="complementary">

	<?php appthemes_before_sidebar_widgets( 'user' ); ?>

	<aside id="user-options" class="widget widget_user_options">

		<div class="sidebox-main">

			<div class="sidebox-heading">
				<h2><?php _e( 'User Options', APP_TD ); ?></h2>
			</div>

			<ul>
				<li><a href="<?php echo clpr_get_dashboard_url(); ?>"><?php _e( 'My Dashboard', APP_TD ); ?></a></li>
				<?php if ( clpr_payments_is_enabled() ) { ?><li><a href="<?php echo CLPR_ORDERS_URL; ?>"><?php _e( 'My Orders', APP_TD ); ?></a></li><?php } ?>
				<li><a href="<?php echo clpr_get_profile_url(); ?>"><?php _e( 'Edit Profile', APP_TD ); ?></a></li>
				<?php if ( current_user_can( 'edit_others_posts' ) ) { ?><li><a href="<?php echo admin_url(); ?>"><?php _e( 'WordPress Admin', APP_TD ); ?></a></li><?php } ?>
				<li><a href="<?php echo clpr_logout_url( home_url() ); ?>"><?php _e( 'Log Out', APP_TD ); ?></a></li>
			</ul>

		</div> <!-- .sidebox-main -->

	</aside>

	<aside id="user-info" class="widget widget_user_options">

		<div class="sidebox-main">

			<div class="sidebox-heading">
				<h2><?php _e( 'Account Info', APP_TD ); ?></h2>
			</div>

			<div class="avatar">
				<?php appthemes_get_profile_pic( $current_user->ID, $current_user->user_email, 150 ); ?>
			</div>

			<ul class="user-info">
				<li class="display-name"><strong><a href="<?php echo get_author_posts_url( $current_user->ID ); ?>"><?php echo $current_user->display_name; ?></a></strong></li>
				<li class="member-since"><strong><?php _e( 'Joined:', APP_TD ); ?></strong> <?php appthemes_get_reg_date( $current_user->user_registered ); ?></li>
				<li class="last-login"><strong><?php _e( 'Last Login:', APP_TD ); ?></strong> <?php appthemes_get_last_login( $current_user->ID ); ?></li>
			</ul>

			<ul class="user-details">
				<li><i class="fa fa-envelope-o" aria-hidden="true"></i><a href="mailto:<?php echo $current_user->user_email; ?>"><?php echo $current_user->user_email; ?></a></li>
				<li><i class="fa fa-twitter-square" aria-hidden="true"></i><?php if ( $current_user->twitter_id ) { ?><a href="https://twitter.com/<?php echo urlencode( $current_user->twitter_id ); ?>" target="_blank"><?php _e( 'Twitter', APP_TD ); ?></a><?php } else { _e( 'N/A', APP_TD ); } ?></li>
				<li><i class="fa fa-facebook-f" aria-hidden="true"></i><?php if ( $current_user->facebook_id ) { ?><a href="<?php echo appthemes_make_fb_profile_url( $current_user->facebook_id ); ?>" target="_blank"><?php _e( 'Facebook', APP_TD ); ?></a><?php } else { _e( 'N/A', APP_TD ); } ?></li>
				<li><i class="fa fa-globe" aria-hidden="true"></i><?php if ( $current_user->user_url ) { ?><a href="<?php echo $current_user->user_url; ?>" target="_blank"><?php echo esc_url( $current_user->user_url ); ?></a><?php } else { _e( 'N/A', APP_TD ); } ?></li>
			</ul>

		</div> <!-- .sidebox-main -->

	</aside>


	<aside id="user-stats" class="widget widget_user_options">

		<div class="sidebox-main">

			<div class="sidebox-heading">
				<h2><?php _e( 'Account Stats', APP_TD ); ?></h2>
			</div>

			<ul class="user-stats">

				<?php
				// calculate the total count of live coupons for current user
				$post_count_live = $wpdb->get_var( $wpdb->prepare( "SELECT count(ID) FROM $wpdb->posts WHERE post_author = %d AND post_type = %s AND post_status IN ('publish', 'unreliable')", $current_user->ID, APP_POST_TYPE ) );
				$post_count_pending = $wpdb->get_var( $wpdb->prepare( "SELECT count(ID) FROM $wpdb->posts WHERE post_author = %d AND post_type = %s AND post_status = 'pending'", $current_user->ID, APP_POST_TYPE ) );
				$post_count_offline = $wpdb->get_var( $wpdb->prepare( "SELECT count(ID) FROM $wpdb->posts WHERE post_author = %d AND post_type = %s AND post_status = 'draft'", $current_user->ID, APP_POST_TYPE ) );
				$post_count_expired = $wpdb->get_var( $wpdb->prepare( "SELECT count(ID) FROM $wpdb->posts WHERE post_author = %d AND post_type = %s AND post_status = 'expired'", $current_user->ID, APP_POST_TYPE ) );
				$post_count_total = $post_count_live + $post_count_pending + $post_count_offline + $post_count_expired;
				?>
				<span><?php _ex( 'Listings', 'user stats', APP_TD ); ?></span>

				<li class="couponLive"><?php _ex( 'Live', 'user stats', APP_TD ); ?> <span><?php echo $post_count_live; ?></span></li>
				<li class="couponPending"><?php _ex( 'Pending', 'user stats', APP_TD ); ?> <span><?php echo $post_count_pending; ?></span></li>
				<li class="couponExpired"><?php _ex( 'Expired', 'user stats', APP_TD ); ?> <span><?php echo $post_count_expired; ?></span></li>
				<li class="couponOffline"><?php _ex( 'Draft', 'user stats', APP_TD ); ?> <span><?php echo $post_count_offline; ?></span></li>
				<li class="couponTotal"><?php _ex( 'Total', 'user stats', APP_TD ); ?> <span><?php echo $post_count_total; ?></span></li>

			</ul>

		</div> <!-- .sidebox-main -->

	</aside>

	<?php dynamic_sidebar( 'sidebar_user' ); ?>

	<?php appthemes_after_sidebar_widgets( 'user' ); ?>

</div> <!-- #sidebar -->
