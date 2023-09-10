<?php
/**
 * Theme specific widgets.
 *
 * @package Clipper\Widgets
 * @author  AppThemes
 * @since   Clipper 1.0.0
 */

/**
 * Facebook Like Box Widget.
 *
 * @since 1.0.0
 */
class CLPR_Widget_Facebook extends APP_Widget_Facebook {

	/**
	 * Setups widget.
	 *
	 * @return void
	 */
	public function __construct() {
		$args = array(
			'defaults' => array(
				'width' => '268',
				'name'  => __( 'Clipper - Facebook Like Box', APP_TD ),
			),
		);

		parent::__construct( $args );
	}
}

/**
 * Email Subscription Widget.
 *
 * @since 1.0.0
 */
class CLPR_Widget_Subscribe extends APP_Widget {

	/**
	 * Setups widget.
	 *
	 * @param array $args (optional)
	 *
	 * @return void
	 */
	public function __construct( $args = array() ) {

		$default_args = array(
			'id_base'  => 'newsletter-subscribe',
			'name'     => __( 'Clipper - Subscribe to Coupons', APP_TD ),
			'defaults' => array(
				'title'      => 'Coupons in Your Inbox',
				'text'       => 'Receive coupons by email. Subscribe now!',
				'action'     => '#',
				'email_name' => 'email',
				'hname1'     => '',
				'hvalue1'    => '',
				'hname2'     => '',
				'hvalue2'    => '',
				'hname3'     => '',
				'hvalue3'    => '',
				'hname4'     => '',
				'hvalue4'    => '',
				'hname5'     => '',
				'hvalue5'    => '',
				'hname6'     => '',
				'hvalue6'    => '',
			),
			'widget_ops' => array(
				'description' => __( 'Subscribe to coupons form.', APP_TD ),
				'classname'   => 'subscribe-box',
			),
			'control_options' => array(
				'width'  => 500,
				'height' => 350,
			),
		);

		extract( $this->_array_merge_recursive( $default_args, $args ) );

		parent::__construct( $id_base, $name, $widget_ops, $control_options, $defaults );
	}

	/**
	 * Returns an array of form fields.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	protected function form_fields() {

		$form_fields = array(
			array(
				'type' => 'text',
				'name' => 'title',
				'desc' => __( 'Title:', APP_TD ),
			),
			array(
				'type' => 'text',
				'name' => 'text',
				'desc' => __( 'Text:', APP_TD ),
			),
			array(
				'type' => 'text',
				'name' => 'action',
				'desc' => __( 'Form Post Action:', APP_TD ),
				//'desc' => __( 'Enter the url where the email subscribe form should post to.<br /> i.e. http://www.aweber.com/', APP_TD ),
			),
			array(
				'type' => 'text',
				'name' => 'email_name',
				'desc' => __( 'Email field name:', APP_TD ),
				//'desc' => __( 'Enter the email field name. i.e. email-address', APP_TD ),
			),
			// Advanced Options
			array(
				'type' => 'text',
				'name' => 'hname1',
				'desc' => __( 'Hidden Field Name 1:', APP_TD ),
			),
			array(
				'type' => 'text',
				'name' => 'hvalue1',
				'desc' => __( 'Hidden Field Value 1:', APP_TD ),
			),
			array(
				'type' => 'text',
				'name' => 'hname2',
				'desc' => __( 'Hidden Field Name 2:', APP_TD ),
			),
			array(
				'type' => 'text',
				'name' => 'hvalue2',
				'desc' => __( 'Hidden Field Value 2:', APP_TD ),
			),
			array(
				'type' => 'text',
				'name' => 'hname3',
				'desc' => __( 'Hidden Field Name 3:', APP_TD ),
			),
			array(
				'type' => 'text',
				'name' => 'hvalue3',
				'desc' => __( 'Hidden Field Value 3:', APP_TD ),
			),
			array(
				'type' => 'text',
				'name' => 'hname4',
				'desc' => __( 'Hidden Field Name 4:', APP_TD ),
			),
			array(
				'type' => 'text',
				'name' => 'hvalue4',
				'desc' => __( 'Hidden Field Value 4:', APP_TD ),
			),
			array(
				'type' => 'text',
				'name' => 'hname5',
				'desc' => __( 'Hidden Field Name 5:', APP_TD ),
			),
			array(
				'type' => 'text',
				'name' => 'hvalue5',
				'desc' => __( 'Hidden Field Value 5:', APP_TD ),
			),
			array(
				'type' => 'text',
				'name' => 'hname6',
				'desc' => __( 'Hidden Field Name 6:', APP_TD ),
			),
			array(
				'type' => 'text',
				'name' => 'hvalue6',
				'desc' => __( 'Hidden Field Value 6:', APP_TD ),
			),
		);

		return $form_fields;
	}

	/**
	 * Displays widget content.
	 *
	 * @since 1.0.0
	 *
	 * @param array $instance
	 * @return void
	 */
	public function content( $instance ) {
		$instance = wp_parse_args( $instance, $this->defaults );
	?>
		<div class="text-box"><p><?php echo $instance['text']; ?></p></div>

		<form method="post" action="<?php echo esc_attr( $instance['action'] ); ?>" class="subscribe-form">

			<fieldset>

				<input type="text" name="<?php echo esc_attr( $instance['email_name'] ); ?>" class="text" value="" placeholder="<?php esc_attr_e( 'Enter email', APP_TD ); ?>" />

				<button name="submit" value="Submit" id="submit" type="submit" class="button small"><?php _e( 'Subscribe', APP_TD ); ?></button>

			</fieldset>

			<input type="hidden" name="<?php echo esc_attr( $instance['hname1'] ); ?>" value="<?php echo esc_attr( $instance['hvalue1'] ); ?>" />
			<input type="hidden" name="<?php echo esc_attr( $instance['hname2'] ); ?>" value="<?php echo esc_attr( $instance['hvalue2'] ); ?>" />
			<input type="hidden" name="<?php echo esc_attr( $instance['hname3'] ); ?>" value="<?php echo esc_attr( $instance['hvalue3'] ); ?>" />
			<input type="hidden" name="<?php echo esc_attr( $instance['hname4'] ); ?>" value="<?php echo esc_attr( $instance['hvalue4'] ); ?>" />
			<input type="hidden" name="<?php echo esc_attr( $instance['hname5'] ); ?>" value="<?php echo esc_attr( $instance['hvalue5'] ); ?>" />
			<input type="hidden" name="<?php echo esc_attr( $instance['hname6'] ); ?>" value="<?php echo esc_attr( $instance['hvalue6'] ); ?>" />

		</form>
	<?php
	}
}

/**
 * Share Coupon Button Widget.
 *
 * @since 1.0.0
 */
class CLPR_Widget_Share_Coupon extends APP_Widget {

	/**
	 * Setups widget.
	 *
	 * @param array $args (optional)
	 *
	 * @return void
	 */
	public function __construct( $args = array() ) {

		$default_args = array(
			'id_base'  => 'share_coupon_button',
			'name'     => __( 'Clipper - Share Coupon Button', APP_TD ),
			'defaults' => array(
				'title'       => __( 'Share a Coupon', APP_TD ),
				'description' => __( 'Spread the Savings with Everyone!', APP_TD ),
			),
			'widget_ops' => array(
				'description' => __( 'Share a coupon button for use in sidebar.', APP_TD ),
				'classname'   => 'share-box',
			),
			'control_options' => array(),
		);

		extract( $this->_array_merge_recursive( $default_args, $args ) );

		parent::__construct( $id_base, $name, $widget_ops, $control_options, $defaults );
	}

