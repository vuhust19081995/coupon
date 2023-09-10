<?php
/**
 * Listing functions and template tags
 *
 * @package Listing\Functions
 * @author  AppThemes
 * @since   Listing 1.0
 */

add_shortcode( 'appthemes_process_listing_link', 'appthemes_process_link_shortcode' );
add_shortcode( 'appthemes_listing_details', 'appthemes_listing_details_shortcode' );
add_shortcode( 'appthemes_listing_terms', 'appthemes_listing_terms_shortcode' );

/**
 * Displays listing details form.
 *
 * Won't display if listing type or form module unavailable.
 *
 * @param int|object|null $item        Optional. Post ID or post object to
 *                                     display form for. Default is global
 *                                     $post.
 * @param array           $form_fields Optional. Form fields array to be used
 *                                     instead.
 * @param string          $item_type   The listing item type. If empty, presumed
 *                                     that the item is a WP_Post object and
 *                                     item type is a post type.
 */
function appthemes_listing_form( $item = null, $form_fields = array(), $item_type = '' ) {

	// Fallback item type presumed as post type.
	$item_type = $item_type ? $item_type : get_post_type( $item );

	$listing_obj = APP_Listing_Director::get( $item_type );

	if ( ! $listing_obj || ! $listing_obj->form ) {
		return;
	}

	$item = $listing_obj->meta->get_item( $item );

	if ( ! $item || is_wp_error( $item ) ) {
		return;
	}

	/* @var $form APP_Listing_Form */
	$form    = $listing_obj->form;
	$item_id = $listing_obj->meta->get_item_id( $item );

	echo $form->get_form( $item_id, $form_fields );
}

/**
 * Retrieves generated listing details HTML
 *
 * @uses APP_Listing_Details::get_details() Original details generator.
 *
 * @param int|WP_Post|null $item     Optional. Post ID or post object to display
 *                                   form for. Default is global $post.
 * @param array            $include  Retrieves A list of field attributes used
 *                                   to include certain fields in the final
 *                                   field list (similar to $exclude).
 * @param array            $exclude  An associative array with field
 *                                   attribute/value(s) used to excluded certain
 *                                   fields from the final field list
 *                                   (e.g: array( 'name' => 'my-field' ) ).
 * @param string           $format   The string that contains tokens to be
 *                                   replaced with field value, title, id and
 *                                   type. The list of allowed tokens:
 *                                    - `%value%`
 *                                    - `%title%`
 *                                    - `%id%`
 *                                    - `%type%`
 *                                   Example:
 *                                   `<li id="%id%"><b>%title%</b>: %value%</li>`.
 * @param string           $type     The listing item type. If empty, presumed
 *                                   that the item is a WP_Post object and item
 *                                   type is a post type.
 *
 * @return string Generated HTML
 */
function appthemes_listing_details( $item = null, $include = array(), $exclude = array(), $format = '', $type = '' ) {

	// Fallback item type presumed as post type.
	$type = $type ? $type : get_post_type( $item );

	$listing_obj = APP_Listing_Director::get( $type );

	if ( ! $listing_obj || ! $listing_obj->details ) {
		return;
	}

	$item = $listing_obj->meta->get_item( $item );

	if ( ! $item || is_wp_error( $item ) ) {
		return;
	}

	/* @var $details APP_Listing_Details */
	$details = $listing_obj->details;
	$item_id = $listing_obj->meta->get_item_id( $item );

	return $details->get_details( $item_id, $exclude, $include, $format );
}

/**
 * Retrieves process URL by given process and listing types.
 *
 * @param string  $listing_type Listing type.
 * @param string  $process_type Process type.
 * @param int     $item_id      An item ID.
 * @param WP_User $user         A user ID to get process URL for. (Default is
 *                              current user).
 *
 * @return string|null The process URL on success, or NULL on failure.
 */
function appthemes_get_process_url( $listing_type = 'post', $process_type = 'new', $item_id = null, $user = null ) {
	global $current_user;

	$process = APP_View_Process::get_process( $listing_type, $process_type );

	if ( ! $process instanceof APP_View_Process ) {
		return;
	}

	if ( $user instanceof WP_User ) {
		$_current_user = $current_user;
		$current_user  = $user;
		$url           = $process->get_process_url( $item_id );
		$current_user  = $_current_user;
	} else {
		$url = $process->get_process_url( $item_id );
	}

	return $url;
}

/**
 * Generates Action Button HTML
 *
 * @param string $url   The button action URL.
 * @param string $label The button content.
 * @param string $class CSS class to be used for button div wrapper.
 *
 * @return string Generated HTML
 */
function appthemes_get_action_link( $url = '', $label = '', $class = 'app-action-button' ) {

	if ( ! $url ) {
		return;
	}

	$link = html( 'a', array(
		'href'  => esc_url( $url ),
		'class' => 'button large',
	), $label );

	$link = html( 'div', array(
		'class' => $class,
	), $link );

	return apply_filters( 'appthemes_get_action_link', $link, $url, $label, $class );
}

