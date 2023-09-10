<?php
/**
 * Coupon code box content - coupon code.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 1.6.0
 */

global $clpr_options;

$coupon_code = wptexturize( get_post_meta( $post->ID, 'clpr_coupon_code', true ) );
$button_text = ( $clpr_options->coupon_code_hide ) ? __( 'Show Coupon Code', APP_TD ) : $coupon_code;
?>

<div class="link-holder">

	<a href="<?php echo clpr_get_coupon_out_url( $post ); ?>" id="coupon-link-<?php echo $post->ID; ?>" class="coupon-code-link" title="<?php esc_attr_e( 'Click to copy &amp; open site', APP_TD ); ?>" target="_blank" rel="nofollow" data-clipboard-text="<?php echo esc_attr( $coupon_code ); ?>" data-tooltip data-click-open="false" data-position="right" data-alignment="center"><span><?php echo $button_text; ?></span><i class="fa fa-scissors" aria-hidden="true"></i></a>

</div> <!-- .link-holder -->
