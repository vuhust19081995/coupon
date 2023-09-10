jQuery( document ).ready( function( $ ) {

	$( '.period_type' ).each( function() {
		var period = $( '#'+$( this ).data( 'period-item' )+'_period' );

		$( this ).change( function() {
			var max = parseInt( payPeriods[ $( this ).val() ] );

			if ( parseInt( period.val() ) > max ) {
				period.val( max );
			}

			period.attr( 'max', max );
		} );

		$( this ).change();
	} );
} );