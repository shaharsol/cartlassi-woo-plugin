<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Cartlassi
 * @subpackage Cartlassi/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Cartlassi
 * @subpackage Cartlassi/includes
 * @author     Your Name <email@example.com>
 */
class Cartlassi {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Cartlassi_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $config;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'cartlassi';

		$this->load_dependencies();
		$this->set_locale();
		$this->load_config();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Cartlassi_Loader. Orchestrates the hooks of the plugin.
	 * - Cartlassi_i18n. Defines internationalization functionality.
	 * - Cartlassi_Admin. Defines all hooks for the admin area.
	 * - Cartlassi_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for config.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cartlassi-constants.php';

		/**
		 * The class responsible for config.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cartlassi-config.php';
		
		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cartlassi-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-cartlassi-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-cartlassi-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-cartlassi-public.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-cartlassi-widget.php';

		$this->loader = new Cartlassi_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Cartlassi_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Cartlassi_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Cartlassi_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_config() {

		$this->config = new Cartlassi_Config();

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Cartlassi_Admin( $this->get_plugin_name(), $this->get_version(), $this->get_config() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'cartlassi_settings_init' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'cartlassi_options_page' );
		// TBD this one still not working, its about integrating with woocommerce admin, and not outside in general
		// wordpress admin
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'cartlassi_wc_options_page' );
		$this->loader->add_action( 'wp_ajax_cartlassi_regenerate_api_key', $plugin_admin, 'regenerate_api_key' );
		$this->loader->add_filter( "plugin_action_links", $plugin_admin, 'add_action_links', 10, 2);

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Cartlassi_Public( $this->get_plugin_name(), $this->get_version(), $this->get_config() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'woocommerce_add_to_cart', $plugin_public, 'add_to_cart', 10, 6 );
		$this->loader->add_action( 'woocommerce_cart_item_removed', $plugin_public, 'remove_from_cart', 10, 2 );
		$this->loader->add_action( 'widgets_init', $plugin_public, 'cartlassi_widgets_init' );
		$this->loader->add_action( 'dynamic_sidebar_params', $plugin_public, 'display_widget' );
		$this->loader->add_action( 'woocommerce_before_single_product', $plugin_public, 'log_click_to_product' );
		$this->loader->add_action( 'woocommerce_init', $plugin_public, 'initiate_wc_sessions' );
		$this->loader->add_action( 'woocommerce_before_cart', $plugin_public, 'log_click_to_cart' , 10, 1);
		$this->loader->add_action( 'woocommerce_ajax_added_to_cart', $plugin_public, 'log_ajax_add_to_cart', 1, 10);
		$this->loader->add_action( 'woocommerce_payment_complete', $plugin_public, 'payment_complete' );
		$this->loader->add_action( 'woocommerce_order_refunded', $plugin_public, 'order_refunded', 10, 2 );
		$this->loader->add_action( 'woocommerce_checkout_create_order_line_item', $plugin_public, 'save_cart_item_key_as_custom_order_item_metadata', 10, 4 );
		$this->loader->add_action( 'wp_ajax_load_widget', $plugin_public, 'load_widget' );
		$this->loader->add_action( 'wp_ajax_nopriv_load_widget', $plugin_public, 'load_widget' );
		$this->loader->add_action( 'wp_ajax_log_click', $plugin_public, 'log_click' );

		$this->loader->add_filter( 'woocommerce_blocks_product_grid_item_html', $plugin_public, 'add_tag_to_block_product_link', 10, 3 );
		$this->loader->add_filter( 'query_vars', $plugin_public, 'expose_cartlassi_query_var' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Cartlassi_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	public function get_config() {
		return $this->config;
	}

}
