<?php
/**
 * Template Name: User Profile
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 1.0.0
 */
?>

<div class="content-area row">

	<div id="primary" class="medium-8 columns">

		<main id="main" class="site-main" role="main">

			<?php
			if ( have_posts() ) :

				while ( have_posts() ) : the_post();
			?>

				<?php get_template_part( 'parts/content', 'edit-profile' ); ?>

			<?php
				endwhile;

			endif;
			?>

		</main>

	</div> <!-- #primary -->

	<?php get_sidebar( 'user' ); ?>

</div> <!-- .row -->
