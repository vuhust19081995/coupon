<?php
/**
 * Template for displaying Create Listing User Form.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   2.0.0
 */

?>

<form id="<?php echo esc_attr( "{$nonce_check}-{$action_check}" );?>" class="app-form <?php echo esc_attr( $form_class );?>" enctype="multipart/form-data" method="post" action="<?php echo esc_attr( $action_url ); ?>">

	<?php wp_nonce_field( $nonce_check, $action_check ); ?>

	<fieldset>
		<?php
		do_action( 'appthemes_listing_create_anonymous_form', $app_listing );
		?>
		<div class="form-field">
			<button class="button expanded coupon" type="submit">
				<?php
				echo esc_html( $action_text );
				?>
			</button>
		</div>
	</fieldset>
</form>
