<?php
/**
 * Claim listing module load.
 *
 * @package Listing\Modules\Claim
 * @author  AppThemes
 * @since   Listing 1.0
 */

// @codeCoverageIgnoreStart
// Module files.
if ( class_exists( 'APP_Listing_Autoload' ) ) {
	APP_Listing_Autoload::add_class_map( array(
		'APP_Listing_Claim_Importer'           => dirname( __FILE__ ) . '/admin/class-listing-claim-importer.php',
		'APP_Listing_Claim_Moderation_Metabox' => dirname( __FILE__ ) . '/admin/class-listing-claim-moderation-metabox.php',
		'APP_Listing_Claim_Settings'           => dirname( __FILE__ ) . '/admin/class-listing-claim-settings.php',
		'APP_Listing_Claimable_Metabox'        => dirname( __FILE__ ) . '/admin/class-listing-claimable-metabox.php',
		'APP_Listing_Claim_Emails'             => dirname( __FILE__ ) . '/class-listing-claim-emails.php',
		'APP_Listing_Claim'                    => dirname( __FILE__ ) . '/class-listing-claim.php',
		'APP_View_Process_Claim'               => dirname( __FILE__ ) . '/class-listing-process-claim.php',
		'APP_Step_Activate_Claim'              => dirname( __FILE__ ) . '/class-listing-step-activate-claim.php',
		'APP_Step_Moderate_Claim'              => dirname( __FILE__ ) . '/class-listing-step-moderate-claim.php',
	) );
}
