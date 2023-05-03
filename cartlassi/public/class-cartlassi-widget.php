<?php

class Cartlassi_Widget extends WP_Widget {
	private $config;
	private $utils;
	private $api;

	public function __construct() {
		parent::__construct(
			'cartlassi_widget', // Base ID
			'Cartlassi_Widget', // Name
			array( 'description' => __( 'Cartlassi Widget', 'text_domain' ) ) // Args
		);
		$this->config = new Cartlassi_Config();
		$this->api = new Cartlassi_Api($this->config);
		$this->utils = new Cartlassi_Utils($this->config, $this->api);
	}

	public function widget( $args, $instance ) {
		// if ( is_admin() ) { 
		// 	return;
		// }
	
		// when the widget is rendered, it adds a placeholder div tag into which
		// a 2nd ajax call will poor content into 

		if (wp_doing_ajax()) {
			check_ajax_referer(Cartlassi_Constants::NONCE_PUBLIC_NAME, 'nonce');

			$payment_method = $this->utils->get_payment_method();
			$is_payment_method = $payment_method->brand && $payment_method->last4;
			$is_appearance_set = !!get_option( Cartlassi_Constants::APPEARANCE_OPTIONS_NAME );
			
			$is_displaying_widget = $is_appearance_set && $is_payment_method;

			if ( $is_displaying_widget ) {
				extract( $args );

				$title = 'We think you may like...';
				
				$cart_id = $this->utils->generate_cart_id();
				$cache_key = Cartlassi_Constants::WIDGET_CACHE_NAME.':'.WC()->session->get_customer_id();
				$products = get_transient($cache_key);
				if (!$products) {
					$limit = wc_get_theme_support( 'product_blocks::default_columns', 3 );
					$products = $this->api->request("/shops/widget/{$cart_id}?limit={$limit}");
					set_transient($cache_key, $products, $this->config->get('widget_cache_expiration'));
				}

				$cnt = count($products);
				if ($cnt == 0) {
					Cartlassi_Logger::log('info', "0 products found for {$cart_id}");
					return wp_die(); // TBD replace to wp_die() here?
				}
				Cartlassi_Logger::log('info', "{$cnt} products found for {$cart_id}");

				$cart_item_to_product_map = array_reduce($products, function($carry, $item){
					if(count($carry) == 0) {
						$carry = [strval($item->id) => $item->cartItemId];
					} else {
						$carry += [strval($item->id) => $item->cartItemId];
					}
					return $carry;
				}, []);

				$products = array_map(function($product) {
					return $product->id;
				}, $products);

				WC()->session->set(Cartlassi_Constants::CURRENT_MAP_NAME, $cart_item_to_product_map);
				$block_name = 'woocommerce/handpicked-products';
				$converted_block = new WP_Block_Parser_Block( $block_name, array(
					'products' 	=> $products,
					'title'		=> 'We think you may like...'
				), array(), '', array() );
		
				$rendered_block = render_block( (array) $converted_block );
				
				echo '<div id="cartlassi-widget-container"><div id="cartlassi-widget-title">We think you may like</div>';
				echo $rendered_block;
				echo '<div id="powered-by-cartlassi">powered by <a href="https://cartlassi.com">Cartlassi</a></div>';
				echo '</div>';
				}

			wp_die();
		} else {
			
			extract( $args );
			// echo $before_widget;
			// echo '<div id="cartlassi-widget-container"><div id="cartlassi-widget-title">We think you may like</div>';
			// if ( ! empty( $title ) ) {
			// 	//echo $before_title . $title . $after_title;
			// }
			echo '<div id="cartlassi-ajax-widget"></div>';
			// echo '<div id="powered-by-cartlassi">powered by <a href="https://cartlassi.com">Cartlassi</a></div>';
			// echo '</div>';
			// echo $after_widget;
		}
	}

	public function form( $instance ) {
		// outputs the options form in the admin
	}

	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
	}
}
