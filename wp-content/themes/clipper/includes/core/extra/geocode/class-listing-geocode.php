<?php
/**
 * Listing geocode address class.
 *
 * This module is responsible for listing address processing.
 *
 * @package Listing\Modules\Geocode
 * @author  AppThemes
 * @since   Listing 1.0
 */

/**
 * Listing Geocode Address class.
 */
class APP_Listing_Geocode {

	/**
	 * Current Listing module object.
	 *
	 * @var APP_Listing
	 */
	protected $listing;

	/**
	 * The listing address form field name.
	 *
	 * @var string
	 */
	protected $name;

	/**
	 * Additional meta fields to be saved with current field value and
	 * coordinates.
	 *
	 * The array keys correspond to keys of retrieved geocode data.
	 * The values correspond to meta field names to be saved in the database.
	 *
	 * @var array
	 */
	protected $fields = array();

	/**
	 * Constructs listing geocode address object.
	 *
	 * @param APP_Listing $listing  Listing object to assign process with.
	 * @param string      $name     Address form field name.
	 */
	public function __construct( APP_Listing $listing, $name = 'address' ) {

		$this->listing = $listing;
		$this->name    = $name;

		if ( did_action( 'init' ) ) {
			$this->init();
		} else {
			add_action( 'init', array( $this, 'init' ) );
		}
	}

	/**
	 * Init module.
	 */
	public function init() {
		if ( ! class_exists( 'APP_Geocoder_Registry' ) || ! APP_Geocoder_Registry::get_active_geocoder() ) {
			return;
		}

		add_filter( 'appthemes_form_field', array( $this, 'form_field' ), 10, 2 );
		add_action( "wp_ajax_{$this->listing->get_type()}_geocode_{$this->get_field_id()}_field", array( $this, 'handle_ajax' ) );
	}

	/**
	 * Adds extra parameters to listing form field.
	 *
	 * @param array $field   Form field.
	 * @param int   $item_id Listing item ID.
	 *
	 * @return array Modified Form field.
	 */
	public function form_field( $field, $item_id ) {

		if ( $this->get_name() !== $field['name'] ) {
			return $field;
		}

		if ( $this->listing->get_type() !== get_post_type( $item_id ) ) {
			return $field;
		}

		$field['listing_id'] = $item_id;

		$field['render'] = array( $this, 'render' );

		// Set custom validator.
		$field['sanitizers'][] = array( $this, 'validate' );

		return $field;
	}

	/**
	 * Validates field.
	 *
	 * Checks if address field was changed or never geocoded and try to update
	 * geocoder data before field save.
	 *
	 * @param mixed          $value  Posted field value.
	 * @param scbCustomField $inst   Field object.
	 * @param WP_Error       $errors Errors object.
	 *
	 * @return mixed Validated value.
	 */
	public function validate( $value, $inst, $errors ) {

		$prev_value = get_post_meta( $inst->listing_id, $inst->name, true );
		$has_coords = appthemes_get_coordinates( $inst->listing_id, false );
		$data       = array();

		if ( $has_coords && $value === $prev_value ) {
			return $value;
		}

		$data_key = $inst->name . '_geo_data';

		if ( ! empty( $_POST[ $data_key ] ) ) {
			parse_str( $_POST[ $data_key ], $data );
			$data = wp_unslash( $data );
		}

		if ( ! trim( $value ) ) {
			$response = $this->delete_data( $inst->listing_id );
			if ( $has_coords && ! $response ) {
				$errors->add( 'cant_delete_coords', __( 'Could not delete geo data.', APP_TD ) );
			}
			return $value;
		}

		if ( empty( $data ) ) {
			$data = $this->geocode_address( $value, $errors );
		}

		$data['address'] = $value;

		if ( empty( $data['lat'] ) && empty( $data['coords']['lng'] ) ) {
			$errors->add( 'cant_geocode_address', __( 'Could not geocode address.', APP_TD ) );
			if ( ! empty( $data['response_code'] ) ) {
				$errors->add( 'geocoder_error_code', sprintf( __( 'Geocoder returned with error code "%s".', APP_TD ), $data['response_code'] ) );
			}
			return $value;
		}

		$this->set_data( $inst->listing_id, $data, $errors );

		return $value;
	}

