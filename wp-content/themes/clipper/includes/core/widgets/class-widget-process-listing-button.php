<?php
/**
 * Listing widgets
 *
 * @package Listing\Widgets
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Process widget class
 *
 * Shows a Process Listing button.
 */
class APP_Widget_Process_Listing_Button extends APP_Widget_Action_Button {

	/**
	 * Current Listing module object.
	 *
	 * @var APP_Listing
	 */
	protected $listing;

	/**
	 * Create a widget.
	 *
	 * @param APP_Listing $listing Listing object to assign process with.
	 * @param array       $args    Widget arguments.
	 */
	public function __construct( APP_Listing $listing, $args = array() ) {

		$this->listing = $listing;

		$type = $this->listing->get_type();
		$name = sprintf( __( 'Pay2Post Process Listing Button - %s', APP_TD ), $type );

		$default_args = array(
			'id_base'         => "appthemes_process_{$type}_button",
			'name'            => $name,
			'defaults'        => array(
				'title'  => '',
			),
			'widget_ops'      => array(
				'description' => $name,
			),
			'control_options' => array(),

		);

		$args = $this->_array_merge_recursive( $default_args, $args );

		parent::__construct( $args );
	}

	/**
	 * Widget form field set
	 *
	 * @return array Widget form fields
	 */
	function form_fields() {

		$fields    = parent::form_fields();
		$type      = $this->listing->get_type();
		$processes = APP_View_Process::get_process( $type );

		if ( empty( $processes ) ) {
			return $fields;
		}

		$choices = array();

		foreach ( $processes as $process_type => $process ) {
			$page = get_post( $process->get_page_id() );

			if ( ! $page ) {
				continue;
			}

			$choices[ $process_type ] = $page->post_title;
		}

		// Remove url field since it always should has default value.
		foreach ( $fields as $key => $field ) {
			if ( 'url' === $field['name'] ) {
				unset( $fields[ $key ] );
			}
		}

		$fields[] = array(
			'desc'   => __( 'Process Type', APP_TD ),
			'type'   => 'select',
			'name'   => 'process_type',
			'values' => $choices,
		);

		return $fields;
	}

	/**
	 * Displays widget content.
	 *
	 * @param array $args     Display arguments including before_title,
	 *                        after_title, before_widget, and after_widget.
	 * @param array $instance The settings for the particular instance of the
	 *                        widget.
	 *
	 * @return void
	 */
	public function widget( $args, $instance ) {
		$instance     = array_merge( $this->defaults, (array) $instance );
		$type         = $this->listing->get_type();
		$process_type = $instance['process_type'];
		$process      = APP_View_Process::get_process( $type, $process_type );

		$instance['action'] = "app-{$process_type}-{$type}-button";
		$instance['url']    = $process->get_process_url();

		if ( $instance['url'] ) {
			parent::widget( $args, $instance );
		}

	}

}
