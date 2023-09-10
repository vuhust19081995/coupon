<?php
/**
 * The Template for displaying all single posts.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 2.0.0
 */
?>

<div class="content-area row">

	<div id="primary" class="large-12 columns">

		<main id="main" class="site-main" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'parts/content', 'image' ); ?>

				<?php get_template_part( 'parts/content', 'comments' ); ?>

			<?php endwhile; ?>

		</main>

	</div> <!-- #primary -->

</div> <!-- .row -->
