<?php
/**
 * Template Name: Blog Template
 *
 * The blog posts main page and fallback catch all template.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 *
 * @link http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 1.0.0
 */
?>

<div class="content-area row">

	<div id="primary" class="medium-8 columns">

		<main id="main" class="site-main" role="main">

			<?php if ( have_posts() ) : ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'parts/content', get_post_type() ); ?>

				<?php endwhile; ?>

				<?php clpr_blog_pagination(); ?>

			<?php else: ?>

				<?php get_template_part( 'parts/content', 'none' ); ?>

			<?php endif; ?>

		</main>

	</div> <!-- #primary -->

	<?php get_sidebar( 'blog' ); ?>

</div> <!-- .row -->
