<?php
/**
 * User Listing Form submodule
 *
 * @package Clipper\Users
 * @author  AppThemes
 * @since   2.0.0
 */

/**
 * Form processing class
 */
class CLPR_User_Listing_Form extends APP_Listing_Form {

	/**
	 * Retrieves core fields
	 *
	 * @return array An array of the fields parameters
	 */
	public function get_core_fields() {

		$fields = array(
			array(
				'type'  => 'input_text',
				'id'    => 'user_login',
				'props' => array(
					'required' => 1,
					'label'    => __( 'Username', APP_TD ),
					'tip'      => __( 'Usernames cannot be changed.', APP_TD ),
				),
			),
			array(
				'type'  => 'input_text',
				'id'    => 'first_name',
				'props' => array(
					'required' => 0,
					'label'    => __( 'First Name', APP_TD ),
				),
			),
			array(
				'type'  => 'input_text',
				'id'    => 'last_name',
				'props' => array(
					'required' => 0,
					'label'    => __( 'Last Name', APP_TD ),
				),
			),
			array(
				'type'  => 'input_text',
				'id'    => 'nickname',
				'props' => array(
					'required' => 1,
					'label'    => __( 'Nickname', APP_TD ),
				),
			),
			array(
				'type'  => 'input_text',
				'id'    => 'display_name',
				'props' => array(
					'required' => 1,
					'label'    => __( 'Display Name', APP_TD ),
				),
			),
			array(
				'type'  => 'email',
				'id'    => 'user_email',
				'props' => array(
					'required' => 1,
					'label'    => __( 'Email', APP_TD ),
				),
			),
			array(
				'type'  => 'url',
				'id'    => 'user_url',
				'props' => array(
					'required' => 0,
					'label'    => __( 'Website', APP_TD ),
				),
			),
			array(
				'type'  => 'textarea',
				'id'    => 'description',
				'props' => array(
					'required'    => 0,
					'label'       => __( 'About Me', APP_TD ),
					'editor_type' => '',
				),
			),
		);

		$contact_methods = array();

		foreach ( wp_get_user_contact_methods() as $name => $desc ) {
			$contact_methods[] = array(
				'type'  => 'input_text',
				'id'    => $name,
				'props' => array(
					'required' => 0,
					'label'    => apply_filters( 'user_' . $name . '_label', $desc ),
					'tip'      => clpr_profile_fields_description( $name ),
				),
			);
		}

		$fields = array_merge( $fields, $contact_methods );

		return apply_filters( 'appthemes_core_fields', $fields, $this->listing->get_type() );
	}

	/**
	 * Applies additional attributes to each field.
	 *
	 * @param array $field   The field parameters array.
	 * @param int   $item_id The item id.
	 *
	 * @return array
	 */
	public function apply_atts( $field, $item_id ) {
		$field = parent::apply_atts( $field, $item_id );

		if ( 'user_login' === $field['name'] ) {
			$field['extra']['disabled'] = 'disabled';
		} elseif ( 'display_name' === $field['name'] ) {
			$field['type']    = 'select';
			$field['choices'] = array_values( appthemes_get_user_profile_display_name_options() );
		} elseif ( 'user_email' === $field['name'] ) {
			$field['name'] = 'email';
		}

		return $field;
	}

	/**
	 * Get form fields metadata retrieved from the listing item.
	 *
	 * @param int $item_id Listing item id.
	 *
	 * @return array Form data
	 */
	public function get_formdata( $item_id ) {
		$formdata = parent::get_formdata( $item_id );

		$formdata['email'] = $formdata['user_email'];
		$formdata['url'] = $formdata['user_url'];

		return $formdata;
	}
}
