<?php
/**
 * Catch-all template for displaying listing process if no other process-*.php
 * overrides template exists.
 *
 * @uses step-*.php
 *
 * @package Clipper\Templates
 * @author AppThemes
 * @since 2.0.0
 */

?>
<div class="content-area row">

	<div id="primary" class="medium-8 columns">

		<main id="main" class="site-main" role="main">

			<?php appthemes_before_loop( 'process' ); ?>

			<?php if ( have_posts() ) : ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php appthemes_before_post( 'process' ); ?>

					<div class="content-box">

						<div class="box-holder">

							<div class="head">

								<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

							</div> <!-- .head -->

							<div class="blog">

								<?php the_content(); ?>

								<?php do_action( 'appthemes_notices' ); ?>

							</div> <!-- .blog -->

							<div class="post-box">

								<?php appthemes_display_checkout(); ?>

							</div> <!-- .post-box -->

						</div> <!-- .box-holder -->

					</div> <!-- .content-box -->

					<?php appthemes_after_post( 'process' ); ?>

				<?php endwhile; ?>

			<?php endif; ?>

			<?php appthemes_after_loop( 'process' ); ?>

		</main>

	</div> <!-- #primary -->

	<?php get_sidebar( 'user' ); ?>

</div> <!-- .row -->
