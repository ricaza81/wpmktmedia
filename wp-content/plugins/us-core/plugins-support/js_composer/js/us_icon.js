! function( $ ) {
	var USIconSetValue = function( $container ) {
		var $select = $container.find( '.us-icon-select' ),
			$input = $container.find( '.us-icon-text' ),
			$value = $container.find( '.us-icon-value' ),
			$preview = $container.find( '.us-icon-preview > i' ),
			icon_set = $select.val(),
			icon_name = $.trim( $input.val() ),
			icon_no_resize = icon_name.replace( /fa-\dx/gi, ' ' ),
			icon_val = '';

		if ( icon_name != '' ) {
			if ( icon_set == 'material' ) {
				icon_name = icon_name.replace( / +/g, '_' );
				$preview.attr( 'class', 'material-icons' ).html( icon_name );
			} else {
				$preview.attr( 'class', icon_set + ' fa-' + icon_no_resize ).html( '' );
			}
			icon_val = icon_set + '|' + icon_name;
		} else {
			// Case when removing all text in input at a time, "Crtl + A + Del", for instance
			$preview.attr( 'class', '' ).html( '' );
		}

		$value.val( icon_val );

	};
	$( '.us-icon-select' ).off( 'change' ).on( 'change', function() {
		var $select = $( this ),
			$container = $select.closest( '.us-icon' ),
			$descContainer = $container.siblings( '.us-icon-desc' ).first(),
			$selectedOption = $select.find( ":selected" ),
			$setLink = $descContainer.find( '.us-icon-set-link' );
		if ( $selectedOption.length ) {
			$setLink.attr( 'href', $selectedOption.data( 'info-url' ) );
		}

		USIconSetValue( $container );
	} );
	$( '.us-icon-text' ).off( 'change keyup' ).on( 'change keyup', function() {
		var $input = $( this ),
			$container = $input.closest( '.us-icon' ),
			val = $input.val();
		if ( val.toLowerCase().replace( /^\s+/g, '' ) !== val ) {
			$input.val( $.trim( val.toLowerCase() ) );
		}

		USIconSetValue( $container );
	} );
}( window.jQuery );