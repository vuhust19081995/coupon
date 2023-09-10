<?php
/**
 * Coupon Listing Type Fields meta box.
 *
 * @package Clipper\Coupons
 * @author  AppThemes
 * @since   2.0.0
 */

/**
 * Coupon Listing Type Fields meta box class.
 */
class CLPR_Coupon_Listing_Type_Fields_Metabox extends APP_Listing_Custom_Forms_Metabox {

	/**
	 * Additional checks before registering the metabox.
	 *
	 * @return bool
	 */
	protected function condition() {
		return false;
	}

	/**
	 * Retrieves the terms associated with given post.
	 *
	 * @param int $post_id Given post ID.
	 * @return type
	 */
	function get_the_terms( $post_id = 0 ) {

		$args = stripslashes_deep( $_POST );

		if ( ! isset( $args['cats'] ) ) {
			return parent::get_the_terms( $post_id );
		}

		$cats  = $args['cats'];
		$terms = array();
		foreach ( $cats as $term_id ) {
			$terms[ $term_id ] = get_term( $term_id, $this->taxonomy );
		}

		return $terms;
	}

	/**
	 * Returns current post ID.
	 *
	 * @return int
	 */
	public function get_post_id() {
		$post_id = parent::get_post_id();

		if ( ! $post_id && ! empty( $_POST['listing_id'] ) ) {
			$post_id = absint( $_POST['listing_id'] );
		}

		return $post_id;
	}

	/**
	 * Filter data before display.
	 *
	 * @param array   $form_data Form data.
	 * @param WP_Post $post      Post object.
	 *
	 * @return array
	 */
	public function before_display( $form_data, $post ) {
		if ( empty( $form_data ) ) {
			$form_data = get_post_custom( $post->ID );
			foreach ( $form_data as $key => $values ) {
				$form_data[ $key ] = maybe_unserialize( $form_data[ $key ][0] );
			}
		}
		return $form_data;
	}

	/**
	 * Saves media data.
	 *
	 * @param int $post_id Post ID.
	 *
	 * @return void
	 */
	protected function save( $post_id ) {
		parent::save( $post_id );

		appthemes_handle_media_upload( $post_id );
	}
}
