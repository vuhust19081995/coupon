/*!
 * jQuery Category Fields Plugin to load Custom Form associated with selected
 * taxonomy terms.
 *
 * Use class 'app-category-fields' to apply plugin automatically.
 *
 * @version 1.0.0
 * @author  AppThemes
 */

( function( $ ) {

	$.fn.appCategoryFields = function( options ) {

		return this.each( function() {

			var settings = $.extend( {
				control      : $( this ),
				listing_id   : $( 'input[name="ID"]' ).val(),
				container    : $( '<div class="app-custom-form"></div>' ),
				addContainer : function( control, container ) {
					control.closest( '.form-field' ).after( container );
				},
				getFieldName : function( control ) {
					return control.is( 'select' ) ? control.attr( 'name' ) + "[]" : control.attr( 'name' );
				},
				getActionType: function( name ) {
					return 'app-render-' + name + '-form';
				}
			}, options );

			var
				ul   = settings.control,
				cats = ul.is( 'select' ) ? ul : ul.find( 'input' ),
				cont = settings.container;

			settings.addContainer( ul, cont );

			var getForm = function() {
				var
					matches = [],
					checked = cats.is( 'select' ) ? cats : cats.filter( ':checked' ),
					name    = settings.getFieldName( cats );

				checked.each( function() {
					matches.push( $( this ).val() );
				} );

				var data = {
					action: settings.getActionType( name ),
					cats: matches,
					listing_id: settings.listing_id,
					security: appCategoryFieldsL10n.nonce
				};

				$.post( AppThemes.ajaxurl, data, function( response ) {
					cont.html( response );

					// Following only for WordPress since 4.8 and for Editors.
					if ( ! wp.editor.initialize || ! wp.editor.remove ) {
						return;
					}

					cont.find( '.wp-editor-area' ).each( function() {
						var id = $( this ).attr( 'id' );
						var $wrap = $( '#wp-' + id + '-wrap' );
						var settings = {};

						// Detroy existing instances to be replaced.
						wp.editor.remove( id );
						if ( $wrap.length ) {
							$( this ).after( $wrap );
							$( this ).appendTo( $wrap.find( '.wp-editor-container' ) );
						}

						if ( $wrap.hasClass( 'tmce-active' ) ) {
							settings.tinymce = true;
						}

						if ( $wrap.hasClass( 'html-active' ) ) {
							settings.quicktags = {
								buttons: 'strong,em,link,block,del,ins,img,ul,ol,li,code'
							};
						}

						wp.editor.initialize( id, settings );
					} );
				} );
			};

			cats.change( function() {
				getForm();
			} );

			if ( ( cats.is( 'select' ) && cats.val() ) || cats.filter( ':checked' ).length > 0 ) {
				getForm();
			}

		} );

	};

}( jQuery ) );

jQuery( document ).ready( function( $ ) {
	$( '.app-category-fields' ).appCategoryFields();
} );
