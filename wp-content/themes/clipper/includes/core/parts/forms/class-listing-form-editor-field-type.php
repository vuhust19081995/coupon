<?php
/**
 * Listing Form Editor field type
 *
 * @package Listing\Form
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Editor Field Type
 */
class APP_Editor_Field_Type {

	/**
	 * Retrieves field html
	 *
	 * @param mixed          $value Field value.
	 * @param scbCustomField $inst  Field object.
	 *
	 * @return string Generated html
	 */
	public static function _render( $value, $inst ) {

		$defaults = array(
			'tinymce'        => 'tmce' === $inst->props['editor_type'],
			'quicktags'      => 'html' === $inst->props['editor_type'],
			'media_buttons'  => false,
			'textarea_name'  => scbForms::get_name( $inst->name ),
			'textarea_rows'  => 10,
			'default_editor' => $inst->props['editor_type'],
			'editor_class'   => ! empty( $inst->props['required'] ) ? 'required' : '',
		);

		$args = apply_filters( 'appthemes_tinymce_editor_settings', $defaults, $inst->name );

		ob_start();

		wp_editor( $value, scbForms::get_name( $inst->name ), $args );

		return str_replace( scbForms::TOKEN, ob_get_clean(), $inst->wrap );
	}

}
