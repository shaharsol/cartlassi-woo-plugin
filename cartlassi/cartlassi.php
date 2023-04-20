<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://cartlassi.com
 * @since             1.0.0
 * @package           Cartlassi
 *
 * @wordpress-plugin
 * Plugin Name:       Cartlassi
 * Plugin URI:        http://cartlassi.com/
 * Description:       Let abandoned carts work for you! Earn commissions from selling and promoting abandoned cart items.
 * Version:           1.0.0
 * Author:            DepressedBrothersInc
 * Author URI:        http://abarbanel.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cartlassi
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CARTLASSI_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_cartlassi() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cartlassi-config.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cartlassi-api.php';
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cartlassi-activator.php';
	Cartlassi_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-plugin-name-deactivator.php
 */
function deactivate_cartlassi() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-cartlassi-deactivator.php';
	Cartlassi_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_cartlassi' );
register_deactivation_hook( __FILE__, 'deactivate_cartlassi' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-cartlassi.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_cartlassi() {

	$plugin = new Cartlassi();
	$plugin->run();

}

// Run the plugin only if woo is active
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	run_cartlassi();
}
