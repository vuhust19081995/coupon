<?php
/**
 * Template Name: Register
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   1.3.2
 * @since   2.0.0 Switched to theme framework include form. Added the_content() & the_title().
 */
?>

<div class="content-area row">

	<div id="primary" class="medium-8 large-4 medium-centered columns">

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

						<div class="content-auth">

							<?php the_content(); ?>

						</div><!-- .content-auth -->

						<div class="post-box">

							<?php do_action( 'appthemes_notices' ); ?>

							<?php if ( get_option( 'users_can_register' ) ) : ?>

								<?php require APP_THEME_FRAMEWORK_DIR . '/templates/foundation/form-registration.php'; ?>

							<?php else : ?>

								<p><?php _e( 'User registration has been disabled.', APP_TD ); ?></p>

							<?php endif; ?>

						</div> <!-- .post-box -->

					</div><!--.box-holder -->

				</div><!--.content-box -->

			<?php
				endwhile;

			endif;
			?>

		</main>

	</div> <!-- #primary -->

</div> <!-- .row -->
