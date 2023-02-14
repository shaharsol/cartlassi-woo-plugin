<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Cartlassi
 * @subpackage Cartlassi/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Cartlassi
 * @subpackage Cartlassi/public
 * @author     Your Name <email@example.com>
 */
class Cartlassi_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cartlassi_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cartlassi_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cartlassi-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Cartlassi_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Cartlassi_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cartlassi-public.js', array( 'jquery' ), $this->version, false );

	}

	public function add_to_cart($cart_id, $product_id, $request_quantity, $variation_id, $variation, $cart_item_data) {
		$apiKey = get_option('cartlassi_api_key');
		$product = wc_get_product( $product_id );
		$body = array(
			'shopId'  		=> '0fffc9a3-8a7b-44a4-9dd2-c45c68ebf11b', // TBD this should be the shopId from options that we burn on activate
			'shopProductId' => strval($product_id),
			'sku'     		=> $product->get_sku(), //
			'description'	=> $product->get_name(), // TBD consider get_short_description?
		);
		$args = array(
			'body'        => $body,
			// 'timeout'     => '5',
			// 'redirection' => '5',
			// 'httpversion' => '1.0',
			// 'blocking'    => true,
			'headers'     => array(
				'Authorization' => "token {$apiKey}"
			),
			// 'cookies'     => array(),
		);
		$cartId = md5($_SERVER['REMOTE_ADDR']);
		$response = wp_remote_post( "http://host.docker.internal:3000/carts/${cartId}", $args );
	} 

	public function remove_from_cart($cart_item_key, $that) {
		$apiKey = get_option('cartlassi_api_key');
		global $woocommerce;
		$product_id = $woocommerce->cart->get_cart()[$cart_item_key]->product_id;
		error_log("cart item key is {$cart_item_key}");
		$keys = array_keys($woocommerce->cart->get_cart());
		foreach($woocommerce->cart->get_cart() as $key => $value) {
			error_log("key is {$key}");
		}
		$product = wc_get_product( $product_id );
		$body = array(
			'shopId'  		=> '0fffc9a3-8a7b-44a4-9dd2-c45c68ebf11b', // TBD this should be the shopId from options that we burn on activate
			'shopProductId' => strval($product_id),
		);
		$args = array(
			'method'	  => 'DELETE',
			'body'        => $body,
			// 'timeout'     => '5',
			// 'redirection' => '5',
			// 'httpversion' => '1.0',
			// 'blocking'    => true,
			'headers'     => array(
				'Authorization' => "token {$apiKey}"
			),
			// 'cookies'     => array(),
		);
		$cartId = md5($_SERVER['REMOTE_ADDR']);
		$response = wp_remote_request( "http://host.docker.internal:3000/carts/${cartId}", $args );
		print_r($response);
	}

}
