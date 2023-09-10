<?php
/**
 * Archive template.
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
			<div class="content-box">

				<div class="box-holder">

					<div class="head no-bg">

						<?php clpr_the_archive_title( '<h1 class="archive">', '</h1>' ); ?>
						<?php the_archive_description( '<div class="taxonomy-description">', '</div>' ); ?>

						<?php
						/**
						 * Fires in the archive page header.
						 *
						 * @since 2.0.0
						 */
						do_action( 'clpr_archive_template_header' );
						?>

					</div> <!-- .head -->

				</div> <!-- .box-holder -->

			</div> <!-- .content-box -->
			<?php endif; ?>

			<?php if ( have_posts() ) : ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php get_template_part( 'parts/content', get_post_type() ); ?>

				<?php endwhile; ?>

			<?php else : ?>

				<?php get_template_part( 'parts/content', 'none' ); ?>

			<?php endif; ?>

		</main>

	</div> <!-- #primary -->

	<?php get_sidebar( 'blog' ); ?>

</div> <!-- .row -->
