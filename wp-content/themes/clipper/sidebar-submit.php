<?php
/**
 * Submit Coupon Sidebar template.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 1.0.0
 */
?>

<div id="sidebar" class="medium-4 columns" role="complementary">

	<?php appthemes_before_sidebar_widgets( 'submit' ); ?>

	<?php dynamic_sidebar( 'sidebar_submit' ); ?>

	<?php appthemes_after_sidebar_widgets( 'submit' ); ?>

</div> <!-- #sidebar -->
