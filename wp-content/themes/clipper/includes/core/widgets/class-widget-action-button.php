<?php
/**
 * Listing widgets
 *
 * @package Listing\Widgets
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Generic Action Button widget class
 */
class APP_Widget_Action_Button extends APP_Widget {

	/**
	 * Create a widget
	 *
	 * @param array $args Widget arguments.
	 */
	public function __construct( $args = array() ) {

		$default_args = array(
			'id_base'         => 'appthemes_action_button',
			'name'            => __( 'Pay2Post Action Button', APP_TD ),
			'control_options' => array(),
			'defaults'        => array(
				'title'  => __( 'Action Button', APP_TD ),
				'label'  => __( 'Action', APP_TD ),
				'url'    => home_url(),
				'action' => '',
			),
			'widget_ops'      => array(
				'description' => __( 'Generic Action Button.', APP_TD ),
			),
		);

		$args = $this->_array_merge_recursive( $default_args, $args );

		parent::__construct(
			$args['id_base'],
			$args['name'],
			$args['widget_ops'],
			$args['control_options'],
			$args['defaults']
		);
	}

	/**
	 * This is where the actual widget content goes.
	 *
	 * @param array $instance The settings for the particular instance of the widget.
	 */
	public function content( $instance ) {
		$instance = array_merge( $this->defaults, (array) $instance );
		$wrapper_class = "app-action-button {$instance['action']}";

		echo appthemes_get_action_link( $instance['url'], $instance['label'], $wrapper_class );
	}

	/**
	 * Widget form field set
	 *
	 * @return array Widget form fields
	 */
	function form_fields() {

		$fields = array(
			array(
				'type' => 'text',
				'name' => 'title',
				'desc' => __( 'Title:', APP_TD ),
			),
			array(
				'type' => 'text',
				'name' => 'label',
				'desc' => __( 'Label:', APP_TD ),
			),
			array(
				'type' => 'text',
				'name' => 'url',
				'desc' => __( 'Url:', APP_TD ),
			),
		);

		return $fields;
	}

}