	/**
	 * Returns an array of form fields.
	 *
	 * @return array
	 */
	protected function form_fields() {
		$form_fields = array(
			array(
				'type' => 'text',
				'name' => 'title',
				'desc' => __( 'Title:', APP_TD ),
			),
			array(
				'type' => 'text',
				'name' => 'description',
				'desc' => __( 'Description:', APP_TD ),
			),
		);

		return $form_fields;
	}

	/**
	 * Displays widget.
	 *
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {
		$this->content( $instance );
	}

	/**
	 * Displays widget content.
	 *
	 * @param array $instance
	 * @return void
	 */
	public function content( $instance ) {

		$instance = wp_parse_args( $instance, $this->defaults );

		$title       = apply_filters( 'widget_title', empty( $instance['title'] ) ? __( 'Share a Coupon', APP_TD ) : $instance['title'] );
		$description = apply_filters( 'widget_title', $instance['description'] );
	?>
		<a href="<?php echo clpr_get_submit_coupon_url(); ?>" class="button expanded share-button">
			<i class="fa fa-bullhorn" aria-hidden="true"></i>
			<span class="lgheading"><?php echo $title; ?></span>
			<span class="smheading"><?php echo $description; ?></span>
		</a>
	<?php
	}

}

/**
 * Most Commented Coupons Widget.
 *
 * @since 1.0.0
 */
class CLPR_Widget_Popular_Coupons extends APP_Widget {

	/**
	 * Setups widget.
	 *
	 * @param array $args (optional)
	 * @return void
	 */
	public function __construct( $args = array() ) {

		$default_args = array(
			'id_base'  => 'custom-coupons',
			'name'     => __( 'Clipper - Popular Coupons', APP_TD ),
			'defaults' => array(
				'title'           => 'Popular Coupons',
				'number'          => 10,
				'hide_unreliable' => '0',
			),
			'widget_ops' => array(
				'description' => __( 'Display the most commented on coupons.', APP_TD ),
				'classname'   => 'widget-custom-coupons',
			),
			'control_options' => array(),
		);

		extract( $this->_array_merge_recursive( $default_args, $args ) );

		parent::__construct( $id_base, $name, $widget_ops, $control_options, $defaults );
	}

	/**
	 * Returns an array of form fields.
	 *
	 * @return array
	 */
	protected function form_fields() {

		$form_fields = array(
			array(
				'type' => 'text',
				'name' => 'title',
				'desc' => __( 'Title:', APP_TD ),
			),
			array(
				'type' => 'text',
				'name' => 'number',
				'sanitize' => 'absint',
				'desc' => __( 'Number of coupons to show:', APP_TD ),
				'extra' => array( 'size' => 3 ),
			),
			array(
				'type' => 'checkbox',
				'name' => 'hide_unreliable',
				'sanitize' => 'absint',
				'desc' => __( 'Hide unreliable coupons', APP_TD ),
			),
		);

		return $form_fields;
	}

	/**
	 * Displays widget.
	 *
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {

		// modify css class
		if ( strpos( $args['before_widget'], 'customclass' ) !== false ) {
			$args['before_widget'] = str_replace( 'customclass', 'cut', $args['before_widget'] );
		}

		parent::widget( $args, $instance );
	}

	/**
	 * Displays widget content.
	 *
	 * @param array $instance
	 * @return void
	 */
	public function content( $instance ) {

		$instance = wp_parse_args( $instance, $this->defaults );

		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) ) {
 			$number = 10;
 		}

		$post_status = ( ! empty( $instance['hide_unreliable'] ) ) ? array( 'publish' ) : array( 'publish', 'unreliable' );

		$coupons_args = array(
			'post_type'      => APP_POST_TYPE,
			'post_status'    => $post_status,
			'posts_per_page' => $number,
			'orderby'        => 'comment_count',
			'order'          => 'DESC',
			'no_found_rows'  => true,
		);

		$coupons = new WP_Query( $coupons_args );
		$result = '';

		$result .= '<div class="coupon-ticker"><ul class="list">';

		if ( $coupons->have_posts() ) {

			while ( $coupons->have_posts() ) {
				$coupons->the_post();
				$comments_text = sprintf( _n( '%1$s comment', '%1$s comments', get_comments_number(), APP_TD ), get_comments_number() );
				$result .= '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a> - ' . $comments_text . '</li>' . PHP_EOL;
			}

		} else {
			$result .= '<li class="no-results">' . __( 'No popular coupons yet.', APP_TD ) . '</li>';
		}

		$result .= '</ul></div>';

		wp_reset_postdata();

		echo $result;
	}
}

/**
 * Expiring Soon Coupons Widget.
 *
 * @since 1.0.0
 */
class CLPR_Widget_Expiring_Coupons extends APP_Widget {

	/**
	 * Setups widget.
	 *
	 * @param array $args (optional)
	 * @return void
	 */
	public function __construct( $args = array() ) {

		$default_args = array(
			'id_base'  => 'expiring-coupons',
			'name'     => __( 'Clipper - Expiring Coupons', APP_TD ),
			'defaults' => array(
				'title'           => 'Expiring Coupons',
				'number'          => 10,
				'hide_unreliable' => '0',
			),
			'widget_ops' => array(
				'description' => __( 'Display the expiring soon coupons.', APP_TD ),
				'classname'   => 'widget-custom-coupons',
			),
			'control_options' => array(),
		);

		extract( $this->_array_merge_recursive( $default_args, $args ) );

		parent::__construct( $id_base, $name, $widget_ops, $control_options, $defaults );
	}

	/**
	 * Returns an array of form fields.
	 *
	 * @return array
	 */
	protected function form_fields() {

		$form_fields = array(
			array(
				'type' => 'text',
				'name' => 'title',
				'desc' => __( 'Title:', APP_TD ),
			),
			array(
				'type'     => 'text',
				'name'     => 'number',
				'sanitize' => 'absint',
				'desc'     => __( 'Number of coupons to show:', APP_TD ),
				'extra'    => array( 'size' => 3 ),
			),
			array(
				'type'     => 'checkbox',
				'name'     => 'hide_unreliable',
				'sanitize' => 'absint',
				'desc'     => __( 'Hide unreliable coupons', APP_TD ),
			),
		);

		return $form_fields;
	}

