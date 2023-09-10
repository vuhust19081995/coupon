<?php
/**
 * Template for displaying Select Plan Form.
 *
 * @package Listing\Templates
 * @author  AppThemes
 * @since   Listing 1.0
 */

/* @var $app_listing APP_Listing */
/* @var $plan APP_Listing_Plan_I */
?>

<form id="<?php echo esc_attr( "{$nonce_check}-{$action_check}" );?>" class="app-form <?php echo esc_attr( $form_class );?>" enctype="multipart/form-data" method="post" action="<?php echo esc_attr( $action_url ); ?>">

	<?php
	wp_nonce_field( $nonce_check, $action_check );
	?>

	<fieldset class="fieldset">

		<?php
		if ( ! empty( $plans ) ) {
		?>

			<table class="listing-plans">
			<tbody>

				<?php
				foreach ( $plans as $key => $plan ) :
				?>

					<tr class="plan-option" id="plan-<?php esc_attr_e( $plan->get_type() ); ?>">

						<?php if ( 1 < count( $plans ) ) { ?>
						<td class="plan-radio">
							<input type="radio" name="plan" <?php echo (0 === $key) ? 'checked="checked"' : ''; ?> value="<?php esc_attr_e( $plan->get_type() ); ?>" id="plan-<?php esc_attr_e( $plan->get_type() ); ?>">
						</td>
						<?php } else { ?>
							<input type="hidden" name="plan" value="<?php esc_attr_e( $plan->get_type() ); ?>"/>
						<?php } ?>

						<td class="plan-details">

							<h4>
								<?php
								echo $plan->get_title();
								?>
							</h4>

							<h5>
								<span class="plan-price">
									<?php
									appthemes_display_price( $plan->get_price() );
									?>
								</span>

								<span class="plan-duration">
									<?php
									echo $plan->get_period_text();
									?>
								</span>
							</h5>

							<div class="plan-description">
								<?php
								echo $plan->get_description();
								?>
							</div>

							<div class="plan-info">
								<?php
								$plan->render();

								do_action( 'appthemes_purchase_plan_fields', $plan );
								?>

								<!-- OTHER INFO HERE -->
							</div>
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

		} else { ?>
			<p><?php _e( 'No plans are available.', APP_TD ); ?></p>
		<?php } ?>

	</fieldset>

	<fieldset>
		<div class="form-field">
			<button class="button large success" type="submit">
				<?php
				echo esc_html( $action_text );
				?>
			</button>
		</div>
	</fieldset>
</form>
