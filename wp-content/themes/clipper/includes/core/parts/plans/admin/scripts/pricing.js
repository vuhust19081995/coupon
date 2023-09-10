jQuery( document ).ready( function ( $ ) {
	$( '#_blank' ).prop( 'checked', true ).parent().hide();
	datepickerL10n.dateFormat = payLabels.dateFormat;

	var enableAddon = function ( enableBox, blankDateBox, actualDateBox, blankExpireBox, expireBox, durationBox ) {
		if ( !! enableBox.prop( "checked" ) ) {

			if ( durationBox.val() == "" ) {
				durationBox.val( 0 );
			}
			durationBox.prop( 'disabled', false );
			var dateToSet = actualDateBox.val() ? $.datepicker.parseDate( "yy-mm-dd", actualDateBox.val() ) : new Date();
			blankDateBox.prop( 'disabled', enableBox.attr( 'id' ) === '_blank' ).datepicker( "setDate", dateToSet );

			if ( expireBox.val() ) {
				blankExpireBox.datepicker( "setDate", $.datepicker.parseDate( "yy-mm-dd", expireBox.val() ) );
			}

			durationHandler( blankDateBox.val(), durationBox, blankExpireBox, expireBox );
		} else {
			blankDateBox.val( '' ).prop( 'disabled', true );
			durationBox.val( '' ).prop( 'disabled', true );
			blankExpireBox.val( '' );
		}
	};

	var expireHandler = function ( selectedDate, durationBox, expireBox, hiddenExpireBox ) {
		var duration = parseInt( durationBox.val() );
		if ( isNaN( duration ) )
			duration = 0;
		if ( duration === 0 ) {
			expireBox.val( payLabels.Never );
			hiddenExpireBox.val( '' );
			return false;
		}
		var selectedDateObj = $.datepicker.parseDate( datepickerL10n.dateFormat, selectedDate, datepickerL10n );
		selectedDateObj.setDate( selectedDateObj.getDate() + duration );
		expireBox.datepicker( 'setDate', selectedDateObj );
	};

	var durationHandler = function ( selectedDate, durationBox, expireBox, hiddenExpireBox ) {
		var
			selectedDateObj = $.datepicker.parseDate( datepickerL10n.dateFormat, selectedDate, datepickerL10n ),
			expireDateObj = $.datepicker.parseDate( datepickerL10n.dateFormat, expireBox.val(), datepickerL10n );

		if ( selectedDateObj && expireDateObj ) {
			var oneDay = 1000 * 60 * 60 * 24;
			difference = Math.ceil( (expireDateObj.getTime() - selectedDateObj.getTime()) / oneDay );
			durationBox.val( difference );
		}

		expireHandler( selectedDate, durationBox, expireBox, hiddenExpireBox );
	};

	$( '.enable-addon' ).each( function () {
		var enabled;       // State of Addon
		var flag;          // Addont type
		var payAddon;      // Current Addon info
		var blankDateBox;  // Date field in localized format
		var actualDateBox; // Date field in mySQL format (for database)
		var blankExpireBox;// Calculated date in localized format
		var expireBox;     // Expire Date field in mySQL format (for database)
		var durationBox;   // Addon duration in days

		enabled = !! $( this ).prop( "checked" );
		flag = $( this ).attr( "name" );

		if ( typeof payAddons !== 'undefined' && flag in payAddons ) {
			payAddon = payAddons[flag];
		} else {
			return true;
		}

		blankDateBox = $( "#_blank_" + payAddon.start_date_key );
		actualDateBox = $( "#" + payAddon.start_date_key );
		blankExpireBox = $( "#_blank_expire_" + flag );
		expireBox = blankExpireBox.parent().find( '.alt-date-field' );
		durationBox = $( "#" + payAddon.duration_key );

		blankExpireBox.datepicker( {
			dateFormat: datepickerL10n.dateFormat,
			altField: expireBox,
			altFormat: "yy-mm-dd",
			onClose: function () {
				durationHandler( blankDateBox.val(), durationBox, blankExpireBox, expireBox );
			}
		} );

		blankDateBox.datepicker( {
			dateFormat: datepickerL10n.dateFormat,
			altField: actualDateBox,
			altFormat: "yy-mm-dd",
			onClose: function ( selectedDate ) {
				expireHandler( selectedDate, durationBox, blankExpireBox, expireBox );
			}
		} );

		durationBox.attr( 'type', 'number' ).change( function () {
			expireHandler( blankDateBox.val(), durationBox, blankExpireBox, expireBox );
		} );

		$( this ).change( function () {
			enableAddon( $( this ), blankDateBox, actualDateBox, blankExpireBox, expireBox, durationBox );
		} ).change();
	} );
} );