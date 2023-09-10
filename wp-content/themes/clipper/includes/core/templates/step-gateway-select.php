<?php
/**
 * Template for displaying Select Gateway step.
 *
 * @package Listing\Templates
 * @author  AppThemes
 * @since   Listing 1.0
 */

?>

<div class="gateway-select">
	<?php
	the_order_summary();
	?>
	<form id="<?php echo esc_attr( "{$nonce_check}-{$action_check}" );?>" class="<?php echo esc_attr( $form_class );?>" action="<?php echo esc_attr( $action_url ); ?>" method="POST">

		<?php
		wp_nonce_field( $nonce_check, $action_check );
		?>

		<p><?php esc_html_e( 'Please select a method for processing your payment:', APP_TD ); ?></p>
		<fieldset>
			<?php
			appthemes_list_gateway_dropdown( 'payment_gateway', $recurring );
			?>
			<button class="button large success" type="submit">
				<?php
				echo esc_html( $action_text );
				?>
			</button>
		</fieldset>
	</form>
</div>
