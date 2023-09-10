<?php
/**
 * Listing Post Plan.
 *
 * @package Listing\Modules\Plan
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * A Listing Post Plan class.
 *
 * Uses WP_Post as referenced object. Retrieves Plan properties from the Post
 * meta fields.
 */
class APP_Listing_Post_Plan extends APP_Listing_General_Plan {

	/**
	 * Retrieves the referenced object.
	 *
	 * @return mixed
	 */
	protected function get_ref_object() {
		return get_post( $this->ref );
	}

	/**
	 * Retrieves a plan title.
	 *
	 * @return string Plan title.
	 */
	public function get_title() {
		return get_the_title( $this->ref );
	}

	/**
	 * Magic method: $plan->arg
	 *
	 * @param string $key The plan property.
	 *
	 * @return mixed
	 */
	public function __get( $key ) {
		return get_post_meta( $this->ref, $key, true );
	}

}
