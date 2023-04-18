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

	protected $calculatedConfig;

	function __construct() {
		$currentEnv = wp_get_environment_type();
		$arraysToMerge = array();
		for($i = 0; $i <= array_search($currentEnv, self::ENVIRONMENTS) ; $i++) {
			$arraysToMerge += $this->config[self::ENVIRONMENTS[$i]];	
		}
		$this->calculatedConfig = array_merge($arraysToMerge);
	}
	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function get($index) {

		return $this->calculatedConfig[$index];

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
			'api_url' => 'http://host.docker.internal:3000',
			'test_key' => 'production',
			'transient_expiration' => HOUR_IN_SECONDS,
		)
	);



}
