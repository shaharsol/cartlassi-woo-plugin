<?php

class Cartlassi_Widget extends WP_Widget {
	private $config;

	public function __construct() {
		parent::__construct(
			'cartlassi_widget', // Base ID
			'Cartlassi_Widget', // Name
			array( 'description' => __( 'Cartlassi Widget', 'text_domain' ) ) // Args
		);
		$this->config = new Cartlassi_Config();
	}

	public function widget( $args, $instance ) {
		// if ( is_admin() ) { 
		// 	return;
		// }
		
		// when the widget is rendered, it adds a placeholder div tag into which
		// a 2nd ajax call will poor content into 

		if (wp_doing_ajax()) {
			check_ajax_referer('cartlassi-public', 'nonce');

			extract( $args );
			// $title = apply_filters( 'widget_title', $instance['title'] );
			$title = 'We think you may like...';
			
			$apiKey = get_option(Cartlassi_Constants::OPTIONS_NAME)[Cartlassi_Constants::API_KEY_FIELD_NAME];
	
			$args = array(
				'headers'     => array(
					'Authorization' => "token {$apiKey}"
				),
			);
			$cartId = md5($_SERVER['REMOTE_ADDR']);
			$response = wp_remote_get( "{$this->config->get('api_url')}/carts/${cartId}/shop", $args );
	
			if ( is_wp_error( $response ) ) {
				$error_message = $response->get_error_message();
				echo "Something went wrong: $error_message";
				error_log($error_message);
				return;
			}
	
			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body );
			$products = array();
			$cartItemToProductMap = array();
			foreach($data as $product) {
				$the_query = new WP_Query( array( 's' => $product->description ) );
				if ( $the_query->have_posts() ) {
					$the_query->the_post();
					$postID = get_the_ID();
					array_push($products, $postID);
					$cartItemToProductMap += [$product->id => $postID];
				}
				/* Restore original Post Data */
				wp_reset_postdata();
	
				// limit the items in widget (need to find the default # of items per line from woo config)
				if ( count($products) == wc_get_theme_support( 'product_blocks::default_columns', 3 )) {
					break;
				}
			}
	
			if (count($products) == 0) {
				return;
			}
			WC()->session->set(Cartlassi_Constants::CURRENT_MAP_NAME, $cartItemToProductMap);
			$block_name = 'woocommerce/handpicked-products';
			$converted_block = new WP_Block_Parser_Block( $block_name, array(
				'products' => $products,
				'title'		=> 'We think you may like...'
			), array(), '', array() );
	
			$rendered_block = render_block( (array) $converted_block );
			
			echo $rendered_block;
			wp_die();
		} else {
			echo $before_widget;
			echo '<div style="border:1px solid;"><span>We think you may like</span>';
			if ( ! empty( $title ) ) {
				echo $before_title . $title . $after_title;
			}
			echo '<div id="cartlassi-ajax-widget"></div>';
			echo '</div>';
			echo $after_widget;
		}


		
	}

	public function form( $instance ) {
		// outputs the options form in the admin
	}

	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
	}
}
