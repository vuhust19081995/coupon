<?php
/**
 * Main loop for displaying coupons.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 1.0
 */
?>

<?php appthemes_before_loop(); ?>

<?php if ( have_posts() ) : ?>

	<?php while ( have_posts() ) : the_post(); ?>

		<?php appthemes_before_post(); ?>

		<?php get_template_part( 'parts/content', 'coupon-item' ); ?>

		<?php appthemes_after_post(); ?>

	<?php endwhile; ?>

	<?php appthemes_after_endwhile(); ?>

<?php else: ?>

	<?php appthemes_loop_else(); ?>

	<div class="blog">

		<h3><?php _e( 'Sorry, no coupons found', APP_TD ); ?></h3>

	</div> <!-- #blog -->

<?php endif; ?>

<?php appthemes_after_loop(); ?>
