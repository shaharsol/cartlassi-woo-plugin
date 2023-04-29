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
		// notify the server the store is active again
		// otherwise if it's a 1st install leave the signup for admin
		if (get_option(Cartlassi_Constants::API_OPTIONS_NAME)) {
			$config = new Cartlassi_Config();
			$api = new Cartlassi_Api($config);

			$body = array(
				'url'  	=> get_bloginfo('url'),
				'email' => get_bloginfo('admin_email'),
				'name'  => get_bloginfo('name'),
				'country' => wc_get_base_location()['country']
			);
			$args = array(
				'method'	=> 'POST',
				'body'        => $body,
			);
			$data = $api->request( "/shops/register", $args );
			update_option (Cartlassi_Constants::API_OPTIONS_NAME, array (
				Cartlassi_Constants::API_KEY_FIELD_NAME => $data->apiKey,
				Cartlassi_Constants::API_SECRET_FIELD_NAME => $data->apiSecret,
			));
		}
	}
}
