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

	function regenerateAPISecret () {

		const data = {
			action: 'cartlassi_regenerate_api_secret',
			nonce: ajax_object.nonce,
		};
		return $.post(ajaxurl, data, function(response, status) {
			if (status === "success") {
				
				const { apiSecret } = JSON.parse(JSON.stringify(response));
				$('#cartlassi_field_api_secret').val(apiSecret);	
			} else {
				alert('error regenerating API secret', data);
				
			}
			return false;
		}, 'json');
	};

	function regenrateHash () {
		$.ajax({
			url: ajax_object.ajax_url,
			type: 'post',
			data: {
				action: 'cartlassi_demo_hash',
				nonce: ajax_object.nonce,
				include_email: $('#cartlassi_field_include_email_in_cart_id').is(':checked'),
				extra_encryption: $('#cartlassi_field_extra_encryption').is(':checked'),
			},
			success: (data, status) => {
				$('#cartlassi-demo-hash').val(JSON.parse(data).hash);
			},
			error: (err) => {
				// alert(JSON.stringify(err));
			}
		});
	}

	function cancelPaymentMethod () {
		$.ajax({
			url: ajax_object.ajax_url,
			type: 'post',
			data: {
				action: 'cartlassi_cancel_payment_method',
				nonce: ajax_object.nonce,
			},
			success: (data, status) => {
				location.reload();
			},
			error: (err) => {
				// alert(JSON.stringify(err));
			}
		});
	}

	function cancelPayoutMethod () {
		$.ajax({
			url: ajax_object.ajax_url,
			type: 'post',
			data: {
				action: 'cartlassi_cancel_payout_method',
				nonce: ajax_object.nonce,
			},
			success: (data, status) => {
				location.reload();
			},
			error: (err) => {
				// alert(JSON.stringify(err));
			}
		});
	}

	$(function() {
		$('#regenerate-api-key-button').click(function(event) {
			event.preventDefault();		
			regenerateAPIKey();
			// // Event.stop(event); // suppress default click behavior, cancel the event
		});
		$('#regenerate-api-secret-button').click(function(event) {
			event.preventDefault();		
			regenerateAPISecret();
			// // Event.stop(event); // suppress default click behavior, cancel the event
		});
		$('#cancel-payment-method-button').click(function(event) {
			event.preventDefault();		
			cancelPaymentMethod();
		});
		$('#cancel-payout-method-button').click(function(event) {
			event.preventDefault();		
			cancelPayoutMethod();
		});

		const queryParams = new Proxy(new URLSearchParams(window.location.search), {
			get: (searchParams, prop) => searchParams.get(prop),
		});
		  
		const paymentMethodform = $('<form></form>');
		paymentMethodform.attr('id', 'pay-form');
		paymentMethodform.attr('method', 'POST');
		paymentMethodform.attr('action', ajax_object.api_url + '/shops/payment-method')
		paymentMethodform.append('<input type="hidden" name="apiKey" value="' + ajax_object.api_key + '">');
		if(queryParams.welcome){
			paymentMethodform.append('<input type="hidden" name="welcome" value="true">');
		}
		const payoutMethodform = $('<form></form>');
		payoutMethodform.attr('id', 'payout-form');
		payoutMethodform.attr('method', 'POST');
		payoutMethodform.attr('action', ajax_object.api_url + '/shops/payout-method')
		payoutMethodform.append('<input type="hidden" name="apiKey" value="' + ajax_object.api_key + '">');
		if(queryParams.welcome){
			payoutMethodform.append('<input type="hidden" name="welcome" value="true">');
		}
		$('body').append(paymentMethodform);
		$('body').append(payoutMethodform);
		$('#pay-button').click(function(event) {
			event.preventDefault();
			$('#pay-form').submit();
		});
		if(queryParams['stripe-refresh']){
			$('#pay-form').submit();
		}
		$('#payout-button').click(function(event) {
			event.preventDefault();
			$('#payout-form').submit();
		});
		$('#cartlassi_field_include_email_in_cart_id').change(function(){
			regenrateHash();				
		});
		$('#cartlassi_field_extra_encryption').change(function(){
			regenrateHash();				
		});

		$('#cartlassi-connect-button').click(function(event) {
			if (!$('#cartlassi-tos').is(':checked')) {
				event.preventDefault();		
				alert('Please accept the terms of service');
			}
		});
	})
})( jQuery );
