<?php
/**
 * Admin Single Listing Plan Metabox
 *
 * @package Listing\Admin\Metaboxes
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Plan meta box.
 */
class APP_Listing_Plan_Metabox extends APP_Meta_Box {

	/**
	 * Current Listing module object.
	 *
	 * @var APP_Listing
	 */
	protected $listing;

	/**
	 * Construct Listing Pricing metabox
	 *
	 * @param APP_Listing $listing Listing module object.
	 * @param string      $title   Metabox title.
	 */
	public function __construct( APP_Listing $listing, $title = '' ) {

		$this->listing = $listing;

		if ( ! $title ) {
			$title = __( 'Listing Plan Details', APP_TD );
		}

		$ptype = $this->listing->get_type();

		parent::__construct( "{$ptype}-plan", $title, $ptype, 'normal', 'high' );
	}

	/**
	 * Enqueues admin scripts.
	 */
	public function admin_enqueue_scripts() {
		if ( is_admin() ) {
			wp_enqueue_style( 'jquery-ui-style' );
			wp_enqueue_style( 'wp-jquery-ui-datepicker', APP_FRAMEWORK_URI . '/styles/datepicker/datepicker.css' );

			wp_enqueue_script(
				'pay-pricing-metabox',
				APP_LISTING_URI . '/parts/plans/admin/scripts/pricing.js',
				array( 'jquery-ui-datepicker-lang', 'jquery-ui-datepicker' ),
				APP_LISTING_VERSION,
				true
			);

			wp_localize_script( 'pay-pricing-metabox', 'payLabels', array(
				'Never'      => __( 'Never', APP_TD ),
				'dateFormat' => _x( 'mm/dd/yy', 'Datepicker default date format, see: http://goo.gl/6MWmLK', APP_TD ),
			) );

			$form_fields = parent::form_fields();
			$addons = array();

			foreach ( $form_fields as$field ) {
				$key = $field['flag_key'];
				$addons[ $field['flag_key'] ] = $field;
			}

			wp_localize_script( 'pay-pricing-metabox', 'payAddons', $addons );
		}
	}

	/**
	 * Retrieves listing info to use as addon data.
	 *
	 * @return array An array of listing info
	 */
	protected function get_listing_info() {
		return array(
			'_blank' => array(
				'title'           => __( 'Listing', APP_TD ),
				'flag_key'        => '_blank',
				'duration_key'    => $this->listing->options->duration_key,
				'duration'        => $this->listing->options->duration,
				'start_date_key'  => 'listing_start_date',
				'expire_date_key' => '_blank',
			),
		);
	}

	/**
	 * Modifies form data before display.
	 *
	 * @param array   $form_data Form data.
	 * @param WP_Post $post      Processed listing.
	 *
	 * @return type
	 */
	public function before_display( $form_data, $post ) {
		$form_data['listing_start_date'] = $post->post_date;
		return $form_data;
	}

	/**
	 * Display some extra HTML before the form.
	 *
	 * @param WP_Post $post Processed listing.
	 */
	public function before_form( $post ) {
		echo html( 'p', __( 'These settings allow you to override the defaults that have been applied to the listings based on the plan the owner chose. They will apply until the listing expires.', APP_TD ) );
	}

	/**
	 * Returns an array of form fields.
	 *
	 * @return array
	 */
	public function form_fields() {
		$fields = parent::form_fields();

		$output = array();

		foreach ( $fields as $field ) {
			$addon_field = $this->addon_fields( (object) $field );
			$output      = array_merge( $output, $addon_field );
		}

		return $output;
	}

	/**
	 * Retrieves form fields
	 *
	 * @return array An array of form fields
	 */
	public function form() {
		$listing = $this->get_listing_info();

		return array( $listing['_blank'] );
	}

	/**
	 * Retrieves addon fields set.
	 *
	 * @param object $addon Addon object.
	 *
	 * @return array An array of addon fields
	 */
	protected function addon_fields( $addon ) {

		$duration = ( isset( $addon->duration ) ) ? $addon->duration : 0 ;

		return array(
			array(
				'title' => $addon->title,
				'type' => 'checkbox',
				'name' => $addon->flag_key,
				'desc' => __( 'Yes', APP_TD ),
				'extra' => array(
					'id' => $addon->flag_key,
					'class' => 'enable-addon',
				),
			),
			array(
				'title' => __( 'Duration', APP_TD ),
				'desc' => __( 'days (0 = Infinite)', APP_TD ),
				'type' => 'text',
				'name' => $addon->duration_key,
				'default' => $duration,
				'extra' => array(
					'class' => 'small-text',
				),
			),
			array(
				'title' => __( 'Start Date', APP_TD ),
				'type' => 'custom',
				'name' => $addon->start_date_key,
				'render' => array( 'APP_Date_Field_Type', '_render' ),
				'sanitize' => array( 'APP_Date_Field_Type', '_sanitize' ),
			),
			array(
				'title' => __( 'Expires on', APP_TD ),
				'type' => 'custom',
				'name' => ! empty( $addon->expire_date_key ) ? $addon->expire_date_key : '_blank',
				'render' => array( 'APP_Date_Field_Type', '_render' ),
				'sanitize' => array( 'APP_Date_Field_Type', '_sanitize' ),
				'extra' => array(
					'id' => '_blank_expire_' . $addon->flag_key,
				),
			),
		);
	}

	/**
	 * Filter data before save.
	 *
	 * @param array $data    An array of posted data.
	 * @param int   $post_id Listing ID.
	 *
	 * @return array An array of filtered data
	 */
	function before_save( $data, $post_id ) {

		unset( $data['_blank'] );
		unset( $data['listing_start_date'] );

		$data = apply_filters( "appthemes_{$this->box_id}_before_save", $data, $post_id );

		return $data;
	}
}
