<?php
/**
 * Attachments template.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 1.2.4
 */
?>

<div class="content-area row">

	<div id="primary" class="medium-8 columns">

		<main id="main" class="site-main" role="main">

			<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

				<div <?php post_class( 'content-box' ); ?> id="post-<?php the_ID(); ?>">

					<div class="box-holder">

						<div class="blog">

							<h1 class="entry-title"><?php the_title(); ?></h1>

							<div class="content-bar">

								<?php clpr_attachment_entry_meta(); ?>

							</div>

							<div class="text-box entry-attachment">

								<?php
									$attachment_width  = apply_filters( 'appthemes_attachment_size', 597 );
									$attachment_height = apply_filters( 'appthemes_attachment_height', 597 );
									$attachment_size = array( $attachment_width, $attachment_height );
								?>

								<a href="<?php echo wp_get_attachment_url(); ?>" title="<?php the_title_attribute(); ?>" rel="attachment"><?php echo wp_get_attachment_image( get_the_ID(), $attachment_size, true ); ?></a>

								<?php if ( wp_attachment_is_image() ) : ?>
									<div id="nav-below" class="navigation">
										<div class="next-prev"><?php previous_image_link( false, __( '&larr; prev', APP_TD ) ); ?>&nbsp;&nbsp;&nbsp;<?php next_image_link( false, __( 'next &rarr;', APP_TD ) ); ?></div>
									</div><!-- /nav-below -->
								<?php endif; ?>

								<?php if ( has_excerpt() ) : ?>
									<div class="entry-caption">
										<?php the_excerpt(); ?>
									</div><!-- .entry-caption -->
								<?php endif; ?>

								<div class="entry-content">
									<?php the_content(); ?>
								</div><!-- .entry-content -->

							</div>

							<?php if ( ! empty( $post->post_parent ) ) : ?>
								<div class="text-footer">

									<p class="page-title">
										<a href="<?php echo get_permalink( $post->post_parent ); ?>" title="<?php esc_attr( printf( __( 'Return to %s', APP_TD ), get_the_title( $post->post_parent ) ) ); ?>" rel="gallery">
											<?php printf( '<span class="meta-nav">' . __( '&larr; Return to %s', APP_TD ) . '</span>', get_the_title( $post->post_parent ) ); ?>
										</a>
									</p>

								</div>
							<?php endif; ?>

						</div>

					</div> <!-- #box-holder -->

				</div> <!-- #content-box -->

			<?php endwhile; ?>

		</main>

	</div> <!-- #primary -->

	<?php get_sidebar( 'coupon' ); ?>

</div> <!-- .row -->
