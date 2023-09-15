<?php
/**
 * Coupon Type Taxonomy Template.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 1.2.4
 */

$term = get_queried_object();
?>

<div class="content-area row">

	<div id="primary" class="medium-8 columns">

		<main id="main" class="site-main" role="main">

			<div class="content-box">

				<div class="box-holder">

					<div class="head no-bg">

						<a class="rss-link" href="<?php echo get_term_feed_link( $term->term_id, $taxonomy ); ?>" rel="nofollow" target="_blank" title="<?php esc_attr_e( 'Coupon Type RSS', APP_TD ); ?>"><i class="fa fa-rss-square" aria-hidden="true"></i></a>
						<h1 class="archive"><?php printf( __( 'Coupon Type: %s', APP_TD ), $term->name ); ?></h1>
						<div class="taxonomy-description"><?php echo term_description(); ?></div>

					</div> <!-- .head -->

				</div> <!-- #box-holder -->

			</div> <!-- #content-box -->

			<div class="content-box">

				<div class="box-holder">

					<div class="head">

						<h2><?php _e( 'Active Coupons', APP_TD ); ?></h2>

						<?php
							// show all active coupons for this type and setup pagination
							$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
							query_posts( array(
								'post_type'           => APP_POST_TYPE,
								'post_status'         => 'publish',
								APP_TAX_TYPE          => $term->slug,
								'ignore_sticky_posts' => 1,
								'paged'               => $paged,
							) );
						?>

						<!-- <div class="counter">
							<?php printf( __( 'Total: %s', APP_TD ), html( 'span', number_format_i18n( $wp_query->found_posts ) ) ); ?>
						</div> -->

					</div> <!-- #head -->

					<?php get_template_part( 'loop', 'coupon' ); ?>

				</div> <!-- #box-holder -->

			</div> <!-- #content-box -->


			<!-- <div class="content-box">

				<div class="box-holder">

					<div class="head">

						<h2><?php //_e( 'Unreliable Coupons', APP_TD ); ?></h2>

						<?php
							// show all unreliable coupons for this type and setup pagination
							// $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
							// $unreliable = array( 'unreliable' );

							// if ( ! $clpr_options->prune_coupons ) {
							// 	$unreliable[] = APP_POST_STATUS_EXPIRED;
							// }

							// query_posts( array(
							// 	'post_type'           => APP_POST_TYPE,
							// 	'post_status'         => $unreliable,
							// 	APP_TAX_TYPE          => $term->slug,
							// 	'ignore_sticky_posts' => 1,
							// 	'paged'               => $paged,
							// ) );
						?>

						<div class="counter-red">
							<?php //printf( __( 'Total: %s', APP_TD ), html( 'span', number_format_i18n( $wp_query->found_posts ) ) ); ?>
						</div>
					</div>

					<?php get_template_part( 'loop', 'coupon' ); ?>

				</div>

			</div> -->

		</main>

	</div> <!-- #primary -->

	<?php wp_reset_query(); ?>

	<?php get_sidebar( 'coupon' ); ?>

</div> <!-- .row -->
