<?php
/**
 * Template for displaying a single form field.
 *
 * @global array  $app_field_array The field parameters.
 * @global string $app_field_token The actual field input token (like "%input%")
 *
 * Template usage:
 *  - `form-field.php`               - for all fields
 *  - `form-field-{$field_type}.php` - for fields of particular type.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 2.0.0
 */

// @codeCoverageIgnoreStart
?>
<div id="<?php echo esc_attr( implode( '_', (array) $app_field_array['name'] ) ); ?>_row" class="form-field row">
	<div class="large-3 columns">
		<label for="<?php echo esc_attr( scbForms::get_name( $app_field_array['name'] ) ); ?>" class="text-right middle">
			<?php echo $app_field_array['title']; ?>
		</label>
	</div>
	<div class="large-9 columns">
		<?php echo $app_field_token; ?>
		<?php if ( isset( $app_field_array['tip'] ) ) { ?>
			<p class="help-text" id="<?php echo esc_attr( implode( '_', (array) $app_field_array['name'] ) ); ?>HelpText">
				<?php echo $app_field_array['tip']; ?>
			</p>
		<?php } ?>
	</div>
</div>
