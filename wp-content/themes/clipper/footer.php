<?php
/**
 * Generic Footer template.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 1.0.0
 */
?>

<footer id="footer" class="site-footer" role="contentinfo">

	<div class="panel">

		<div class="row column panel-holder">

			<?php get_sidebar( 'footer' ); ?>

	</div> <!-- .row -->

	</div> <!-- panel -->

	<div class="bar">

		<div class="row bar-holder">

			<div class="medium-6 columns">

				<div class="copyright">
				<?php if ( get_theme_mod( 'footer_copyright_text' ) ) : ?>
					<?php echo get_theme_mod( 'footer_copyright_text' ); ?>
				<?php else : ?>
					<a href="https://www.appthemes.com/themes/clipper/" target="_blank" rel="nofollow">Clipper Theme</a> | <?php _e( 'Powered by', APP_TD ); ?> <a href="https://wordpress.org/" target="_blank" rel="nofollow">WordPress</a>
				<?php endif; ?>
				</div> <!-- .copyright -->

			</div> <!-- .column -->

			<div class="medium-6 columns">

				<?php
				wp_nav_menu( array(
					'theme_location' => 'secondary',
					'container'      => false,
					'menu_class'     => 'inline-list',
					'fallback_cb'    => false,
					'depth'          => 1,
				) );
				?>

			</div> <!-- .column -->

		</div> <!-- .row -->

	</div> <!-- .bar -->

</div> <!-- #footer -->
