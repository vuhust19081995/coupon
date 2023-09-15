<?php
/**
 * Coupon store taxonomy template.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 1.0.0
 */

$term       = get_queried_object();
$stores_url = esc_url( clpr_get_store_meta( $term->term_id, 'clpr_store_url', true ) );
$url_out    = clpr_get_store_out_url( $term );
?>

<div class="content-area row">

	<div id="primary" class="medium-8 columns">

		<main id="main" class="site-main" role="main">

			<div class="content-box">

				<div class="box-holder">

					<div class="head no-bg store">

						<div class="row">

							<div class="medium-3 columns">

								<div class="thumb-wrap">
									<a href="<?php echo esc_url( $url_out ); ?>" rel="nofollow"><img class="store-thumb" src="<?php echo clpr_get_store_image_url( $term->term_id, 'term_id', 768 ); ?>" alt="<?php esc_attr( printf( __( '%s screenshot', APP_TD ), $term->name ) ); ?>" /></a>
								</div>

							</div> <!-- .columns -->

							<div class="medium-9 columns">

								<div class="info">
									<!-- <a class="rss-link" href="<?php //echo get_term_feed_link( $term->term_id, $taxonomy ); ?>" rel="nofollow" target="_blank" title="<?php //esc_attr_e( 'Store RSS', APP_TD ); ?>"><i class="fa fa-rss-square" aria-hidden="true"></i></a> -->
									<h1><?php echo $term->name; ?></h1>
									<div class="store-description"><?php echo apply_filters( 'the_content', term_description(), $term->term_id, $taxonomy ); ?></div>
									<p class="store-url"><a href="<?php echo esc_url( $url_out ); ?>" target="_blank"><?php echo $stores_url; ?></a></p>
								</div> <!-- #info -->

							</div> <!-- .columns -->

						</div> <!-- .row -->

						<div class="row">

							<div class="small-12 columns">

								<div class="adsense">
									<?php appthemes_advertise_content(); ?>
								</div> <!-- #adsense -->

							</div> <!-- .columns -->

						</div> <!-- .row -->

					</div> <!-- .head -->

				</div> <!-- #box-holder -->

			</div> <!-- #content-box -->


			<div class="content-box">

				<div class="box-holder">

					<div class="head">

						<h2><?php _e( 'Active Coupons', APP_TD ); ?></h2>

						<?php
							// show all active coupons for this store and setup pagination
							$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
							query_posts( array(
								'post_type'           => APP_POST_TYPE,
								'post_status'         => 'publish',
								APP_TAX_STORE         => $term->slug,
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
							// show all unreliable coupons for this store and setup pagination
							// $paged      = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
							// $unreliable = array( 'unreliable' );

							// if ( ! $clpr_options->prune_coupons ) {
							// 	$unreliable[] = APP_POST_STATUS_EXPIRED;
							// }

							// query_posts( array(
							// 	'post_type'           => APP_POST_TYPE,
							// 	'post_status'         => $unreliable,
							// 	APP_TAX_STORE         => $term->slug,
							// 	'ignore_sticky_posts' => 1,
							// 	'paged'               => $paged,
							// ) );
						?>

						<div class="counter-red">
							<?php // printf( __( 'Total: %s', APP_TD ), html( 'span', number_format_i18n( $wp_query->found_posts ) ) ); ?>
						</div>

					</div>
					<?php //get_template_part( 'loop', 'coupon' ); ?>

				</div>

			</div> -->

		</main>

	</div> <!-- #primary -->

	<?php wp_reset_query(); ?>

	<?php get_sidebar( 'store' ); ?>

</div> <!-- .row -->
