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
	private $config;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this cartlassi.
	 * @param      string    $version    The version of this cartlassi.
	 */
	public function __construct( $plugin_name, $version, $config ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->config = $config;

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
		$apiKey = $this->getApiKey();
		$nonce = wp_create_nonce( 'cartlassi' );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cartlassi-admin.js', array( 'jquery' ), $this->version, false );

		// in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
		wp_localize_script( $this->plugin_name, 'ajax_object',
				array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'api_key' => $apiKey , 'nonce' => $nonce) 
		);
		

	}

	function cartlassi_settings_init() {
		register_setting( 'cartlassi', Cartlassi_Constants::CARTLASSI_OPTIONS_NAME );
	
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

		add_settings_field(
			Cartlassi_Constants::CARTLASSI_API_KEY_FIELD_NAME, // As of WP 4.6 this value is used only internally.
									// Use $args' label_for to populate the id inside the callback.
			__( 'Your Cartlassi API Key', 'cartlassi' ),
			array($this, 'cartlassi_field_api_key_cb'),
			'cartlassi',
			'cartlassi_section_default',
			array(
				'label_for'         => Cartlassi_Constants::CARTLASSI_API_KEY_FIELD_NAME,
				'class'             => 'cartlassi_row',
				'cartlassi_custom_data' => 'custom',
			)
		);

		add_settings_field(
			'cartlassi_field_payment_method', // As of WP 4.6 this value is used only internally.
									// Use $args' label_for to populate the id inside the callback.
			__( 'Payment Method', 'cartlassi' ),
			array($this, 'cartlassi_field_payment_method_cb'),
			'cartlassi',
			'cartlassi_section_default',
			array(
				'label_for'         => 'cartlassi_field_payment_method',
				'class'             => 'cartlassi_row',
				'cartlassi_custom_data' => 'custom',
			)
		);
	}

	function cartlassi_section_default_callback( $args ) {
		?>
		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'The place to configure Cartlassi.', 'cartlassi' ); ?></p>
		<?php
	}

	function cartlassi_field_before_sidebar_cb( $args ) {
		// Get the value of the setting we've registered with register_setting()
		$options = get_option( Cartlassi_Constants::CARTLASSI_OPTIONS_NAME );
		?>
		<select
				id="<?php echo esc_attr( $args['label_for'] ); ?>"
				data-custom="<?php echo esc_attr( $args['cartlassi_custom_data'] ); ?>"
				name="cartlassi_options[<?php echo esc_attr( $args['label_for'] ); ?>]">
				<?php 
					foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) { 
						if (is_active_sidebar($sidebar['id']) && $sidebar['id'] !== 'sidebar-cartlassi') {
				?>
							<option value="<?php echo ( $sidebar['id'] ); ?>" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], $sidebar['id'] , false ) ) : ( '' ); ?>>
									<?php echo ( $sidebar['name'] ); ?>
							</option>
				<?php
						}
					} 
				?>
		</select>
		<p class="description">
			<?php esc_attr_e( 'In order not to mess with your template files, we\'ll hook into an existing and *active* sidebar and display the Cartlassi widget just before it. Please select which sidebar it should be.', 'cartlassi' ); ?>
		</p>
		<?php
	}

	function cartlassi_field_api_key_cb( $args ) {
		// Get the value of the setting we've registered with register_setting()
		$options = get_option( Cartlassi_Constants::CARTLASSI_OPTIONS_NAME );
		?>
		<input type="text"
				readonly
				id="<?php echo esc_attr( $args['label_for'] ); ?>"
				name="cartlassi_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
				value="<?php echo isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : '' ; ?>"
				class="regular-text"
				>
		<p class="description">
			<?php esc_attr_e( 'This is the key your shop use to authenticate with Cartlassi servers. To regenarate the API Key, please click the Regenerate button below', 'cartlassi' ); ?>
		</p>
		<button
			id="regenerate-api-key-button"
			class=""
		><?php esc_html_e( 'Regenerate API Key', 'cartlassi' ); ?></button>
		<?php
	}

	function cartlassi_field_payment_method_cb( $args ) {
		$apiKey = $this->getApiKey();

		$args = array(
			// 'timeout'     => '5',
			// 'redirection' => '5',
			// 'httpversion' => '1.0',
			// 'blocking'    => true,
			'headers'     => array(
				'Authorization' => "token {$apiKey}"
			),
			// 'cookies'     => array(),
		);
		
		$response = wp_remote_get( "{$this->config->get('api_url')}/shops/payment-method", $args );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			error_log("WWWWWWWWWWW ${error_message}");
			wp_send_json_error($response);
		} else {
			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body );
			// if ($data->brand && $data->last4) {
			// 	echo "{$data->brand} {$data->last4}";
			// } else {
				?>
		
				<button type="submit"
					id="pay-button"
					class=""
				><?php esc_html_e( 'Payment Method', 'cartlassi' ); ?></button>
				<?php
			// }
		}		
		
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
	
		$stripeSessionId = isset( $_GET['session_id'] ) ? $_GET['session_id'] : false;
		if ($stripeSessionId) {
			$apiKey = $this->getApiKey();

			$args = array(
				'body'			=> array(
					'session_id' => $stripeSessionId
				),
				'headers'     => array(
					'Authorization' => "token {$apiKey}"
				),
			);
			$response = wp_remote_post( "{$this->config->get('api_url')}/shops/complete-stripe", $args );
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

	function regenerate_api_key () {
		check_ajax_referer('cartlassi', 'nonce');
		$apiKey = $this->getApiKey();

		$args = array(
			'headers'     => array(
				'Authorization' => "token {$apiKey}"
			),
		);
		$response = wp_remote_post( "{$this->config->get('api_url')}/shops/regenerate-api-key", $args );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			error_log("WWWWWWWWWWW ${error_message}");
			wp_send_json_error($response);
		} else {
			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body );
			$options = get_option(Cartlassi_Constants::CARTLASSI_OPTIONS_NAME);
			$options[Cartlassi_Constants::CARTLASSI_API_KEY_FIELD_NAME] = $data->apiKey;
			update_option(Cartlassi_Constants::CARTLASSI_OPTIONS_NAME, $options);
			// error_log("WWWWWWWWWWW $body");
			echo $body;
		}
		wp_die();
	}

	protected function getApiKey() {
		return get_option(Cartlassi_Constants::CARTLASSI_OPTIONS_NAME)[Cartlassi_Constants::CARTLASSI_API_KEY_FIELD_NAME];
	}
}
