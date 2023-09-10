<?php
/**
 * Edit profile content template.
 *
 * @package Clipper\Templates
 * @since 2.0.0
 */

$current_user = wp_get_current_user();
?>

<div class="content-box">

	<div class="box-holder">

		<div class="blog">

			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

			<div class="text-box">

				<?php do_action( 'appthemes_notices' ); ?>

				<?php the_content(); ?>

			</div>

			<form id="profileForm" method="post" class="profileForm" name="profile" action="">

				<?php wp_nonce_field( 'app-edit-profile' ); ?>

				<?php
				/**
				 * Fires before the 'Username' field on the 'Edit Your Profile' page.
				 *
				 * @since 1.0.0
				 *
				 * @param WP_User $current_user The current WP_User object.
				 */
				do_action( 'profile_personal_options', $current_user );
				?>

				<?php appthemes_listing_form( $current_user, array(), 'user' ); ?>

				<?php
				$show_password_fields = apply_filters( 'show_password_fields', true );
				if ( $show_password_fields ) :
				?>
				<div class="user-pass1-wrap manage-password">

					<div class="row">
						<div class="small-3 columns">
							<label for="pass1" class="text-right middle"><?php _e( 'New Password', APP_TD ); ?></label>
						</div>
						<div class="small-9 columns">
							<button type="button" class="button wp-generate-pw hide-if-no-js"><?php _e( 'Generate Password', APP_TD ); ?></button>

							<span class="wp-pwd hide-if-js">
								<input type="password" name="pass1" id="pass1" value="" autocomplete="off" data-pw="<?php echo esc_attr( wp_generate_password( 24 ) ); ?>" aria-describedby="pass-strength-result" />
								<input type="text" style="display:none" name="pass2" id="pass2" autocomplete="off" />
							</span>
						</div>
					</div>

					<div class="row wp-pwd hide-if-js">
						<div class="small-3 columns">
						</div>
						<div class="small-9 columns">
							<div id="pass-strength-result"><?php _e( 'Strength indicator', APP_TD ); ?></div>
							<p class="help-text"><?php _e( 'Hint: Password should be at least seven characters long.', APP_TD ); ?></p>
						</div>
					</div>

					<div class="row wp-pwd hide-if-js">
						<div class="small-3 columns">
						</div>
						<div class="small-9 columns">
							<button type="button" class="button small wp-hide-pw hide-if-no-js" data-start-masked="<?php echo (int) isset( $_POST['pass1'] ); ?>" data-toggle="0" aria-label="<?php esc_attr_e( 'Hide password' ); ?>">
								<span class="dashicons dashicons-hidden"></span>
								<span class="text"><?php _e( 'Hide', APP_TD ); ?></span>
							</button>
							<button type="button" class="button small wp-cancel-pw hide-if-no-js" data-toggle="0" aria-label="<?php esc_attr_e( 'Cancel password change', APP_TD ); ?>">
								<span class="text"><?php _e( 'Cancel', APP_TD ); ?></span>
							</button>
						</div>
					</div>

				</div><!-- .user-pass1-wrap -->
				<?php endif; ?>

				<?php
				/**
				 * Fires after the 'New Password' field on the 'Edit Your Profile' page.
				 *
				 * @since 1.0.0
				 *
				 * @param WP_User $current_user The current WP_User object.
				 */
				do_action( 'show_user_profile', $current_user );
				?>

				<div class="row">
					<div class="small-3 columns">
					</div>
					<div class="small-9 columns">
						<button type="submit" class="button expanded" id="update-profile" name="submit" value="submit"><?php _e( 'Update Profile', APP_TD ); ?></button>
					</div>
				</div>

				<?php
				// Need to pass in these values otherwise they get blown away in wp-admin/profile.php.
				if ( ! empty( $current_user->rich_editing ) ) { ?>
					<input type="hidden" name="rich_editing" value="<?php esc_attr_e( $current_user->rich_editing ); ?>" />
				<?php } ?>
				<?php if ( ! empty( $current_user->admin_color ) ) { ?>
					<input type="hidden" name="admin_color" value="<?php esc_attr_e( $current_user->admin_color ); ?>" />
				<?php } ?>
				<?php if ( ! empty( $current_user->comment_shortcuts ) ) { ?>
					<input type="hidden" name="comment_shortcuts" value="<?php esc_attr_e( $current_user->comment_shortcuts ); ?>" />
				<?php } ?>
				<input type="hidden" name="admin_bar_front" value="<?php esc_attr_e( get_user_option( 'show_admin_bar_front', $current_user->ID ) ); ?>" />

				<input type="hidden" name="from" value="profile" />
				<input type="hidden" name="action" value="app-edit-profile" />
				<input type="hidden" name="user_id" id="user_id" value="<?php esc_attr_e( $current_user->ID ); ?>" />
				<input type="hidden" name="checkuser_id" value="<?php echo $user_ID; ?>" /> <!-- not needed? -->

			</form>

		</div> <!-- #blog -->

	</div> <!-- #box-holder -->

</div> <!-- #content-box -->
