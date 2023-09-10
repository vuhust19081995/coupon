<?php
/**
 * Listing expiration submodule
 *
 * @package Clipper\Coupons
 * @author  AppThemes
 * @since   2.0.0
 */

/**
 * Expire Listing processing class
 */
class CLPR_Coupon_Listing_Expire extends APP_Listing_Expire {

	/**
	 * Construct Expire Listing module
	 *
	 * @param APP_Listing $listing Listing module object.
	 */
	public function __construct( APP_Listing $listing ) {

		parent::__construct( $listing );

		add_filter( 'appthemes_optional_form_fields_include_filters', array( $this, '_include_filters' ) );
		add_filter( "appthemes_decorate_listing_plan_{$this->listing->get_type()}", array( $this, '_decorate_plan' ) );
		add_filter( "appthemes_{$listing->get_type()}-plan_metabox_fields", array( $this, '_plan_form' ) );
	}

	/**
	 * Registers extra post statuses
	 */
	protected function register_post_statuses() {
		global $clpr_options;

		register_post_status( APP_POST_STATUS_EXPIRED, array(
			'label'                     => _x( 'Expired', 'listing', APP_TD ),
			'public'                    => ! $clpr_options->prune_coupons,
			'protected'                 => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>', APP_TD ),
		) );
	}

	/**
	 * Prunes expired listings
	 */
	public function _prune_expired_listings() {
		global $clpr_options;

		add_filter( 'posts_clauses', array( $this, '_expired_listing_sql' ), 10, 2 );

		$expired_posts = new WP_Query( array(
			'post_type'        => $this->listing->get_type(),
			'post_status'      => 'publish',
			'expired_listings' => true,
			'posts_per_page'   => intval( $this->limit ),
		) );

		remove_filter( 'posts_clauses', array( $this, '_expired_listing_sql' ), 10, 2 );

		/* @var $plans_module APP_Listing_Plans_Registry */
		/* @var $plan         APP_Listing_Current_Plan_I */
		$plans_module = $this->listing->plans;
		$plan         = null;
		$message      = '';
		$links_list   = '';
		$subject      = __( 'Clipper Coupons Expired', APP_TD );

		foreach ( $expired_posts->posts as $post ) {

			if ( $plans_module ) {
				$plan = $plans_module->get_current_plan( $post->ID );
			}

			if ( $plan ) {
				$plan->deactivate();
			}

			$links_list .= html( 'li', get_permalink( $post->ID ) ) . PHP_EOL;
		}

		$message .= html( 'p', __( 'Your cron job has run successfully. ', APP_TD ) ) . PHP_EOL;
		if ( empty( $links_list ) ) {
			$message .= html( 'p', __( 'No expired coupons were found.', APP_TD ) ) . PHP_EOL;
		} else {
			$message .= html( 'p', __( 'The following coupons expired and have been taken down from your website: ', APP_TD ) ) . PHP_EOL;
			$message .= html( 'ul', $links_list ) . PHP_EOL;
		}

		$message .= html( 'p', __( 'Regards,', APP_TD ) . '<br />' . __( 'Clipper', APP_TD ) ) . PHP_EOL;

		if ( $clpr_options->prune_coupons_email ) {
			$email = array( 'to' => get_option( 'admin_email' ), 'subject' => $subject, 'message' => $message );
			$email = apply_filters( 'clpr_email_admin_coupons_expired', $email );

			appthemes_send_email( $email['to'], $email['subject'], $email['message'] );
		}
	}

	/**
	 * Retrieves SQL query clauses for expired listings
	 *
	 * @global wpdb $wpdb
	 *
	 * @param array    $clauses  The list of clauses for the query.
	 * @param WP_Query $wp_query The WP_Query instance (passed by reference).
	 *
	 * @return array Modified clauses
	 */
	public function _expired_listing_sql( $clauses, $wp_query ) {
		global $wpdb;

		if ( $wp_query->get( 'expired_listings' ) ) {
			$clauses['join']  .= ' INNER JOIN ' . $wpdb->postmeta . ' AS exp1 ON (' . $wpdb->posts . '.ID = exp1.post_id)' ;
			$clauses['where'] .= " AND ( exp1.meta_key = 'clpr_expire_date' AND exp1.meta_value < CURRENT_DATE() AND exp1.meta_value > 0 )";
		}

		return $clauses;
	}

	/**
	 * Decorates given current plan with a expire behaviour.
	 *
	 * @param APP_Listing_Plan_I $plan Given listing plan object.
	 *
	 * @return \CLPR_Coupon_Listing_Current_Plan_Expire_Decorator Decorated plan object.
	 */
	public function _decorate_current_plan( APP_Listing_Plan_I $plan ) {
		return new CLPR_Coupon_Listing_Current_Plan_Expire_Decorator( $plan, $this->listing->get_type() );
	}

	/**
	 * Decorates given plan with a expire behaviour.
	 *
	 * @param APP_Listing_Plan_I $plan Given listing plan object.
	 *
	 * @return \CLPR_Coupon_Listing_Plan_Expire_Decorator Decorated plan object.
	 */
	public function _decorate_plan( APP_Listing_Plan_I $plan ) {
		return new CLPR_Coupon_Listing_Plan_Expire_Decorator( $plan, $this->listing->get_type() );
	}


	/**
	 * Includes Expire Date field to the Optional Edit Info form.
	 *
	 * This allows user change expiration date in the Renew and similar
	 * processes.
	 *
	 * @param array $include A list of field attributes used to include certain
	 *                       fields in the final field list
	 *                       (e.g: array( 'name' => 'my-field' ) ).
	 *
	 * @return array Filtered includes array.
	 */
	public function _include_filters( $include ) {

		$checkout = appthemes_get_checkout();

		if ( ! $checkout ) {
			return $include;
		}

		$item_id = $checkout->get_data( 'listing_id' );
		$item = get_post( $item_id );

		if ( ! $item || $this->listing->get_type() !== $item->post_type ) {
			return $include;
		}

		$filters = array(
			'name' => array( 'clpr_expire_date' ),
		);

		$include = array_merge_recursive( $include, $filters );

		return $include;
	}

	/**
	 * Returns an array of form fields.
	 *
	 * @param array $fields Filtered fields array.
	 *
	 * @return array Form fields
	 */
	public function _plan_form( $fields ) {

		foreach ( $fields as &$field ) {
			if ( '_blank' === $field['flag_key'] ) {
				$field['expire_date_key'] = 'clpr_expire_date';
				$field['title'] = get_post_type_object( APP_POST_TYPE )->labels->singular_name;
				return $fields;
			}
		}

		return $fields;
	}

}
