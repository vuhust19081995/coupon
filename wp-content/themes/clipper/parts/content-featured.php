<?php
/**
 * Featured coupons slider template.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 1.0.0
 * @todo    Deprecate since moved to slick slider in 2.0.0
 */

global $clpr_options;

if ( ! $clpr_options->featured_slider ) {
	return;
}
?>

<div class="row">

	<div class="large-12 columns">

		<?php appthemes_before_loop( 'slider' ); ?>

		<?php if ( $featured = clpr_get_featured_slider_coupons() ) : ?>

			<div class="featured-slider">

				<div class="gallery-c">

					<div class="gallery-holder">

						<div class="prev"></div>

						<div class="slide">

							<div class="slide-contain">

								<ul class="slider">

									<?php while ( $featured->have_posts() ) : $featured->the_post(); ?>

										<?php appthemes_before_post( 'slider' ); ?>

										<li>

											<div class="image">
												<a href="<?php the_permalink(); ?>"><img src="<?php echo clpr_get_store_image_url( $post->ID, 'post_id', 160 ); ?>" alt="" /></a>
											</div>

											<?php appthemes_before_post_title( 'slider' ); ?>

											<span><?php clpr_coupon_title(); ?></span>

											<?php appthemes_after_post_title( 'slider' ); ?>

										</li>

										<?php appthemes_after_post( 'slider' ); ?>

									<?php endwhile; ?>

									<?php appthemes_after_endwhile( 'slider' ); ?>

								</ul>

							</div>

						</div>

						<div class="next"></div>

					</div>

				</div>

			</div>

		<?php endif; ?>

		<?php appthemes_after_loop( 'slider' ); ?>

		<?php wp_reset_postdata(); ?>

	</div> <!-- .columns -->

</div> <!-- .row -->
