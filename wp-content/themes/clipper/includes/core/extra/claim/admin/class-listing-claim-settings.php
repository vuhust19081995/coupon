<?php
/**
 * Claim Listing submodule settings
 *
 * @package Listing\Modules\Claim\Settings
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Claim Settings class.
 */
class APP_Listing_Claim_Settings extends APP_Listing_Core_Settings {

	/**
	 * Registers settings
	 *
	 * @param APP_Listing_Settings $page Current settings page object.
	 */
	public function register( $page ) {

		$options = $this->listing->options;
		$type    = $this->listing->get_type();

		if ( empty( $page->tab_sections[ $type ]['moderation'] ) ) {
			$page->tab_sections[ $type ]['moderation'] = array(
				'title'  => __( 'Moderation', APP_TD ),
				'fields' => array(),
				'options' => $options,
			);
		}

		$page->tab_sections[ $type ]['moderation']['fields'][] = array(
			'title' => __( 'Claimed Listings', APP_TD ),
			'type'  => 'checkbox',
			'name'  => 'moderate_claimed_listings',
			'desc'  => __( 'Manually approve each new listing claim', APP_TD ),
			'tip'   => __( 'Left unchecked, listing claims are transfered immediately to the requesting claimee.', APP_TD ),
		);

	}
}
