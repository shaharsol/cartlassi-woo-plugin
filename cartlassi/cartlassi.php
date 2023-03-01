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

// If woocommerce not active, abort
// Test to see if WooCommerce is active (including network activated).
// $plugin_path = trailingslashit( WP_PLUGIN_DIR ) . 'woocommerce/woocommerce.php';

// require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// var_dump($plugin_path);
// var_dump(wp_get_active_network_plugins());
if ( !class_exists( 'woocommerce' ) ) {
// if ( !in_array( $plugin_path, wp_get_active_and_valid_plugins() ) ) {
    // 	return;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CARTLASSI_VERSION', '1.0.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-plugin-name-activator.php
 */
function activate_cartlassi() {
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
run_cartlassi();
