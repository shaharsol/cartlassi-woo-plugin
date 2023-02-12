<?php

/**
 * The admin-specific functionality of the cartlassi.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Cartlassi
 * @subpackage Cartlassi/admin
 */

/**
 * The admin-specific functionality of the cartlassi.
 *
 * Defines the cartlassi name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Cartlassi
 * @subpackage Cartlassi/admin
 * @author     Your Name <email@example.com>
 */
class Cartlassi_Admin {

	/**
	 * The ID of this cartlassi.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $cartlassi_name    The ID of this cartlassi.
	 */
	private $cartlassi_name;

	/**
	 * The version of this cartlassi.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this cartlassi.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $cartlassi_name       The name of this cartlassi.
	 * @param      string    $version    The version of this cartlassi.
	 */
	public function __construct( $cartlassi_name, $version ) {

		$this->cartlassi_name = $cartlassi_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->cartlassi_name, cartlassi_dir_url( __FILE__ ) . 'css/cartlassi-name-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->cartlassi_name, cartlassi_dir_url( __FILE__ ) . 'js/cartlassi-name-admin.js', array( 'jquery' ), $this->version, false );

	}

}
