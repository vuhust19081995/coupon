<?php
/**
 * Listing Form Taxonomy Input field type
 *
 * @package Listing\Form
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Taxonomy Input Field type
 */
class APP_Tax_Input_Field_Type {

	/**
	 * Retrieves field html
	 *
	 * @param mixed          $value Field value.
	 * @param scbCustomField $inst  Field object.
	 *
	 * @return string Generated html
	 */
	public static function _render( $value, $inst ) {
		$type = $inst->props['field_type'];
		$html = '';

		if ( 'text' === $type ) {
			$html = self::_render_textarea( $value, $inst );
		} elseif ( 'select' === $type ) {
			$html = self::_render_dropdown( $value, $inst );
		} else {
			$html = self::_render_checkboxes( $value, $inst );
		}

		return str_replace( scbForms::TOKEN, $html, $inst->wrap );
	}

	/**
	 * Retrieves HTML for textarea field type
	 *
	 * @param mixed          $value Field value.
	 * @param scbCustomField $inst  Field object.
	 *
	 * @return string Generated html
	 */
	protected static function _render_textarea( $value, $inst ) {

		$tax   = get_taxonomy( $inst->props['tax'] );
		$value = (array) $value;

		if ( $tax->hierarchical ) {
			$value = array_keys( $value );
		}

		$comma = _x( ', ', 'term delimiter', APP_TD );
		$value = implode( $comma, $value );
		return html( 'textarea', array(
			'name'  => scbForms::get_name( $inst->name ),
			'rows'  => 3,
			'cols'  => 20,
			'class' => ( $inst->props['required'] ) ? 'required' : '',
			'placeholder' => ( ! empty( $inst->props['placeholder'] ) ) ? $inst->props['placeholder'] : '',
		), $value );
	}

	/**
	 * Retrieves HTML for dropdown field type
	 *
	 * @param mixed          $value Field value.
	 * @param scbCustomField $inst  Field object.
	 *
	 * @return string Generated html
	 */
	protected static function _render_dropdown( $value, $inst ) {

		$tax = get_taxonomy( $inst->props['tax'] );

		if ( $tax->hierarchical ) {
			$value_field = 'term_id';
		} else {
			$value_field = 'name';
		}

		$args = array(
			'hide_empty'   => 0,
			'echo'         => 0,
			'selected'     => ! empty( $value ) ? key( $value ) : 0,
			'hierarchical' => 1,
			'name'         => scbForms::get_name( $inst->name ),
			'taxonomy'     => $inst->props['tax'],
			'value_field'  => $value_field,
			'orderby'      => 'name',
		);

		$class = array();

		if ( $inst->props['required'] ) {
			$class[] = 'required';
		}

		if ( isset( $inst->props['ul_class'] ) ) {
			$class[] = $inst->props['ul_class'];
		}

		if ( ! empty( $class ) ) {
			$args['class'] = implode( ' ', $class );
		}

		if ( isset( $inst->props['dropdown_options'] ) && is_array( $inst->props['dropdown_options'] ) ) {
			$args = array_merge( $args, $inst->props['dropdown_options'] );
		}

		return wp_dropdown_categories( $args );
	}

	/**
	 * Retrieves HTML for checkbox field type
	 *
	 * @param mixed          $value Field value.
	 * @param scbCustomField $inst  Field object.
	 *
	 * @return string Generated html
	 */
	protected static function _render_checkboxes( $value, $inst ) {
		$tax  = $inst->props['tax'];
		$ul_class = $tax . 'checklist';

		if ( isset( $inst->props['ul_class'] ) ) {
			$ul_class = $inst->props['ul_class'] . ' ' . $ul_class;
		}

		if ( file_exists( ABSPATH . '/wp-admin/includes/class-walker-category-checklist.php' ) ) {
			require_once ABSPATH . '/wp-admin/includes/class-walker-category-checklist.php';
		}

		require_once ABSPATH . '/wp-admin/includes/template.php';
		require_once 'walker-category-checklist-class.php';

		ob_start();

		$walker = new APP_Walker_Category_Checklist;
		$walker->extra = ( isset( $inst->extra ) ) ? $inst->extra : array();
		$walker->item_id = $inst->listing_id;

		wp_terms_checklist( 0, array(
			'taxonomy'      => $tax,
			'selected_cats' => array_keys( (array) $value ),
			'walker'        => $walker,
			'checked_ontop' => false,
		) );

		$html = ob_get_clean();
		$html = html( 'ul', array( 'class' => $ul_class ), $html );

		return $html;
	}

}
