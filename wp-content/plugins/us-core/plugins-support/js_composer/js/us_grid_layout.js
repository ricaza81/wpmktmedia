! function( $ ) {
	$( '.us-grid-layout > .wpb-select' ).off( 'change' ).on( 'change', function() {
		var $select = $( this ),
			$container = $select.closest( '.us-grid-layout' ),
			$descEdit = $container.find( '.us-grid-layout-desc-edit' ),
			$descAdd = $container.find( '.us-grid-layout-desc-add' ),
			$selectedOption = $select.find( ":selected" ),
			$editLink = $descEdit.find( '.edit-link' );
		if ( $selectedOption.length && $selectedOption.data( 'edit-url' ) != undefined ) {
			$editLink.attr( 'href', $selectedOption.data( 'edit-url' ) );
			$descEdit.show();
			$descAdd.hide();
		} else {
			$descAdd.show();
			$descEdit.hide();
		}

	} ).change();
}( window.jQuery );
