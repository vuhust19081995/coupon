<?php
/**
 * Voting functions.
 *
 * @package Clipper\Voting
 * @author  AppThemes
 * @since   Clipper 1.0
 */


add_action( 'delete_post', 'clpr_delete_post_votes' );


/**
 * Set's up a default votes data for a coupon.
 *
 * @param int $item_id The item ID.
 */
function clpr_set_initial_votes_data( $item_id ) {
	// empty votes from both voting tables
	clpr_delete_post_votes( $item_id, false );

	// update clpr_votes_down and clpr_votes_up to 0 votes
	update_post_meta( $item_id, 'clpr_votes_down', '0' );
	update_post_meta( $item_id, 'clpr_votes_up', '0' );

	// update clpr_votes_percent to 100%
	update_post_meta( $item_id, 'clpr_votes_percent', '100' );
}

/**
 * Deletes post votes for given post ID.
 *
 * @param int $post_id Post ID.
 * @param bool $delete_metadata (optional) Should the post metadata mirrors be deleted.
 *
 * @return bool Whether the votes get deleted.
 */
function clpr_delete_post_votes( $post_id, $delete_metadata = false ) {
	global $wpdb;

	if ( ! $post_id = absint( $post_id ) ) {
		return false;
	}

	$post = get_post( $post_id );

	if ( ! $post || APP_POST_TYPE !== $post->post_status ) {
		return false;
	}

	// delete post votes from both tables
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->clpr_votes WHERE post_id = '%d'", $post_id ) );
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->clpr_votes_total WHERE post_id = '%d'", $post_id ) );

	// delete post metadata mirrors
	if ( $delete_metadata ) {
		delete_post_meta( $post_id, 'clpr_votes_up' );
		delete_post_meta( $post_id, 'clpr_votes_down' );
		delete_post_meta( $post_id, 'clpr_votes_percent' );
	}

	return true;
}


/**
 * Handles voting on coupon.
 * Called via jQuery thumbsVote function.
 *
 * @return void
 */
function clpr_vote_update() {
	global $wpdb;

	// set all the params passed in via the jQuery click
	$post_id  = (int) $_POST['pid'];
	$user_id  = (int) $_POST['uid'];
	$vote_val = (int) $_POST['vid'];

	// get the visitors IP
	$user_ip = appthemes_get_ip();

	// update the votes up/down field depending on value passed in
	$set_vote = ( $vote_val == 1 ) ? 'votes_up' : 'votes_down';

	// first try and update the existing post total counter
	$query = $wpdb->prepare( "UPDATE $wpdb->clpr_votes_total SET votes_total = votes_total+1, $set_vote = $set_vote+1 WHERE post_id = %d LIMIT 1", $post_id );
	$results = $wpdb->query( $query );

	// no results found so let's add a new record for the post
	if ( $results == 0 ) {
		$wpdb->insert( $wpdb->clpr_votes_total, array( 'post_id' => $post_id, $set_vote => 1, 'votes_total' => 1, 'last_update' => current_time( 'mysql' ) ) );
	}


	// now lets update the votes table which contains all vote transactions

	// must be a guest visitor
	if ( $user_id < 1 ) {
		// first try and update the existing guest record based on IP
		$data = array(
			'post_id' => $post_id,
			'vote' => $vote_val,
			'date_stamp' => current_time( 'mysql' ),
		);

		$where = array(
			'user_id' => 0,
			'post_id' => $post_id,
			'ip_address' => $user_ip
		);
		$results = $wpdb->update( $wpdb->clpr_votes, $data, $where );

		// no results found so let's add a new record for the guest
		if ( $results == 0 ) {
			$wpdb->insert( $wpdb->clpr_votes, array( 'post_id' => $post_id, 'user_id' => 0, 'vote' => $vote_val, 'ip_address' => $user_ip, 'date_stamp' => current_time( 'mysql' ) ) );
		}

	} else {

		// first try and update the existing logged in user record
		$data = array(
			'post_id' => $post_id,
			'vote' => $vote_val,
			'date_stamp' => current_time( 'mysql' ),
		);

		$where = array(
			'user_id' => $user_id,
			'post_id' => $post_id,
			'ip_address' => $user_ip
		);
		$results = $wpdb->update( $wpdb->clpr_votes, $data, $where );

		// no results found so let's add a new record for the logged in user
		if ( $results == 0 ) {
			$wpdb->insert( $wpdb->clpr_votes, array( 'post_id' => $post_id, 'user_id' => $user_id, 'vote' => $vote_val, 'ip_address' => $user_ip, 'date_stamp' => current_time( 'mysql' ) ) );
		}
	}


	// now lets get all post ids this visitor or user has voted on already
	// so we can set the transient values in the db

	// must be a guest visitor
	if ( $user_id < 1 ) {
		$query = $wpdb->prepare( "SELECT post_id FROM $wpdb->clpr_votes WHERE user_id = 0 AND ip_address = %s", $user_ip );
		$user_votes = $wpdb->get_col( $query );
	// must be a registered user
	} else {
		$query = $wpdb->prepare( "SELECT post_id FROM $wpdb->clpr_votes WHERE user_id = %d", $user_id );
		$user_votes = $wpdb->get_col( $query );
	}

	$user_votes = array_values( $user_votes );

	// first remove the existing unique transient (if any) just to be safe
	appthemes_delete_visitor_transient( 'visitor_votes' );

	// set the unique transient with results array to expire in 30 days
	appthemes_set_visitor_transient( 'visitor_votes', $user_votes, 30 * DAY_IN_SECONDS );

	// grab the new votes up/down for the post
	$row = $wpdb->get_row( $wpdb->prepare( "SELECT votes_up AS votesup, votes_down AS votesdown, votes_total AS votestotal FROM $wpdb->clpr_votes_total WHERE post_id = %d", $post_id ) );

	// calculate the total successful percentage and round to remove all decimals
	$votes_percent = round( $row->votesup / $row->votestotal * 100 );

	// update/create meta keys on the post so it's easy to call from the loop
	update_post_meta( $post_id, 'clpr_votes_up', $row->votesup );
	update_post_meta( $post_id, 'clpr_votes_down', $row->votesdown );
	update_post_meta( $post_id, 'clpr_votes_percent', $votes_percent );

	// updates coupon status (unreliable/publish)
	clpr_status_update( $post_id );

	echo $votes_percent . '%'; // send back the % result so we can update the coupon % value in real-time
	die; // so it doesn't return an extra zero
}


