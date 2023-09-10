<?php
/**
 * Generic Header template.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 1.0.0
 * @since   2.0.0 Moved main components into template parts.
 */
?>

<header id="header" class="header" role="banner">

	<?php get_template_part( 'parts/nav-top-bar-primary' ); ?>

	<?php get_template_part( 'parts/header-branding-search' ); ?>

</header><!-- .header -->
