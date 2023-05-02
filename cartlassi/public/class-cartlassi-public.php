<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://cartlassi.com
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
	private $config;
	private $api;
	private $utils;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $config, $api, $utils ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->config = $config;
		$this->api = $api;
		$this->utils = $utils;

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
		$nonce = wp_create_nonce( Cartlassi_Constants::NONCE_PUBLIC_NAME );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cartlassi-public.js', array( 'jquery' ), $this->version, false );

		// in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
		wp_localize_script( $this->plugin_name, 'ajax_object',
				array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'nonce' => $nonce) 
		);
	}

	/**
	 * action: woocommerce_add_to_cart
	 *
	 * Whenevr a shoper adds any product to cart, we want to register that in the global cart
	 * 
	 * @since    1.0.0
	 */
	public function add_to_cart($cart_id, $product_id, $request_quantity, $variation_id, $variation, $cart_item_data) {
		
		$product = wc_get_product( $product_id );
		$body = array(
			'shopProductId' => strval($product_id),
			'shopCartId' 	=> strval($cart_id),
			'sku'     		=> $product->get_sku(), //
			'url'			=> get_permalink($product_id),
		);
		
		$args = array(
			'method'	=> 'POST',
			'body'      => $body,
		);

		$cart_id = $this->utils->generate_cart_id();
		$response = $this->api->request("/carts/{$cart_id}", $args);

	} 

	/**
	 * action: woocommerce_cart_item_removed
	 *
	 * Whenevr a shoper removes any product from cart, we want to register that in the global cart
	 * 
	 * @since    1.0.0
	 */
	public function remove_from_cart($cart_item_key, $that) {
		// TBD get rid of the jsin_decode(jsin_encode)... make it work in another way
		$product_id = json_decode(json_encode($that))->removed_cart_contents->{$cart_item_key}->product_id;

		$body = array(
			'shopProductId' => strval($product_id),
			'shopCartId'	=> strval($cart_item_key),
		);

		$args = array(
			'method'	  => 'DELETE',
			'body'        => $body,
		);

		$cart_id = $this->utils->generate_cart_id();
		$response = $this->api->request("/carts/{$cart_id}", $args);
	}

	function cartlassi_widgets_init() {
		$cartlassi_sidebar = register_sidebar( array(
			'name'          => __( 'Cartlassi Sidebar', 'textdomain' ),
			'id'            => Cartlassi_Constants::SIDEBAR_ID,
			'description'   => __( 'A sidebar for cartlassi plugin.', 'textdomain' ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2 class="widgettitle">',
			'after_title'   => '</h2>',
		) );
		register_widget( 'Cartlassi_Widget' );

		if ( !is_active_sidebar($cartlassi_sidebar) ) {
			$this->insert_widget_in_sidebar('cartlassi_widget', array('title' => 'We think you may like...'), $cartlassi_sidebar);
		}

	}

	protected function insert_widget_in_sidebar( $widget_id, $widget_data, $sidebar ) {
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

	/**
	 * filter: dynamic_sidebar_params
	 *
	 * Since we can't modify the theme, the only way to place the cartlassi widget
	 * is relative to another widget. 
	 * In the admin options we let the user select before which other sidebar
	 * the cartlassi widget should appear. 
	 * This is the actual implementation of echoing the sidebar in the right location
	 * 
	 * @since    1.0.0
	 */
	function display_widget_helper ($bool_func, $option_name, $sidebar_id, $cartlassi_options) {
		if ( $bool_func() ) {
			if ( !isset($cartlassi_options[$option_name]) || !$cartlassi_options[$option_name] ) {
				return true;
			}
			if ($sidebar_id == $cartlassi_options[$option_name]) {
				dynamic_sidebar(Cartlassi_Constants::SIDEBAR_ID);
			}
			return false;
		}
		return true;
		
	}

	function display_widget($params) {
		// First determine if to show the widget at all.
		if ( is_cart() || is_checkout() || is_account_page() || is_wc_endpoint_url() ) {
			return $params;
		}

		$sidebar_id = $params[0]['id'];
		$cartlassi_options = get_option(Cartlassi_Constants::APPEARANCE_OPTIONS_NAME);

		$invocations = array (
			array (
				'bool_func' => 'is_shop',
				'option_name' => Cartlassi_Constants::BEFORE_SIDEBAR_SHOP_FIELD_NAME
			),
			array (
				'bool_func' => 'is_product_category',
				'option_name' => Cartlassi_Constants::BEFORE_SIDEBAR_CATEGORY_FIELD_NAME
			),
			array (
				'bool_func' =>'is_product_tag',
				'option_name' => Cartlassi_Constants::BEFORE_SIDEBAR_PRODUCT_TAG_FIELD_NAME
			),
			array (
				'bool_func' => 'is_product',
				'option_name' => Cartlassi_Constants::BEFORE_SIDEBAR_PRODUCT_FIELD_NAME
			),
		);

		foreach($invocations as $invocation) {
			$continue = $this->display_widget_helper($invocation['bool_func'], $invocation['option_name'], $sidebar_id, $cartlassi_options);
			if ( !$continue ) {
				break;
			}
		}

		if ($continue) {
			if ( is_page() ) {
				if ( isset($cartlassi_options[Cartlassi_Constants::BEFORE_SIDEBAR_OTHER_PAGES_PAGES_FIELD_NAME]) ) {
					$is_listed_page = is_page(explode(',',$cartlassi_options[Cartlassi_Constants::BEFORE_SIDEBAR_OTHER_PAGES_PAGES_FIELD_NAME]));
				} else {
					$is_listed_page = false;
				}

				if ( 
					( ( $cartlassi_options[Cartlassi_Constants::BEFORE_SIDEBAR_OTHER_PAGES_STRATEGY_FIELD_NAME] ==  Cartlassi_Constants::OTHER_PAGES_OPTION_DONT_SHOW_BUT ) && $is_listed_page )
					|| ( ( $cartlassi_options[Cartlassi_Constants::BEFORE_SIDEBAR_OTHER_PAGES_STRATEGY_FIELD_NAME] ==  Cartlassi_Constants::OTHER_PAGES_OPTION_SHOW_EXCEPT ) && !$is_listed_page )
				) {
					if ($sidebar_id == $cartlassi_options[Cartlassi_Constants::BEFORE_SIDEBAR_OTHER_PAGES_FIELD_NAME]) {
						dynamic_sidebar(Cartlassi_Constants::SIDEBAR_ID);
					}
				}
			}
		}

		return $params;
	}

	function load_widget() {
		dynamic_sidebar(Cartlassi_Constants::SIDEBAR_ID);
		wp_die();
	}

	/**
	 * filter: woocommerce_blocks_product_grid_item_html
	 *
	 * During woo generation of the hand picked block we use as a widget to display products,
	 * we need to alter the href of each a tag and also add `data-cartlassi` attribute to the tag.
	 * This way, when user click on a product from the widget, or adds it to the cart using ajax,
	 * they can mark that the attribution should go to the specific cart item which resulted in user
	 * engagement.
	 * 
	 * @since    1.0.0
	 */
	function add_tag_to_block_product_link ($html, $data, $product) {

		// TBD make sure this happens ONLY in cartlassi widget
		// otherwise we take over links from every widget in the site...

		// $cartlassi_cart_item_id = array_search( $product->get_id(), WC()->session->get( Cartlassi_Constants::CURRENT_MAP_NAME ) ); 
		$map = WC()->session->get( Cartlassi_Constants::CURRENT_MAP_NAME );
		$cartlassi_cart_item_id = isset($map[$product->get_id()]) ? $map[$product->get_id()] : false; 
		if ($cartlassi_cart_item_id) {
			// $with_cartlassi_hrefs = preg_replace('/href="([^"]+?)"/i', 'href="$1&cartlassi='.$cartlassi_cart_item_id.'"', $html);
			// $with_cartlassi_hrefs = preg_replace('/href="([^"]+?)"/i', 'href="$1&cartlassi='.$cartlassi_cart_item_id.'"  data-product-id="'.$product->get_id().'" data-cartlassi="'.$cartlassi_cart_item_id.'"', $html);
			// $with_cartlassi_hrefs = preg_replace('/href="(([^?]+)(?:\??))([^"]*?)"/i', 'href="$2?cartlassi='.$cartlassi_cart_item_id.'&$3"  data-product-id="'.$product->get_id().'" data-cartlassi="'.$cartlassi_cart_item_id.'"', $html);
			$with_cartlassi_hrefs = preg_replace('/href="([^?"]+)(?:\??)(.*?)"/i', 'href="$1?cartlassi='.$cartlassi_cart_item_id.'&$2" data-cartlassi="'.$cartlassi_cart_item_id.'" data-product-id="'.$product->get_id().'"', $html);

			return $with_cartlassi_hrefs;
		}
		return $html;
	}

	function log_click () {
		$cartlassi_id = $_POST['cartlassi_id'];
		$product_id = $_POST['product_id'];
		if ( $cartlassi_id &&  $product_id) {
			$body = array(
				'fromCartItemId' => $cartlassi_id,
				'toShopProductId' => strval($product_id),
			);
			$args = array(
				'method'	=> 'POST',
				'body'        => $body,
			);
			$response = $this->api->request("/clicks", $args );
		}
		wp_die();
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

	/**
	 * filter: query_vars
	 *
	 * Enable the cartlassi query var(s) to be fetched using get_query_var()
	 * 
	 * @since    1.0.0
	 */
	function expose_cartlassi_query_var ($qvars) {
		$qvars[]= 'cartlassi';
		return $qvars;
	}

	/**
	 * action: woocommerce_payment_complete
	 *
	 * When a sale occured, we need to see if a cartlassi click can be attributed to it
	 * if so, we need to create a sale, so a commission can be paid 
	 * 
	 * @since    1.0.0
	 */
	function payment_complete ( $order_id ) {
		$order = wc_get_order( $order_id );
		$order_items = $order->get_items();

		foreach( $order_items as $item_id => $item ){
		
			$product_id = $item->get_product_id(); // the Product id
			$cart_item_key = $item->get_meta( Cartlassi_Constants::ORDER_ITEM_CART_ITEM_KEY );
			// $product = wc_get_product( $product_id );

			$body = array(
				'shopProductId' => strval($product_id),
				'shopCartId' 	=> strval($cart_item_key),
				'shopOrderId'	=> strval($order_id),
				'amount'		=> $item->get_total(),
				'currency'		=> get_woocommerce_currency(), // TBD change to get_woocommerce_currency()? or extract from product?
			);
			$args = array(
				'method' => 'POST',
				'body'   => $body,
			);

			$cart_id = $this->utils->generate_cart_id();
			$response = $this->api->request("/carts/{$cart_id}/checkout", $args);
		}
	}

	function order_refunded ( $order_id, $refund_id ) {
		$order = wc_get_order( $order_id );
		$refunds = $order->get_refunds();
		foreach ($refunds as $refund) {
			if($refund->id == $refund_id) {
				foreach( $refund->get_items() as $refunded_item_id => $refunded_item ) {
					$original_item_id = $refunded_item->get_meta('_refunded_item_id');
					$item = $order->get_item($original_item_id);
					$cart_item_key = $item->get_meta( Cartlassi_Constants::ORDER_ITEM_CART_ITEM_KEY );
					if ($cart_item_key) {
						$body = array(
							"shopCartId" => $cart_item_key,
						);
						$args = array(
							'method' => 'POST',
							'body'	 => $body,
						);
						$response = $this->api->request("/carts/{$order_id}/refund", $args );
					}
				}
				break;
			}
		}
	}

	/**
	 * action: woocommerce_checkout_create_order_line_item
	 *
	 * When a user proceeds to checkout, at some point the cart turns into an order.
	 * since we work with the cart_item_key as unique identifer, we want to propagate
	 * it down the line to the order, so every line item will have it's corresponding
	 * cartlassi id. so for each order item, we save the cart_item_key that was attached
	 * to it once it was still in the cart.
	 * 
	 * @since    1.0.0
	 */
	function save_cart_item_key_as_custom_order_item_metadata( $item, $cart_item_key, $values, $order ) {
		// Save the cart item key as hidden order item meta data
		$item->update_meta_data( Cartlassi_Constants::ORDER_ITEM_CART_ITEM_KEY, $cart_item_key );
	}

	function get_product_feed() {
		$products = wc_get_products( array(
			'limit'  => -1, // All products
			'status' => 'publish', // Only published products
		) );
		$filtered = array_map(function($product) {
			$tag_ids = $product->get_tag_ids();
			$tags = array_map(function($tag_id) {
				return (get_term($tag_id))->name;
			}, $tag_ids);

			$category_ids = $product->get_category_ids();
			$categories = array_map(function($category_id) {
				$term = get_term_by( 'id', $category_id, 'product_cat' );
				return isset ($term->name) ? $term->name : '';
			}, $category_ids);

			$description = $product->get_description();
			if (!$description) {
				$description = $product->get_short_description();
			}
			return array (
				'id' => $product->get_id(),
				'name' => $product->get_name(),
				'description' => $product->get_description(),
				'short_description' => $product->get_short_description(),
				// 'tags'				=> implode(', ', $tags),
				// 'categories'				=> implode(', ', $categories),
				'tags'				=> $tags,
				'categories'				=> $categories,
				'sku'     		=> $product->get_sku(),
			);
		}, $products);
		return $filtered;
	}

	function cartlassi_api_init() {
		$reg = register_rest_route( 'cartlassi/v1', 'feed', array(
			'methods' => WP_REST_Server::READABLE,
			'callback' => array($this, 'get_product_feed'),
			'permission_callback' => '__return_true',
		) );
	}

	
	
	

}
