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
 * @package Listing\Templates
 * @author  AppThemes
 * @since   Listing 1.0
 */

// @codeCoverageIgnoreStart
?>
<div class="form-field <?php echo esc_attr( $app_field_array['props']['type'] ); ?>">
	<label for="<?php echo esc_attr( scbForms::get_name( $app_field_array['name'] ) ); ?>">
		<?php echo $app_field_array['title']; ?>
	</label>
	<?php echo $app_field_token; ?>
</div>
