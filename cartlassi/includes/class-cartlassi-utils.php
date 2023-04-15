<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Cartlassi
 * @subpackage Cartlassi/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Cartlassi
 * @subpackage Cartlassi/includes
 * @author     Your Name <email@example.com>
 */
class Cartlassi_Utils {

	protected $config;

	public function __construct($config) {
		$this->config = $config;
	}

	const AES_METHOD = 'aes-256-cbc';
	private function encrypt($message, $password)
	{
		$iv_size        = openssl_cipher_iv_length(Self::AES_METHOD);
		$iv             = openssl_random_pseudo_bytes($iv_size);
		$ciphertext     = openssl_encrypt($message, Self::AES_METHOD, $password, OPENSSL_RAW_DATA, $iv);
		$ciphertext_hex = bin2hex($ciphertext);
		$iv_hex         = bin2hex($iv);
		return "$iv_hex:$ciphertext_hex";
	}

	public function generate_cart_id () {
		$cart_id = md5($_SERVER['REMOTE_ADDR']);
		$options = get_option( Cartlassi_Constants::DATA_OPTIONS_NAME );
		if ( isset($options[Cartlassi_Constants::INCLUDE_EMAIL_IN_CART_ID_FIELD_NAME] )) {
			$customer = new WC_Customer(WC()->session->get_customer_id());
			$cart_id .= md5($customer->get_email());
		}

		if ( !isset($options[Cartlassi_Constants::EXTRA_ENCRYPTION_FIELD_NAME] )) {
			return $cart_id;
		}

		return $this->encrypt($cart_id, str_replace('-','',get_option( Cartlassi_Constants::API_OPTIONS_NAME )[Cartlassi_Constants::API_SECRET_FIELD_NAME]));
		
	}

	public function demo_cart_id ($include_email) {
		$cart_id = md5($_SERVER['REMOTE_ADDR']);
		if ( $include_email ) {
			$cart_id .= md5(get_bloginfo('admin_email'));
		}
		return $cart_id;
	}

	public function get_api_key() {
		return get_option(Cartlassi_Constants::API_OPTIONS_NAME)[Cartlassi_Constants::API_KEY_FIELD_NAME];
	}

	public function get_payment_method () {
		$apiKey = self::get_api_key();

		$args = array(
			'headers'     => array(
				'Authorization' => "token {$apiKey}"
			),
		);
		
		$response = wp_remote_get( "{$this->config->get('api_url')}/shops/payment-method", $args );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			error_log("WWWWWWWWWWW {$error_message}");
			return wp_send_json_error($response);
		}
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body );
		return $data;
	}

	public function get_payout_method () {
		$apiKey = self::get_api_key();

		$args = array(
			'headers'     => array(
				'Authorization' => "token {$apiKey}"
			),
		);
		
		$response = wp_remote_get( "{$this->config->get('api_url')}/shops/payout-method", $args );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			error_log("WWWWWWWWWWW {$error_message}");
			return wp_send_json_error($response);
		}
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body );
		return $data;
	}
}
