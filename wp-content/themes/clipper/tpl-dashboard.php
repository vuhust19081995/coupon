<?php
/**
 * Template Name: User Dashboard
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 1.0.0
 */

global $i;
?>

<div class="content-area row">

	<div id="primary" class="medium-8 columns">

		<main id="main" class="site-main" role="main">

			<?php
			if ( have_posts() ) :

				while ( have_posts() ) : the_post();
			?>

				<div class="content-box">

					<div class="box-holder">

						<div class="blog">

							<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

							<div class="text-box">

								<?php do_action( 'appthemes_notices' ); ?>

								<?php if ( $listings = clpr_get_user_dashboard_listings() ) : ?>

									<?php
										// build the row counter depending on what page we're on
										$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
										$posts_per_page = $listings->get( 'posts_per_page' );
										$i = ( $paged != 1 ) ? ( $paged * $posts_per_page - $posts_per_page ) : 0;
									?>

									<?php the_content(); ?>

									<table class="couponList hover stack">

										<thead>
											<tr>
												<th class="listing-count" data-class="expand">&nbsp;</th>
												<th class="listing-title"><?php _e( 'Title', APP_TD ); ?></th>
												<?php if ( current_theme_supports( 'app-stats' ) ) { ?>
													<th class="listing-views" data-hide="phone"><?php _e( 'Views', APP_TD ); ?></th>
												<?php } ?>
												<th class="listing-status" data-hide="phone"><?php _e( 'Status', APP_TD ); ?></th>
												<th class="listing-options" data-hide="phone"><?php _e( 'Options', APP_TD ); ?></th>
											</tr>
										</thead>

										<tbody>

										<?php while ( $listings->have_posts() ) : $listings->the_post(); $i++; ?>

											<?php get_template_part( 'parts/content', 'dashboard' ); ?>

										<?php endwhile; ?>

										</tbody>

									</table>

									<?php appthemes_pagination( '', '', $listings ); ?>

								<?php else : ?>

									<div class="pad10"></div>
										<p class="text-center"><?php _e( 'You currently have no coupons.', APP_TD ); ?></p>
									<div class="pad10"></div>

								<?php endif; ?>

								<?php wp_reset_postdata(); ?>

							</div> <!-- /text-box -->

						</div> <!-- /blog -->

					</div> <!-- /box-holder -->

				</div> <!-- #content-box -->

			<?php
				endwhile;

			endif;
			?>

		</main>

	</div> <!-- #primary -->

	<?php get_sidebar( 'user' ); ?>

</div> <!-- .row -->