/**
 * Checks if the visitor or user has already voted.
 *
 * @param int $post_id
 * @param array $transient
 *
 * @return bool
 */
function clpr_vote_check( $post_id, $transient ) {

	// see if the transient is an array
	if ( ! is_array( $transient ) ) {
		return false;
	}

	// see if the post id exists in the array meaning they already voted
	if ( in_array( $post_id, $transient ) ) {
		return true;
	} else {
		return false;
	}
}


/**
 * Deletes all votes.
 *
 * @return void
 */
function clpr_reset_votes() {
	global $wpdb;

	// empty both voting tables
	$wpdb->query( "TRUNCATE $wpdb->clpr_votes_total ;" );
	$wpdb->query( "TRUNCATE $wpdb->clpr_votes ;" );

	// now clear out all visitor transients from the options table
	$sql = "DELETE FROM $wpdb->options WHERE option_name LIKE '_transient_visitor_votes-%' OR option_name LIKE '_transient_timeout_visitor_votes-%'";
	$wpdb->query( $sql );

	// update clpr_votes_up and clpr_votes_down to 0 votes
	$sql = "UPDATE $wpdb->postmeta SET meta_value = '0' WHERE meta_key = 'clpr_votes_up' OR meta_key = 'clpr_votes_down'";
	$wpdb->query( $sql );

	// update clpr_votes_percent to 100%
	$sql = "UPDATE $wpdb->postmeta SET meta_value = '100' WHERE meta_key = 'clpr_votes_percent'";
	$wpdb->query( $sql );
}


/**
 * Deletes all votes for individual coupon.
 * Called via jQuery resetVotes function.
 *
 * @return void
 */
function clpr_reset_coupon_votes_ajax() {
	global $wpdb;

	if ( ! current_user_can( 'manage_options' ) || ! isset( $_POST['pid'] ) ) {
		die;
	}

	$coupon_id = (int) $_POST['pid'];

	clpr_set_initial_votes_data( $coupon_id );

	// now clear out coupon id from visitor transients
	$sql = "SELECT * FROM $wpdb->options WHERE option_name LIKE '_transient_visitor_votes-%' AND option_value LIKE '%\"".$coupon_id."\"%'";
	$results = $wpdb->get_results( $sql );
	if ( $results ) {
		foreach ( $results as $result ) {
			$voted_coupons = unserialize( $result->option_value );
			if ( empty( $voted_coupons ) && ! is_array( $voted_coupons ) ) {
				continue;
			}

				foreach ( $voted_coupons as $key => $id ) {
					if ( $coupon_id == $id ) {
						unset( $voted_coupons[ $key ] );
					}
				}
				update_option( $result->option_name, $voted_coupons );
		}
	}

	die; // so it doesn't return an extra zero
}


