<?php

/**
 * Fired during plugin activation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Cartlassi
 * @subpackage Cartlassi/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Cartlassi
 * @subpackage Cartlassi/includes
 * @author     Your Name <email@example.com>
 */
class Cartlassi_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$blogInfo = get_bloginfo();

		$body = array(
			'url'  	=> get_bloginfo('url'),
			'email' => get_bloginfo('admin_email'),
			'name'  => get_bloginfo('name'), //
		);
		$args = array(
			'body'        => $body,
			// 'timeout'     => '5',
			// 'redirection' => '5',
			// 'httpversion' => '1.0',
			// 'blocking'    => true,
			// 'headers'     => array(),
			// 'cookies'     => array(),
		);
		$response = wp_remote_post( "http://host.docker.internal:3000/shops/register", $args );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			echo "Something went wrong: $error_message";
		} else {
			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body );
	
			add_option ('cartlassi_api_key', $data->apiKey);
		}

		// $cartlassi_sidebar = register_sidebar( array(
		// 	'name'          => __( 'Cartlassi Sidebar', 'textdomain' ),
		// 	'id'            => 'sidebar-cartlassi',
		// 	'description'   => __( 'A sidebar for cartlassi plugin.', 'textdomain' ),
		// 	'before_widget' => '<li id="%1$s" class="widget %2$s">',
		// 	'after_widget'  => '</li>',
		// 	'before_title'  => '<h2 class="widgettitle">',
		// 	'after_title'   => '</h2>',
		// ) );
		// error_log("cartlassi_sidebar is {$cartlassi_sidebar}");
		// // register_widget( 'Cartlassi_Widget' );

		// Self::insert_widget_in_sidebar('cartlassi_widget', array(), $cartlassi_sidebar);

		// $ok	 = dynamic_sidebar($cartlassi_sidebar);
		// if ( $ok ) {
		// 	error_log('sidebar ok!');
		// } else {
		// 	error_log('sidebar NOT ok!');
		// }

		
	}

	protected static function insert_widget_in_sidebar( $widget_id, $widget_data, $sidebar ) {
		// Retrieve sidebars, widgets and their instances
		$sidebars_widgets = get_option( 'sidebars_widgets', array() );
		$widget_instances = get_option( 'widget_' . $widget_id, array() );
	
		error_log(var_export($sidebars_widgets, true));
		error_log(var_export($widget_instances, true));

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

}
