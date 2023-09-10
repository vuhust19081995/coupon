<?php
/**
 * Page loop content template.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 1.6
 */
?>

<div class="content-box">

	<div class="box-holder">

		<div class="blog">

			<?php appthemes_before_page_title(); ?>

			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

			<?php appthemes_after_page_title(); ?>

			<?php the_post_thumbnail( 'full' ); ?>

			<?php appthemes_before_page_content(); ?>

			<div class="text-box">

				<?php the_content(); ?>

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

				<?php edit_post_link( __( 'Edit Page', APP_TD ), '<span class="edit-link">', '</span>' ); ?>

			</div>

			<?php appthemes_after_page_content(); ?>

		</div> <!-- #blog -->

	</div> <!-- #box-holder -->

</div> <!-- #content-box -->
