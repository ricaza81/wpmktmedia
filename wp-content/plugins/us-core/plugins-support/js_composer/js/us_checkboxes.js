;( function( $, undefined ) {
	"use strict";
	/* Class USCheckboxes */
	function USCheckboxes( container ) {
		// Elements
		this.$container = $( container );
		this.$value = $( 'input.us_checkboxes_value:first', this.$container );
		// Watch checkboxes
		this.$container
			.on( 'change', 'input.us_checkboxes_checkbox', this._events.change.bind( this ) );
	};
	USCheckboxes.prototype = {
		_events: {
			change: function( e ) {
				var $target = $( e.currentTarget ),
					targetValue = $target.val(),
					values = ( this.$value.val() || '' ).split(',');
				if ( $target.is( ':checked' ) ) {
					values.push( targetValue );
				} else {
					values = values.filter(function ( val ) {
						return val && val !== targetValue;
					})
				}
				this.$value.val( values.filter( function( v ) { return !!v; } ).join(',') );
			}
		}
	};
	$( '.us_checkboxes' ).each(function() {
		new USCheckboxes( this );
	});
} )( jQuery );
