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
	 * @var      string    $plugin_name    The ID of this cartlassi.
	 */
	private $plugin_name;

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
	 * @param      string    $plugin_name       The name of this cartlassi.
	 * @param      string    $version    The version of this cartlassi.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/cartlassi-admin.css', array(), $this->version, 'all' );

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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cartlassi-admin.js', array( 'jquery' ), $this->version, false );

	}

	function cartlassi_settings_init() {
		register_setting( 'cartlassi', 'cartlassi_options' );
	
		// Register a new section in the "cartlassi" page.
		add_settings_section(
			'cartlassi_section_default',
			__( 'Cartlassi settings', 'cartlassi' ), array($this, 'cartlassi_section_default_callback'),
			'cartlassi'
		);
	
		// Register a new field in the "cartlassi_section_developers" section, inside the "cartlassi" page.
		add_settings_field(
			'cartlassi_field_before_sidebar', // As of WP 4.6 this value is used only internally.
									// Use $args' label_for to populate the id inside the callback.
				__( 'Before which sidebar to display the cartlassi widget?', 'cartlassi' ),
			array($this, 'cartlassi_field_before_sidebar_cb'),
			'cartlassi',
			'cartlassi_section_default',
			array(
				'label_for'         => 'cartlassi_field_before_sidebar',
				'class'             => 'cartlassi_row',
				'cartlassi_custom_data' => 'custom',
			)
		);
	}

	function cartlassi_section_default_callback( $args ) {
		?>
		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Follow the white rabbit.', 'cartlassi' ); ?></p>
		<?php
	}

	function cartlassi_field_before_sidebar_cb( $args ) {
		// Get the value of the setting we've registered with register_setting()
		$options = get_option( 'cqartlassi_options' );
		?>
		<select
				id="<?php echo esc_attr( $args['label_for'] ); ?>"
				data-custom="<?php echo esc_attr( $args['cartlassi_custom_data'] ); ?>"
				name="cartlassi_options[<?php echo esc_attr( $args['label_for'] ); ?>]">
				<?php foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) { ?>
					<option value="<?php echo ucwords( $sidebar['id'] ); ?>" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], ucwords( $sidebar['id'] ) , false ) ) : ( '' ); ?>>
							<?php echo ucwords( $sidebar['name'] ); ?>
					</option>
				<?php } ?>
		</select>
		<p class="description">
			<?php esc_html_e( 'You take the blue pill and the story ends. You wake in your bed and you believe whatever you want to believe.', 'cartlassi' ); ?>
		</p>
		<p class="description">
			<?php esc_html_e( 'You take the red pill and you stay in Wonderland and I show you how deep the rabbit-hole goes.', 'cartlassi' ); ?>
		</p>
		<?php
	}

	function cartlassi_options_page() {
		add_menu_page(
			'Cartlassi',
			'Cartlassi Options',
			'manage_options',
			'cartlassi',
			// 'cartlassi_options_page_html'
			array($this, 'cartlassi_options_page_html')
		);
	}

	function cartlassi_options_page_html() {
		// check user capabilities
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
	
		// add error/update messages
	
		// check if the user have submitted the settings
		// WordPress will add the "settings-updated" $_GET parameter to the url
		if ( isset( $_GET['settings-updated'] ) ) {
			// add settings saved message with the class of "updated"
			add_settings_error( 'cartlassi_messages', 'cartlassi_message', __( 'Settings Saved', 'cartlassi' ), 'updated' );
		}
	
		// show error/update messages
		settings_errors( 'cartlassi_messages' );
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				// output security fields for the registered setting "cartlassi"
				settings_fields( 'cartlassi' );
				// output setting sections and their fields
				// (sections are registered for "cartlassi", each field is registered to a specific section)
				do_settings_sections( 'cartlassi' );
				// output save settings button
				submit_button( 'Save Settings' );
				?>
			</form>
		</div>
		<?php
	}
	

}
