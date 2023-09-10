<?php
/**
 * Template part for displaying Order Summary content.
 *
 * @package Listing\Templates
 * @author  AppThemes
 * @since   Listing 1.0
 */

// @codeCoverageIgnoreStart
?>

<?php the_order_summary(); ?>

<p><?php esc_html_e( 'Your order has been completed.', APP_TD ); ?></p>
