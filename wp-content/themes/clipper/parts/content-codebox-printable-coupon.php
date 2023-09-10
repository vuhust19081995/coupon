<?php
/**
 * Coupon code box content - printable coupon.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 1.6.0
 */
?>

<div class="link-holder">

	<a href="<?php echo clpr_get_coupon_out_url( $post ); ?>" id="coupon-link-<?php echo $post->ID; ?>" class="coupon-code-link" title="<?php esc_attr_e( 'Click to Print', APP_TD ); ?>" target="_blank" rel="nofollow" data-clipboard-text="<?php esc_attr_e( 'Print Coupon', APP_TD ); ?>" data-tooltip data-click-open="false" data-position="right" data-alignment="center"><span><?php _e( 'Print Coupon', APP_TD ); ?></span><i class="fa fa-scissors" aria-hidden="true"></i></a>

</div> <!-- .link-holder -->
