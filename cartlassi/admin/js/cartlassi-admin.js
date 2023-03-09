(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
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

	function regenerateAPIKey () {

		const data = {
			action: 'cartlassi_regenerate_api_key',
			nonce: ajax_object.nonce,
		};
		return $.post(ajaxurl, data, function(response, status) {
			if (status === "success") {
				const { apiKey } = JSON.parse(JSON.stringify(response));
				$('#cartlassi_field_api_key').val(apiKey);	
			} else {
				alert('error regenerating API key', data);
				
			}
			return false;
		}, 'json');
	};

	$(function() {
		$('#regenerate-api-key-button').click(function(event) {
			event.preventDefault();		
			regenerateAPIKey();
			// // Event.stop(event); // suppress default click behavior, cancel the event
		});
		const form = $('<form></form>');
		form.attr('id', 'pay-form');
		form.attr('method', 'POST');
		form.attr('action', 'http://localhost:3000/shops/payment-method')
		form.append('<input type="hidden" name="apiKey" value="' + ajax_object.api_key + '">');
		$('body').append(form);
		$('#pay-button').click(function(event) {
			event.preventDefault();
			// alert('aloha');
			$('#pay-form').submit();
			// alert('shaloa');
		});
	
	})


})( jQuery );
