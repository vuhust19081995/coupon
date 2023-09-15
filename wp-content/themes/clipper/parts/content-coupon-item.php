<?php
/**
 * Coupon listing loop content template.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 2.0.0
 */

global $clpr_options, $withcomments;

$withcomments = 1;
?>

<div id="post-<?php echo $post->ID; ?>" <?php post_class( 'item' ); ?>>

	<div class="item-holder">

			<div class="item-top row">

				<div class="large-3 medium-6 columns">

					<div class="store-holder">

						<div class="store-image">

							<a href="<?php echo esc_url( clpr_get_first_term_link( $post->ID, APP_TAX_STORE ) ); ?>" aria-hidden="true">

								<div class="item-cover" style="background-image: url(<?php echo esc_url( clpr_get_store_image_url( $post->ID, 'post_id', 768 ) ); ?>);"></div>

							</a>

						</div> <!-- .store-image -->

						<!-- <div class="store-name">
							<?php echo get_the_term_list( $post->ID, APP_TAX_STORE, ' ', ', ', '' ); ?>
						</div> -->

					</div> <!-- .store-holder -->

				</div> <!-- .columns -->

				<div class="large-9 medium-6 columns">

					<div class="row">

						<div class="large-8 medium-12 columns">

							<div class="item-panel">

								<?php appthemes_before_post_title(); ?>

								<header class="entry-header">

									<h1 class="entry-title"><?php clpr_coupon_title(); ?></h1>

								</header>

								<?php appthemes_after_post_title(); ?>

								<?php clpr_coupon_code_box(); ?>

								<div class="clear"></div>

							</div> <!-- #item-panel -->

						</div> <!-- .columns -->

						<div class="large-4 medium-12 columns">

							<?php clpr_vote_box_badge( $post->ID ); ?>

						</div>


					</div> <!-- .row -->

				</div> <!-- .columns -->

			</div> <!-- .row -->

			<?php appthemes_before_post_content(); ?>

			<div class="item-content">

				<?php clpr_coupon_content(); ?>

			</div>

			<?php appthemes_after_post_content(); ?>

			<!-- <div class="item-meta">

				<div class="row">

					<div class="small-6 columns">

						<div class="taxonomy">
							<p class="category"><span><?php _e( 'Category:', APP_TD ); ?></span> <?php echo get_the_term_list( $post->ID, APP_TAX_CAT, ' ', ', ', '' ); ?></p>
							<p class="tag"><span><?php _e( 'Tags:', APP_TD ); ?></span> <?php echo get_the_term_list( $post->ID, APP_TAX_TAG, ' ', ', ', '' ); ?></p>
						</div>

					</div>

					<div class="small-6 columns">

						<div class="calendar">
							<p class="create"><span><?php _e( 'Created:', APP_TD ); ?></span> <time class="entry-date published" datetime="<?php echo get_the_date( 'c' ); ?>"><?php echo get_the_date(); ?></time></p>
							<p class="expire">
								<?php if ( clpr_get_expire_date( $post->ID ) ) { ?>
									<span><?php esc_html_e( 'Expires:', APP_TD ); ?></span> <time class="entry-date expired" datetime="<?php echo clpr_get_expire_date( $post->ID, 'c' ); ?>"><?php echo clpr_get_expire_date( $post->ID, 'display' ); ?></time>
								<?php } else { ?>
									<span><?php esc_html_e( 'Expires:', APP_TD ); ?></span> <?php esc_html_e( 'Never', APP_TD ); ?>
								<?php } ?>
							</p>
						</div>

					</div>

				</div>

			</div> -->


		<div class="item-footer">

			<ul class="soc-container">

				<li class="stats"><i class="fa fa-bar-chart" aria-hidden="true"></i><?php if ( $clpr_options->stats_all && current_theme_supports( 'app-stats' ) ) appthemes_stats_counter( $post->ID ); ?></li>
				<li><a class="share" href="#"><i class="fa fa-share-alt" aria-hidden="true"></i><?php _e( 'Share', APP_TD ); ?></a>

					<div class="drop">

						<?php
						// assemble the text and url we'll pass into each social media share link
						$social_text = urlencode( strip_tags( get_the_title() . ' ' . __( 'coupon from', APP_TD ) . ' ' . get_bloginfo( 'name' ) ) );
						$social_url = urlencode( get_permalink( $post->ID ) );
						?>

						<ul>
							<li><a class="facebook" href="javascript:void(0);" onclick="window.open('https://www.facebook.com/sharer.php?t=<?php echo $social_text; ?>&amp;u=<?php echo $social_url; ?>','doc', 'width=638,height=500,scrollbars=yes,resizable=auto');" rel="nofollow"><i class="fa fa-facebook-official" aria-hidden="true"></i><?php _e( 'Facebook', APP_TD ); ?></a></li>
							<li><a class="twitter" href="https://twitter.com/home?status=<?php echo $social_text; ?>+-+<?php echo $social_url; ?>" rel="nofollow" target="_blank"><i class="fa fa-twitter-square" aria-hidden="true"></i><?php _e( 'Twitter', APP_TD ); ?></a></li>
							<li><a class="digg" href="https://digg.com/submit?phase=2&amp;url=<?php echo $social_url; ?>&amp;title=<?php echo $social_text; ?>" rel="nofollow" target="_blank"><i class="fa fa-digg" aria-hidden="true"></i><?php _e( 'Digg', APP_TD ); ?></a></li>
							<li><a class="reddit" href="https://reddit.com/submit?url=<?php echo $social_url; ?>&amp;title=<?php echo $social_text; ?>" rel="nofollow" target="_blank"><i class="fa fa-reddit-square" aria-hidden="true"></i><?php _e( 'Reddit', APP_TD ); ?></a></li>
						</ul>

					</div> <!-- .drop -->
				</li>

				<li><?php clpr_comments_popup_link( '<i class="fa fa-comment-o" aria-hidden="true"></i><span></span> ' . __( 'Comment', APP_TD ), '<i class="fa fa-comment-o" aria-hidden="true"></i><span>1</span> ' . __( 'Comment', APP_TD ), __( '<span>%</span> Comments', APP_TD ), 'show-comments' ); // leave spans for ajax to work correctly ?></li>

				<?php clpr_report_coupon( true ); ?>

			</ul>

			<div id="comments-<?php echo $post->ID; ?>" class="comments-list">

				<p class="links">
					<span class="pencil"><i class="fa fa-pencil" aria-hidden="true"></i></span>
					<?php if ( comments_open() ) clpr_comments_popup_link( __( 'Add a comment', APP_TD ), __( 'Add a comment', APP_TD ), __( 'Add a comment', APP_TD ), 'mini-comments' ); else echo '<span class="closed">' . __( 'Comments closed', APP_TD ) . '</span>'; ?>
					<span class="minus"><i class="fa fa-times-circle" aria-hidden="true"></i></span>
					<?php clpr_comments_popup_link( __( 'Close comments', APP_TD ), __( 'Close comments', APP_TD ), __( 'Close comments', APP_TD ), 'show-comments' ); ?>
				</p>

				<?php comments_template( '/comments-mini.php' ); ?>

			</div> <!-- .comments-list -->

			<div class="author vcard">
				<a class="url fn n" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>" rel="author"><?php the_author(); ?></a>
			</div> <!-- .author -->

		</div> <!-- .item-footer -->

	</div> <!-- .item-holder -->

</div> <!-- .item -->
