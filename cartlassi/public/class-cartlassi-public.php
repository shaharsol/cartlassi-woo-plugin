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
		$apiKey = get_option('cartlassi_options')['cartlassi_field_api_key'];
		$product = wc_get_product( $product_id );
		$body = array(
			'shopProductId' => strval($product_id),
			'shopCartId' 	=> strval($cart_id),
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
		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			error_log("WWWWWWWWWWW ${error_message}");
		}
	} 

	public function remove_from_cart($cart_item_key, $that) {
		var_dump($that);
		$apiKey = get_option('cartlassi_options')['cartlassi_field_api_key'];
		$product_id = json_decode(json_encode($that))->removed_cart_contents->{$cart_item_key}->product_id;
		// $cart_id = json_decode(json_encode($that))->id;
		// $cart_id = $that->get_cart_id();
		// $cart_id = WC()->cart->get_cart_id();


		error_log("product id is {$product_id}");

		//$product = wc_get_product( $product_id );
		$body = array(
			'shopProductId' => strval($product_id),
			'shopCartId'	=> strval($cart_id),
		);
		error_log(var_export($body,true));
		var_dump($cart_id);
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
	}

	function cartlassi_widgets_init() {
		$cartlassi_sidebar = register_sidebar( array(
			'name'          => __( 'Cartlassi Sidebar', 'textdomain' ),
			'id'            => 'sidebar-cartlassi',
			'description'   => __( 'A sidebar for cartlassi plugin.', 'textdomain' ),
			'before_widget' => '<li id="%1$s" class="widget %2$s">',
			'after_widget'  => '</li>',
			'before_title'  => '<h2 class="widgettitle">',
			'after_title'   => '</h2>',
		) );
		register_widget( 'Cartlassi_Widget' );

		if ( !is_active_sidebar($cartlassi_sidebar) ) {
			$this->insert_widget_in_sidebar('cartlassi_widget', array('title' => 'We think you may like...'), $cartlassi_sidebar);
		}

	}

	function insert_widget_in_sidebar( $widget_id, $widget_data, $sidebar ) {
		// Retrieve sidebars, widgets and their instances
		$sidebars_widgets = get_option( 'sidebars_widgets', array() );
		$widget_instances = get_option( 'widget_' . $widget_id, array() );
	
		// Retrieve the key of the next widget instance
		$numeric_keys = array_filter( array_keys( $widget_instances ), 'is_int' );
		$next_key = $numeric_keys ? max( $numeric_keys ) + 1 : 2;
	
		// Add this widget to the sidebar
		if ( ! isset( $sidebars_widgets[ $sidebar ] ) ) {
			$sidebars_widgets[ $sidebar ] = array();
		}
		$sidebars_widgets[ $sidebar ][] = $widget_id . '-' . $next_key;
	
		// Add the new widget instance
		$widget_instances[ $next_key ] = $widget_data;
	
		// Store updated sidebars, widgets and their instances
		update_option( 'sidebars_widgets', $sidebars_widgets );
		update_option( 'widget_' . $widget_id, $widget_instances );
	}	

	function display_widget($params) {
		$sidebarId = $params[0]['id'];
		$cartlassiOptions = get_option('cartlassi_options');
		if ($sidebarId == $cartlassiOptions['cartlassi_field_before_sidebar']) {
			echo dynamic_sidebar('sidebar-cartlassi');
		}
		return $params;
	}

	function add_tag_to_block_product_link ($html, $data, $product) {

		// TBD make sure this happens ONLY in cartlassi widget
		// otherwise we take over links from every widget in the site...

		$cartlassiCartItemId = array_search( $product->id, WC()->session->get( 'cartlassi_current_map' ) ); 
		if ($cartlassiCartItemId) {
			// $withCartlassiHrefs = preg_replace('/href="([^"]+?)"/i', 'href="$1&cartlassi='.$cartlassiCartItemId.'"', $html);
			$withCartlassiHrefs = preg_replace('/href="([^"]+?)"/i', 'href="$1&cartlassi='.$cartlassiCartItemId.'"  data-cartlassi="'.$cartlassiCartItemId.'"', $html);
			return $withCartlassiHrefs;
		}
		return $html;
	}

	function log_click_to_product () {
		global $product;

		$cartlassi = get_query_var('cartlassi');
		if ( $cartlassi ) {
			$apiKey = get_option('cartlassi_options')['cartlassi_field_api_key'];

			$body = array(
				'fromCartItemId' => $cartlassi,
				'toProductId' => strval($product->id),
			);
			$args = array(
				'body'        => $body,
				// 'timeout'     => '5',
				// 'redirection' => '5',
				// 'httpversion' => '1.0',
				// 'blocking'    => true,
				'headers'     => array(
					'Authorization' => "Bearer {$apiKey}"
				),
				// 'cookies'     => array(),
			);
			$response = wp_remote_post( "http://host.docker.internal:3000/clicks", $args );
		}
	}

	function initiate_wc_sessions () {
		if ( is_user_logged_in() || is_admin() ) {
			return;
		}
		if ( isset( WC()->session ) ) {
			if ( !WC()->session->has_session() ) {
				WC()->session->set_customer_session_cookie(true);
			}
		}
	}

	function log_click_to_cart ( $data	) {
		error_log('log_click_to_cart');
	}

	function log_ajax_add_to_cart (	$productId ) {
		// error_log('log_ajax_add_to_cart');
		// error_log(wp_get_referer());
		// error_log(wp_get_original_referer());
		// error_log(var_export($_POST, true));
		$cartlassi = $_POST['cartlassi'];
		if ( $cartlassi ) {
			$apiKey = get_option('cartlassi_options')['cartlassi_field_api_key'];

			$body = array(
				'fromCartItemId' => $cartlassi,
				'toProductId' => strval($productId),
			);
			$args = array(
				'body'        => $body,
				// 'timeout'     => '5',
				// 'redirection' => '5',
				// 'httpversion' => '1.0',
				// 'blocking'    => true,
				'headers'     => array(
					'Authorization' => "Bearer {$apiKey}"
				),
				// 'cookies'     => array(),
			);
			$response = wp_remote_post( "http://host.docker.internal:3000/clicks", $args );
		}
	}

	function expose_cartlassi_query_var ($qvars) {
		$qvars[]= 'cartlassi';
		return $qvars;
	}

	function payment_complete ( $orderId ) {
		$order = wc_get_order( $order_id );
		$order_items = $order->get_items();

		foreach( $order_items as $item_id => $item ){
		
			$product_id = $item->get_product_id(); // the Product id

			// TBD probably need shop_cart_id also to make this unique.
			// let's think...


		
			// // order item data as an array
			// $item_data = $item->get_data();
		
			// echo $item_data['name'];
			// echo $item_data['product_id'];
			// echo $item_data['variation_id'];
			// echo $item_data['quantity'];
			// echo $item_data['tax_class'];
			// echo $item_data['subtotal'];
			// echo $item_data['subtotal_tax'];
			// echo $item_data['total'];
			// echo $item_data['total_tax'];
		
		}
	}

	function order_refunded ( $orderId ) {
		$order = wc_get_order( $order_id );
	}


}
