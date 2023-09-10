<?php
/**
 * Helper functions.
 *
 * @package Clipper\Helpers
 * @since   2.0.0
 */

 /**
  * Rename the WordPress sticky class since it conflicts with Foundation.
  *
  * @since 2.0.0
  *
  * @param array $classes Array of post css classes.
  * @return array         Array of post css classes.
  */
 function clpr_rename_sticky_class( $classes ) {

 	$classes   = array_diff( $classes, array( 'sticky' ) );
 	$classes[] = 'wp-sticky';

 	return $classes;
 }
 add_filter( 'post_class', 'clpr_rename_sticky_class' );

/**
 * Internal compat function to mimic mb_strimwidth().
 *
 * @since 2.0.0
 *
 * (PHP 4 &gt;= 4.0.6, PHP 5, PHP 7)<br/>
 * Get truncated string with specified width
 * @link http://php.net/manual/en/function.mb-strimwidth.php
 *
 * @param string $str        The string being decoded.
 * @param int    $start      The start position offset. Number of characters
 *                           from the beginning of string. First character is 0.
 * @param int    $width      The width of the desired trim.
 * @param string $trimmarker [optional] A string that is added to the end of
 *                           string when string is truncated.
 *
 * @return string The truncated string. If <i>trimmarker</i> is set, <i>trimmarker</i> is appended to the return value.
 */
function clpr_strimwidth( $str, $start, $width, $trimmarker = '' ) {

	if ( function_exists( 'mb_strimwidth' ) ) {
		$str = mb_strimwidth( $str, $start, $width, $trimmarker );
	} else {
		$trimmarker = ( mb_strlen( $str ) - $start <= $width ) ? '' : $trimmarker;
		$str = rtrim( mb_substr( $str, $start, max( 0, $width - mb_strlen( $trimmarker ) ) ) ) . $trimmarker;
	}

	return $str;
}

/**
 * Retrieve protected post password form content to set the button class.
 *
 * @since 2.0.0
 *
 * @param int|WP_Post $post Optional. Post ID or WP_Post object. Default is global $post.
 * @return string HTML content for password form for password protected post.
 */
function clpr_the_password_form( $post = 0 ) {

	$post = get_post( $post );
	$label = 'pwbox-' . ( empty( $post->ID ) ? rand() : $post->ID );
	$output = '<form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" class="post-password-form" method="post">
	<p>' . __( 'This content is password protected. To view it, enter your password below.', APP_TD ) . '</p>
	<p><label for="' . $label . '">' . __( 'Password', APP_TD ) . ' <input name="post_password" id="' . $label . '" type="password"></label> <input type="submit" name="Submit" value="' . esc_attr__( 'Submit', APP_TD ) . '" class="button small" /></p></form>
	';

	return $output;
}
add_filter( 'the_password_form', 'clpr_the_password_form' );

/**
 * Generate icon tooltips via Foundation Framework's tooltip js.
 *
 * @todo Function not used. Implement or deprecate.
 *
 * @since 2.0.0
 *
 * @param string $text     The text to display in the tooltip.
 * @param string $position (Optional) The placement of the tooltip (e.g. top, left, right).
 * @param string $id       (Optional) The span ID in case you want to control differently.
 * @param string $icon     (Optional) The Font Awesome icon you want to use. Defaults to fa-question-circle.
 * @return string
 */
function clpr_tooltip_icon( $text = '', $position = 'top', $id = '', $icon = 'fa-question-circle' ) {

	$output = '<span data-tooltip aria-haspopup="true" id="' . esc_attr( $id ) . '" class="clpr-tooltip has-tip ' . esc_attr( $position ) . ' " data-disable-hover="false" tabindex="2" title="' . esc_attr( $text ) . '"><i class="fa ' . esc_attr( $icon ) . '" aria-hidden="true"></i></span>';

	/**
	 * Filters the icon tooltip.
	 *
	 * @since 2.0.0
	 *
	 * @param string $text     The text to display in the tooltip.
	 * @param string $position (Optional) The placement of the tooltip (e.g. top, left, right).
	 * @param string $id       (Optional) The span ID in case you want to control differently.
	 * @param string $icon     (Optional) The Font Awesome icon you want to use. Defaults to fa-question-circle.
	 */
	return apply_filters( 'clpr_tooltip_icon', $output, $text, $position, $id, $icon );
}
