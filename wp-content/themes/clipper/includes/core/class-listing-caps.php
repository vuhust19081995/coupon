<?php
/**
 * Listing Capabilities submodule
 *
 * @package Listing\Caps
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Mapping meta capabilities
 */
class APP_Listing_Caps {

	/**
	 * Current Listing module object.
	 *
	 * @var APP_Listing
	 */
	protected $listing;

	/**
	 * Constructs meta caps mapper
	 *
	 * @param APP_Listing $listing Current Listing module object.
	 */
	public function __construct( APP_Listing $listing ) {

		$this->listing = $listing;

		add_filter( 'map_meta_cap', array( $this, 'map_meta_cap' ), 99, 4 );
	}

	/**
	 * Filter a user's capabilities depending on specific context and/or privilege.
	 *
	 * @param array  $caps    Returns the user's actual capabilities.
	 * @param string $cap     Capability name.
	 * @param int    $user_id The user ID.
	 * @param array  $args    Adds the context to the cap. Typically the object ID.
	 *
	 * @return array Mapped caps
	 */
	public function map_meta_cap ( $caps, $cap, $user_id, $args ) {
		switch ( $cap ) {

			case 'edit_post':

				$post = get_post( $args[0] );
				if ( empty( $post ) || $this->listing->get_type() !== $post->post_type ) {
					break;
				}

				$post_type = get_post_type_object( $post->post_type );

				if ( $this->listing->options->allow_edit || get_post_meta( $post->ID, 'app_is_renewal', true ) ) {
					$caps = array_diff( $caps, array( $post_type->cap->edit_published_posts ) );
				}

				if ( defined( 'APP_POST_STATUS_EXPIRED' ) && APP_POST_STATUS_EXPIRED === $post->post_status && ! $this->listing->options->allow_renew ) {
					$caps = array_merge( $caps, array( $post_type->cap->edit_others_posts ) );
				}

				break;

			case 'upload_files':
			case 'upload_media':

				if ( isset( $_REQUEST['post_id'] ) && absint( $_REQUEST['post_id'] ) ) { // Input var okay.

					$post = get_post( absint( $_REQUEST['post_id'] ) ); // Input var okay.
					if ( empty( $post ) || $this->listing->get_type() !== $post->post_type ) {
						break;
					}

					$post_type = get_post_type_object( $post->post_type );

					if ( user_can( $user_id, $post_type->cap->edit_post, $post->ID ) ) {
						$caps = array( 'exist' );
					}

				} else {
					if ( user_can( $user_id, 'edit_posts' ) ) {
						$caps = array( 'exist' );
					}
				}

				break;
		}
		return $caps;
	}
}
