<?php
/**
 * WordPress StarStruck Plugin integration.
 *
 * @url https://marketplace.appthemes.com/plugins/starstruck/
 *
 * @package Clipper\Integrations
 * @author AppThemes
 * @since 2.0.7
 */

/**
 * Custom actions and filters to use with plugin.
 */
class CLPR_StarStruck {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'starstruck_jsonld_schema', array( $this, 'jsonld_schema' ), 10, 3 );
	}

	public function jsonld_schema( $output, $data, $type ) {

		if ( 'taxonomy' === $type && is_tax( 'stores' ) ) {
			$term = get_queried_object();

			$output[0]['@type'] = 'Store';
			$output[0]['name']  = $term->name;
			$output[0]['image'] = clpr_get_store_image_url( $term->term_id, 'term_id', 768 );

			$output[0]['aggregateRating']['reviewCount'] = (int) $output[0]['aggregateRating']['reviewCount'];
		}

		return $output;
	}

}
