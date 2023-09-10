<?php
/**
 * Template for displaying Summary step.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   2.0.0
 */

if ( isset( $app_order ) ) {
	appthemes_get_template_part( 'parts/content-transaction-summary' );
} else {
	clpr_payments_display_order_summary_continue_button();
}

