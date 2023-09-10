<?php
/**
 * Template for displaying checkout steps.
 *
 * @package Listing\Templates
 * @author  AppThemes
 * @since   Listing 1.0
 */

if ( isset( $step_content ) && ! empty( $step_content ) ) {
	echo html( 'div', $step_content );
}
