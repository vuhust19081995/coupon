<?php
/**
 * Coupon search form template.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 2.0.0
 */

 $search_query = '';

 // If search is for coupons, fill in the search query value.
 // This is to prevent the blog search from populating this.
 if ( isset( $_GET[ 's' ] ) && get_post_type() == APP_POST_TYPE ) {
 	$search_query = get_search_query( false );
 }
?>

<div class="search-box">

	<form method="get" class="search" action="<?php echo home_url( '/' ); ?>" role="search">

		<div class="input-group">

			<input type="search" id="search-field" class="input-group-field" id="s" name="s" value="<?php echo esc_attr( $search_query ); ?>" placeholder="<?php echo esc_attr_x( 'Search Coupons', 'placeholder', APP_TD ); ?>" />

			<div class="input-group-button">
				<button type="submit" class="button"><i class="fa fa-search" aria-hidden="true"></i></button>
			</div>

		</div>

		<input type="hidden" name="post_type" value="<?php echo esc_attr( APP_POST_TYPE ); ?>">

	</form>

</div> <!-- .search-box -->
