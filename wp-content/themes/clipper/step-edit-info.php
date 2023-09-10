<?php
/**
 * Template for displaying Listing Form.
 *
 * @package Clipper\Listing\Templates
 * @author  AppThemes
 * @since   2.0.0
 */

?>
<form id="<?php echo esc_attr( "{$nonce_check}-{$action_check}" );?>" class="post-form app-form <?php echo esc_attr( $form_class );?>" enctype="multipart/form-data" method="post" action="<?php echo esc_attr( $action_url ); ?>">

	<?php wp_nonce_field( $nonce_check, $action_check ); ?>

	<?php // The ID is really needed for some scripts! ?>
	<input type="hidden" name="ID" value="<?php echo esc_attr( $listing->ID ); ?>" />

	<?php
	appthemes_listing_form( $listing, $form_fields, $app_listing->get_type() );

	/**
	 * Fires following the form fields in the listing submission/edit form.
	 *
	 * @since 2.0.0
	 */
	do_action( 'clpr_edit_listing_form' );
	?>

	<div class="row">
		<div class="small-3 columns">
		</div>
		<div class="small-9 columns">
			<button type="submit" class="button expanded coupon" id="submitted" name="submitted" value="submitted"><?php echo esc_html( $action_text ); ?></button>
		</div>
	</div>

	<!-- autofocus the field -->
	<script type="text/javascript">try{document.getElementById('post_title').focus();}catch(e){}</script>
</form>
