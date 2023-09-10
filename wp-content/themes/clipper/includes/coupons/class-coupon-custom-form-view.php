<?php

/**
 * Coupon Code Type Subform Views.
 *
 * @package Clipper\Coupons
 * @author  AppThemes
 * @since   2.0.0
 */

class CLPR_Coupon_Custom_Form_View extends APP_View_Page {

	/**
	 * Construct view.
	 *
	 * @param string $template      Unique template file name.
	 * @param string $default_title The Page/Post title.
	 * @param string $ptype         The type of viewing item. Default "page".
	 */
	public function __construct( $template, $default_title, $ptype = 'page' ) {
		parent::__construct( $template, $default_title, $ptype );
		add_action( 'appthemes_first_run', array( $this, 'setup' ) );
	}

	/**
	 * Setup data on the view first install.
	 */
	public function setup() {
		$post_id = $this->get_page_id();
		$form   = get_post_meta( $post_id, 'va_form', true );

		if ( ! $form ) {
			update_post_meta( $post_id, 'va_form', $this->form_fields() );
			$this->set_terms();
		}
	}

	/**
	 * Retrieves the fields to be installed.
	 *
	 * @return array
	 */
	public function form_fields() {
		return array();
	}

	/**
	 * Set the taxonomy terms.
	 */
	public function set_terms() {}

}


class CLPR_Coupon_Type_Code_Custom_Form_View extends CLPR_Coupon_Custom_Form_View {
	/**
	 * Retrieves the fields to be installed.
	 *
	 * @return array
	 */
	public function form_fields() {
		return array(
			array(
				'type'  => 'input_text',
				'id'    => 'clpr_coupon_code',
				'props' => array(
					'required'    => 1,
					'label'       => '',
					'placeholder' => _x( 'Enter coupon code', 'placeholder', APP_TD ),
				),
			),
		);
	}

	/**
	 * Set the taxonomy terms.
	 */
	public function set_terms() {
		$term_id = appthemes_maybe_insert_term( __( 'Coupon Code', APP_TD ), APP_TAX_TYPE, array( 'slug' => 'coupon-code' ) );
		wp_set_object_terms( $this->get_page_id(), $term_id, APP_TAX_TYPE );
	}
}

class CLPR_Coupon_Type_Printable_Custom_Form_View extends CLPR_Coupon_Custom_Form_View {
	/**
	 * Retrieves the fields to be installed.
	 *
	 * @return array
	 */
	public function form_fields() {
		return array(
			array(
				'type'  => 'file',
				'id'    => '_printable_coupon',
				'props' => array(
					'required'    => 1,
					'label'       => '',
					'extensions'  => 'Image',
					'file_limit'  => 1,
					'embed_limit' => 0,
					'file_size'   => '',
				),
			),
		);
	}

	/**
	 * Set the taxonomy terms.
	 */
	public function set_terms() {
		$term_id = appthemes_maybe_insert_term( __( 'Printable Coupon', APP_TD ), APP_TAX_TYPE, array( 'printable-coupon' ) );
		wp_set_object_terms( $this->get_page_id(), $term_id, APP_TAX_TYPE );
	}
}
