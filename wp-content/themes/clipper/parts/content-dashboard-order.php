<?php
/**
 * Dashboard Coupon Listings loop content.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 1.6.0
 */
global $i;

$order = appthemes_get_order( $post->ID );

$listing_id = _clpr_get_order_coupon_id( $order );
$listing = $listing_id ? get_post( $listing_id ) : false;
$listing_link = $listing ? html( 'a', array( 'href' => get_permalink( $listing_id ) ), get_the_title( $listing_id ) ) : '';
?>

<tr class="<?php echo $order->get_status(); ?>">

	<td class="order-id">
		#<?php echo $order->get_id(); ?>
	</td>

	<td class="order-summary">
		<?php echo $listing_link; ?>
		<?php clpr_display_ordered_items( $order ); ?>
		<span class="clock"><span><?php echo appthemes_display_date( $post->post_date, 'date' ); ?></span></span>
	</td>

	<td class="order-price">
		<?php appthemes_display_price( $order->get_total() ); ?>
	</td>

	<td class="order-status">
		<span class="order-gateway"><?php echo clpr_get_order_gateway_name( $order ); ?></span>
		<span class="order-status"><?php echo $order->get_display_status(); ?></span>
	</td>

	<td class="order-options">
		<?php clpr_dashboard_order_actions( $post->ID ); ?>
	</td>

</tr>