	/**
	 * Retrieves geocode data by address.
	 *
	 * @param string   $value  An address to geocode.
	 * @param WP_Error $errors Errors object.
	 *
	 * @return array Geodata.
	 */
	public function geocode_address( $value, $errors ) {
		$data = appthemes_geocode_address( $value );
		return json_decode( wp_json_encode( $data ), true );
	}

	/**
	 * Deletes geo data from database.
	 *
	 * @param int $listing_id Listing ID.
	 *
	 * @return int|bool Boolean False on failure.
	 */
	public function delete_data( $listing_id ) {
		foreach ( $this->get_geo_fields() as $name ) {
			$this->listing->meta->delete_meta( $listing_id, $name );
		}
		return appthemes_delete_coordinates( $listing_id );
	}

	/**
	 * Set geo data.
	 *
	 * @param int      $listing_id Listing ID.
	 * @param array    $data       Geo data array to be saved.
	 * @param WP_Error $errors     Errors object.
	 */
	public function set_data( $listing_id, $data, $errors ) {
		$data['lat'] = ! empty( $data['coords']['lat'] ) ? $data['coords']['lat'] : ( ! empty( $data['lat'] ) ? $data['lat'] : 0 );
		$data['lng'] = ! empty( $data['coords']['lng'] ) ? $data['coords']['lng'] : ( ! empty( $data['lng'] ) ? $data['lng'] : 0 );

		// Address components can come as not formatted array, so we have to
		// re-generate it from raw data.
		if ( isset( $data['address_components'] ) && ! isset( $data['address_components']['country'] ) ) {
			$geocoder = APP_Geocoder_Registry::get_active_geocoder();
			if ( $geocoder ) {
				$data['address_components'] = $geocoder->parse_address_components( $data['address_components'] );
			}
		}

		$response = appthemes_set_coordinates( $listing_id, $data['lat'], $data['lng'] );

		if ( false === $response ) {
			$errors->add( 'cant_save_geodata', __( 'Could not save geo data.', APP_TD ) );
			return;
		}

		foreach ( $this->get_geo_fields() as $key => $name ) {
			// Some fields may refer to address components.
			if ( ! isset( $data[ $key ] ) && isset( $data['address_components'][ $key ] ) ) {
				$data[ $key ] = $data['address_components'][ $key ];
			}
			if ( isset( $data[ $key ] ) ) {
				$this->listing->meta->update_meta( $listing_id, $name, $data[ $key ] );
			} else {
				$this->listing->meta->delete_meta( $listing_id, $name );
			}
		}
	}

	/**
	 * Retrieves geo data from database.
	 *
	 * @param int $listing_id The listing ID.
	 *
	 * @return array
	 */
	public function get_data( $listing_id ) {
		$data = array();

		foreach ( $this->get_geo_fields() as $key => $value ) {
			$data[ $key ] = $this->listing->meta->get_meta( $listing_id, $value, true );
		}

		$coords = appthemes_get_coordinates( $listing_id );
		$data = array_merge( $data, (array) $coords );

		return $data;
	}

