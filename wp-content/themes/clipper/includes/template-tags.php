<?php
/**
 * Functions used in the Templates and Loops
 *
 * @package Clipper\TemplateTags
 * @since   2.0.0
 */

if ( ! function_exists( 'clpr_the_archive_title' ) ) :
/**
* Display the archive title based on the queried object.
*
* Based off the WordPress the_archive_title() function.
* Added here so text strings can be translated with theme.
*
* @since 2.0.0
*
* @see the_archive_title()
*
* @param string $before Optional. Content to prepend to the title. Default empty.
* @param string $after  Optional. Content to append to the title. Default empty.
* @return string
*/
function clpr_the_archive_title( $before = '', $after = '' ) {

	$title = clpr_get_the_archive_title();

	if ( ! empty( $title ) ) {
		echo $before . $title . $after;
	}
}
endif;

if ( ! function_exists( 'clpr_get_the_archive_title' ) ) :
/**
* Retrieve the archive title based on the queried object.
* Based off the WordPress core function. Forked so we can
* extract the text strings for easy translation.
*
* @since 2.0.0
*
* @return string Archive title.
*/
function clpr_get_the_archive_title() {

	if ( is_category() ) {
		$title = single_cat_title( '', false );
	} elseif ( is_tag() ) {
		$title = single_tag_title( '', false );
	} elseif ( is_author() ) {
		$title = sprintf( __( 'Author: %s', APP_TD ), '<span class="vcard">' . get_the_author() . '</span>' );
	} elseif ( is_year() ) {
		$title = sprintf( __( 'Year: %s', APP_TD ), get_the_date( _x( 'Y', 'yearly archives date format' ) ) );
	} elseif ( is_month() ) {
		$title = sprintf( __( 'Month: %s', APP_TD ), get_the_date( _x( 'F Y', 'monthly archives date format' ) ) );
	} elseif ( is_day() ) {
		$title = sprintf( __( 'Day: %s', APP_TD ), get_the_date( _x( 'F j, Y', 'daily archives date format' ) ) );
	} elseif ( is_tax( 'post_format' ) ) {
		if ( is_tax( 'post_format', 'post-format-aside' ) ) {
			$title = _x( 'Asides', 'post format archive title', APP_TD );
		} elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
			$title = _x( 'Galleries', 'post format archive title', APP_TD );
		} elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
			$title = _x( 'Images', 'post format archive title', APP_TD );
		} elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
			$title = _x( 'Videos', 'post format archive title', APP_TD );
		} elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
			$title = _x( 'Quotes', 'post format archive title', APP_TD );
		} elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
			$title = _x( 'Links', 'post format archive title', APP_TD );
		} elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
			$title = _x( 'Statuses', 'post format archive title', APP_TD );
		} elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
			$title = _x( 'Audio', 'post format archive title', APP_TD );
		} elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
			$title = _x( 'Chats', 'post format archive title', APP_TD );
		}
	} elseif ( is_post_type_archive() ) {
		$title = sprintf( __( 'Archives: %s', APP_TD ), post_type_archive_title( '', false ) );
	} elseif ( is_tax() ) {
		$tax = get_taxonomy( get_queried_object()->taxonomy );
		/* translators: 1: Taxonomy singular name, 2: Current taxonomy term */
		$title = sprintf( __( '%1$s: %2$s' ), $tax->labels->singular_name, single_term_title( '', false ) );
	} else {
		$title = __( 'Archives', APP_TD );
	}

	/**
	 * Filter the archive title.
	 *
	 * @since 2.0.0
	 *
	 * @param string $title Archive title to be displayed.
	 */
	return apply_filters( 'clpr_get_the_archive_title', $title );
}
endif;

if ( ! function_exists( 'clpr_blog_post_meta' ) ) :
/**
 * Adds the post meta before the blog post content.
 *
 * @since 1.0.0
 *
 * @return string
 */
function clpr_blog_post_meta() {
?>
	<div class="content-bar">

		<div class="row">

			<div class="small-6 columns">

				<span class="entry-date">
					<i class="fa fa-calendar" aria-hidden="true"></i><?php echo get_the_date(); ?>
				</span>

				<span class="entry-category">
					<i class="fa fa-folder-open-o" aria-hidden="true"></i><?php the_category( ', ' ); ?>
				</span>

			</div><!-- .columns -->

			<div class="small-6 columns">

				<span class="comment-count">
					<i class="fa fa-comments-o" aria-hidden="true"></i><?php printf( _nx( '1 Comment', '%1$s Comments', get_comments_number(), 'comments title', APP_TD ), number_format_i18n( get_comments_number() ) ); ?>
				</span>

			</div><!-- .columns -->

		</div><!-- .row -->

	</div><!-- .content-bar -->
<?php
}
endif;

if ( ! function_exists( 'clpr_blog_pagination' ) ) :
/**
 * Adds the pagination to blog posts lists.
 *
 * @since 1.0.0
 *
 * @return void
 */
function clpr_blog_pagination() {
?>
	<div class="content-box">

		<div class="box-holder">

			<?php appthemes_pagination(); ?>

		</div> <!-- .box-holder -->

	</div> <!-- .content-box -->
<?php
}
endif;

if ( ! function_exists( 'clpr_user_bar_box' ) ) :
/**
 * Adds the user bar box after the blog post content.
 *
 * @since 1.0.0
 * @since 2.0.0 Removed action and use directly instead (optional).
 *
 * @todo Function not used. Implement or deprecate.
 *
 * @return void
 */
function clpr_user_bar_box() {
  global $post;

	if ( ! is_singular() ) {
		return;
	}

	// assemble the text and url we'll pass into each social media share link
	$social_text = urlencode( strip_tags( get_the_title() . ' ' . __( 'post from', APP_TD ) . ' ' . get_bloginfo( 'name' ) ) );
	$social_url  = urlencode( get_permalink( $post->ID ) );
?>

	<div class="user-bar">

		<?php
		if ( comments_open() ) {
			$leave_comment = __( 'Leave a comment &#8594;', APP_TD );
			comments_popup_link( $leave_comment, $leave_comment, $leave_comment, '', '' );
		}
		?>

		<ul class="soc-container">
			<li><a class="rss" href="<?php echo get_post_comments_feed_link( get_the_ID() ); ?>" rel="nofollow"><?php _e( 'Post Comments RSS', APP_TD ); ?></a></li>
			<li><a class="twitter" href="https://twitter.com/home?status=<?php echo $social_text; ?>+-+<?php echo $social_url; ?>" target="_blank" rel="nofollow"><?php _e( 'Twitter', APP_TD ); ?></a></li>
			<li><a class="facebook" href="javascript:void(0);" onclick="window.open('https://www.facebook.com/sharer.php?t=<?php echo $social_text; ?>&amp;u=<?php echo $social_url; ?>','doc', 'width=638,height=500,scrollbars=yes,resizable=auto');" rel="nofollow"><?php _e( 'Facebook', APP_TD ); ?></a></li>
			<li><a class="digg" href="https://digg.com/submit?phase=2&amp;url=<?php echo $social_url; ?>&amp;title=<?php echo $social_text; ?>" target="_blank" rel="nofollow"><?php _e( 'Digg', APP_TD ); ?></a></li>
		</ul>

	</div>
<?php
}
endif;
