<?php
/**
 * Template part for displaying Addons.
 *
 * @package Listing\Templates
 * @author  AppThemes
 * @since   Listing 1.0
 */

/* @var $addon APP_Listing_Addon_I */
?>

<div class="addon-featured-wrap">
<h6><?php _e( 'Featured', APP_TD ); ?></h6>
<?php foreach ( $addons as $addon ) : ?>
	<label>
		<?php echo $addon->get_purchase_option(); ?>
	</label>
<?php endforeach; ?>
</div><!-- .addon-featured-wrap -->
