<?php
/**
 * Template for displaying single order.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   2.0.0
 */

// @codeCoverageIgnoreStart
?>
<div class="content-area row">

	<div id="primary" class="medium-8 columns">

		<main id="main" class="site-main" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

			<div class="content-box">

				<div class="box-holder">

					<div class="head">

						<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

					</div> <!-- .head -->

					<div class="blog">

						<?php do_action( 'appthemes_notices' ); ?>

					</div> <!-- .blog -->

					<div class="post-box">

						<?php appthemes_get_template_part( 'parts/content-transaction', $app_order_content ); ?>

					</div> <!-- .post-box -->

				</div> <!-- .box-holder -->

			</div> <!-- .content-box -->

			<?php endwhile; ?>

		</main>

	</div> <!-- #primary -->

	<?php get_sidebar( 'user' ); ?>

</div> <!-- .row -->
