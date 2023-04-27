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
	protected $api;

	public function __construct($config, $api) {
		$this->config = $config;
		$this->api = $api;
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

	protected function get_cart_id ($ip, $email, $extra_encryption) {
		$cart_id = md5($ip);
		if ( $email ) {
			$cart_id .= md5($email);
		}

		if ( !$extra_encryption ) {
			return $cart_id;
		}

		return $this->encrypt($cart_id, str_replace('-','',get_option( Cartlassi_Constants::API_OPTIONS_NAME )[Cartlassi_Constants::API_SECRET_FIELD_NAME]));
	}

	private function getUserIpAddress(){
		if(!empty($_SERVER['HTTP_CLIENT_IP'])){
			//ip from share internet
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			//ip pass from proxy
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}elseif(!empty($_SERVER['UPSTREAM_ADDR'])){
			//ip pass from proxy
			$ip = $_SERVER['UPSTREAM_ADDR'];
		}else{
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}

	public function generate_cart_id () {
		$ipAddress = $this->getUserIpAddress();
		$email = null;
		$options = get_option( Cartlassi_Constants::DATA_OPTIONS_NAME );
		if ( isset($options[Cartlassi_Constants::INCLUDE_EMAIL_IN_CART_ID_FIELD_NAME] )) {
			$customer = new WC_Customer(WC()->session->get_customer_id());
			$email = $customer->get_email();
		}
		return $this->get_cart_id($ipAddress, $email, isset($options[Cartlassi_Constants::EXTRA_ENCRYPTION_FIELD_NAME] ));
	}

	public function demo_cart_id ($include_email, $extra_encryption) {
		return $this->get_cart_id($ipAddress, $include_email ? get_bloginfo('admin_email') : null, $extra_encryption);
	}

	public function get_api_key() {
		return get_option(Cartlassi_Constants::API_OPTIONS_NAME)[Cartlassi_Constants::API_KEY_FIELD_NAME];
	}

	public function get_payment_method ($use_cache = true) {
		if ($use_cache) {
			$cached = get_transient(Cartlassi_Constants::PAYMENT_METHOD_TRANSIENT);
			if ($cached) {
				return $cached;
			}
		}
		$fresh = $this->api->request('/shops/payment-method');
		set_transient(Cartlassi_Constants::PAYMENT_METHOD_TRANSIENT, $fresh, $this->config->get('transient_expiration'));
		return $fresh;
	}

	public function get_payout_method ($use_cache = true) {
		if ($use_cache) {
			$cached = get_transient(Cartlassi_Constants::PAYOUT_METHOD_TRANSIENT);
			if ($cached) {
				return $cached;
			}
		}
		$fresh = $this->api->request('/shops/payout-method');
		set_transient(Cartlassi_Constants::PAYOUT_METHOD_TRANSIENT, $fresh, $this->config->get('transient_expiration'));
		return $fresh;
	}
}
