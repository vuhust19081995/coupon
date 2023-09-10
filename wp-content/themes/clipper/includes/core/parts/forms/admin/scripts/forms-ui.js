/* global listingL10n */

(function ($) {
	$(document).on( 'formbuilderprops', function( e, frmb ) {

		frmb.addPropertyType( 'disable', {
			html  : wp.template( "app-field-property-checkbox" ),
			getData : function( context ) {
				return $( context ).find( ".prop-" + this.type ).prop( "checked" ) ? 1 : 0;
			}
		} );

		frmb.addPropertyType( 'tax', {
			label : listingL10n.taxonomy_label,
			html  : wp.template( "app-field-property-select" ),
			opts  : listingL10n.taxonomies
		} );

		frmb.addPropertyType( 'field_type', {
			label : listingL10n.field_type_label,
			html  : wp.template( "app-field-property-select" ),
			opts  : listingL10n.tax_inputs
		} );

		frmb.addPropertyType( 'editor_type', {
			label : listingL10n.editor_type_label,
			html  : wp.template( "app-field-property-select" ),
			opts  : listingL10n.editor_types
		} );

		frmb.addPropertyType( 'file_limit', {
			label      : listingL10n.file_limit_label,
			value      : -1,
			html       : wp.template( "app-field-property-input" ),
			input_type : 'number'
		} );

		frmb.addPropertyType( 'embed_limit', {
			label      : listingL10n.embed_limit_label,
			value      : -1,
			html       : wp.template( "app-field-property-input" ),
			input_type : 'number'
		} );

		frmb.addPropertyType( 'file_size', {
			label      : listingL10n.file_size_label,
			value      : listingL10n.file_size,
			tip        : listingL10n.file_size_tip,
			extra      : 'max="' + listingL10n.file_size + '"',
			html       : wp.template( "app-field-property-input" ),
			input_type : 'number'
		} );

		frmb.addPropertyType( 'placeholder', {
			label : listingL10n.placeholder_label,
			html  : wp.template( "app-field-property-input" )
		} );

		frmb._field.props = $.extend( {}, frmb._field.props, { disable : frmb._propertyTypes.disable } );
	} );

	$(document).on( 'formbuilderactions', function( e, frmb ) {
		frmb.addActionType( 'disable', {
			label : '<span class="dashicons dashicons-hidden"></span>',
			tip   : listingL10n.disable_tip,
			html  : wp.template( "app-field-action" )
		} );

		// add global actions to all field types
		frmb._field.actions = $.extend( {}, frmb._field.actions, { disable : frmb._actionTypes.disable } );
	} );

	$(document).on( 'formbuilderfields', function( e, frmb ) {

		// add placeholder property to text-like field type
		var textTypes = ['input_text', 'number', 'url', 'email'];
		for ( var index = 0; index < textTypes.length; ++index) {
			frmb.addFiledType( textTypes[index], {
				props : {
					placeholder : $.extend( {}, frmb._propertyTypes.placeholder )
				}
			} );
		}

		// add editor_type property to textarea field type
		frmb.addFiledType( 'textarea', {
			props : {
				editor_type : $.extend( {}, frmb._propertyTypes.editor_type ),
				placeholder : $.extend( {}, frmb._propertyTypes.placeholder, { label: listingL10n.placeholder_label + ' ' + listingL10n.placeholder_cond } )
			}
		} );

		// add media properties to file field type
		frmb.addFiledType( 'file', {
			props : {
				file_limit  : $.extend( {}, frmb._propertyTypes.file_limit ),
				embed_limit : $.extend( {}, frmb._propertyTypes.embed_limit ),
				file_size   : $.extend( {}, frmb._propertyTypes.file_size )
			}
		} );

		// add Tax Input field type if taxonomies available
		if ( listingL10n.taxonomies.length !== 0 ) {
			frmb.addFiledType( 'tax_input', {
				title : listingL10n.taxonomy_label,
				props : {
					tax        : $.extend( {}, frmb._propertyTypes.tax ),
					field_type : $.extend( {}, frmb._propertyTypes.field_type ),
					placeholder : $.extend( {}, frmb._propertyTypes.placeholder, { label: listingL10n.placeholder_label + ' ' + listingL10n.placeholder_cond } )
				},
				getData : function ( context ) {
					return {
						id    : 'tax_input[' + this.props.tax.getData( context ) + ']',
						type  : this.type,
						props : {}
					};
				}
			} );
		}

		$(document).on( 'change', '.prop-tax', function(e) { frmb._validate(e); } );
	} );

	$(document).on( 'formbuildervalidate', function( e, args ) {

		var _validate = function(e) {

			var valid = true;

			$( ".prop-tax" )
				.css( { backgroundColor:'#ffffff' } )
				.siblings( 'label.error' )
				.remove();

			$( ".prop-tax" ).each( function() {
				var val = $(this).val();
				var same = $( ".prop-tax" ).filter( function () {
					return $(this).val() === val;
				} )
				.length;

				if ( same > 1 ) {
					valid = false;
					$( this )
						.css( { backgroundColor:'#ffdada' } )
						.parent()
						.append('<label class="error">'+ listingL10n.same_tax +'</label>');
				}
			} );

			if ( ! valid ) {
				args.event.preventDefault();
			}
		};

		_validate(e);

	} );

	$( document ).on( 'formbuildercreate', function( e, args ) {
		// handle disable field
		$( '.frmb' ).on( 'click', 'a.disable', function( e ) {
			var input = $( this ).closest('.frm-elements').find( 'input.prop-disable' );

			input.prop( "checked", ! input.prop( "checked" ) );
			input.change();
			e.preventDefault();
		} );

		$( '.frmb' ).on( 'change', 'input.prop-disable', function( e ) {
			var action  = $( this ).closest( '.frm-elements' ).find( 'a.disable' );
			var icon    = action.find('.dashicons');
			var title   = ( $( this ).prop( "checked" ) ) ? listingL10n.enable_tip : listingL10n.disable_tip;
			var opacity = ( ! $( this ).prop( "checked" ) + 1 ) / 2;

			action.attr( 'title', title );
			$( this ).closest( 'li' ).css( 'opacity', opacity );

			icon.toggleClass( 'dashicons-visibility', $( this ).prop( "checked" ) );
			icon.toggleClass( 'dashicons-hidden', ! $( this ).prop( "checked" ) );
		} );

		$( 'input.prop-disable' ).closest( '.frm-group' ).hide();
		$( 'input.prop-disable' ).change();

		$( '.frmb' ).on( 'change', 'select.prop-editor_type, select.prop-field_type', function( e ) {
			var val = $( this ).val();
			var postbox = $( this ).closest( '.postbox' );
			var type = postbox.data( 'field-type' );

			if ( 'textarea' !== type && 'tax_input' !== type ) {
				return;
			}

			if ( val && 'text' !== val ) {
				$( this ).closest( '.frm-elements' ).find( '.prop-placeholder' ).attr( 'disabled', 'disabled' );
			} else {
				$( this ).closest( '.frm-elements' ).find( '.prop-placeholder' ).removeAttr( 'disabled' );
			}
		} );
		$( 'select.prop-editor_type, select.prop-field_type' ).change();
	} );

}(jQuery));
