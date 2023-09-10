<?php
/**
 * Template Name: Full Width Page
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 1.0.0
 */
?>

<div class="content-area row">

	<div id="primary" class="medium-12 columns">

		<main id="main" class="site-main" role="main">

			<?php appthemes_before_page_loop(); ?>

			<?php if ( have_posts() ) : ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php appthemes_before_page(); ?>

					<?php get_template_part( 'parts/content', 'page' ); ?>

					<?php appthemes_after_page(); ?>

				<?php endwhile; ?>

				<?php appthemes_after_page_endwhile(); ?>

			<?php else: ?>

				<?php appthemes_page_loop_else(); ?>

			<?php endif; ?>

			<?php appthemes_after_page_loop(); ?>

			<?php get_template_part( 'parts/content', 'comments' ); ?>

		</main>

	</div> <!-- #primary -->

</div> <!-- .row -->