	/**
	 * Displays widget.
	 *
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {

		// modify css class
		if ( strpos( $args['before_widget'], 'customclass' ) !== false ) {
			$args['before_widget'] = str_replace( 'customclass', 'cut', $args['before_widget'] );
		}

		parent::widget( $args, $instance );
	}

	/**
	 * Displays widget content.
	 *
	 * @param array $instance
	 * @return void
	 */
	public function content( $instance ) {

		$instance = wp_parse_args( $instance, $this->defaults );

		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) ) {
 			$number = 10;
 		}

		$post_status = ( ! empty( $instance['hide_unreliable'] ) ) ? array( 'publish' ) : array( 'publish', 'unreliable' );

		$coupons_args = array(
			'post_type'      => APP_POST_TYPE,
			'post_status'    => $post_status,
			'posts_per_page' => $number,
			'meta_query'     => array(
				array(
					'key'     => 'clpr_expire_date',
					'value'   => date( 'Y-m-d', current_time( 'timestamp' ) ),
					'type'    => 'date',
					'compare' => '>='
				),
			),
			'no_found_rows'     => true,
			'orderby_meta_date' => true,
			'orderby'           => 'meta_value',
			'order'             => 'ASC',
		);

		// add filter to order by date
		add_filter( 'posts_orderby', array( $this, 'orderby_meta_date' ), 10, 2 );

		$coupons = new WP_Query( $coupons_args );

		// remove filter to don't affect any other queries
		remove_filter( 'posts_orderby', array( $this, 'orderby_meta_date' ), 10, 2 );

		$result = '';

		$result .= '<div class="coupon-ticker"><ul class="list">';

		if ( $coupons->have_posts() ) {

			while ( $coupons->have_posts() ) {
				$coupons->the_post();

				$expire_date = clpr_get_expire_date( get_the_ID(), 'raw' );

				if ( appthemes_days_between_dates( $expire_date ) > 0 ) {
					$time = clpr_get_expire_date( get_the_ID(), 'time' );
					$time_left = human_time_diff( $time + ( 24*3600 ), current_time( 'timestamp' ) );

					$expires_text = sprintf( __( 'expires in %s', APP_TD ), $time_left );
				} else {
					$expires_text = __( 'expires today', APP_TD );
				}

				$result .= '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a> - ' . $expires_text . '</li>' . PHP_EOL;
			}

		} else {
			$result .= '<li class="no-results">' . __( 'No expiring coupons yet.', APP_TD ) . '</li>';
		}

		$result .= '</ul></div>';

		wp_reset_postdata();

		echo $result;
	}

	/**
	 * Modifies ORDER BY part of query, to order by date from meta_value.
	 *
	 * @param string $orderby
	 * @param object $wp_query
	 * @return string
	 */
	public function orderby_meta_date( $orderby, $wp_query ) {
		global $wpdb;

		if ( $wp_query->get( 'orderby_meta_date' ) ) {
			$orderby = " CAST( $wpdb->postmeta.meta_value AS DATE ) " . $wp_query->get( 'order' );
		}

		return $orderby;
	}

}


/**
 * Related Coupons Widget.
 *
 * @since 1.6.0
 */
class CLPR_Widget_Related_Coupons extends APP_Widget {

	/**
	 * Setups widget.
	 *
	 * @param array $args (optional)
	 *
	 * @return void
	 */
	public function __construct( $args = array() ) {

		$default_args = array(
			'id_base'  => 'related-coupons',
			'name'     => __( 'Clipper - Related Coupons', APP_TD ),
			'defaults' => array(
				'title'           => 'Related Coupons',
				'number'          => 10,
				'hide_unreliable' => '0',
			),
			'widget_ops' => array(
				'description' => __( 'Display the related coupons. Use in coupon sidebar.', APP_TD ),
				'classname'   => 'widget-custom-coupons',
			),
			'control_options' => array(),
		);

		extract( $this->_array_merge_recursive( $default_args, $args ) );

		parent::__construct( $id_base, $name, $widget_ops, $control_options, $defaults );
	}

	/**
	 * Returns an array of form fields.
	 *
	 * @return array
	 */
	protected function form_fields() {

		$form_fields = array(
			array(
				'type' => 'text',
				'name' => 'title',
				'desc' => __( 'Title:', APP_TD ),
			),
			array(
				'type'     => 'text',
				'name'     => 'number',
				'sanitize' => 'absint',
				'desc'     => __( 'Number of coupons to show:', APP_TD ),
				'extra'    => array( 'size' => 3 ),
			),
			array(
				'type'     => 'checkbox',
				'name'     => 'hide_unreliable',
				'sanitize' => 'absint',
				'desc'     => __( 'Hide unreliable coupons', APP_TD ),
			),
		);

		return $form_fields;
	}

	/**
	 * Displays widget.
	 *
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {

		// display only on single coupon page
		if ( ! is_singular( APP_POST_TYPE ) ) {
			return;
		}

		$categories = $this->get_post_terms_ids( $instance );
		if ( empty( $categories ) ) {
			return;
		}

		$instance['terms'] = $categories;

		parent::widget( $args, $instance );
	}

	/**
	 * Displays widget content.
	 *
	 * @param array $instance
	 * @return void
	 */
	public function content( $instance ) {
		global $post;

		$instance = wp_parse_args( $instance, $this->defaults );

		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) ) {
 			$number = 10;
 		}

		if ( empty( $instance['terms'] ) || ! is_array( $instance['terms'] ) ) {
 			return;
 		}

		$post_status = ( ! empty( $instance['hide_unreliable'] ) ) ? array( 'publish' ) : array( 'publish', 'unreliable' );

		$coupons_args = array(
			'post_type'      => APP_POST_TYPE,
			'post_status'    => $post_status,
			'post__not_in'   => array( $post->ID ),
			'posts_per_page' => $number,
			'tax_query'      => array(
				array(
					'taxonomy'         => APP_TAX_CAT,
					'field'            => 'id',
					'terms'            => $instance['terms'],
					'include_children' => false,
				),
			),
			'no_found_rows' => true,
			'orderby'       => 'rand',
		);

		$coupons = new WP_Query( $coupons_args );

		$result = '';

		$result .= '<div class="coupon-ticker"><ul class="list">';

		if ( $coupons->have_posts() ) {

			while ( $coupons->have_posts() ) {
				$coupons->the_post();
				$store_name = clpr_get_coupon_store_name( $post->ID );
				$stores_text = sprintf( __( 'from %s', APP_TD ), $store_name );
				$result .= '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a> - ' . $stores_text . '</li>' . PHP_EOL;
			}

		} else {
			$result .= '<li class="no-results">' . __( 'No related coupons found.', APP_TD ) . '</li>';
		}

		$result .= '</ul></div>';

		wp_reset_postdata();

		echo $result;
	}

	/**
	 * Returns post terms IDs.
	 *
	 * @param array $instance
	 * @return array
	 */
	protected function get_post_terms_ids( $instance ) {
		global $post;

		$terms_ids = array();

		if ( ! $post ) {
			return $terms_ids;
		}

		$terms = get_the_terms( $post->ID, APP_TAX_CAT );
		if ( $terms ) {
			foreach ( $terms as $term ) {
				$terms_ids[] = $term->term_id;
			}
		}

		return $terms_ids;
	}
}

/**
 * Top Coupons Today Widget.
 *
 * @since 1.6.0
 */
class CLPR_Widget_Top_Coupons_Today extends APP_Widget {

	/**
	 * Setups widget.
	 *
	 * @param array $args (optional)
	 *
	 * @return void
	 */
	public function __construct( $args = array() ) {

		$default_args = array(
			'id_base'  => 'top_coupons_today',
			'name'     => __( 'Clipper - Top Coupons Today', APP_TD ),
			'defaults' => array(
				'title'           => 'Top Coupons Today',
				'number'          => 10,
				'hide_unreliable' => '0',
			),
			'widget_ops' => array(
				'description' => __( 'Display the top coupons today.', APP_TD ),
				'classname'   => 'widget-top-coupons-today',
			),
			'control_options' => array(),
		);

		extract( $this->_array_merge_recursive( $default_args, $args ) );

		parent::__construct( $id_base, $name, $widget_ops, $control_options, $defaults );
	}

	/**
	 * Checks if widget should be registered.
	 *
	 * @return bool
	 */
	protected function condition() {
		global $clpr_options;

		// don't register widget when stats are disabled
		return ( $clpr_options->stats_all && current_theme_supports( 'app-stats' ) );
	}

