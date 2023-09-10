<?php
/**
 * Template Name: Stores Template
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 1.1.0
 */
?>

<div class="content-area row">

	<div id="primary" class="large-12 columns">

		<main id="main" class="site-main" role="main">

			<?php
			if ( have_posts() ) :

				while ( have_posts() ) : the_post();
			?>

				<div class="content-box">

					<div class="box-holder">

						<div class="head">

							<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

						</div><!-- .head -->

						<div class="blog">

							<?php the_content(); ?>

							<div class="text-box">

								<?php echo clpr_stores_list(); ?>

							</div>

						</div> <!-- .blog -->

					</div> <!-- .box-holder -->

				</div> <!-- .content-box -->

			<?php
				endwhile;

			endif;
			?>

		</main>

	</div> <!-- #primary -->

</div> <!-- .row -->