/**
 * Displays reset coupon votes link for admins. Use only in loop.
 *
 * @return void
 */
function clpr_reset_coupon_votes_link() {
	global $post;

	if ( ! current_user_can( 'manage_options' ) || ! in_the_loop() ) {
		return;
	}

	$response = "<span class=\'text\'>" . __( 'Votes has been reseted!', APP_TD ) . "</span>";
	$onclick = 'onClick="resetVotes(' . $post->ID . ', \'reset_' . $post->ID . '\', \'' . $response . '\');"';
	echo '<p class="edit" id="reset_' . $post->ID . '"><a class="coupon-reset-link" ' . $onclick . ' title="' . __( 'Reset Coupon Votes', APP_TD ) . '">' . __( 'Reset Votes', APP_TD ) . '</a></p>';
}

/**
 * Displays the coupon voting widget. Use only in loop.
 *
 * @since 1.0.0
 *
 * @param int   $post_id
 * @param array $transient
 * @return void
 */
function clpr_vote_box( $post_id, $transient ) {
	global $user_ID;

	$response = "<span class=\'text\'>" . __( 'Thanks for your response!', APP_TD ) . "</span><span class=\'checkmark\'>&nbsp;</span>";
?>

	<div class="thumbsup-vote">

		<div class="frame" id="vote_<?php the_ID(); ?>">

			<?php if ( clpr_vote_check( $post_id, $transient ) == false ) : ?>

				<span class="text"><?php _e( 'Did this coupon work for you?', APP_TD ); ?></span>

				<div id="loading-<?php the_ID(); ?>" class="loading"></div>

				<div id="ajax-<?php the_ID(); ?>" class="thumbsup-wrap">

					<div class="row">

						<div class="small-12 columns">

							<span class="vote thumbsup-down">
								<span class="thumbsdown" onclick="thumbsVote(<?php echo $post_id; ?>, <?php echo $user_ID; ?>, 'vote_<?php the_ID(); ?>', 0, '<?php echo $response; ?>');"><i class="fa fa-thumbs-down" aria-hidden="true"></i></span>
							</span>

							<span class="vote thumbsup-up">
								<span class="thumbsup" onclick="thumbsVote(<?php echo $post_id; ?>, <?php echo $user_ID; ?>, 'vote_<?php the_ID(); ?>', 1, '<?php echo $response; ?>');"><i class="fa fa-thumbs-up" aria-hidden="true"></i></span>
							</span>

						</div><!-- .columns -->

					</div><!-- .row -->

				</div><!-- #ajax -->

			<?php else : ?>

				<?php clpr_votes_chart(); ?>

			<?php endif; ?>

		</div>

	</div>

<?php
}


/**
 * Displays coupon success % badge.
 *
 * @param int $post_id
 * @param array $transient
 *
 * @return void
 */
function clpr_vote_badge( $post_id, $transient ) {

	$percent = round( get_post_meta( $post_id, 'clpr_votes_percent', true ) );
	// figure out which color badge to show based on percentage
	if ( $percent >= 75 ) {
		$style = 'green';
	} else if ( $percent >= 40 && $percent < 75 ) {
		$style = 'orange';
	} else {
		$style = 'red';
	}
?>
	<span class="thumbsup-badge badge-<?php echo $style; ?>"><span class="percent"><?php echo $percent; ?>%</span><span class="success"><?php _e( 'success', APP_TD ); ?></span></span>
<?php

}


/**
 * Displays vote results chart.
 *
 * @param int $post_id (optional)
 *
 * @return void
 */
function clpr_votes_chart( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();

	$post = get_post( $post_id );
	if ( ! $post ) {
		return;
	}

	// get the votes
	$votes_up = (int) get_post_meta( $post->ID, 'clpr_votes_up', true );
	$votes_down = (int) get_post_meta( $post->ID, 'clpr_votes_down', true );
	$votes_total = ( $votes_up + $votes_down );

	// only show the results if there's at least one vote
	if ( $votes_total < 1 ) {
		return;
	}

	// calculate votes percents
	$votes_up_percent = ( $votes_up / $votes_total * 100 );
	$votes_down_percent = ( $votes_down / $votes_total * 100 );
?>
	<div class="results">
		<?php _e( 'Results:', APP_TD ); ?>
		<span class="votes-green"><?php echo $votes_up; ?></span> / <span class="votes-red"><?php echo $votes_down; ?></span>
		<div class="progress progress-green"><span style="width: <?php echo round( $votes_up_percent ); ?>%;"><b><?php echo round( $votes_up_percent ); ?>%</b></span></div>
		<div class="progress progress-red"><span style="width: <?php echo round( $votes_down_percent ); ?>%;"><b><?php echo round( $votes_down_percent ); ?>%</b></span></div>
	</div>
<?php
}