	/**
	 * Returns an array of form fields.
	 *
	 * @return array
	 */
	protected function form_fields() {

		$form_fields = array(
			array(
				'type' => 'text',
				'name' => 'title',
				'desc' => __( 'Title:', APP_TD ),
			),
			array(
				'type'     => 'text',
				'name'     => 'number',
				'sanitize' => 'absint',
				'desc'     => __( 'Number of coupons to show:', APP_TD ),
				'extra'    => array( 'size' => 3 ),
			),
			array(
				'type'     => 'checkbox',
				'name'     => 'hide_unreliable',
				'sanitize' => 'absint',
				'desc'     => __( 'Hide unreliable coupons', APP_TD ),
			),
		);

		return $form_fields;
	}

	/**
	 * Displays widget content.
	 *
	 * @param array $instance
	 * @return void
	 */
	public function content( $instance ) {

		$instance = wp_parse_args( $instance, $this->defaults );

		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) ) {
 			$number = 10;
 		}

		$post_status = ( ! empty( $instance['hide_unreliable'] ) ) ? array( 'publish' ) : array( 'publish', 'unreliable' );

		$coupons_args = array(
			'post_type'      => APP_POST_TYPE,
			'post_status'    => $post_status,
			'posts_per_page' => $number,
			'paged'          => 1,
			'no_found_rows'  => true,
		);

		$coupons = new CLPR_Popular_Posts_Query( $coupons_args, 'today' );
		$result = '';

		$result .= '<div class="coupon-ticker"><ul class="list">';

		if ( $coupons->have_posts() ) {

			while ( $coupons->have_posts() ) {
				$coupons->the_post();
				$views = appthemes_get_stats_by( get_the_ID(), 'today' );
				$stats_text = sprintf( _n( '%1$s view', '%1$s views', $views, APP_TD ), $views );

				$result .= '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a> (' . $stats_text . ')</li>' . PHP_EOL;
			}

		} else {
			$result .= '<li class="no-results">' . __( 'No views yet.', APP_TD ) . '</li>';
		}

		$result .= '</ul></div>';

		wp_reset_postdata();

		echo $result;
	}
}

/**
 * Top Coupons Overall Widget.
 *
 * @since 1.6.0
 */
class CLPR_Widget_Top_Coupons_Overall extends APP_Widget {

	/**
	 * Setups widget.
	 *
	 * @param array $args (optional)
	 *
	 * @return void
	 */
	public function __construct( $args = array() ) {

		$default_args = array(
			'id_base'  => 'top_coupons_overall',
			'name'     => __( 'Clipper - Top Coupons Overall', APP_TD ),
			'defaults' => array(
				'title'           => 'Top Coupons Overall',
				'number'          => 10,
				'hide_unreliable' => '0',
			),
			'widget_ops' => array(
				'description' => __( 'Display the top coupons overall.', APP_TD ),
				'classname'   => 'widget-top-coupons-overall',
			),
			'control_options' => array(),
		);

		extract( $this->_array_merge_recursive( $default_args, $args ) );

		parent::__construct( $id_base, $name, $widget_ops, $control_options, $defaults );
	}

	/**
	 * Checks if widget should be registered.
	 *
	 * @return bool
	 */
	protected function condition() {
		global $clpr_options;

		// don't register widget when stats are disabled
		return ( $clpr_options->stats_all && current_theme_supports( 'app-stats' ) );
	}

	/**
	 * Returns an array of form fields.
	 *
	 * @return array
	 */
	protected function form_fields() {

		$form_fields = array(
			array(
				'type' => 'text',
				'name' => 'title',
				'desc' => __( 'Title:', APP_TD ),
			),
			array(
				'type'     => 'text',
				'name'     => 'number',
				'sanitize' => 'absint',
				'desc'     => __( 'Number of coupons to show:', APP_TD ),
				'extra'    => array( 'size' => 3 ),
			),
			array(
				'type'     => 'checkbox',
				'name'     => 'hide_unreliable',
				'sanitize' => 'absint',
				'desc'     => __( 'Hide unreliable coupons', APP_TD ),
			),
		);

		return $form_fields;
	}

	/**
	 * Displays widget content.
	 *
	 * @param array $instance
	 * @return void
	 */
	public function content( $instance ) {

		$instance = wp_parse_args( $instance, $this->defaults );

		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) ) {
 			$number = 10;
 		}

		$post_status = ( ! empty( $instance['hide_unreliable'] ) ) ? array( 'publish' ) : array( 'publish', 'unreliable' );

		$coupons_args = array(
			'post_type'      => APP_POST_TYPE,
			'post_status'    => $post_status,
			'posts_per_page' => $number,
			'paged'          => 1,
			'no_found_rows'  => true,
		);

		$coupons = new CLPR_Popular_Posts_Query( $coupons_args, 'total' );
		$result = '';

		$result .= '<div class="coupon-ticker"><ul class="list">';

		if ( $coupons->have_posts() ) {

			while ( $coupons->have_posts() ) {
				$coupons->the_post();
				$views = appthemes_get_stats_by( get_the_ID(), 'total' );
				$stats_text = sprintf( _n( '%1$s view', '%1$s views', $views, APP_TD ), $views );

				$result .= '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a> (' . $stats_text . ')</li>' . PHP_EOL;
			}

		} else {
			$result .= '<li class="no-results">' . __( 'No views yet.', APP_TD ) . '</li>';
		}

		$result .= '</ul></div>';

		wp_reset_postdata();

		echo $result;
	}
}

/**
 * Related Stores (Sub Stores) Widget.
 *
 * @since 1.0.0
 */
class CLPR_Widget_Sub_Stores extends APP_Widget {

	/**
	 * Setups widget.
	 *
	 * @param array $args (optional)
	 *
	 * @return void
	 */
	public function __construct( $args = array() ) {

		$default_args = array(
			'id_base'  => 'coupon-substores',
			'name'     => __( 'Clipper - Related Stores', APP_TD ),
			'defaults' => array(
				'title'  => 'Related Stores',
				'number' => 0,
			),
			'widget_ops' => array(
				'description' => __( 'Display the related stores. Use in store sidebar. Visible only on the store archive page when current store has sub stores.', APP_TD ),
				'classname'   => 'widget-custom-stores',
			),
			'control_options' => array(),
		);

		extract( $this->_array_merge_recursive( $default_args, $args ) );

		parent::__construct( $id_base, $name, $widget_ops, $control_options, $defaults );
	}

	/**
	 * Returns an array of form fields.
	 *
	 * @return array
	 */
	protected function form_fields() {

		$form_fields = array(
			array(
				'type' => 'text',
				'name' => 'title',
				'desc' => __( 'Title:', APP_TD ),
			),
			array(
				'type'     => 'text',
				'name'     => 'number',
				'sanitize' => 'absint',
				'desc'     => __( 'Number of stores to show (0 for all):', APP_TD ),
				'extra'    => array( 'size' => 3 ),
			),
		);

		return $form_fields;
	}

	/**
	 * Displays widget.
	 *
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {

		// display only on store archive page
	  if ( ! is_tax( APP_TAX_STORE ) ) {
	  	return;
	  }

		// display only when there are sub stores
		$terms_list = $this->get_terms_list( $instance );
	  if ( ! $terms_list ) {
	  	return;
	  }
	  $instance['terms_list'] = $terms_list;

		// modify css class
		if ( strpos( $args['before_widget'], 'customclass' ) !== false ) {
			$args['before_widget'] = str_replace( 'customclass', 'cut', $args['before_widget'] );
		}

		parent::widget( $args, $instance );
	}

	/**
	 * Displays widget content.
	 *
	 * @param array $instance
	 * @return void
	 */
	public function content( $instance ) {

		$instance = wp_parse_args( $instance, $this->defaults );

		if ( empty( $instance['terms_list'] ) || ! is_string( $instance['terms_list'] ) ) {
 			return;
 		}

		echo '<div class="store-widget"><ul class="list">';

		echo $instance['terms_list'];

		echo '</ul></div>';
	}

