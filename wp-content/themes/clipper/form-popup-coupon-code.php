<?php
/**
 * Coupon Code Popup Form Template.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 1.6
 */
?>

<div class="content-box comment-form coupon-code-popup">

	<div class="box-holder">

		<div class="post-box">

				<a href="<?php echo esc_url( clpr_get_first_term_link( $post->ID, APP_TAX_STORE ) ); ?>" aria-hidden="true">
					<div class="brand-image" style="background-image: url(<?php echo esc_url( clpr_get_store_image_url( $post->ID, 'post_id', 768 ) ); ?>);"></div>
				</a>

			<!-- <div class="head row"><h3><?php //printf( __( 'Your %s coupon code:', APP_TD ), get_the_term_list( $post->ID, APP_TAX_STORE, ' ', ', ', '' ) ); ?></h3></div> -->

			<div id="respond" class="row">

				<div class="popup-code-copy row columns" style="display: flex;">
					<div class="text" style="border: 2px solid #486CDE; color: #486CDE; display: flex; align-items: center; justify-content: center;"><?php echo esc_attr( $coupon_code ); ?></div>
					<button style="background-color: #486CDE; margin: 0; width: 60px;" id="copy-button" class="btn submit" data-clipboard-text="<?php echo esc_attr( $coupon_code ); ?>" ><?php _e( 'Copy', APP_TD ); ?></button>
				</div>

				<div class="popup-code-info row columns" style="background-color: white; box-shadow: none; border: 1px solid #3085d6; font-weight: 300">
					<p style="color: #0d1216"><?php _e( 'Paste this code at checkout when you are done shopping.', APP_TD ); ?></p>
					<a href="<?php echo clpr_get_coupon_out_url( $post ); ?>" title="<?php _e( 'Click to open site', APP_TD ); ?>" target="_blank" ><?php _e( 'Open site', APP_TD ); ?></a>
				</div>

				<?php do_action( 'clpr_coupon_code_popup', $post->ID ); ?>

			</div>

		</div>

	</div>

</div>
