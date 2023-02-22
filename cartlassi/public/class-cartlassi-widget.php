<?php

class Cartlassi_Widget extends WP_Widget {
	public function __construct() {
		parent::__construct(
			'cartlassi_widget', // Base ID
			'Cartlassi_Widget', // Name
			array( 'description' => __( 'Cartlassi Widget', 'text_domain' ) ) // Args
		);
	}

	public function widget( $args, $instance ) {
		if ( is_admin() ) { 
			return;
		}
		
		extract( $args );
		// $title = apply_filters( 'widget_title', $instance['title'] );
		
		$title = $instance['title'];
		
		$apiKey = get_option('cartlassi_options')['cartlassi_field_api_key'];

		$args = array(
			// 'body'        => $body,
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
		$response = wp_remote_get( "http://host.docker.internal:3000/carts/${cartId}", $args );

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
			if ( count($products) == 3) {
				break;
			}
		}

		$options = get_option('cartlassi_options');
		$options['current_map'] = $cartItemToProductMap;
		update_option( 'cartlassi_options', $options );

		$block_name = 'woocommerce/handpicked-products';
		$converted_block = new WP_Block_Parser_Block( $block_name, array(
			'products' => $products,
		), array(), '', array() );

		$rendered_block = render_block( (array) $converted_block );

		echo $before_widget;
		echo '<div style="border:1px solid;"><span>We think you may like</span>';
		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}
		echo $rendered_block;
		echo '</div>';
		echo $after_widget;

		
	}

	public function form( $instance ) {
		// outputs the options form in the admin
	}

	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
	}
}
