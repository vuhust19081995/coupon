<?php
/**
 * CSV Coupons Importer.
 *
 * @package Clipper\Admin\Importer
 * @author  AppThemes
 * @since   Clipper 1.2
 */

/**
 * CSV Coupons Importer.
 */
class CLPR_Importer extends APP_Importer {

	/**
	 * Setups importer.
	 *
	 * @return void
	 */
	function setup() {
		parent::setup();

		$this->args['admin_action_priority'] = 11;
		add_filter( 'appthemes_importer_import_row_data', array( $this, 'prevent_duplicate' ), 10, 1 );
		add_action( 'appthemes_after_import_upload_form', array( $this, 'example_csv_files' ) );

		if ( isset( $_GET['sample'] ) ) {
			$this->_generate_sample();
		}
	}

	protected function _generate_sample() {

		check_admin_referer( 'get-sample-file' );

		$sample_data = $this->get_sample_data();

		$keys = array_keys( $sample_data );
		$vals = array_values( $sample_data );

		$string = $this->array2csv( $keys ) . $this->array2csv( $vals );

		header( 'Content-type: application/x-msdownload', true, 200 );
		header( 'Content-Disposition: attachment; filename=listings.csv' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );
		echo $string;
		exit();
	}

	public function get_sample_data() {
		$data = array(
			'coupon_title'       => '10% Off Amazon',
			'coupon_description' => '<p>Great coupon from Amazon.com that gives 10% off any purchase. Can be used multiple times so make sure to take advantage of this deal often.</p><p>This is the default coupon created when Clipper is first installed. It is for demonstration purposes only and is not actually a 10% off Amazon.com coupon.</p>',
			'coupon_excerpt'     => '',
			'coupon_status'      => 'publish',
			'author'             => 1,
			'date'               => current_time( 'mysql' ),
			'slug'               => '10-off-amazon',
			'coupon_code'        => 'AMAZON10',
			'expire_date'        => date( 'Y-m-d', current_time( 'timestamp' ) + YEAR_IN_SECONDS ),
			'print_url'          => '',
			'id'                 => '5534f940d81c5f8e',
			'coupon_aff_url'     => 'http://www.amazon.com/?tag=20-ebt',
			'coupon_category'    => 'Electronics',
			'coupon_tag'         => 'Books,Electronics,Online Store',
			'coupon_type'        => __( 'Coupon Code', APP_TD ),
			'stores'             => 'Amazon.com',
			'store_desc'         => "Amazon is the world's largest online retailer. The company also produces consumer electronics, and is a major provider of cloud computing services.",
			'store_url'          => 'http://www.amazon.com/',
			'store_aff_url'      => 'http://www.amazon.com/?rstore=123',
			'clpr_featured'      => 1,
		);
		return $data;
	}

	/**
	 * Formats a line (passed as a fields  array) as CSV and returns the CSV as a string.
	 */
	protected function array2csv( array &$fields, $delimiter = ',', $enclosure = '"', $enclose_all = false ) {
		$delimiter_esc	 = preg_quote( $delimiter, '/' );
		$enclosure_esc	 = preg_quote( $enclosure, '/' );

		$output = array();
		foreach ( $fields as $field ) {
			// Enclose fields containing $delimiter, $enclosure or whitespace.
			if ( $enclose_all || preg_match( "/(?:${delimiter_esc}|${enclosure_esc}|\s)/", $field ) ) {
				$output[] = $enclosure . str_replace( $enclosure, $enclosure . $enclosure, $field ) . $enclosure;
			} else {
				$output[] = $field;
			}
		}

		return implode( $delimiter, $output ) . "\n";
	}

	/**
	 * Prevents duplicate entries while importing.
	 *
	 * @param array $data
	 *
	 * @return array|bool
	 */
	public function prevent_duplicate( $data ) {
		if ( ! empty( $data['post_meta']['clpr_id'] ) ) {
			if ( clpr_get_listing_by_ref( $data['post_meta']['clpr_id'] ) ) {
				return false;
			}
		}

		return $data;
	}

	/**
	 * Displays links to example CSV files on Importer page.
	 *
	 * @return void
	 */
	public function example_csv_files() {
		$download_url = add_query_arg( 'sample', 1 );
		$download_url = wp_nonce_url( $download_url, 'get-sample-file' );
		$link = html( 'a', array( 'href' => $download_url, 'title' => __( 'Download CSV file', APP_TD ) ), __( 'Coupons', APP_TD ) );

		echo html( 'p', sprintf( __( 'Download example CSV file: %s', APP_TD ), $link ) );
	}

}


/**
 * Setups CSV importer.
 *
 * @return void
 */
function clpr_csv_importer() {
	$fields = array(
		'coupon_title'       => 'post_title',
		'coupon_description' => 'post_content',
		'coupon_excerpt'     => 'post_excerpt',
		'coupon_status'      => 'post_status',
		'author'             => 'post_author',
		'date'               => 'post_date',
		'slug'               => 'post_name',
	);

	$args = array(
		'taxonomies'     => array( 'coupon_category', 'coupon_tag', 'coupon_type', 'stores' ),

		'custom_fields'  => array(
			'coupon_code'        => 'clpr_coupon_code',
			'expire_date'        => 'clpr_expire_date',
			'print_url'          => 'clpr_print_url',
			'id'                 => 'clpr_id',
			'coupon_aff_url'     => 'clpr_coupon_aff_url',
			CLPR_ITEM_FEATURED   => CLPR_ITEM_FEATURED,
			'clpr_votes_down'    => array( 'default' => '0' ),
			'clpr_votes_up'      => array( 'default' => '0' ),
			'clpr_votes_percent' => array( 'default' => '100' ),
		),

		'tax_meta' => array(
			'stores' => array(
				'store_aff_url' => 'clpr_store_aff_url',
				'store_url'     => 'clpr_store_url',
				'store_desc'    => 'clpr_store_desc',
			),
		),
	);

	$args = apply_filters( 'clpr_csv_importer_args', $args );

	appthemes_add_instance( array( 'CLPR_Importer' => array( 'coupon', $fields, $args ) ) );
}
add_action( 'wp_loaded', 'clpr_csv_importer' );

