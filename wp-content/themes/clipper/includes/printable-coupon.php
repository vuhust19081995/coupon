<?php
/**
 * Printable Coupon functions.
 *
 * @package Clipper\Printable-Coupon
 * @author  AppThemes
 * @since   Clipper 1.6
 */


/**
 * Returns the printable coupon image associated to the coupon listing.
 *
 * @param int $post_id Post ID.
 * @param string $size (optional)
 * @param string $return (optional)
 *
 * @return string|int|bool An image url, html link, or image ID. Boolean False if image not found.
 */
function clpr_get_printable_coupon( $post_id, $size = 'thumb-large', $return = 'html' ) {
	// go see if any images are associated with the coupon and grab the first one
	$args = array(
		'post_parent' => $post_id,
		'post_status' => 'inherit',
		'post_type' => 'attachment',
		//'post_mime_type' => 'image',
		APP_TAX_IMAGE => 'printable-coupon',
		'numberposts' => 1,
		'order' => 'ASC',
		'orderby' => 'ID',
	);
	$images = get_children( $args );

	if ( $images ) {

		// Move over bacon.
		$image = array_shift( $images );

		// Get the coupon image.
		$couponimg = wp_get_attachment_image( $image->ID, $size );

		if ( $couponimg ) {
			// Grab the large image for onclick.
			$img_large_url_raw = wp_get_attachment_image_url( $image->ID, 'large' );
		} else {
			$img_large_url_raw = wp_get_attachment_url( $image->ID );
		}

		if ( 'url' === $return ) {
			return $img_large_url_raw;
		} elseif ( 'id' === $return ) {
			return $image->ID;
		} elseif ( $couponimg ) {
			return '<a href="' . $img_large_url_raw . '" target="_blank" title="' . the_title_attribute( 'echo=0' ) . '" class="preview" rel="' . $img_large_url_raw . '">' . $couponimg . '</a>';
		}

	// if no image found, try to find in meta (coupons from importer)
	} else {
		$image_url = get_post_meta( $post_id, 'clpr_print_url', true );
		if ( ! empty( $image_url ) ) {
			if ( $size == 'thumb-med' ) {
				$size_out = 'width="75" height="75" class="attachment-thumb-med"';
			} else {
				$size_out = 'width="180" height="180" class="attachment-thumb-large"';
			}

			if ( $return == 'url' ) {
				return $image_url;
			} elseif( $return == 'id' ) {
				return 'postmeta';
			} else {
				$post = get_post( $post_id );
				return '<a href="' . $image_url . '" target="_blank" title="' . $post->post_title . '" class="preview" rel="' . $image_url . '"><img ' . $size_out . ' title="' . $post->post_title . '" alt="' . $post->post_title . '" src="' . $image_url . '" /></a>';
			}
		}
	}

	return false;
}
