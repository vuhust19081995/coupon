<?php
/**
 * Listing Form Media field type
 *
 * @package Listing\Form
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Media Manager Field type
 */
class APP_Media_Field_Type {

	/**
	 * Retrieves field html
	 *
	 * @param mixed          $value Field value.
	 * @param scbCustomField $inst  Field object.
	 *
	 * @return string Generated html
	 */
	public static function _render( $value, $inst ) {

		$class = ( isset( $inst->extra ) && isset( $inst->extra['class'] ) ) ? $inst->extra['class'] : '';

		$file_limit  = ( isset( $inst->props['file_limit'] ) ) ? $inst->props['file_limit'] : 1 ;
		$embed_limit = ( isset( $inst->props['embed_limit'] ) ) ? $inst->props['embed_limit'] : 0 ;
		$file_size   = ( isset( $inst->props['file_size'] ) ) ? intval( $inst->props['file_size'] ) * 1024 : wp_max_upload_size();

		ob_start();

		echo '<div class="'. esc_attr( $class ) .'">';

		$listing_module = APP_Listing_Director::get( $inst->listing_type );

		$atts = array(
			'id'     => scbForms::get_name( $inst->name ),
			'object' => $listing_module->meta->get_meta_type(),
		);

		if ( $inst->props['required'] ) {
			$atts['no_media_text'] = __( 'No media added yet', APP_TD ) . ' ' . html( 'input type="hidden" name="' . $inst->name . '_required" class="required_media"', '' );
		}

		appthemes_media_manager(
			$inst->listing_id,
			$atts,
			array(
				'mime_types'  => $inst->extensions,
				'file_limit'  => (int) $file_limit,
				'embed_limit' => (int) $embed_limit,
				'file_size'   => (int) $file_size,
			)
		);

		echo '</div>';

		return str_replace( scbForms::TOKEN, ob_get_clean(), $inst->wrap );
	}

	/**
	 * Retrieves the formatted attachments/embeds list.
	 *
	 * @param mixed          $value Field value.
	 * @param scbCustomField $inst  Field object.
	 *
	 * @return string Generated html
	 */
	public static function _publish( $value, $inst ) {

		$embeds = get_post_meta( $inst->listing_id, $inst->name . '_embeds', true );

		ob_start();

		if ( $value ) {
			appthemes_output_attachments( $value );
		}

		if ( $embeds ) {
			appthemes_output_embeds( $embeds );
		}

		$output = ob_get_clean();

		return APP_Detail_Publisher::_publish( $output, $inst );
	}
}
