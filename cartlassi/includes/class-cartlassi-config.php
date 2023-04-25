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
class Cartlassi_Config {

	const ENVIRONMENTS = array (
		'local', 'development', 'staging', 'production'
	);

	protected $calculated_config;

	function __construct() {
		$current_env = wp_get_environment_type();
		$result = [];
		foreach(self::ENVIRONMENTS as $env) {
			$result = array_merge($result, $this->config[$env]);
			if ($env == $current_env) {
				break;
			}
		}
		$this->calculated_config = $result;
	}
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function get($index) {

		return $this->calculated_config[$index];

	}

	protected $config = array(
		'local' => array (
			'api_url' => 'http://host.docker.internal:3000',
			'api_public_url' => 'http://localhost:3000',
			'test_key' => 'local',
			'transient_expiration' => MINUTE_IN_SECONDS,
		),
		'development' => array (
			'api_url' => 'http://host.docker.internal:3000',
			'test_key' => 'development',
			'transient_expiration' => MINUTE_IN_SECONDS,
		),
		'staging' => array (
			'api_url' => 'http://host.docker.internal:3000',
			'test_key' => 'staging',
			'transient_expiration' => MINUTE_IN_SECONDS,
		),
		'production' => array (
			'api_url' => 'https://cartlassi.herokuapp.com',
			'api_public_url' => 'https://cartlassi.herokuapp.com',
			'test_key' => 'production',
			'transient_expiration' => HOUR_IN_SECONDS,
		)
	);



}


