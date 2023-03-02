<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Cartlassi
 * @subpackage Cartlassi/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Cartlassi
 * @subpackage Cartlassi/includes
 * @author     Your Name <email@example.com>
 */
class Cartlassi_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		$config = new Cartlassi_Config();
		$apiKey = get_option('cartlassi_options')['cartlassi_field_api_key'];
		$args = array(
			'method'	  => 'DELETE',
			'headers'     => array(
				'Authorization' => "token {$apiKey}"
			),
		);
		$response = wp_remote_request( "{$config->get('api_url')}/shops/register", $args );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			echo "Something went wrong: $error_message";
		} else {
			// delete_option ('cartlassi_api_key');
		}

	}

}
