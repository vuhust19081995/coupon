<?php
/**
 * Featured coupons slider template.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 2.0.0
 */

global $clpr_options;

if ( ! $clpr_options->featured_slider ) {
	return;
}
?>

<div class="row">

	<div class="large-12 columns">

		<?php if ( $featured = clpr_get_featured_slider_coupons() ) : ?>

				<div class="slick-slider app-slick-slider">

					<?php while ( $featured->have_posts() ) : $featured->the_post(); ?>

							<div class="slide">

								<?php if ( $clpr_options->link_single_page ) { ?>
									<a href="<?php the_permalink(); ?>">
										<div class="slide-cover" style="background-image: url(<?php echo esc_attr( clpr_get_store_image_url( $post->ID, 'post_id', 160 ) ); ?>);"></div>
									</a>
								<?php } else { ?>
									<div class="slide-cover" style="background-image: url(<?php echo esc_attr( clpr_get_store_image_url( $post->ID, 'post_id', 160 ) ); ?>);"></div>
								<?php } ?>

								<div class="slide-link">

									<?php clpr_coupon_title(); ?>

								</div> <!-- .slide-link -->

							</div> <!-- .slide -->

					<?php endwhile; ?>

				</div> <!-- .slick-slider -->

		<?php endif; ?>

		<?php wp_reset_postdata(); ?>

	</div> <!-- .columns -->

</div> <!-- .row -->
