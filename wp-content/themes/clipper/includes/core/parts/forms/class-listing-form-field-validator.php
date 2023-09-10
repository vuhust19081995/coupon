<?php
/**
 * Listing Form Field Validator
 *
 * @package Listing\Form
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * General form Validator class
 */
class APP_Form_Field_Validator {

	/**
	 * Errors object.
	 *
	 * @var WP_Error
	 */
	protected $errors;

	/**
	 * Construct Validator object.
	 *
	 * @param WP_Error $errors Errors object.
	 */
	public function __construct( WP_Error $errors ) {
		$this->errors = $errors;
	}

	/**
	 * Validates posted data and adds errors
	 *
	 * @param mixed          $value Posted field value.
	 * @param scbCustomField $inst  Field object.
	 *
	 * @return mixed Validated value.
	 */
	public function validate( $value, $inst ) {
		$sanitizers = (array) $inst->sanitizers;

		foreach ( $sanitizers as $sanitizer ) {
			$value = call_user_func( $sanitizer, $value, $inst, $this->errors );
		}

		return $value;
	}

	/**
	 * Validate required fields.
	 *
	 * @param mixed          $value  Posted field value.
	 * @param scbCustomField $inst   Field object.
	 * @param WP_Error       $errors Errors object.
	 *
	 * @return mixed Validated value.
	 */
	public function required( $value, $inst, $errors ) {

		// Validate required fields.
		if ( $inst->props['required'] && empty( $value ) ) {

			$title = ( isset( $inst->title ) ) ? $inst->title : $inst->desc ;

			$errors->add(
				'required_field',
				sprintf( __( 'ERROR: Field %s is required!', APP_TD ), $title )
			);

		}

		return $value;
	}

	/**
	 * Validate URL fields.
	 *
	 * @param mixed          $value  Posted field value.
	 * @param scbCustomField $inst   Field object.
	 * @param WP_Error       $errors Errors object.
	 *
	 * @return mixed Validated value.
	 */
	public function url( $value, $inst, $errors ) {
		if ( $value && ! wp_http_validate_url( $value ) ) {
			$title = ( isset( $inst->title ) ) ? $inst->title : $inst->desc ;
			$errors->add(
				'invalid_url',
				sprintf( __( 'ERROR: Field %s contains an invalid URL!', APP_TD ), $title )
			);
		}
		return $value;
	}

	/**
	 * Validate Email fields.
	 *
	 * @param mixed          $value  Posted field value.
	 * @param scbCustomField $inst   Field object.
	 * @param WP_Error       $errors Errors object.
	 *
	 * @return mixed Validated value.
	 */
	public function email( $value, $inst, $errors ) {
		if ( $value && ! is_email( $value ) ) {
			$title = ( isset( $inst->title ) ) ? $inst->title : $inst->desc ;
			$errors->add(
				'invalid_email',
				sprintf( __( 'ERROR: Field %s contains an invalid Email address!', APP_TD ), $title )
			);
		}
		return $value;
	}

	/**
	 * Validate number fields.
	 *
	 * @param mixed          $value  Posted field value.
	 * @param scbCustomField $inst   Field object.
	 * @param WP_Error       $errors Errors object.
	 *
	 * @return mixed Validated value.
	 */
	public function number( $value, $inst, $errors ) {
		if ( $value && ! is_numeric( $value ) ) {
			$title = ( isset( $inst->title ) ) ? $inst->title : $inst->desc ;
			$errors->add(
				'invalid_number',
				sprintf( __( 'ERROR: Field %s contains an invalid number!', APP_TD ), $title )
			);

		}

		return $value;
	}

}
