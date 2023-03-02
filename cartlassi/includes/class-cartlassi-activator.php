<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Cartlassi
 * @subpackage Cartlassi/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Cartlassi
 * @subpackage Cartlassi/includes
 * @author     Your Name <email@example.com>
 */
class Cartlassi_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$config = new Cartlassi_Config();

		$blogInfo = get_bloginfo();

		$body = array(
			'url'  	=> get_bloginfo('url'),
			'email' => get_bloginfo('admin_email'),
			'name'  => get_bloginfo('name'), //
		);
		error_log(var_export($body,true));
		$args = array(
			'body'        => $body,
		);
		$response = wp_remote_post( "{$config->get('api_url')}/shops/register", $args );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			echo "Something went wrong: $error_message";
		} else {
			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body );
			update_option ('cartlassi_options', array (
				'cartlassi_field_api_key' => $data->apiKey
			));
			
		}
	}
}
