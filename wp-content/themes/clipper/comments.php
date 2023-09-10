<?php
/**
 * The template for displaying comments.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 1.0.0
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}
?>

<?php appthemes_before_blog_comments(); ?>

<?php if ( have_comments() ) : ?>

	<div class="content-box" id="comments">

		<div class="box-holder">

			<div class="comments-box">

				<div class="head">

					<h3>
						<?php
						$comments_number = get_comments_number();
						if ( 1 == $comments_number ) {
							/* translators: %s: post title */
							printf( _x( 'One response to &ldquo;%s&rdquo;', 'comments title', APP_TD ), get_the_title() );
						} else {
							printf(
								/* translators: 1: number of comments/messages, 2: post title */
								_nx(
									'%1$s response to &ldquo;%2$s&rdquo;',
									'%1$s responses to &ldquo;%2$s&rdquo;',
									$comments_number,
									'comments title',
									APP_TD
								),
								number_format_i18n( $comments_number ),
								get_the_title()
							);
						}
						?>
					</h3>

				</div> <!-- .head -->

				<ul class="comments">
					<?php
					wp_list_comments( array(
						'avatar_size' => 150,
						'callback'    => 'clpr_comment_template',
						'type'        => 'comment',
						'short_ping'  => true,
					) );
					?>
				</ul>

				<?php if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) : ?>
					<nav id="comment-nav-below" class="navigation comment-navigation" role="navigation">
						<h2 class="screen-reader-text"><?php esc_html_e( 'Comment navigation', APP_TD ); ?></h2>
						<div class="nav-links">
							<div class="row">
								<div class="nav-previous small-6 columns"><?php previous_comments_link( esc_html__( '&larr; Older Comments', APP_TD ) ); ?></div>
								<div class="nav-next small-6 columns text-right"><?php next_comments_link( esc_html__( 'Newer Comments &rarr;', APP_TD ) ); ?></div>
							</div> <!-- .row -->
						</div><!-- .nav-links -->
					</nav><!-- #comment-nav-below -->
				<?php endif; // Check for comment navigation. ?>

			</div> <!-- .comments-box -->

		</div> <!-- .box-holder -->

	</div> <!-- .content-box -->

<?php endif; ?>

<?php
// If comments are closed and there are comments, let's leave a little note, shall we?
if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) :
?>
	<p class="no-comments"><?php _e( 'Discussion is closed.', APP_TD ); ?></p>
<?php endif; ?>

<?php appthemes_after_blog_comments(); ?>

<?php appthemes_before_blog_respond(); ?>


<?php
// Replace below if statement with comment_form();
if ( comments_open() ) : ?>

	<div class="content-box" id="reply">

		<div class="box-holder">

			<div class="head">

				<h3 class="comments-title"><?php comment_form_title( __( 'Leave a Reply', APP_TD ), __( 'Leave a Reply to %s', APP_TD ) ); ?></h3>

			</div> <!-- .head -->

			<div class="post-box">

				<?php appthemes_before_blog_comments_form(); ?>

				<?php appthemes_blog_comments_form(); ?>

				<?php appthemes_after_blog_comments_form(); ?>

			</div> <!-- .post-box -->

		</div> <!-- .box-holder -->

	</div> <!-- .content-box -->

<?php endif; ?>

<?php appthemes_after_blog_respond(); ?>
