<?php
/**
 * Search Index module load.
 *
 * @package Listing\Modules\SearchIndex
 * @author  AppThemes
 * @since   Listing 1.0
 */

// @codeCoverageIgnoreStart
// Module files.
if ( class_exists( 'APP_Listing_Autoload' ) ) {
	APP_Listing_Autoload::add_class_map( array(
		'APP_Listing_Search_Index' => dirname( __FILE__ ) . '/class-listing-search-index.php',
	) );
}