	/**
	 * Returns <li> list of terms.
	 *
	 * @param array $instance
	 * @return string
	 */
	protected function get_terms_list( $instance ) {

		$instance = wp_parse_args( $instance, $this->defaults );

		$term_id = get_queried_object_id();

		$hidden_stores = clpr_hidden_stores();

		// we cant exclude current store
		if ( in_array( $term_id, $hidden_stores ) ) {
			$hidden_stores = array_diff( $hidden_stores, array( $term_id ) );
		}
		$hidden_stores_list = implode( ',', $hidden_stores );

		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) ) {
 			$number = null;
 		}

		$tax_args = array(
			'orderby'            => 'name',
			'order'              => 'ASC',
			'hierarchical'       => 1,
			'show_count'         => 1,
			'pad_counts'         => 0,
			'app_pad_counts'     => 1,
			'use_desc_for_title' => 0,
			'show_option_none'   => 0,
			'hide_empty'         => 0,
			'depth'              => 1,
			'number'             => $number,
			'title_li'           => '',
			'taxonomy'           => APP_TAX_STORE,
			'child_of'           => $term_id,
			'exclude'            => $hidden_stores_list,
			'echo'               => 0,
		);

		return wp_list_categories( $tax_args );
	}
}

/**
 * Most Popular Stores Widget.
 *
 * @since 1.0.0
 */
class CLPR_Widget_Popular_Stores extends APP_Widget {

	/**
	 * Setups widget.
	 *
	 * @param array $args (optional)
	 *
	 * @return void
	 */
	public function __construct( $args = array() ) {

		$default_args = array(
			'id_base'  => 'custom-stores',
			'name'     => __( 'Clipper - Popular Stores', APP_TD ),
			'defaults' => array(
				'title'  => 'Popular Stores',
				'number' => 10,
			),
			'widget_ops' => array(
				'description' => __( 'Display the most popular stores.', APP_TD ),
				'classname'   => 'widget-custom-stores',
			),
			'control_options' => array(),
		);

		extract( $this->_array_merge_recursive( $default_args, $args ) );

		parent::__construct( $id_base, $name, $widget_ops, $control_options, $defaults );
	}

	/**
	 * Returns an array of form fields.
	 *
	 * @return array
	 */
	protected function form_fields() {
		$form_fields = array(
			array(
				'type' => 'text',
				'name' => 'title',
				'desc' => __( 'Title:', APP_TD ),
			),
			array(
				'type'     => 'text',
				'name'     => 'number',
				'sanitize' => 'absint',
				'desc'     => __( 'Number of stores to show:', APP_TD ),
				'extra'    => array( 'size' => 3 ),
			),
		);

		return $form_fields;
	}

	/**
	 * Displays widget.
	 *
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {

		// modify css class
		if ( strpos( $args['before_widget'], 'customclass' ) !== false ) {
			$args['before_widget'] = str_replace( 'customclass', 'cut', $args['before_widget'] );
		}

		parent::widget( $args, $instance );
	}


	/**
	 * Displays widget content.
	 *
	 * @param array $instance
	 * @return void
	 */
	public function content( $instance ) {

		$instance = wp_parse_args( $instance, $this->defaults );

		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) ) {
 			$number = 10;
 		}

		$tax_args = array(
			'orderby'        => 'count',
			'order'          => 'DESC',
			'hide_empty'     => 1,
			'show_count'     => 1,
			'pad_counts'     => 0,
			'app_pad_counts' => 1,
			'exclude'        => clpr_hidden_stores(),
		);
		$stores = get_terms( APP_TAX_STORE, $tax_args );

		$result = '';
		$i = 0;

		$result .= '<div class="store-widget"><ul class="list">';

		if ( $stores && is_array( $stores ) ) {

			foreach ( $stores as $store ) {
				if ( $i >= $number ) {
					break;
				}

				$link = get_term_link( $store, APP_TAX_STORE );
				$coupons_text = sprintf( _n( '%1$d coupon', '%1$d coupons', $store->count, APP_TD ), $store->count );
				$result .= '<li><a class="tax-link" href="' . $link . '">' . $store->name . '</a> - ' . $coupons_text . '</li>' . PHP_EOL;
				$i++;
			}

		} else {
			$result .= '<li class="no-results">' . __( 'No stores found.', APP_TD ) . '</li>';
		}

		$result .= '</ul></div>';

		echo $result;
	}
}

/**
 * Featured Stores Widget.
 *
 * @since 1.0.0
 */
class CLPR_Widget_Featured_Stores extends APP_Widget {

	/**
	 * Setups widget.
	 *
	 * @param array $args (optional)
	 *
	 * @return void
	 */
	public function __construct( $args = array() ) {

		$default_args = array(
			'id_base'  => 'featured-stores',
			'name'     => __( 'Clipper - Featured Stores', APP_TD ),
			'defaults' => array(
				'title'  => 'Featured Stores',
				'number' => 10,
			),
			'widget_ops' => array(
				'description' => __( 'Display stores that are marked as featured.', APP_TD ),
				'classname'   => 'widget-featured-stores',
			),
			'control_options' => array(),
		);

		extract( $this->_array_merge_recursive( $default_args, $args ) );

		parent::__construct( $id_base, $name, $widget_ops, $control_options, $defaults );
	}

	/**
	 * Returns an array of form fields.
	 *
	 * @return array
	 */
	protected function form_fields() {

		$form_fields = array(
			array(
				'type' => 'text',
				'name' => 'title',
				'desc' => __( 'Title:', APP_TD ),
			),
			array(
				'type'     => 'text',
				'name'     => 'number',
				'sanitize' => 'absint',
				'desc'     => __( 'Number of stores to rotate:', APP_TD ),
				'extra'    => array( 'size' => 3 ),
			),
		);

		return $form_fields;
	}

	/**
	 * Displays widget.
	 *
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {

		// modify css class
		if ( strpos( $args['before_widget'], 'customclass' ) !== false ) {
			$args['before_widget'] = str_replace( 'customclass', 'cut', $args['before_widget'] );
		}

		parent::widget( $args, $instance );
	}

	/**
	 * Displays widget content.
	 *
	 * @param array $instance
	 * @return void
	 */
	public function content( $instance ) {

		$instance = wp_parse_args( $instance, $this->defaults );

		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) ) {
 			$number = 10;
 		}

		$hidden_stores   = clpr_hidden_stores();
		$featured_stores = clpr_featured_stores();
		$featured_stores = array_diff( $featured_stores, $hidden_stores );

		$tax_args = array(
			'orderby'    => 'rand',
			'order'      => 'DESC',
			'hide_empty' => 0,
			'number'     => $number,
			'include'    => $featured_stores,
		);
		$stores = get_terms( APP_TAX_STORE, $tax_args );

		$result = '';
		$i = 0;

		if ( $stores && is_array( $stores ) ) {

			$result .= '<div class="store-widget-slider"><ul class="list">';

			foreach ( $stores as $store ) {
				if ( $i >= $number ) {
					break;
				}

				$link = get_term_link( $store, APP_TAX_STORE );
				$store_img = html( 'img', array( 'src' => clpr_get_store_image_url( $store->term_id, 'term_id', 160 ), 'alt' => esc_attr( sprintf( __( '%s screenshot', APP_TD ), $store->name ) ) ) );
				$result .= '<li><a href="' . $link . '">' . $store_img . '<span>' . $store->name . '</span></a></li>' . PHP_EOL;
				$i++;
			}

			$result .= '</ul></div>';
		}

		echo $result;
	}
}

