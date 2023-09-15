<?php
/**
 * Template Name: Coupons Home Template
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 1.5.0
 */

$post_status = ( $clpr_options->exclude_unreliable ) ? array( 'publish' ) : array( 'publish', 'unreliable' );
if ( ! $clpr_options->prune_coupons && ! $clpr_options->exclude_unreliable ) {
	$post_status[] = APP_POST_STATUS_EXPIRED;
}
$posts_count = appthemes_count_posts( APP_POST_TYPE, $post_status );
?>

<?php get_template_part( 'parts/content', 'featured-slick' ); ?>

<div class="content-area row">

	<div id="primary" class="medium-8 columns">

		<main id="main" class="site-main" role="main">

			<div class="content-box">

				<div class="box-holder">

					<div class="head">

						<h2><?php _e( 'New Coupons', APP_TD ); ?></h2>

						<!-- <div class="counter"> -->
							<!-- <?php printf( __( 'Total: %s', APP_TD ), html( 'span', number_format_i18n( $posts_count ) ) ); ?> -->
						<!-- </div> .counter -->

					</div> <!-- #head -->

					<?php
					// show all coupons and setup pagination
					$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

					query_posts( array(
						'post_type'           => APP_POST_TYPE,
						'post_status'         => $post_status,
						'ignore_sticky_posts' => 1,
						'paged'               => $paged,
					) );
					?>

					<?php get_template_part( 'loop', 'coupon' ); ?>

				</div> <!-- #box-holder -->

			</div> <!-- #content-box -->

		</main>

	</div> <!-- #primary -->

	<?php get_sidebar( 'home' ); ?>

</div> <!-- .row -->
