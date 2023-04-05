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
		$nonce = wp_create_nonce( Cartlassi_Constants::NONCE_ADMIN_NAME );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cartlassi-admin.js', array( 'jquery' ), $this->version, false );

		// in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
		wp_localize_script( $this->plugin_name, 'ajax_object',
				array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'api_key' => $apiKey , 'nonce' => $nonce, 'api_url' => $this->config->get('api_public_url')) 
		);
	}

	function cartlassi_settings_init() {
		register_setting( Cartlassi_Constants::OPTION_GROUP, Cartlassi_Constants::OPTIONS_NAME );
	
		// Register a new section in the "cartlassi" page.
		add_settings_section(
			Cartlassi_Constants::APPEARANCE_SECTION_NAME,
			__( 'Appearance settings', Cartlassi_Constants::TEXT_DOMAIN ), 
			array($this, 'cartlassi_section_default_callback'),
			Cartlassi_Constants::OPTIONS_PAGE,
		);

		add_settings_section(
			Cartlassi_Constants::DATA_SECTION_NAME,
			__( 'Data Settings', Cartlassi_Constants::TEXT_DOMAIN ), 
			array($this, 'cartlassi_section_data_callback'),
			Cartlassi_Constants::OPTIONS_PAGE,
		);

		add_settings_section(
			Cartlassi_Constants::API_SECTION_NAME,
			__( 'API Settings', Cartlassi_Constants::TEXT_DOMAIN ), 
			array($this, 'cartlassi_section_api_callback'),
			Cartlassi_Constants::OPTIONS_PAGE,
		);

		add_settings_section(
			Cartlassi_Constants::PAYMENTS_SECTION_NAME,
			__( 'Payment Method Settings', Cartlassi_Constants::TEXT_DOMAIN ), 
			array($this, 'cartlassi_section_payment_method_callback'),
			Cartlassi_Constants::OPTIONS_PAGE,
		);
	
		// Register a new field in the "cartlassi_section_developers" section, inside the "cartlassi" page.
		add_settings_field(
			Cartlassi_Constants::BEFORE_SIDEBAR_SHOP_FIELD_NAME, 
			__( 'Shop page', Cartlassi_Constants::TEXT_DOMAIN ),
			array($this, 'cartlassi_field_before_sidebar_cb'),
			Cartlassi_Constants::OPTIONS_PAGE,
			Cartlassi_Constants::APPEARANCE_SECTION_NAME,
			array(
				'label_for'         => Cartlassi_Constants::BEFORE_SIDEBAR_SHOP_FIELD_NAME,
				'class'             => Cartlassi_Constants::OPTIONS_ROW_CLASS_NAME,
				'cartlassi_custom_data' => __( 'Recommended before the top most widget on the page.', Cartlassi_Constants::TEXT_DOMAIN ),
			)
		);

		add_settings_field(
			Cartlassi_Constants::BEFORE_SIDEBAR_CATEGORY_FIELD_NAME, 
			__( 'Category page', Cartlassi_Constants::TEXT_DOMAIN ),
			array($this, 'cartlassi_field_before_sidebar_cb'),
			Cartlassi_Constants::OPTIONS_PAGE,
			Cartlassi_Constants::APPEARANCE_SECTION_NAME,
			array(
				'label_for'         => Cartlassi_Constants::BEFORE_SIDEBAR_CATEGORY_FIELD_NAME,
				'class'             => Cartlassi_Constants::OPTIONS_ROW_CLASS_NAME,
				'cartlassi_custom_data' => __( 'Recommended before the top most widget on the page.', Cartlassi_Constants::TEXT_DOMAIN ),
			)
		);

		add_settings_field(
			Cartlassi_Constants::BEFORE_SIDEBAR_PRODUCT_TAG_FIELD_NAME, 
			__( 'Product tag page', Cartlassi_Constants::TEXT_DOMAIN ),
			array($this, 'cartlassi_field_before_sidebar_cb'),
			Cartlassi_Constants::OPTIONS_PAGE,
			Cartlassi_Constants::APPEARANCE_SECTION_NAME,
			array(
				'label_for'         => Cartlassi_Constants::BEFORE_SIDEBAR_PRODUCT_TAG_FIELD_NAME,
				'class'             => Cartlassi_Constants::OPTIONS_ROW_CLASS_NAME,
				'cartlassi_custom_data' => __( 'Recommended before the top most widget on the page.', Cartlassi_Constants::TEXT_DOMAIN ),
			)
		);

		add_settings_field(
			Cartlassi_Constants::BEFORE_SIDEBAR_PRODUCT_FIELD_NAME, 
			__( 'Product page', Cartlassi_Constants::TEXT_DOMAIN ),
			array($this, 'cartlassi_field_before_sidebar_cb'),
			Cartlassi_Constants::OPTIONS_PAGE,
			Cartlassi_Constants::APPEARANCE_SECTION_NAME,
			array(
				'label_for'         => Cartlassi_Constants::BEFORE_SIDEBAR_PRODUCT_FIELD_NAME,
				'class'             => Cartlassi_Constants::OPTIONS_ROW_CLASS_NAME,
				'cartlassi_custom_data' => __( 'Recommended before the top of the footer widgets.', Cartlassi_Constants::TEXT_DOMAIN ),
			)
		);

		add_settings_field(
			Cartlassi_Constants::BEFORE_SIDEBAR_OTHER_PAGES_FIELD_NAME, 
			__( 'Other pages', Cartlassi_Constants::TEXT_DOMAIN ),
			array($this, 'cartlassi_field_before_sidebar_cb'),
			Cartlassi_Constants::OPTIONS_PAGE,
			Cartlassi_Constants::APPEARANCE_SECTION_NAME,
			array(
				'label_for'         => Cartlassi_Constants::BEFORE_SIDEBAR_OTHER_PAGES_FIELD_NAME,
				'class'             => Cartlassi_Constants::OPTIONS_ROW_CLASS_NAME,
				'cartlassi_custom_data' => __( 'Recommended before the top of the footer widgets.', Cartlassi_Constants::TEXT_DOMAIN ),
			)
		);

		add_settings_field(
			Cartlassi_Constants::BEFORE_SIDEBAR_OTHER_PAGES_STRATEGY_FIELD_NAME, 
			__( '', Cartlassi_Constants::TEXT_DOMAIN ),
			array($this, 'cartlassi_field_before_sidebar_other_pages_cb'),
			Cartlassi_Constants::OPTIONS_PAGE,
			Cartlassi_Constants::APPEARANCE_SECTION_NAME,
			array(
				'label_for'         => Cartlassi_Constants::BEFORE_SIDEBAR_OTHER_PAGES_STRATEGY_FIELD_NAME,
				'class'             => 'cartlassi-stick-to-upper-field1',
				'cartlassi_custom_data' => __( 'Recommended before the top of the footer widgets.', Cartlassi_Constants::TEXT_DOMAIN ),
			)
		);

		add_settings_field(
			Cartlassi_Constants::BEFORE_SIDEBAR_OTHER_PAGES_PAGES_FIELD_NAME, 
			__( '', Cartlassi_Constants::TEXT_DOMAIN ),
			array($this, 'cartlassi_field_before_sidebar_other_pages_pages_cb'),
			Cartlassi_Constants::OPTIONS_PAGE,
			Cartlassi_Constants::APPEARANCE_SECTION_NAME,
			array(
				'label_for'         => Cartlassi_Constants::BEFORE_SIDEBAR_OTHER_PAGES_PAGES_FIELD_NAME,
				'class'             => 'cartlassi-stick-to-upper-field2',
				'cartlassi_custom_data' => __( 'Recommended before the top of the footer widgets.', Cartlassi_Constants::TEXT_DOMAIN ),
			)
		);

		add_settings_field(
			Cartlassi_Constants::INCLUDE_IP_IN_CART_ID_FIELD_NAME, 
			__( 'Hashed customer IP address.', Cartlassi_Constants::TEXT_DOMAIN ),
			array($this, 'cartlassi_field_include_ip_in_cart_id_cb'),
			Cartlassi_Constants::OPTIONS_PAGE,
			Cartlassi_Constants::DATA_SECTION_NAME,
			array(
				'label_for'         => Cartlassi_Constants::INCLUDE_IP_IN_CART_ID_FIELD_NAME,
				'class'             => Cartlassi_Constants::OPTIONS_ROW_CLASS_NAME,
				'cartlassi_custom_data' => __( 'Mandatory, can\'t uncheck.', Cartlassi_Constants::TEXT_DOMAIN ),
			)
		);

		add_settings_field(
			Cartlassi_Constants::INCLUDE_EMAIL_IN_CART_ID_FIELD_NAME, 
			__( 'Hashed customer email.', Cartlassi_Constants::TEXT_DOMAIN ),
			array($this, 'cartlassi_field_include_email_in_cart_id_cb'),
			Cartlassi_Constants::OPTIONS_PAGE,
			Cartlassi_Constants::DATA_SECTION_NAME,
			array(
				'label_for'         => Cartlassi_Constants::INCLUDE_EMAIL_IN_CART_ID_FIELD_NAME,
				'class'             => Cartlassi_Constants::OPTIONS_ROW_CLASS_NAME,
				'cartlassi_custom_data' => __('Recommended as it improves performance of the widget. Check this if it adheres with your privacy policy', Cartlassi_Constants::TEXT_DOMAIN ),
			)
		);

		add_settings_field(
			Cartlassi_Constants::API_KEY_FIELD_NAME, 
			__( 'Your Cartlassi API Key', Cartlassi_Constants::TEXT_DOMAIN ),
			array($this, 'cartlassi_field_api_key_cb'),
			Cartlassi_Constants::OPTIONS_PAGE,
			Cartlassi_Constants::API_SECTION_NAME,
			array(
				'label_for'         => Cartlassi_Constants::API_KEY_FIELD_NAME,
				'class'             => Cartlassi_Constants::OPTIONS_ROW_CLASS_NAME,
				'cartlassi_custom_data' => 'custom',
			)
		);

		add_settings_field(
			Cartlassi_Constants::PAYMENT_METHOD_FIELD_NAME, 
			__( 'Payment Method', Cartlassi_Constants::TEXT_DOMAIN ),
			array($this, 'cartlassi_field_payment_method_cb'),
			Cartlassi_Constants::OPTIONS_PAGE,
			Cartlassi_Constants::PAYMENTS_SECTION_NAME,
			array(
				'label_for'         => Cartlassi_Constants::PAYMENT_METHOD_FIELD_NAME,
				'class'             => Cartlassi_Constants::OPTIONS_ROW_CLASS_NAME,
				'cartlassi_custom_data' => 'custom',
			)
		);

		add_settings_field(
			Cartlassi_Constants::PAYOUT_METHOD_FIELD_NAME, 
			__( 'Payout Method', Cartlassi_Constants::TEXT_DOMAIN ),
			array($this, 'cartlassi_field_payout_method_cb'),
			Cartlassi_Constants::OPTIONS_PAGE,
			Cartlassi_Constants::PAYMENTS_SECTION_NAME,
			array(
				'label_for'         => Cartlassi_Constants::PAYOUT_METHOD_FIELD_NAME,
				'class'             => Cartlassi_Constants::OPTIONS_ROW_CLASS_NAME,
				'cartlassi_custom_data' => 'custom',
			)
		);
	}

	function cartlassi_section_default_callback( $args ) {
		?>
		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Here you can configure how Cartlassi looks in your shop and where it appears. Please note that in order not to modify any of your template files, we\'ll hook into an existing and *active* sidebar and display the Cartlassi widget just before it. For each of the page types, please select which sidebar it should appear before.', Cartlassi_Constants::TEXT_DOMAIN ); ?></p>
		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'We\'re not showing the Cartlassi widget on checkout, cart, account and any of the wc endpoint pages', Cartlassi_Constants::TEXT_DOMAIN ); ?></p>
		<?php
	}

	function cartlassi_section_data_callback( $args ) {
		?>
		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Here you can configure what data your shop shares with Cartlassi. FYI we never submit any of the raw data. we hash it before so the data we share looks like `122c4a55d1a70cef972cac3982dd49a6`.', Cartlassi_Constants::TEXT_DOMAIN ); ?></p>
		<?php
	}

	function cartlassi_section_api_callback( $args ) {
		?>
		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Here you can configure how your shop communicates with the Cartlassi API servers.', Cartlassi_Constants::TEXT_DOMAIN ); ?></p>
		<?php
	}

	function cartlassi_section_payment_method_callback( $args ) {
		?>
		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Here you can configure your payment and payout methods.', Cartlassi_Constants::TEXT_DOMAIN ); ?></p>
		<?php
	}

	function cartlassi_field_before_sidebar_cb( $args ) {
		// Get the value of the setting we've registered with register_setting()
		$options = get_option( Cartlassi_Constants::OPTIONS_NAME );
		?>
		<select
				id="<?php echo esc_attr( $args['label_for'] ); ?>"
				data-custom="<?php echo esc_attr( $args['cartlassi_custom_data'] ); ?>"
				name="<?php echo esc_attr( Cartlassi_Constants::OPTIONS_NAME ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>]">
				<?php 
					foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) { 
						if (is_active_sidebar($sidebar['id']) && $sidebar['id'] !== Cartlassi_Constants::SIDEBAR_ID) {
				?>
							<option value="<?php echo ( $sidebar['id'] ); ?>" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], $sidebar['id'] , false ) ) : ( '' ); ?>>
									<?php echo ( $sidebar['name'] ); ?>
							</option>
				<?php
						}
					} 
				?>
				<option value="" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], '' , false ) ) : ( '' ); ?>>
					<?php echo __( 'Don\'t show at all', Cartlassi_Constants::TEXT_DOMAIN ); ?>
				</option>

		</select>
		<p class="description">
			<?php echo $args['cartlassi_custom_data']; ?>
		</p>
		<?php
		
	}

	function cartlassi_field_before_sidebar_other_pages_cb( $args ) {
		// Get the value of the setting we've registered with register_setting()
		$options = get_option( Cartlassi_Constants::OPTIONS_NAME );
		?>
		<input type="radio" id="<?php echo esc_attr( $args['label_for'] ); ?>"
				data-custom="<?php echo esc_attr( $args['cartlassi_custom_data'] ); ?>"
				name="<?php echo esc_attr( Cartlassi_Constants::OPTIONS_NAME ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>]"
				<?php checked( $options[ $args['label_for'] ], Cartlassi_Constants::OTHER_PAGES_OPTION_SHOW_EXCEPT , true ) ?>
				value="<?php echo esc_attr(Cartlassi_Constants::OTHER_PAGES_OPTION_SHOW_EXCEPT)?>"> <?php echo __('Show on all other pages except'); ?>
		<input type="radio" id="<?php echo esc_attr( $args['label_for'] ); ?>"
				class="cartlassi-push-right"
				data-custom="<?php echo esc_attr( $args['cartlassi_custom_data'] ); ?>"
				name="<?php echo esc_attr( Cartlassi_Constants::OPTIONS_NAME ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>]"
				<?php checked( $options[ $args['label_for'] ], Cartlassi_Constants::OTHER_PAGES_OPTION_DONT_SHOW_BUT , true ) ?>
				value="<?php echo esc_attr(Cartlassi_Constants::OTHER_PAGES_OPTION_DONT_SHOW_BUT)?>"> <?php echo __('Don\'t show on any other page but'); ?>
		
		<?php
	}

	function cartlassi_field_before_sidebar_other_pages_pages_cb( $args ) {
		$options = get_option( Cartlassi_Constants::OPTIONS_NAME );
		?>
		
		<input type="text"
			id="<?php echo esc_attr( $args['label_for'] ); ?>"
			name="<?php echo esc_attr( Cartlassi_Constants::OPTIONS_NAME ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>]"
			value="<?php echo isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : '' ; ?>"
			class="regular-text"
			>
		<p class="description">
			<?php echo __('Enter page names separated by commas. example: about-us, jobs'); ?>
		</p>
		
		<?php
	}


	function cartlassi_field_api_key_cb( $args ) {
		// Get the value of the setting we've registered with register_setting()
		$options = get_option( Cartlassi_Constants::OPTIONS_NAME );
		?>
		<input type="text"
				readonly
				id="<?php echo esc_attr( $args['label_for'] ); ?>"
				name="<?php echo esc_attr( Cartlassi_Constants::OPTIONS_NAME ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>]"
				value="<?php echo isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : '' ; ?>"
				class="regular-text"
				>
		<p class="description">
			<?php esc_attr_e( 'This is the key your shop uses to authenticate against Cartlassi servers. If you beleive your key was tempered with, you can regenarate the API Key by clicking the Regenerate button below', Cartlassi_Constants::TEXT_DOMAIN ); ?>
		</p>
		<button
			id="regenerate-api-key-button"
			class="button button-secondary"
		><?php esc_html_e( 'Regenerate API Key', Cartlassi_Constants::TEXT_DOMAIN ); ?></button>
		<?php
	}

	function cartlassi_field_payment_method_cb( $args ) {
		$apiKey = $this->getApiKey();

		$args = array(
			'headers'     => array(
				'Authorization' => "token {$apiKey}"
			),
		);
		
		$response = wp_remote_get( "{$this->config->get('api_url')}/shops/payment-method", $args );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			error_log("WWWWWWWWWWW ${error_message}");
			wp_send_json_error($response);
		} else {
			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body );
			if ($data->brand && $data->last4) {
				echo "{$data->brand} {$data->last4}";
			} else {
				?>
		
				<button type="submit"
					id="pay-button"
					class="button button-secondary"
				><?php esc_html_e( 'Add Payment Method', Cartlassi_Constants::TEXT_DOMAIN ); ?></button>
				<?php
			}
		}		
		
	}

	function cartlassi_field_payout_method_cb( $args ) {
		$apiKey = $this->getApiKey();

		$args = array(
			'headers'     => array(
				'Authorization' => "token {$apiKey}"
			),
		);
		
		$response = wp_remote_get( "{$this->config->get('api_url')}/shops/payout-method", $args );

		if ( is_wp_error( $response ) ) {
			$error_message = $response->get_error_message();
			error_log("WWWWWWWWWWW ${error_message}");
			wp_send_json_error($response);
		} else {
			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body );
			if ($data->stripeConnectConnected) {
				esc_html_e( 'Connected via Stripe Connect', Cartlassi_Constants::TEXT_DOMAIN );
			} else {
				?>
		
				<button type="submit"
					id="payout-button"
					class="button button-secondary"
				><?php esc_html_e( 'Add Payout Method', Cartlassi_Constants::TEXT_DOMAIN ); ?></button>
				<?php
			}
		}		
	}

	function cartlassi_field_include_ip_in_cart_id_cb ( $args ) {
		$options = get_option( Cartlassi_Constants::OPTIONS_NAME );
		?>
			<input type="checkbox"
				disabled
				id="<?php echo esc_attr( $args['label_for'] ); ?>"
				name="<?php echo esc_attr( Cartlassi_Constants::OPTIONS_NAME ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>]"
				value="<?php echo isset( $options[ $args['label_for'] ] ) ? true : false ; ?>"
				checked
			>
			<p class="description">
				<?php echo $args['cartlassi_custom_data']; ?>
			</p>
		<?php 
	}

	function cartlassi_field_include_email_in_cart_id_cb ( $args ) {
		$options = get_option( Cartlassi_Constants::OPTIONS_NAME );
		?>
			<input type="checkbox"
				id="<?php echo esc_attr( $args['label_for'] ); ?>"
				name="<?php echo esc_attr( Cartlassi_Constants::OPTIONS_NAME ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>]"
				value="<?php echo isset( $options[ $args['label_for'] ] ) ? true : false ; ?>"
				<?php echo isset( $options[ $args['label_for'] ] ) ? 'checked' : '' ; ?>
			>
			<p class="description">
				<?php echo $args['cartlassi_custom_data']; ?>
			</p>
		<?php 
	}

	function cartlassi_options_page() {
		add_menu_page(
			'',
			'Cartlassi',
			'manage_options',
			Cartlassi_Constants::TOP_MENU_SLUG,
			null,
			// TBD insert cartlassi icon here
		);

		add_submenu_page(
			Cartlassi_Constants::TOP_MENU_SLUG,
			'Settings',
			'Settings',
			'manage_options',
			'cartlassi',
			array($this, 'cartlassi_options_page_html')
		);

		$hook = add_submenu_page(
			Cartlassi_Constants::TOP_MENU_SLUG,
			'Sales',
			'Sales',
			'manage_options',
			'cartlassi-sales',
			array($this, 'cartlassi_sales_page_html')
		);
		add_action( "load-$hook", [ $this, 'screen_option_sales' ] );

		$hook = add_submenu_page(
			Cartlassi_Constants::TOP_MENU_SLUG,
			'Commissions',
			'Commissions',
			'manage_options',
			'cartlassi-commissions',
			array($this, 'cartlassi_commissions_page_html')
		);
		add_action( "load-$hook", [ $this, 'screen_option_commissions' ] );
	}

	function cartlassi_wc_options_page() {
		wc_admin_connect_page(
			array(
				'id'        => 'cartlassi-woocommerce-settings',
				'screen_id' => 'woocommerce_page_wc-settings-general',
				'title'     => array('Settings', 'General'),
				'path'      => add_query_arg( 'page', 'wc-settings', 'admin.php' ),
			)
		);
	}

	function cartlassi_sales_page_html() {
		?>
			<div class="wrap">

				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-2">
						<div id="post-body-content">
							<div class="meta-box-sortables ui-sortable">
								<form method="post">
									<?php
										$this->salesList->prepare_items();
										$this->salesList->display(); 
									?>
								</form>
							</div>
						</div>
					</div>
					<br class="clear">
					</div>
				</div>
			</div>
		<?php
	}

	function cartlassi_commissions_page_html() {
		?>
			<div class="wrap">

				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-2">
						<div id="post-body-content">
							<div class="meta-box-sortables ui-sortable">
								<form method="post">
									<?php
										$this->commissionsList->prepare_items();
										$this->commissionsList->display(); 
									?>
								</form>
							</div>
						</div>
					</div> 
					<br class="clear">
					</div>
				</div>
			</div>
		<?php
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

		if ( $_GET['account-connected'] ) {
			// a stripe connect process has just ended, need to process it.
			$apiKey = $this->getApiKey();

			$args = array(
				'headers'     => array(
					'Authorization' => "token {$apiKey}"
				),
			);
			$response = wp_remote_post( "{$this->config->get('api_url')}/shops/complete-stripe-connect", $args );
		}
		// add error/update messages
	
		// check if the user have submitted the settings
		// WordPress will add the "settings-updated" $_GET parameter to the url
		if ( isset( $_GET['settings-updated'] ) ) {
			// add settings saved message with the class of "updated"
			add_settings_error( 'cartlassi_messages', 'cartlassi_message', __( 'Settings Saved', Cartlassi_Constants::TEXT_DOMAIN ), 'updated' );
		}
	
		// show error/update messages
		settings_errors( 'cartlassi_messages' );
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				// output security fields for the registered setting "cartlassi"
				settings_fields( Cartlassi_Constants::OPTION_GROUP );
				// output setting sections and their fields
				// (sections are registered for "cartlassi", each field is registered to a specific section)
				do_settings_sections( Cartlassi_Constants::OPTIONS_PAGE );
				// output save settings button
				submit_button( 'Save Settings' );
				?>
			</form>
		</div>
		<?php
	}

	function regenerate_api_key () {
		check_ajax_referer(Cartlassi_Constants::NONCE_ADMIN_NAME, 'nonce');
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
			$options = get_option(Cartlassi_Constants::OPTIONS_NAME);
			$options[Cartlassi_Constants::API_KEY_FIELD_NAME] = $data->apiKey;
			update_option(Cartlassi_Constants::OPTIONS_NAME, $options);
			// error_log("WWWWWWWWWWW $body");
			echo $body;
		}
		wp_die();
	}

	protected function getApiKey() {
		return get_option(Cartlassi_Constants::OPTIONS_NAME)[Cartlassi_Constants::API_KEY_FIELD_NAME];
	}

	public function add_action_links($links, $file){
		error_log($file);
		if ($file == 'cartlassi/cartlassi.php') {
			$mylinks = array(
			 '<a href="' . admin_url( 'admin.php?page=cartlassi' ) . '">Settings</a>',
			);
			error_log(var_export($mylinks,true));
			return array_merge( $mylinks , $links );
		}else{
			return $links;
		}
	}

	public function set_screen($status, $option, $value) {
		return $value;
	}

	/**
	* Screen options
	*/
	public function screen_option_sales() {

		$option = 'per_page';
		$args = [
		'label' => 'Sales',
		'default' => 10,
		'option' => 'sales_per_page'
		];
		
		add_screen_option( $option, $args );
		
		$this->salesList = new Sales_List($this->config, $this->getApiKey());
	}

	public function screen_option_commissions() {

		$option = 'per_page';
		$args = [
		'label' => 'Commissions',
		'default' => 10,
		'option' => 'commissions_per_page'
		];
		
		add_screen_option( $option, $args );
		
		$this->commissionsList = new Commissions_List($this->config, $this->getApiKey());
	}
}
