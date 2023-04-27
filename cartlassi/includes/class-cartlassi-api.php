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
class Cartlassi_Api {
	protected $config;
	
	function __construct($config) {
		$this->config = $config;
	}

	public function request($endpoint, $args = [], $associative = null) {
		$api_key = get_option(Cartlassi_Constants::API_OPTIONS_NAME)[Cartlassi_Constants::API_KEY_FIELD_NAME];
		$args = array_merge($args, array(
			'headers'     => array(
				'Authorization' => "token {$api_key}"
			),
		));
		$response = wp_remote_request("{$this->config->get('api_url')}{$endpoint}", $args);
		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			echo "Something went wrong: $error_message";
			// error_log($error_message);
			wc_get_logger()->error( "Cartlassi API Error: {$error_message}", array(
				'source' => Cartlassi_Constants::PLUGIN_NAME
			) );
			return false;
		}
		$body = wp_remote_retrieve_body( $response );
		$data = json_decode( $body, $associative );
		return $data;
	}
}
