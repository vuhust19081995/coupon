<?php
/**
 * Template for displaying single order.
 *
 * @package Listing\Templates
 * @author  AppThemes
 * @since   Listing 1.0
 */

// @codeCoverageIgnoreStart
?>

<div class="app-theme-compat">

<?php appthemes_notices(); ?>

<?php appthemes_before_post(); ?>

	<div id="overview" class="single-content white-con step">

	<?php appthemes_before_post_content(); ?>

	<?php appthemes_get_template_part( 'content-transaction', $app_order_content ); ?>

	<?php appthemes_after_post_content(); ?>

	</div>

<?php appthemes_after_post(); ?>

</div>
