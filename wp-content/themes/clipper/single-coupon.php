<?php
/**
 * The single coupon page template file.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 1.0.0
 */
?>

<div class="content-area row">

	<div id="primary" class="medium-8 columns">

		<main id="main" class="site-main" role="main">

			<?php do_action( 'appthemes_notices' ); ?>

			<?php appthemes_before_loop(); ?>

			<?php if ( have_posts() ) : ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php appthemes_stats_update( $post->ID ); //records the page hit ?>

					<?php appthemes_before_post(); ?>

					<?php get_template_part( 'parts/content', 'coupon-single' ); ?>

					<?php appthemes_after_post(); ?>

					<?php comments_template(); ?>

			<?php endwhile; ?>

				<?php appthemes_after_endwhile(); ?>

			<?php else: ?>

				<?php appthemes_loop_else(); ?>

				<div class="blog">

					<h3><?php _e( 'Sorry, no coupons yet.', APP_TD ); ?></h3>

				</div> <!-- #blog -->

			<?php endif; ?>

			<?php appthemes_after_loop(); ?>

		</main>

	</div> <!-- #primary -->

	<?php get_sidebar( 'coupon' ); ?>

</div> <!-- .row -->
