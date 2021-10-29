/**
 * USOF Field: Reset
 */
! function( $, undefined ) {
	var _window = window,
		_document = document;

	if ( _window.$usof === undefined ) {
		return;
	}

	$usof.field[ 'reset' ] = {

		init: function() {
			this.$btnReset = this.$row.find( '.usof-button.type_reset' ).on( 'click', this.reset.bind( this ) );
			this.resetStateTimer = null;
			this.i18n = ( this.$row.find( '.usof-form-row-control-i18n' )[ 0 ].onclick() || {} );
		},

		reset: function() {
			if ( ! confirm( this.i18n.reset_confirm ) ) {
				return;
			}
			clearTimeout( this.resetStateTimer );
			this.$btnReset.addClass( 'loading' );
			$.ajax( {
				type: 'POST',
				url: $usof.ajaxUrl,
				dataType: 'json',
				data: {
					action: 'usof_reset',
					_wpnonce: $usof.instance.$container.find( '[name="_wpnonce"]' ).val(),
					_wp_http_referer: $usof.instance.$container.find( '[name="_wp_http_referer"]' ).val()
				},
				success: function( result ) {
					this.$btnReset.removeClass( 'loading' );
					alert( this.i18n.reset_complete );
					location.reload();
				}.bind( this )
			} );
		}

	};
}( jQuery );
