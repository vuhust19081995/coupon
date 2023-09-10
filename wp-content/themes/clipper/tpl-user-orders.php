<?php
/**
 * Template Name: User Orders
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 1.5.0
 */

$current_user = wp_get_current_user();
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

						<div class="head">

							<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

						</div><!-- .head -->

						<div class="blog">

							<?php the_content(); ?>

							<div class="text-box">

								<?php do_action( 'appthemes_notices' ); ?>

								<?php if ( $orders = clpr_get_user_dashboard_orders() ) { ?>

									<?php
										// build the row counter depending on what page we're on
										$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
										$posts_per_page = $orders->get( 'posts_per_page' );
										$i = ( $paged != 1 ) ? ( $paged * $posts_per_page - $posts_per_page ) : 0;
									?>

									<div class="orders-legend">
									<?php
										echo html( 'h4', __( 'Statuses Legend:', APP_TD ) );
										echo html( 'span', html( 'strong', __( 'Pending:', APP_TD ) ) . ' ' . __( 'Order not processed.', APP_TD ) );
										echo html( 'span', html( 'strong', __( 'Failed:', APP_TD ) ) . ' ' . __( 'Order failed or manually canceled.', APP_TD ) );
										echo html( 'span', html( 'strong', __( 'Completed:', APP_TD ) ) . ' ' . __( 'Order processed succesfully but pending moderation before activation.', APP_TD ) );
										echo html( 'span', html( 'strong', __( 'Activated:', APP_TD ) ) . ' ' . __( 'Order processed succesfully and activated.', APP_TD ) );
									?>
									</div>

									<table class="couponList hover stack">

										<thead>
											<tr>
												<th class="order-id" data-class="expand"><?php _e( 'ID', APP_TD ); ?></th>
												<th class="order-summary"><?php _e( 'Order Summary', APP_TD ); ?></th>
												<th class="order-price" data-hide="phone"><?php _e( 'Price', APP_TD ); ?></th>
												<th class="order-status" data-hide="phone"><?php _e( 'Payment/Status', APP_TD ); ?></th>
												<th class="order-options" data-hide="phone"><?php _e( 'Options', APP_TD ); ?></th>
											</tr>
										</thead>

										<tbody>

										<?php while ( $orders->have_posts() ) : $orders->the_post(); $i++; ?>

											<?php get_template_part( 'parts/content-dashboard', 'order' ); ?>

										<?php endwhile; ?>

										</tbody>

									</table>

									<?php appthemes_pagination( '', '', $orders ); ?>

								<?php } else { ?>

									<div class="pad10"></div>
										<p class="text-center"><?php _e( 'You currently have no orders.', APP_TD ); ?></p>
									<div class="pad10"></div>

								<?php } ?>

								<?php wp_reset_postdata(); ?>

							</div> <!-- .text-box -->

						</div> <!-- .blog -->

					</div> <!-- .box-holder -->

				</div> <!-- .content-box -->

			<?php
				endwhile;

			endif;
			?>

		</main>

	</div> <!-- #primary -->

	<?php get_sidebar( 'user' ); ?>

</div> <!-- .row -->
