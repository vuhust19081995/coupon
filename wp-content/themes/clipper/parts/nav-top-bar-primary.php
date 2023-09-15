<?php
/**
 * The template for displaying the top navigation
 *
 * @package Clipper
 * @since 2.0.0
 */
?>

<nav id="top-bar-primary" class="top-bar">

	<div class="row columns" style="display: flex;">

			<div class="top-bar-right">
				<ul class="add-nav menu align-center simple">
					<?php
						if ( get_header_image() ) {
							?>
							<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
								<img src="<?php header_image(); ?>" class="header-logo" width="<?php echo get_custom_header()->width; ?>" height="<?php echo get_custom_header()->height; ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" />
							</a>
							<?php
						}
					?>
					<!-- <?php clpr_login_head(); ?> -->

				</ul>

			</div><!-- .top-bar-right -->

			<?php
			wp_nav_menu( array(
				'menu_class'      => 'inline-list menu-primary menu medium-horizontal vertical align-center',
				'container_class' => 'top-bar-left',
				'theme_location'  => 'primary',
				'items_wrap'      => '<ul id="%1$s" class="%2$s" data-responsive-menu="accordion medium-dropdown" data-close-on-click-inside="false">%3$s</ul>',
				'fallback_cb'     => false,
				'walker'          => new CLPR_Topbar_Menu_Walker(),
			) );
			?>
			<div class="soc-container">
				<?php if ( ! empty( $clpr_options->facebook_id ) ) { ?>
					<li><a class="facebook" href="<?php echo appthemes_make_fb_profile_url( $clpr_options->facebook_id ); ?>" rel="nofollow" target="_blank" title="<?php _e( 'Facebook', APP_TD ); ?>"><i class="fa fa-facebook-square" aria-hidden="true"></i></a></li>
				<?php } ?>

				<?php if ( ! empty( $clpr_options->twitter_id ) ) { ?>
					<li><a class="twitter" href="https://twitter.com/<?php echo stripslashes( $clpr_options->twitter_id ); ?>" rel="nofollow" target="_blank" title="><?php _e( 'Twitter', APP_TD ); ?>"><i class="fa fa-twitter-square" aria-hidden="true"></i></a></li>
				<?php } ?>
			</div>
	</div><!-- .row -->

</nav>
