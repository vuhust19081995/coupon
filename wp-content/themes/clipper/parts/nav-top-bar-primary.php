<?php
/**
 * The template for displaying the top navigation
 *
 * @package Clipper
 * @since 2.0.0
 */
?>

<nav id="top-bar-primary" class="top-bar">

	<div class="row columns">

			<div class="top-bar-right">

				<ul class="soc-container">

					<li><a class="rss" href="<?php echo appthemes_get_feed_url(); ?>" rel="nofollow" target="_blank" title="<?php _e( 'RSS', APP_TD ); ?>"><i class="fa fa-rss-square" aria-hidden="true"></i></a></li>

					<?php if ( ! empty( $clpr_options->facebook_id ) ) { ?>
						<li><a class="facebook" href="<?php echo appthemes_make_fb_profile_url( $clpr_options->facebook_id ); ?>" rel="nofollow" target="_blank" title="<?php _e( 'Facebook', APP_TD ); ?>"><i class="fa fa-facebook-official" aria-hidden="true"></i></a></li>
					<?php } ?>

					<?php if ( ! empty( $clpr_options->twitter_id ) ) { ?>
						<li><a class="twitter" href="https://twitter.com/<?php echo stripslashes( $clpr_options->twitter_id ); ?>" rel="nofollow" target="_blank" title="><?php _e( 'Twitter', APP_TD ); ?>"><i class="fa fa-twitter-square" aria-hidden="true"></i></a></li>
					<?php } ?>

				</ul>

				<ul class="add-nav menu align-right simple">

					<?php clpr_login_head(); ?>

				</ul>

			</div><!-- .top-bar-right -->

			<?php
			wp_nav_menu( array(
				'menu_class'      => 'inline-list menu-primary menu medium-horizontal vertical',
				'container_class' => 'top-bar-left',
				'theme_location'  => 'primary',
				'items_wrap'      => '<ul id="%1$s" class="%2$s" data-responsive-menu="accordion medium-dropdown" data-close-on-click-inside="false">%3$s</ul>',
				'fallback_cb'     => false,
				'walker'          => new CLPR_Topbar_Menu_Walker(),
			) );
			?>
	</div><!-- .row -->

</nav>
