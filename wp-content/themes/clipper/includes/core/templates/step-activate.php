<?php
/**
 * Template for displaying Summary step.
 *
 * @package Listing\Templates
 * @author  AppThemes
 * @since   Listing 1.0
 */

if ( isset( $app_order ) ) {

	the_order_summary( $app_order->get_id() );
	?>

	<p>
		<?php
		esc_html_e( 'Your order has been completed.', APP_TD );
		?>
	</p>

<?php
}

$main_item = get_post( appthemes_get_checkout()->get_data( 'listing_id' ) );
?>

<a href="<?php echo esc_url( get_permalink( $main_item->ID ) ); ?>">
	<button class="button large success">
		<?php
		echo esc_html( sprintf( __( 'Continue to %s', APP_TD ), $main_item->post_title ) );
		?>
	</button>
</a>
