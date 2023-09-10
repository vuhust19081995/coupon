<?php
/**
 * Search results template.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 1.0.0
 */

if ( $clpr_options->search_stats ) {
	appthemes_save_search();
}
?>

<div class="content-area row">

	<div id="primary" class="medium-8 columns">

		<main id="main" class="site-main" role="main">

			<?php appthemes_before_loop( 'search' ); ?>

			<?php if ( have_posts() ) : ?>

				<div class="content-box">

					<div class="box-holder">

						<div class="head">
							<h1 class="entry-title"><?php printf( __( '%d %s for "%s"', APP_TD ), $wp_query->found_posts, _n( 'result', 'results', number_format_i18n( $wp_query->found_posts ), APP_TD ), get_search_query() ); ?></h1>
						</div> <!-- .head -->

						<?php while ( have_posts() ) : the_post(); ?>

							<?php appthemes_before_post( 'search' ); ?>

							<?php
							// If search is for coupons, use a different template.
							if ( isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] == APP_POST_TYPE ) {
								get_template_part( 'parts/content', 'coupon-item' );
							} else {
								get_template_part( 'parts/content', get_post_type() );
							}
							?>

							<?php appthemes_after_post( 'search' ); ?>

						<?php endwhile; ?>

						<?php appthemes_after_endwhile( 'search' ); ?>

					</div> <!-- .box-holder -->

				</div> <!-- .content-box -->

			<?php else: ?>

				<?php appthemes_loop_else( 'search' ); ?>

				<?php get_template_part( 'parts/content', 'none' ); ?>

			<?php endif; ?>

			<?php appthemes_after_loop( 'search' ); ?>

		</main>

	</div> <!-- #primary -->

	<?php
	// If search is for coupons, use a different sidebar.
	if ( isset( $_GET[ 'post_type' ] ) && $_GET[ 'post_type' ] == APP_POST_TYPE ) {
		get_sidebar( 'search' );
	} else {
		get_sidebar( 'blog' );
	}
	?>

</div> <!-- .row -->