/**
 * Coupon Catetories Widget.
 *
 * @since 1.0.0
 */
class CLPR_Widget_Coupon_Categories extends APP_Widget {

	/**
	 * Setups widget.
	 *
	 * @param array $args (optional)
	 *
	 * @return void
	 */
	public function __construct( $args = array() ) {

		$default_args = array(
			'id_base'  => 'coupon-cats',
			'name'     => __( 'Clipper - Coupon Categories', APP_TD ),
			'defaults' => array(
				'title'  => 'Coupon Categories',
				'number' => 0,
				'hide_empty' => 0,
			),
			'widget_ops' => array(
				'description' => __( 'Display the coupon categories.', APP_TD ),
				'classname'   => 'widget-coupon-cats',
			),
			'control_options' => array(),
		);

		extract( $this->_array_merge_recursive( $default_args, $args ) );

		parent::__construct( $id_base, $name, $widget_ops, $control_options, $defaults );
	}

	/**
	 * Returns an array of form fields.
	 *
	 * @return array
	 */
	protected function form_fields() {

		$form_fields = array(
			array(
				'type' => 'text',
				'name' => 'title',
				'desc' => __( 'Title:', APP_TD ),
			),
			array(
				'type'     => 'text',
				'name'     => 'number',
				'sanitize' => 'absint',
				'desc'     => __( 'Number of categories to show (0 for all):', APP_TD ),
				'extra'    => array( 'size' => 3 ),
			),
			array(
				'type'     => 'checkbox',
				'name'     => 'hide_empty',
				'sanitize' => 'absint',
				'desc'     => __( 'Hide empty categories', APP_TD ),
			),
		);

		return $form_fields;
	}

	/**
	 * Displays widget.
	 *
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {

		// modify css class
		if ( strpos( $args['before_widget'], 'customclass' ) !== false ) {
			$args['before_widget'] = str_replace( 'customclass', 'cut', $args['before_widget'] );
		}

		parent::widget( $args, $instance );
	}

	/**
	 * Displays widget content.
	 *
	 * @param array $instance
	 * @return void
	 */
	public function content( $instance ) {

		$instance = wp_parse_args( $instance, $this->defaults );

		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) ) {
 			$number = null;
 		}

		if ( empty( $instance['hide_empty'] ) ) {
			$hide_empty = 0;
		} else {
			$hide_empty = 1;
		}

		$tax_args = array(
			'orderby'            => 'name',
			'order'              => 'ASC',
			'hierarchical'       => 1,
			'show_count'         => 1,
			'pad_counts'         => 0,
			'app_pad_counts'     => 1,
			'use_desc_for_title' => 0,
			'hide_empty'         => $hide_empty,
			'depth'              => 1,
			'number'             => null,
			'title_li'           => '',
			'taxonomy'           => APP_TAX_CAT,
			'clpr_number'        => $number,
		);

		echo '<div class="coupon-cats-widget"><ul class="list">';

		add_filter( 'get_terms', array( $this, 'limit_number_of_terms' ), 10, 3 );
		wp_list_categories( $tax_args );
		remove_filter( 'get_terms', array( $this, 'limit_number_of_terms' ), 10, 3 );

		echo '</ul></div>';
	}

	/**
	 * Callback function to limit number of categories.
	 *
	 * @param array $terms
	 * @param array $taxonomies
	 * @param array $args
	 * @return array
	 */
	public function limit_number_of_terms( $terms, $taxonomies, $args ) {

		if ( ! isset( $args['clpr_number'] ) || is_null( $args['clpr_number'] ) ) {
			return $terms;
		}

		$i = 0;
		$number = absint( $args['clpr_number'] );

		foreach ( (array) $terms as $key => $term ) {
			if ( $i >= $number || $term->parent != 0 ) {
				unset( $terms[ $key ] );
				continue;
			}

			$i++;
		}

		return $terms;
	}
}

/**
 * Coupon Sub Catetories Widget.
 *
 * @since 1.0.0
 */
class CLPR_Widget_Coupon_Sub_Categories extends APP_Widget {

	/**
	 * Setups widget.
	 *
	 * @param array $args (optional)
	 *
	 * @return void
	 */
	public function __construct( $args = array() ) {

		$default_args = array(
			'id_base'  => 'coupon-subcats',
			'name'     => __( 'Clipper - Coupon Sub Categories', APP_TD ),
			'defaults' => array(
				'title'  => 'Sub Categories',
				'number' => 0,
				'hide_empty' => 0,
			),
			'widget_ops' => array(
				'description' => __( 'Display the coupon sub categories. Use in coupon sidebar. Visible only on the category archive page when current category has subcategories.', APP_TD ),
				'classname' => 'widget-coupon-cats',
			),
			'control_options' => array(),
		);

		extract( $this->_array_merge_recursive( $default_args, $args ) );

		parent::__construct( $id_base, $name, $widget_ops, $control_options, $defaults );
	}

	/**
	 * Returns an array of form fields.
	 *
	 * @return array
	 */
	protected function form_fields() {

		$form_fields = array(
			array(
				'type' => 'text',
				'name' => 'title',
				'desc' => __( 'Title:', APP_TD ),
			),
			array(
				'type'     => 'text',
				'name'     => 'number',
				'sanitize' => 'absint',
				'desc'     => __( 'Number of categories to show (0 for all):', APP_TD ),
				'extra'    => array( 'size' => 3 ),
			),
			array(
				'type'     => 'checkbox',
				'name'     => 'hide_empty',
				'sanitize' => 'absint',
				'desc'     => __( 'Hide empty categories', APP_TD ),
			),
		);

		return $form_fields;
	}

	/**
	 * Displays widget.
	 *
	 * @param array $args
	 * @param array $instance
	 * @return void
	 */
	function widget( $args, $instance ) {

		// display only on category archive page
	  if ( ! is_tax( APP_TAX_CAT ) ) {
	  	return;
	  }

		// display only when there are sub categories
		$terms_list = $this->get_terms_list( $instance );

	  if ( ! $terms_list ) {
	  	return;
	  }
	  $instance['terms_list'] = $terms_list;

		// modify css class
		if ( strpos( $args['before_widget'], 'customclass' ) !== false ) {
			$args['before_widget'] = str_replace( 'customclass', 'cut', $args['before_widget'] );
		}

		parent::widget( $args, $instance );
	}

	/**
	 * Displays widget content.
	 *
	 * @param array $instance
	 * @return void
	 */
	public function content( $instance ) {

		$instance = wp_parse_args( $instance, $this->defaults );

		if ( empty( $instance['terms_list'] ) || ! is_string( $instance['terms_list'] ) ) {
 			return;
 		}

		echo '<div class="coupon-cats-widget"><ul class="list">';

		echo $instance['terms_list'];

		echo '</ul></div>';
	}

	/**
	 * Returns <li> list of terms.
	 *
	 * @param array $instance
	 * @return string
	 */
	protected function get_terms_list( $instance ) {

		$instance = wp_parse_args( $instance, $this->defaults );

		$term_id = get_queried_object_id();

		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) ) {
 			$number = null;
 		}

		if ( empty( $instance['hide_empty'] ) ) {
			$hide_empty = 0;
		} else {
			$hide_empty = 1;
		}

		$tax_args = array(
			'orderby'            => 'name',
			'order'              => 'ASC',
			'hierarchical'       => 1,
			'show_count'         => 1,
			'pad_counts'         => 0,
			'app_pad_counts'     => 1,
			'use_desc_for_title' => 0,
			'show_option_none'   => 0,
			'hide_empty'         => $hide_empty,
			'depth'              => 1,
			'number'             => $number,
			'title_li'           => '',
			'taxonomy'           => APP_TAX_CAT,
			'child_of'           => $term_id,
			'echo'               => 0,
		);

		return wp_list_categories( $tax_args );
	}

}

