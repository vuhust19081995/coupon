<?php
/**
 * The template for displaying the mini comments box.
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

if ( have_comments() ) : ?>

	<div class="comments-box coupon">

		<ul class="comments-mini">

			<?php wp_list_comments( array(
				'callback'          => 'clpr_mini_comments',
				'reverse_top_level' => 'desc',
			) ); ?>

		</ul>

	</div> <!-- .comments-box -->

<?php endif; ?>