/**
 * Generates Listing Process button HTML
 *
 * @param array $atts {
 *     Shortcode attributes.
 *
 *     @type string $type    Post type slug to retrieve button for.
 *                           Default 'post'.
 *     @type string $process Process type to retrieve button for. Default 'new'.
 *     @type string $label   The button content. By default process page title.
 *     @type string $class   The button wrapper CSS class.
 * }
 * @return string Generated HTML
 */
function appthemes_process_link_shortcode( $atts ) {

	$a = shortcode_atts( array(
		'type'    => 'post',
		'process' => 'new',
		'label'   => '',
		'class'   => '',
	), $atts );

	$url = appthemes_get_process_url( $a['type'], $a['process'] );

	if ( ! $url ) {
		return;
	}

	$process = APP_View_Process::get_process( $a['type'], $a['process'] );

	$label = ( $a['label'] ) ? $a['label'] : get_the_title( $process->get_page_id() );
	$class = $a['class'];

	if ( ! $class ) {
		$class = "app-action-button app-{$a['process']}-{$a['type']}-button";
	}

	return appthemes_get_action_link( $url, $label, $class );
}

/**
 * Retrieves Listing Details Shortcode HTML.
 *
 * Note, this shortcode doesn't generate taxonomy fields,
 * use [appthemes_listing_terms] instead.
 *
 * @param array $atts {
 *     Shortcode attributes.
 *
 *     @type  int   $item_id Current listing item id.
 *     @type  array $fields  Delimited by comma fields names to be included.
 *                           Shows all fields if empty.
 *     @type  array $exclude Delimited by comma fields names to be excluded.
 *                           Default 'post_title, post_content'.
 * }
 * @return string Generated HTML
 */
function appthemes_listing_details_shortcode( $atts = array() ) {

	$a = shortcode_atts( array(
		'item_id' => null,
		'type'    => '',
		'fields'  => '',
		'exclude' => '',
	), $atts );

	// Fallback item type presumed as post type.
	$type = $a['type'] ? $a['type'] : get_post_type( $a['item_id'] );

	$listing_obj = APP_Listing_Director::get( $type );

	if ( ! $listing_obj ) {
		return;
	}

	$item = $listing_obj->meta->get_item( $a['item_id'] );

	if ( ! $item || is_wp_error( $item ) ) {
		return;
	}

	$fields          = array();
	$exclude         = array();
	$include_filters = array();
	$exclude_filters = array(
		'name' => array( 'post_title', 'post_content' ),
		'type' => 'tax_input',
	);

	$a['fields'] = trim( $a['fields'] );
	$a['exclude'] = trim( $a['exclude'] );

	if ( ! empty( $a['fields'] ) ) {
		$fields = array_map( 'trim', explode( ',', $a['fields'] ) );
		$include_filters = array( 'name' => $fields );
	}

	if ( ! empty( $a['exclude'] ) ) {
		$exclude = array_map( 'trim', explode( ',', $a['exclude'] ) );
		$exclude_filters['name'] = array_merge( $exclude, $exclude_filters['name'] );
	}

	return appthemes_listing_details( $item, $include_filters, $exclude_filters, '', $type );
}

/**
 * Retrieves Listing Terms Shortcode HTML
 *
 * @param array $atts {
 *     Shortcode attributes.
 *
 *     @type  int   $item_id    Current listing item id.
 *     @type  array $taxonomies Delimited by comma taxonomy names to be included.
 *                              Shows all taxonomy types if empty.
 *     @type  array $exclude    Delimited by comma taxonomy names to be excluded.
 * }
 * @return string Generated HTML
 */
function appthemes_listing_terms_shortcode( $atts = array() ) {

	$a = shortcode_atts( array(
		'item_id'    => null,
		'type'       => '',
		'taxonomies' => '',
		'exclude'    => '',
	), $atts );

	// Fallback item type presumed as post type.
	$type = $a['type'] ? $a['type'] : get_post_type( $a['item_id'] );

	$listing_obj = APP_Listing_Director::get( $type );

	if ( ! $listing_obj ) {
		return;
	}

	$item = $listing_obj->meta->get_item( $a['item_id'] );

	if ( ! $item || is_wp_error( $item ) ) {
		return;
	}

	$taxonomies      = array();
	$exclude         = array();
	$include_filters = array( 'type' => 'tax_input' );
	$exclude_filters = array();

	$a['taxonomies'] = trim( $a['taxonomies'] );
	$a['exclude'] = trim( $a['exclude'] );

	if ( ! empty( $a['taxonomies'] ) ) {
		$taxonomies = array_map( 'trim', explode( ',', $a['taxonomies'] ) );
		foreach ( $taxonomies as &$taxonomy ) {
			$taxonomy = sprintf( 'tax_input[%s]', $taxonomy );
		}

		$include_filters = array( 'name' => $taxonomies );
	}

	if ( ! empty( $a['exclude'] ) ) {
		$exclude = array_map( 'trim', explode( ',', $a['exclude'] ) );
		foreach ( $exclude as &$taxonomy ) {
			$taxonomy = sprintf( 'tax_input[%s]', $taxonomy );
		}
		$exclude_filters = array( 'name' => $exclude );
	}

	return appthemes_listing_details( $item, $include_filters, $exclude_filters, '', $type );
}