	/**
	 * Field renderer.
	 *
	 * @param string   $value Field value (raw address).
	 * @param scbField $inst  Field object.
	 *
	 * @return string Generated HTML.
	 */
	public function render( $value, $inst ) {

		$field_id   = $this->get_field_id();
		$coord      = appthemes_get_coordinates( $inst->listing_id );
		$ajax_nonce = wp_create_nonce( "{$this->listing->get_type()}_geocode_{$field_id}_nonce" );
		$action     = "{$this->listing->get_type()}_geocode_{$field_id}_field";

		$field = array(
			'type'      => 'text',
			'name'      => $inst->name,
			'title'     => $inst->title,
			'desc'      => $inst->desc,
			'wrap'      => $inst->wrap,
			'wrap_each' => $inst->wrap_each,
			'extra'     => array_merge( (array) $inst->extra, array(
				'id' => $field_id,
			) ),
		);

		ob_start();
		echo scbForms::input_with_value( $field, $value );

		?>
		<input id="<?php echo esc_attr( $field_id ); ?>_find_on_map" type="button" style="display:none;" value="<?php esc_attr_e( 'Find on map', APP_TD ); ?>">
		<div id="<?php echo esc_attr( $field_id ); ?>_map_div" style="margin:20px 0;width:100%;height:350px;position:relative;display:none"></div>
		<input id="<?php echo esc_attr( $field_id ); ?>_lat" name="lat" type="hidden" value="<?php echo esc_attr( $coord->lat ); ?>" />
		<input id="<?php echo esc_attr( $field_id ); ?>_lng" name="lng" type="hidden" value="<?php echo esc_attr( $coord->lng ); ?>" />
		<input id="<?php echo esc_attr( $field_id ); ?>_geo_data" name="<?php echo esc_attr( $field_id ); ?>_geo_data" type="hidden" value="" />

		<script type="text/javascript">

			jQuery( function( $ ) {
				if ( !$().appthemes_map || !$().appAddressAutocomplete ) {
					return;
				}

				var
					input = $( "#<?php echo esc_js( $field_id ); ?>" ),
					id = input.attr( 'id' ),
					lat_input = $( "#"+id+"_lat" ),
					lng_input = $( "#"+id+"_lng" ),
					data_input = $( "#"+id+"_geo_data" );
					button = $( "#"+id+"_find_on_map" );

				input
					.appAddressAutocomplete( {
						ready: function() {
							var widget = $( this ).data( 'appthemesAppAddressAutocomplete' );
							if ( ! widget.api ) {
								var data = {};
								widget.getPlaceData = function() {
									return data;
								};
								button
									.click( function() {
										$.getJSON( AppThemes.ajaxurl, {
											action: '<?php echo $action; ?>',
											security: '<?php echo $ajax_nonce; ?>',
											address: input.val()
										}, function( response ) {
											data = {
												lat: response.coords.lat,
												lng: response.coords.lng,
												formatted_address: response.address,
												address_components: response.address_components
											};
											widget.onPlaceChange();
										} );
									} )
									.show();
							}
						}
					} )
					.on( 'appaddressautocompleteonplacechange', function() {
						var map = $('#'+id+'_map_div').data( 'appthemesAppthemes_map' );
						map.update_marker_position( {
							lat: lat_input.val(),
							lng: lng_input.val()
						} );
					} )
					.on( 'appaddressautocompletepopulateinputs', function( e, data ) {
						data_input.val( decodeURIComponent( $.param( data ) ) );
					} );

				$('#'+id+'_map_div').appthemes_map( {
					markers: [ {
						lat : +lat_input.val(),
						lng : +lng_input.val(),
						draggable : ( function() {
							// Don't make marker draggable If drag and drop is not supported by map provider.
							return $.appthemes.appthemes_map.prototype._marker_drag_end.toString() !== 'function( marker ) {}';
						} )()
					} ],
					center_lat: +lat_input.val(),
					center_lng: +lng_input.val(),
					marker_drag_end: function( marker, lat, lng ) {
						lat_input.val( lat );
						lng_input.val( lng );

						$.getJSON( AppThemes.ajaxurl, {
							action: '<?php echo $action; ?>',
							security: '<?php echo $ajax_nonce; ?>',
							lat: lat,
							lng: lng
						}, function( response ) {
							data_input.val( decodeURIComponent( $.param( response ) ) );
							if( response.address ) {
								input.val( response.address );
							}
						} );
					}
				} )
				.show();
			} );
		</script>
		<?php

		return ob_get_clean();
	}

	/**
	 * Handles AJAX actions.
	 */
	public function handle_ajax() {
		check_ajax_referer( "{$this->listing->get_type()}_geocode_{$this->get_field_id()}_nonce", 'security' );

		if ( isset( $_GET['address'] ) ) {
			$api_response = appthemes_geocode_address( sanitize_text_field( $_GET['address'] ) );
		} elseif ( isset( $_GET['lat'] ) ) {
			$api_response = appthemes_geocode_lat_lng( floatval( $_GET['lat'] ), floatval( $_GET['lng'] ) );
		}

		if ( ! $api_response ) {
			die( "error" );
		}

		die( json_encode( $api_response ) );
	}

	/**
	 * Retrieves address field name.
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Retrieves address field name.
	 *
	 * @return string
	 */
	public function get_field_id() {
		return implode( '_', (array) $this->get_name() );
	}

	/**
	 * Retrives additional meta fields to be saved with current field value and
	 * coordinates.
	 *
	 * @return array
	 */
	public function get_geo_fields() {
		return apply_filters( 'appthemes_listing_geo_fields', $this->fields, $this->get_name(), $this->listing->get_type() );
	}
}
