<?php
/**
 * Dashboard coupon listings loop content.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   Clipper 1.6.0
 */
global $i;

$status = clpr_get_listing_status_name( $post->ID );
$expire_date = clpr_get_expire_date( $post->ID, 'display' );
?>

<tr class="<?php echo $status; ?>">

	<td class="listing-count show-for-large"><?php echo $i; ?>.</td>

	<td class="listing-title">
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

		<div class="meta">
			<span class="folder"><?php echo get_the_term_list( $post->ID, APP_TAX_CAT, '', ', ', '' ); ?></span> |
			<span class="clock"><span><?php echo appthemes_display_date( $post->post_date, 'date' ); ?></span></span>
		</div>
	</td>

	<?php if ( current_theme_supports( 'app-stats' ) ) { ?>
		<td class="listing-views">
			<?php echo appthemes_get_stats_by( $post->ID, 'total' ); ?>
		</td>
	<?php } ?>

	<td class="listing-status">
		<span class="status"><?php echo clpr_get_status_i18n( $status ); ?></span>
		<?php if ( $expire_date && in_array( $status, array( 'live', 'live_unreliable', 'live_expired', 'ended' ) ) ) { ?>
			<p class="small">(<?php echo $expire_date; ?>)</p>
		<?php } ?>
	</td>

	<td class="listing-options">
		<?php clpr_dashboard_listing_actions( $post->ID ); ?>
	</td>

</tr>
