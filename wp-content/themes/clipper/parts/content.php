<?php
/**
 * Post loop content template.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 1.6.0
 */

// Records the page hit on single blog page view.
// @todo Use a hook instead of hardcoded here.
if ( is_single() ) {
	appthemes_stats_update( $post->ID );
}
?>

<div id="post-<?php the_ID(); ?>" <?php post_class( 'content-box' ); ?>>

	<div class="box-holder">

		<div class="head">

			<?php appthemes_before_blog_post_title(); ?>

			<?php
			if ( is_single() ) {
				the_title( '<h1 class="entry-title">', '</h1>' );
			} else {
				the_title( '<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h1>' );
			}
			?>

			<?php appthemes_after_blog_post_title(); ?>

		</div> <!-- .head -->

		<div class="blog">

			<?php clpr_blog_post_meta(); ?>

			<div class="text-box">

				<?php
				if ( is_single() ) {
					the_post_thumbnail( 'full', array( 'class' => 'featured-image' ) );
				}
				?>

				<?php the_content( __( 'Continue reading &raquo;', APP_TD ) ); ?>

				<?php
				wp_link_pages( array(
					'before'      => '<nav class="page-links"><span class="page-links-title">' . __( 'Pages:', APP_TD ) . '</span>',
					'after'       => '</nav>',
					'link_before' => '<span>',
					'link_after'  => '</span>',
					'pagelink'    => '<span class="screen-reader-text">' . __( 'Page', APP_TD ) . ' </span>%',
					'separator'   => '<span class="screen-reader-text">, </span>',
				) );
				?>

				<?php edit_post_link( __( 'Edit Post', APP_TD ), '<span class="edit-link">', '</span>' ); ?>

			</div> <!-- .text-box -->

			<?php appthemes_after_blog_post_content(); ?>

		</div> <!-- #blog -->

	</div> <!-- #box-holder -->

</div> <!-- #content-box -->
