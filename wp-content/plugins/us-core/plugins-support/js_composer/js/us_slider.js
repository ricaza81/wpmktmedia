! function( $) {
	"use strict";
	$('.vc_ui-panel-content-container .usof-form-row.type_slider')
		.each(function() {
			var $this = $( this );
			$this.usofField();
			$this.data('usofField')
				.trigger('beforeShow');

		});
} ( window.jQuery) ;
