/**
 * Setup plugins
 */
( function ( $ ) {

    $.validator.addMethod( 'maxchoice', function ( value, element, param ) {
        return $( 'input[name="' + element.name + '"]:checked' ).length <= Number( param ) || ! Number( param );
    }, $.validator.format( CLPR_i18n.category_limit ) );

    $.validator.addMethod( 'required_media', function ( value, element ) {
		return $( element ).closest( '.no-media' ).length === 0 || $( element ).closest( '.no-media' ).css('display') === 'none';
    }, $.validator.messages.required );

	$('.app-form').submit( function(e) {
		if ( typeof tinyMCE != "undefined" ) {
			// update underlying textarea before submit validation
			tinyMCE.triggerSave();
		}
	} ).validate( {
		ignore: '.ignore',
		errorClass: "invalid",
		errorElement: "div",
		rules: {
			'post_content': {
				minlength: 15
			}
		},
		errorPlacement: function( error, element ) {
			if ( element.attr( 'type' ) === 'checkbox' || element.attr( 'type' ) === 'radio' ) {
				element.closest( 'ul' ).before( error );
			} else {
				error.insertAfter( element );
			}
		}
	} );

}( jQuery ) );