/**
 * Most Searched Phrases Widget.
 *
 * @since 1.0.0
 */
class CLPR_Widget_Popular_Searches extends APP_Widget {

	/**
	 * Setups widget.
	 *
	 * @param array $args (optional)
	 * @return void
	 */
	public function __construct( $args = array() ) {

		$default_args = array(
			'id_base'  => 'popular-searches',
			'name'     => __( 'Clipper - Popular Searches', APP_TD ),
			'defaults' => array(
				'title'  => 'Popular Searches',
				'number' => 10,
			),
			'widget_ops' => array(
				'description' => __( 'Display the most searched phrases.', APP_TD ),
				'classname'   => 'widget-coupon-searches',
			),
			'control_options' => array(),
		);

		extract( $this->_array_merge_recursive( $default_args, $args ) );

		parent::__construct( $id_base, $name, $widget_ops, $control_options, $defaults );
	}

	/**
	 * Checks if widget should be registered.
	 *
	 * @return bool
	 */
	protected function condition() {
		global $clpr_options;

		// don't register widget when search stats are disabled
		return $clpr_options->search_stats;
	}

	/**
	 * Returns an array of form fields.
	 *
	 * @return array
	 */
	protected function form_fields() {
		$form_fields = array(
			array(
				'type' => 'text',
				'name' => 'title',
				'desc' => __( 'Title:', APP_TD ),
			),
			array(
				'type'     => 'text',
				'name'     => 'number',
				'sanitize' => 'absint',
				'desc'     => __( 'Number of phrases to show:', APP_TD ),
				'extra'    => array( 'size' => 3 ),
			),
		);

		return $form_fields;
	}

	/**
	 * Displays widget content.
	 *
	 * @param array $instance
	 * @return void
	 */
	public function content( $instance ) {
		global $wpdb;

		$instance = wp_parse_args( $instance, $this->defaults );

		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) ) {
 			$number = 10;
 		}

		$sql = "SELECT terms, SUM(count) as total_count FROM $wpdb->clpr_search_total WHERE last_hits > 0 GROUP BY terms ORDER BY total_count DESC LIMIT %d";
		$popular_searches = $wpdb->get_results( $wpdb->prepare( $sql, $number ) );

		$result = '';

		$result .= '<div class="coupon-searches-widget"><ul class="list">';

		if ( $popular_searches ) {

			foreach ( $popular_searches as $searched ) {
				$url = add_query_arg( array( 's' => urlencode( $searched->terms ) ), home_url( '/' ) );
				$count = sprintf( _n( '%s time', '%s times', $searched->total_count, APP_TD ), $searched->total_count );
				$result .= '<li><a href="' . $url . '">' . $searched->terms . '</a> - ' . $count . '</li>' . PHP_EOL;
			}

		} else {
			$result .= '<li class="no-results">' . __( 'No searches yet.', APP_TD ) . '</li>';
		}

		$result .= '</ul></div>';

		echo $result;
	}
}

/**
 * Tabbed Blog Widget.
 *
 * @since 1.0.0
 */
class CLPR_Widget_Tabbed_Blog extends APP_Widget {

	/**
	 * Setups widget.
	 *
	 * @param array $args (optional)
	 * @return void
	 */
	public function __construct( $args = array() ) {

		$default_args = array(
			'id_base'  => 'tabbed-blog',
			'name'     => __( 'Clipper - Tabbed Blog Widget', APP_TD ),
			'defaults' => array(
				'number' => 10,
			),
			'widget_ops' => array(
				'description' => __( 'Display a tabbed widget for blog posts.', APP_TD ),
				'classname'   => 'widget-tabbed-blog',
			),
			'control_options' => array(),
		);

		extract( $this->_array_merge_recursive( $default_args, $args ) );

		parent::__construct( $id_base, $name, $widget_ops, $control_options, $defaults );
	}

	/**
	 * Returns an array of form fields.
	 *
	 * @return array
	 */
	protected function form_fields() {

		$form_fields = array(
			array(
				'type'     => 'text',
				'name'     => 'number',
				'sanitize' => 'absint',
				'desc'     => __( 'Number of items to show:', APP_TD ),
				'extra'    => array( 'size' => 3 ),
			),
		);

		return $form_fields;
	}

	/**
	 * Displays widget content.
	 *
	 * @param array $instance
	 *
	 * @return void
	 */
	public function content( $instance ) {

		$instance = wp_parse_args( $instance, $this->defaults );
		add_action( 'wp_print_footer_scripts', array( __CLASS__, 'print_footer_scripts' ) );

		$recent_posts = $this->get_recent_posts( $instance );
		$popular_posts = $this->get_popular_posts( $instance );
		$comments = $this->get_recent_comments( $instance );
	?>
		<div class="blog-tabs" id="blog-tabs">

			<div class="sidebox-heading">
				<ul id="blog_tab_controls" class="tabset">
					<li><a href="#blogtab1"><span><?php _e( 'Recent', APP_TD ); ?></span><em class="bullet">&nbsp;</em></a></li>
					<li><a href="#blogtab2"><span><?php _e( 'Popular', APP_TD ); ?></span><em class="bullet">&nbsp;</em></a></li>
					<li><a href="#blogtab3"><span><?php _e( 'Comments', APP_TD ); ?></span><em class="bullet">&nbsp;</em></a></li>
				</ul>
			</div>

			<div class="tab-content" id="blogtab1">
				<ul>
				<?php
					if ( $recent_posts ) {
						echo $this->get_posts_list( $recent_posts );
					} else {
						echo html( 'li', __( 'There are no blog articles yet.', APP_TD ) );
					}
				?>
				</ul>
			</div>

			<div class="tab-content" id="blogtab2">
				<ul>
				<?php
					if ( $popular_posts ) {
						echo $this->get_posts_list( $popular_posts );
					} else {
						echo html( 'li', __( 'There are no popular blog posts yet.', APP_TD ) );
					}
				?>
				</ul>
			</div>

			<div class="tab-content" id="blogtab3">
				<ul>
				<?php
					if ( $comments ) {
						echo $this->get_comments_list( $comments );
					} else {
						echo html( 'li', __( 'There are no blog comments yet.', APP_TD ) );
					}
				?>
				</ul>
			</div>

		</div>
<?php
	}

	/**
	 * Returns html list of posts.
	 *
	 * @param object $posts
	 * @return string
	 */
	protected function get_posts_list( $posts ) {

		$list = '';

		while ( $posts->have_posts() ) {

			$posts->the_post();

			$link = html_link( get_permalink(), get_the_title() );
			$date = html( 'span', get_the_date() );

			$list .= html( 'li', $link . ' ' . $date );
		}

		wp_reset_postdata();

		return $list;
	}

	/**
	 * Returns html list of comments.
	 *
	 * @param array $comments
	 * @return string
	 */
	protected function get_comments_list( $comments ) {

		$list = '';

		foreach ( $comments as $comment ) {

			$permalink = get_comment_link( $comment );
			$post_title = get_the_title( $comment->comment_post_ID );
			$link = html_link( $permalink, $post_title );

			$list .= html( 'li', sprintf( __( '%1$s on %2$s', APP_TD ), $comment->comment_author, $link ) );
		}

		return $list;
	}