/**
 * Displays coupon voting widget and success % badge. Use only in loop.
 *
 * @param int $post_id
 * @param array $transient (optional)
 *
 * @return void
 */
function clpr_vote_box_badge( $post_id, $transient = null ) {
	global $user_ID;

	if ( is_null( $transient ) ) {
		$transient = appthemes_get_visitor_transient( 'visitor_votes' );
	}

	$response = "<span class=\'text\'>" . __( 'Thanks for voting!', APP_TD ) . "</span>";

	$percent = round( get_post_meta( $post_id, 'clpr_votes_percent', true ) );
	// figure out which color badge to show based on percentage
	if ( $percent >= 75 ) {
		$style = 'green';
	} else if ( $percent >= 40 && $percent < 75 ) {
		$style = 'orange';
	} else {
		$style = 'red';
	}
?>

	<div class="thumbsup-vote">

		<div class="stripe-badge">
			<span class="success"><?php _e( 'success', APP_TD ); ?></span>
			<span style="background-color: #028174;" class="thumbsup-stripe-badge stripe-badge-<?php echo $style; ?>"><span class="percent"><?php echo $percent; ?>%</span></span>
		</div>

		<div class="frame" id="vote_<?php the_ID(); ?>">

			<?php if ( clpr_vote_check( $post_id, $transient ) == false ) : ?>

				<div id="loading-<?php the_ID(); ?>" class="loading"></div>

				<div id="ajax-<?php the_ID(); ?>" class="thumbsup-wrap">

					<div class="row">

						<div class="small-12 columns">

							<span class="vote thumbsup-up">
								<span class="thumbsup" onClick="thumbsVote(<?php echo $post_id; ?>, <?php echo $user_ID; ?>, 'vote_<?php the_ID(); ?>', 1, '<?php echo $response; ?>');"><i class="fa fa-thumbs-up" aria-hidden="true"></i></span>
							</span>

							<span class="vote thumbsup-down">
								<span class="thumbsdown" onClick="thumbsVote(<?php echo $post_id; ?>, <?php echo $user_ID; ?>, 'vote_<?php the_ID(); ?>', 0, '<?php echo $response; ?>');"><i class="fa fa-thumbs-down" aria-hidden="true"></i></span>
							</span>

						</div><!-- .columns -->

					</div><!-- .row -->

				</div><!-- #ajax -->

			<?php else : ?>

				<?php clpr_votes_chart_numbers(); ?>

			<?php endif; ?>

		</div>

	</div>

<?php
}

/**
 * Display coupon vote results. Use only in loop.
 *
 * @since 1.0.0
 *
 * @return void
 */
function clpr_votes_chart_numbers() {
	global $post;
?>
	<div class="results">
		<?php
			// get the votes for the post
			$votes_up = get_post_meta( $post->ID, 'clpr_votes_up', true );
			$votes_down = get_post_meta( $post->ID, 'clpr_votes_down', true );

			// do some math
			$votes_total = ( $votes_up + $votes_down );

			// only show the results if there's at least one vote
			if ( $votes_total != 0 ) {

				$votes_up_percent = ( $votes_up / $votes_total * 100 );
				$votes_down_percent = ( $votes_down / $votes_total * 100 );
				?>

				<div style="display: flex; margin-bottom: 10px;">
					<i style="margin-right: 5px;" class="fa fa-thumbs-up" aria-hidden="true"></i>
					<div style="flex: 1" class="success progress" role="progressbar" tabindex="0">
						<span class="progress-meter" style="width: <?php echo esc_attr( round( $votes_up_percent ) ); ?>%">
						<p class="progress-meter-text"><?php echo number_format_i18n( $votes_up ); ?></p>
						</span>
					</div>
				</div>

				<div style="display: flex;">
					<i style="margin-right: 5px;" class="fa fa-thumbs-down" aria-hidden="true"></i>
					<div style="flex: 1" class="alert progress" role="progressbar" tabindex="0">
						<span class="progress-meter" style="width: <?php echo esc_attr( round( $votes_down_percent ) ); ?>%">
						<p class="progress-meter-text"><?php echo $votes_down ? number_format_i18n( $votes_down ) : ''; ?></p>
						</span>
					</div>
				</div>
		<?php } ?>

	</div><!-- .results -->

<?php
}
