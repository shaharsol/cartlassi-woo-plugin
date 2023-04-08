(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$.ajax({
		url: ajax_object.ajax_url,
		type: 'post',
		data: {
			action: 'cartlassi_load_widget',
			nonce: ajax_object.nonce,
		},
		success: (data, status) => {
			$('#cartlassi-ajax-widget').html(data);
			$('#cartlassi-ajax-widget').on('click', 'a', (event) => {
				$.post(ajax_object.ajax_url, {
					action: 'cartlassi_log_click',
					nonce: ajax_object.nonce,
					product_id: $(event.currentTarget).data('product-id'),
					cartlassi_id: $(event.currentTarget).data('cartlassi'), 
				}, function(response, status) {
					// alert(status);
					// alert(response);
				}, 'json');
		
			})
			$( document.body ).trigger( 'post-load' );
		},
		error: (err) => {
			// alert(JSON.stringify(err));
		}
	});

})( jQuery );