	/**
	 * Returns recent posts.
	 *
	 * @param array $instance
	 * @return object|bool A boolean False if no posts found.
	 */
	protected function get_recent_posts( $instance ) {

		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) ) {
 			$number = 10;
 		}

		$args = array(
			'post_type'      => 'post',
			'posts_per_page' => $number,
			'paged'          => 1,
			'no_found_rows'  => true,
		);

		$posts = new WP_Query( $args );

		if ( ! $posts->have_posts() ) {
			return false;
		}

		return $posts;
	}

	/**
	 * Returns popular posts.
	 *
	 * @param array $instance
	 * @return object|bool A boolean False if no posts found.
	 */
	protected function get_popular_posts( $instance ) {

		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) ) {
 			$number = 10;
 		}

		$args = array(
			'post_type'      => 'post',
			'posts_per_page' => $number,
			'paged'          => 1,
			'no_found_rows'  => true,
		);

		$posts = new CLPR_Popular_Posts_Query( $args );

		if ( ! $posts->have_posts() ) {
			return false;
		}

		return $posts;
	}

	/**
	 * Returns recent comments.
	 *
	 * @param array $instance
	 * @return object|bool A boolean False if no comments found.
	 */
	protected function get_recent_comments( $instance ) {

		if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) ) {
 			$number = 10;
 		}

		$args = array(
			'post_type' => 'post',
			'number'    => $number,
			'status'    => 'approve',
			'type'      => '',
			'orderby'   => 'comment_date_gmt',
			'order'     => 'DESC',
		);

		$comments = get_comments( $args );

		if ( ! $comments ) {
			return false;
		}

		return $comments;
	}

	/**
	 * Prints widget scripts in footer.
	 *
	 * @return void
	 */
	public static function print_footer_scripts() {
?>
		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready(function() {
				jQuery('#blog_tab_controls li').first().addClass('active');
				jQuery('#blog-tabs .tab-content').first().show();

				jQuery( '#blog_tab_controls' ).on('click', 'a', function() {
					jQuery('#blog_tab_controls li').removeClass('active');
					jQuery('#blog-tabs .tab-content').hide();

					jQuery(this).parent().addClass('active');
					jQuery( jQuery(this).attr('href') ).show();
					return false;
				});
			});
			//]]>
		</script>
<?php
	}
}

/**
 * Footer Contact Form Widget. Not used.
 *
 * @since 1.0.0
 */
class CLPR_Widget_Contact_Footer extends APP_Widget {

	/**
	 * Setups widget.
	 *
	 * @param array $args (optional)
	 * @return void
	 */
	public function __construct( $args = array() ) {

		$default_args = array(
			'id_base'  => 'contact_form',
			'name'     => __( 'Clipper - Footer Contact Form', APP_TD ),
			'defaults' => array(
				'title'        => __( 'Contact Form', APP_TD ),
				'action'       => '#',
				'name_name'    => 'full_name',
				'email_name'   => 'email_address',
				'message_name' => 'message',
				'hname1'       => '',
				'hvalue1'      => '',
			),
			'widget_ops' => array(
				'description' => __( 'A simple contact form designed for the footer.', APP_TD ),
				'classname'   => 'widget_contact_form',
			),
			'control_options' => array( 'width' => 400, 'height' => 350 ),
		);

		extract( $this->_array_merge_recursive( $default_args, $args ) );

		parent::__construct( $id_base, $name, $widget_ops, $control_options, $defaults );
	}

	/**
	 * Returns an array of form fields.
	 *
	 * @return array
	 */
	protected function form_fields() {

		$form_fields = array(
			array(
				'type' => 'text',
				'name' => 'title',
				'desc' => __( 'Title:', APP_TD ),
			),
			array(
				'type' => 'text',
				'name' => 'action',
				'desc' => __( 'Form Post Action:', APP_TD ),
			),
			array(
				'type' => 'text',
				'name' => 'name_name',
				'desc' => __( 'Name field name:', APP_TD ),
			),
			array(
				'type' => 'text',
				'name' => 'email_name',
				'desc' => __( 'Email field name:', APP_TD ),
			),
			array(
				'type' => 'text',
				'name' => 'message_name',
				'desc' => __( 'Message field name:', APP_TD ),
			),
			array(
				'type' => 'text',
				'name' => 'hname1',
				'desc' => __( 'Hidden Field Name:', APP_TD ),
			),
			array(
				'type' => 'text',
				'name' => 'hvalue1',
				'desc' => __( 'Hidden Field Value:', APP_TD ),
			),
		);

		return $form_fields;
	}

	/**
	 * Displays widget content.
	 *
	 * @param array $instance
	 * @return void
	 */
	public function content( $instance ) {
		$instance = wp_parse_args( $instance, $this->defaults );
	?>
		<div class="contact-form-holder">
			<form class="contact-form" action="<?php echo esc_attr( $instance['action'] ); ?>" method="post">
				<fieldset>
					<input type="text" name="<?php echo esc_attr( $instance['name_name'] ); ?>" class="text" value="" placeholder="<?php esc_attr_e( 'Your name', APP_TD ); ?>" />
					<input type="email" name="<?php echo esc_attr( $instance['email_name'] ); ?>" class="text" value="" placeholder="<?php esc_attr_e( 'Your email address', APP_TD ); ?>" />
					<textarea name="<?php echo esc_attr( $instance['message_name'] ); ?>" rows="10" cols="30" class="text-area" placeholder="<?php esc_attr_e( 'Enter message', APP_TD ); ?>"></textarea>
					<div class="row">
						<button onsubmit="this.where.reset();return false;" name="submit" value="Submit" id="submit" title="<?php _e( 'Send', APP_TD ); ?>" type="submit" class="btn-submit"><span><?php _e( 'Send', APP_TD ); ?></span></button>
					</div>

					<input type="hidden" name="<?php echo esc_attr( $instance['hname1'] ); ?>" value="<?php echo esc_attr( $instance['hvalue1'] ); ?>" />
				</fieldset>
			</form>
		</div>
	<?php
	}
}

/**
 * Registers the custom sidebar widgets.
 *
 * @return void
 */
function clpr_widgets_init() {

	// widgets registered via APP_Widget::_register_widget()

}
add_action( 'widgets_init', 'clpr_widgets_init' );

/**
 * Removes some of the default sidebar widgets.
 *
 * @return void
 */
function clpr_unregister_widgets() {

	//unregister_widget( 'WP_Widget_Calendar' );
	unregister_widget( 'P2P_Widget' );
	//unregister_widget( 'WP_Widget_Search' );
	//unregister_widget( 'WP_Widget_Pages' );
	//unregister_widget( 'WP_Widget_Archives' );
	//unregister_widget( 'WP_Widget_Links' );
	//unregister_widget( 'WP_Widget_Categories' );
	//unregister_widget( 'WP_Widget_Recent_Posts' );
	//unregister_widget( 'WP_Widget_Tag_Cloud' );

}
add_action( 'widgets_init', 'clpr_unregister_widgets', 11 );

/**
 * Set the default font size for the tag cloud.
 *
 * @return array $args All the tag cloud arguments
 */
function clpr_tag_cloud_args( $args ) {

	$args['smallest'] = '9';
	$args['largest'] = '18';

	return $args;
}
add_filter( 'widget_tag_cloud_args', 'clpr_tag_cloud_args' );
