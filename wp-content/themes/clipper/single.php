<?php
/**
 * The template for displaying all single posts.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 1.0.0
 */
?>

<div class="content-area row">

	<div id="primary" class="medium-8 columns">

		<main id="main" class="site-main" role="main">

			<?php appthemes_before_blog_loop(); ?>

			<?php while ( have_posts() ) : the_post(); ?>

				<?php appthemes_before_blog_post(); ?>

				<?php get_template_part( 'parts/content', get_post_type() ); ?>

				<?php appthemes_advertise_content(); ?>

				<?php get_template_part( 'parts/content', 'comments' ); ?>

				<?php appthemes_after_blog_post(); ?>

			<?php endwhile; ?>

			<?php appthemes_after_blog_endwhile(); ?>

		</main>

	</div> <!-- #primary -->

	<?php get_sidebar( 'blog' ); ?>

</div> <!-- .row -->
