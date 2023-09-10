<?php
/**
 * Blog search form template.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 1.0.0
 */
?>

<form method="get" class="search" action="<?php echo home_url( '/' ); ?>" role="search">

	<label class="screen-reader-text" for="s"><?php _e( 'Search for:', APP_TD ); ?></label>
	<input type="search" class="text" id="s" name="s" value="<?php the_search_query(); ?>" placeholder="<?php echo esc_attr_x( 'Search', 'placeholder', APP_TD ); ?>" />

	<button type="submit" class="button small" value="<?php echo esc_attr_x( 'Search', 'submit button', APP_TD ); ?>"><span><?php _e( 'Search', APP_TD ); ?></span></button>

</form>
