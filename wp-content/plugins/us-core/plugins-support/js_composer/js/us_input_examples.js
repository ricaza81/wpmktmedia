! function( $ ) {
	$( document ).on( 'click', '.usof-example', function( ev ) {
		ev.preventDefault();
		ev.stopPropagation();

		var $target = $( ev.target ).closest( 'span' ),
			$input = $target
				.closest( '.edit_form_line:not(.usof-not-live)' )
				.find( 'input[type="text"]' ),
			value = $target.html();

		$input.val( value ).trigger( 'change' );
	} );
}( window.jQuery );
