/**
 * USOF Field: Text / Textarea
 */
! function( $, undefined ) {
	var _window = window,
		_document = document;

	if ( _window.$usof === undefined ) {
		return;
	}

	$usof.field[ 'text' ] = $usof.field[ 'textarea' ] = {
		init: function() {
			this.$row.on( 'click', '.usof-example', this.exampleClick.bind( this ) );
			this.$input.on( 'change keyup', function() {
				this.trigger( 'change', [ this.getValue() ] );
			}.bind( this ) );
		},
		exampleClick: function( ev ) {
			var $target = $( ev.target ).closest( '.usof-example' ),
				example = $target.html();
			this.setValue( example );
		}
	};
}( jQuery );
