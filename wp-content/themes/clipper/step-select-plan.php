<?php
/**
 * Template for displaying Select Plan Form.
 *
 * @package Clipper\Templates
 * @author  AppThemes
 * @since   2.0.0
 */

/* @var $app_listing APP_Listing */
/* @var $plan APP_Listing_Plan_I */
?>

<?php
if ( empty( $plans ) ) :
	echo '<p>' . __( 'No plans are available.', APP_TD ) . '</p>';
else :
?>

	<form id="<?php echo esc_attr( "{$nonce_check}-{$action_check}" );?>" class="app-form <?php echo esc_attr( $form_class );?>" enctype="multipart/form-data" method="post" action="<?php echo esc_attr( $action_url ); ?>">

		<?php wp_nonce_field( $nonce_check, $action_check ); ?>

		<fieldset>

				<table id="listing-plans" class="table-plain table-listing-plans">
					<tbody>

						<?php foreach ( $plans as $key => $plan ) : ?>

							<tr class="plan-option" id="plan-<?php esc_attr_e( $plan->get_type() ); ?>">

								<?php if ( 1 < count( $plans ) ) { ?>
								<td class="plan-radio">
									<input type="radio" name="plan" <?php echo (0 === $key) ? 'checked="checked"' : ''; ?> value="<?php esc_attr_e( $plan->get_type() ); ?>" id="plan-<?php esc_attr_e( $plan->get_type() ); ?>">
								</td>
								<?php } else { ?>
									<input type="hidden" name="plan" value="<?php esc_attr_e( $plan->get_type() ); ?>"/>
								<?php } ?>

								<td class="plan-details">

									<h2><?php echo $plan->get_title(); ?></h2>

									<div class="plan-description">
										<?php echo $plan->get_description(); ?>
									</div>

									<div class="plan-info">
										<?php do_action( 'appthemes_purchase_plan_fields', $plan ); ?>
									</div>

								</td>

								<td class="plan-costs text-right">
									<p class="h5">
										<span class="plan-price"><?php appthemes_display_price( $plan->get_price() ); ?></span>
										<span class="plan-duration"><?php echo $plan->get_period_text(); ?></span>
									</p>

									<div class="plan-addons">

										<?php if ( $plan->has_options() ) : ?>

											<button class="small dropdown button" type="button" data-toggle="plan-<?php esc_attr_e( $plan->get_type() ); ?>-addon-dropdown"><?php _e( 'Extras', APP_TD ); ?></button>
											<div class="dropdown-pane" id="plan-<?php esc_attr_e( $plan->get_type() ); ?>-addon-dropdown" data-dropdown data-auto-focus="true" data-close-on-click="true">
												<?php $plan->render(); ?>
											</div><!-- .dropdown-pane -->

										<?php endif; ?>

									</div><!-- .plan-addons-->

								</td>

							</tr>

						<?php endforeach; ?>

					</tbody>
				</table>

			<?php
			if ( $app_listing->options->charge ) {
				do_action( 'appthemes_purchase_fields' );
			}

			do_action( 'appthemes_after_plans_list' );
			?>

		</fieldset>

		<fieldset>
			<div class="form-field">
				<button class="button expanded coupon" type="submit">
					<?php
					echo esc_html( $action_text );
					?>
				</button>
			</div>
		</fieldset>
	</form>

<?php endif; ?>
