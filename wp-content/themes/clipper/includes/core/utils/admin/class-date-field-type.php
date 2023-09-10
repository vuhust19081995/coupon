<?php
/**
 * Date field custom controller
 *
 * @package Listing\Utils
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Date field custom controller class.
 */
class APP_Date_Field_Type {

	/**
	 * Retrieves field html.
	 *
	 * @param mixed          $value Field value.
	 * @param scbCustomField $inst  Field object.
	 *
	 * @return string Generated html
	 */
	static function _render( $value, $inst ) {
		$output = '';
		// Hidden field.
		$hidden = array(
			'name' => $inst->name,
			'type' => 'hidden',
			'value' => ( ! $value ) ? $value : '',
			'extra' => array( 'class' => 'alt-date-field' ),
		);

		if ( ! isset( $inst->extra ) ) {
			$inst->extra = array();
		}

		if ( ! is_array( $inst->name ) && false === strpos( $inst->name, '[' ) ) {
			$name = $inst->name;
		} else {
			$name = '';
		}

		$hidden['extra']['id'] = $name;
		$front_name = '_blank_' . $name;

		if ( ! isset( $inst->extra['id'] ) ) {
			$front_id = $front_name;
		} else {
			$front_id = $inst->extra['id'];
		}

		// Front field.
		$args = array(
			'name' => $front_name,
			'id' => $front_id,
			'value' => '',
			'desc' => $inst->desc,
			'desc_pos' => 'after',
			'extra' => $inst->extra,
			'type' => 'text',
		);

		$output = scbForms::input_with_value( $args, $value );
		$output .= scbForms::input_with_value( $hidden, $value );
		return $output;
	}

	/**
	 * Sanitize field value.
	 *
	 * @param mixed          $value Field value.
	 * @param scbCustomField $inst  Field object.
	 *
	 * @return mixed Sanitized value
	 */
	static function _sanitize( $value, $inst ) {
		if ( ! empty( $value ) ) {
			return date( 'Y-m-d H:i:s', strtotime( $value ) );
		} else {
			return false;
		}
	}
}
