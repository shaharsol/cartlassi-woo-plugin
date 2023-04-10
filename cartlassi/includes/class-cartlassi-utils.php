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
	public static function generate_cart_id () {
		$cart_id = md5($_SERVER['REMOTE_ADDR']);
		$options = get_option( Cartlassi_Constants::DATA_OPTIONS_NAME );
		if ( isset($options[Cartlassi_Constants::INCLUDE_EMAIL_IN_CART_ID_FIELD_NAME] )) {
			$customer = new WC_Customer(WC()->session->get_customer_id());
			$cart_id .= md5($customer->get_email());
		}

		return $cart_id;
	}

	public static function demo_cart_id ($include_email) {
		$cart_id = md5($_SERVER['REMOTE_ADDR']);
		if ( $include_email ) {
			error_log('axqui');
			$cart_id .= md5(get_bloginfo('admin_email'));
		}
		return $cart_id;
	}
}
