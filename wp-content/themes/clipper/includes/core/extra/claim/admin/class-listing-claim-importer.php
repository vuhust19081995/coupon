<?php
/**
 * Listing Claim Importer
 *
 * @package Listing\Modules\Claim
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Claim Importer class
 */
class APP_Listing_Claim_Importer {

	/**
	 * Current Listing module object.
	 *
	 * @var APP_Listing
	 */
	protected $listing;

	/**
	 * Construct module
	 *
	 * @param APP_Listing $listing Listing module object.
	 */
	public function __construct( APP_Listing $listing ) {

		$this->listing = $listing;

		add_action( 'appthemes_after_import_upload_row', array( $this, 'import_form_option' ) );
		add_action( 'appthemes_importer_import_row_post_meta', array( $this, 'import_form_action' ) );
	}

	function import_form_option() {
		if ( empty( $_GET['page'] ) || $_GET['page'] !== 'app-importer-' . $this->listing->get_type() ) {
			return;
		}
		?>
			<tr>
				<th>
					<label><?php _e( 'Claimable', APP_TD ); ?></label>
				</th>
				<td>
					<fieldset>
						<label><input type="checkbox" name="listing_claimable" value="1" /><?php _e( 'Allow listings to be claimable', APP_TD ); ?></label>
					</fieldset>
				<p class="description"><?php _e( 'Claimable listings will have a link that allows users to claim them.', APP_TD ); ?></p>
				</td>
			</tr>
		<?php
	}

	function import_form_action( $post_meta ) {
		if ( ! empty( $_POST['listing_claimable'] ) ) {
			$post_meta['listing_claimable'] = 1;
		}

		return $post_meta;
	}
}