<?php
/**
 * Single coupon custom post loop content template.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 2.0.0
 */
?>

<div <?php post_class( 'content-box' ); ?> id="post-<?php the_ID(); ?>">

	<div class="box-holder">

		<div class="head">

			<?php appthemes_before_post_title(); ?>

			<h1 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h1>

			<?php appthemes_after_post_title(); ?>

		</div> <!-- .head -->

		<div class="blog">

			<div class="content-bar">

				<div class="row">

					<div class="small-6 columns">

						<span class="entry-date">
							<i class="fa fa-calendar" aria-hidden="true"></i><?php echo get_the_date(); ?>
						</span>

						<span class="entry-category">
							<i class="fa fa-folder-open-o" aria-hidden="true"></i><?php echo get_the_term_list( $post->ID, APP_TAX_CAT, '', '<span class="sep">, </span>', '' ); ?>
						</span>

					</div> <!-- .columns -->

					<div class="small-6 columns">

						<span class="comment-count">
							<i class="fa fa-comments-o" aria-hidden="true"></i><?php printf( _nx( '1 Comment', '%1$s Comments', get_comments_number(), 'comments title', APP_TD ), number_format_i18n( get_comments_number() ) ); ?>
						</span>

					</div> <!-- .columns -->

				</div> <!-- .row -->

			</div> <!-- .content-bar -->

			<?php
			// Get the first store taxonomy item.
			$store      = clpr_get_first_term( $post->ID, APP_TAX_STORE );
			$store_link = get_term_link( $store->slug, APP_TAX_STORE );
			?>

			<div class="head-box">

				<div class="row">

					<div class="large-3 medium-6 columns">

						<div class="store-holder">

							<div class="store-image">
								<a href="<?php echo esc_url( clpr_get_first_term_link( $post->ID, APP_TAX_STORE ) ); ?>" aria-hidden="true">
									<div class="item-cover" style="background-image: url(<?php echo esc_url( clpr_get_store_image_url( $post->ID, 'post_id', 768 ) ); ?>);"></div>
								</a>
							</div>

						</div>

					</div> <!-- .columns -->

					<div class="large-9 medium-6 columns">

						<div class="row">

							<div class="large-8 medium-12 columns">

								<div class="item-panel">

									<?php clpr_coupon_code_box(); ?>

									<div class="clear"></div>

									<div class="store-info">
										<?php echo '<a href="' . esc_url( $store_link ) . '">' . $store->name . '</a>'; ?>
									</div>

									<?php
										// Get the expiration date and format it for display.
										if ( clpr_get_expire_date( $post->ID ) ) {
											echo '<span class="coupon-expires">' . __( 'Expires: ', APP_TD ) . '<time class="entry-date expired" datetime="' . clpr_get_expire_date( $post->ID, 'c' ) . '">' . clpr_get_expire_date( $post->ID, 'display' ) . '</time></span>';
										}
									?>

									</div> <!-- #coupon-main -->

							</div> <!-- .columns -->

							<div class="large-4 medium-12 columns">

								<?php clpr_vote_box_badge( $post->ID ); ?>

							</div> <!-- .columns -->

						</div> <!-- .row -->

				</div> <!-- .row -->

			</div> <!-- #head-box -->

			<div class="text-box">

				<?php appthemes_before_post_content(); ?>

				<?php the_content(); ?>

				<?php clpr_edit_coupon_link(); ?>

				<?php clpr_reset_coupon_votes_link(); ?>

				<?php appthemes_after_post_content(); ?>

			</div>

			<div class="text-footer">

				<div class="row">

					<div class="small-6 columns">

						<div class="tags"><i class="fa fa-tags" aria-hidden="true"></i><?php _e( 'Tags:', APP_TD ); ?> <?php if ( get_the_term_list( $post->ID, APP_TAX_TAG ) ) echo get_the_term_list( $post->ID, APP_TAX_TAG, '', ', ', '' ); else _e( 'None', APP_TD ); ?></div>

					</div> <!-- .columns -->

					<div class="small-6 columns">

						<?php if ( $clpr_options->stats_all && current_theme_supports( 'app-stats' ) ) { ?>
							<div class="stats"><i class="fa fa-bar-chart" aria-hidden="true"></i><?php appthemes_stats_counter( $post->ID ); ?></div>
						<?php } ?>

					</div> <!-- .columns -->

				</div> <!-- .row -->

			</div> <!-- .text-footer -->

		</div> <!-- .blog -->

	</div> <!-- .box-holder -->

</div> <!-- .content-box -->
