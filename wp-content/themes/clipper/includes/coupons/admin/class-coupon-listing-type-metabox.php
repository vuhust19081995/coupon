<?php
/**
 * Coupon Listing Type meta box.
 *
 * @package Clipper\Coupons
 * @author  AppThemes
 * @since   2.0.0
 */

/**
 * Coupon Listing Type meta box class.
 */
class CLPR_Coupon_Listing_Type_Metabox extends APP_Listing_Custom_Forms_Metabox {

	/**
	 * The error object.
	 *
	 * @var WP_Error
	 */
	protected $errors;

	/**
	 * The Coupon Type Fields metabox.
	 *
	 * @var APP_Listing_Custom_Forms_Metabox
	 */
	protected $fields_box;

	/**
	 * Constructs the listing custom fields meta box.
	 *
	 * @param APP_Listing $listing  Listing module object.
	 * @param string      $taxonomy Taxonomy that will be used for custom forms
	 *                              items query.
	 * @param string      $title    Meta box title.
	 */
	public function __construct( APP_Listing $listing, $taxonomy, $title = '' ) {
		parent::__construct( $listing, $taxonomy, $title );

		$this->fields_box = $this->listing->coupon_type_form_fields_box;

		add_action( "wp_ajax_app-render-tax_input[$taxonomy][]-type-form", array( $this, 'ajax_render_form' ) );
		add_action( "wp_ajax_nopriv_app-render-tax_input[$taxonomy][]-type-form", array( $this, 'ajax_render_form' ) );
	}

	/**
	 * Retrieves all the valid fields for the meta box (excludes internal fields
	 * and others set by the include/exclude filters).
	 *
	 * @return array List of valid fields.
	 */
	protected function _get_form_valid_fields() {

		$include = array(
			'name' => 'tax_input[' . $this->taxonomy . ']',
		);
		$valid_fields = $this->listing->form->get_form_fields( array(), $include, $this->get_post_id() );
		foreach ( $valid_fields as &$type_field ) {
			$type_field['props']['ul_class'] = 'app-coupon-type-fields';
			// Fix issue https://github.com/AppThemes/Clipper/issues/784 .
			$type_field['props']['field_type'] = 'checkbox';
			$type_field['sanitize'] = 'wp_kses_post_deep';
		}

		return $valid_fields;
	}

	/**
	 * Filter data before display.
	 *
	 * @param array   $form_data The form data.
	 * @param WP_Post $post      Current post object.
	 *
	 * @return array
	 */
	public function before_display( $form_data, $post ) {
		$form_data = parent::before_display( $form_data, $post );
		$form_data['tax_input'][ $this->taxonomy ] = wp_list_pluck( wp_get_object_terms( $post->ID, $this->taxonomy ), 'name', 'term_id' );

		return $form_data;
	}

	/**
	 * Enqueue scripts.
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_script(
			'app-category-fields',
			APP_LISTING_URI . '/extra/custom-forms/scripts/jquery.app.categoryfields.js',
			array( 'jquery' ),
			APP_LISTING_VERSION,
			true
		);

		wp_localize_script( 'app-category-fields', 'appCategoryFieldsL10n', array(
			'nonce' => wp_create_nonce( 'app-render-tax_input-form' ),
		) );

		appthemes_enqueue_media_manager( array( 'post_id' => $this->get_post_id() ) );
	}

	/**
	 * Displays extra HTML before the form.
	 *
	 * @param WP_Post $post Current post.
	 *
	 * @return void
	 */
	public function before_form( $post ) {
?>
		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready(function($) {
				$( '.app-coupon-type-fields' ).appCategoryFields( {
					listing_id   : $( 'input[name="post_ID"]' ).val(),
					addContainer : function( control, container ) {
						control.closest( '.form-table' ).after( container );
					},
					getActionType: function( name ) {
						return 'app-render-' + name + '-type-form';
					}
				} );
			});
			//]]>
		</script>
<?php
	}

	/**
	 * Filter data before save.
	 *
	 * @param array $post_data Posted data.
	 * @param int   $post_id   Given post ID.
	 *
	 * @return array
	 */
	protected function before_save( $post_data, $post_id ) {
		// We must to remove tax_input field from the list to update since it
		// will try to save tax_input data as meta field.
		// But also we need to ensure that posted data is valid.
		// So if it's invalid we can freely return the $post_data so it process
		// save() method correctly and return errors in the usual metabox way.
		// Otherwise return empty error for early exit from the save() method.
		$this->errors = $this->listing->form->validate_post_data( $this->form_fields(), $post_data );

		if ( ! $this->errors instanceof WP_Error || ! $this->errors->get_error_codes() ) {
			$post_data = array();
			$this->fields_box->save( $post_id );
		}

		return $post_data;
	}

	/**
	 * Look for existing errors after each field is processed.
	 *
	 * @param array $post_data The item posted data.
	 * @param int   $post_id   The item ID.
	 */
	protected function validate_post_data( $post_data, $post_id ) {
		if ( $this->errors instanceof WP_Error && $this->errors->get_error_codes() ) {
			$post_data = $this->errors;
		}
		return $post_data;
	}

	/**
	 * Generates form by ajax request.
	 */
	public function ajax_render_form() {
		check_ajax_referer( 'app-render-tax_input-form', 'security' );
		$this->fields_box->display( get_post( $this->get_post_id() ) );
		die;
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
}
