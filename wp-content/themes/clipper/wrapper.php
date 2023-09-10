<?php
/**
 * The main page wrapper for serving up all content
 *
 * Loads each content template within this wrapper.
 *
 * @package Clipper
 * @author  AppThemes
 *
 * @since 1.3.1
 * @since 2.0.0 Updated and rebuilt for modern standards.
 */

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
	<head>
		<meta charset="<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="profile" href="http://gmpg.org/xfn/11">
		<title><?php wp_title(''); ?></title>

		<?php wp_head(); ?>
	</head>

	<body <?php body_class(); ?>>

		<?php appthemes_before(); ?>

		<div id="wrapper">

				<?php appthemes_before_header(); ?>

				<?php get_header( app_template_base() ); ?>

				<?php appthemes_after_header(); ?>

				<?php load_template( app_template_path() ); ?>

			<?php appthemes_before_footer(); ?>

			<?php get_footer( app_template_base() ); ?>

			<?php appthemes_after_footer(); ?>

		</div> <!-- #wrapper -->

		<?php wp_footer(); ?>

		<?php appthemes_after(); ?>

	</body>
</html>
