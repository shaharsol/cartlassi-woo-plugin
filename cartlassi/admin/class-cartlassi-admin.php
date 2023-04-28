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
use \Automattic\WooCommerce\Admin\Features\Navigation\Menu;
use \Automattic\WooCommerce\Admin\Features\Features;

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
	private $api;
	private $utils;
	private $commissions_list;
	private $sales_list;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this cartlassi.
	 * @param      string    $version    The version of this cartlassi.
	 */
	public function __construct( $plugin_name, $version, $config, $api, $utils ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->config = $config;
		$this->api = $api;
		$this->utils = $utils;

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
		$nonce = wp_create_nonce( Cartlassi_Constants::NONCE_ADMIN_NAME );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/cartlassi-admin.js', array( 'jquery' ), $this->version, false );

		// in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
		wp_localize_script( $this->plugin_name, 'ajax_object',
				array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'api_key' => $this->utils->get_api_key() , 'nonce' => $nonce, 'api_url' => $this->config->get('api_public_url')) 
		);
	}

	function cartlassi_settings_init() {
		// register_setting( Cartlassi_Constants::OPTION_GROUP, Cartlassi_Constants::OPTIONS_NAME );
		register_setting( Cartlassi_Constants::APPEARANCE_SECTION_NAME, Cartlassi_Constants::APPEARANCE_OPTIONS_NAME );
		register_setting( Cartlassi_Constants::DATA_SECTION_NAME, Cartlassi_Constants::DATA_OPTIONS_NAME );
		register_setting( Cartlassi_Constants::API_SECTION_NAME, Cartlassi_Constants::API_OPTIONS_NAME );
		register_setting( Cartlassi_Constants::PAYMENTS_SECTION_NAME, Cartlassi_Constants::PAYMENTS_OPTIONS_NAME );
	
		// Register a new section in the "cartlassi" page.
		add_settings_section(
			Cartlassi_Constants::APPEARANCE_SECTION_NAME,
			__( 'Appearance settings', Cartlassi_Constants::TEXT_DOMAIN ), 
			array($this, 'cartlassi_section_default_callback'),
			Cartlassi_Constants::APPEARANCE_SECTION_PAGE,
		);

		add_settings_section(
			Cartlassi_Constants::DATA_SECTION_NAME,
			__( 'Data Settings', Cartlassi_Constants::TEXT_DOMAIN ), 
			array($this, 'cartlassi_section_data_callback'),
			Cartlassi_Constants::DATA_SECTION_PAGE,
		);

		add_settings_section(
			Cartlassi_Constants::API_SECTION_NAME,
			__( 'API Settings', Cartlassi_Constants::TEXT_DOMAIN ), 
			array($this, 'cartlassi_section_api_callback'),
			Cartlassi_Constants::API_SECTION_PAGE,
		);

		add_settings_section(
			Cartlassi_Constants::PAYMENTS_SECTION_NAME,
			__( 'Payment Method Settings', Cartlassi_Constants::TEXT_DOMAIN ), 
			array($this, 'cartlassi_section_payment_method_callback'),
			Cartlassi_Constants::PAYMENTS_SECTION_PAGE,
		);
	
		// Register a new field in the "cartlassi_section_developers" section, inside the "cartlassi" page.
		add_settings_field(
			Cartlassi_Constants::BEFORE_SIDEBAR_SHOP_FIELD_NAME, 
			__( 'Shop page', Cartlassi_Constants::TEXT_DOMAIN ),
			array($this, 'cartlassi_field_before_sidebar_cb'),
			Cartlassi_Constants::APPEARANCE_SECTION_PAGE,
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
			Cartlassi_Constants::APPEARANCE_SECTION_PAGE,
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
			Cartlassi_Constants::APPEARANCE_SECTION_PAGE,
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
			Cartlassi_Constants::APPEARANCE_SECTION_PAGE,
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
			Cartlassi_Constants::APPEARANCE_SECTION_PAGE,
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
			Cartlassi_Constants::APPEARANCE_SECTION_PAGE,
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
			Cartlassi_Constants::APPEARANCE_SECTION_PAGE,
			Cartlassi_Constants::APPEARANCE_SECTION_NAME,
			array(
				'label_for'         => Cartlassi_Constants::BEFORE_SIDEBAR_OTHER_PAGES_PAGES_FIELD_NAME,
				'class'             => 'cartlassi-stick-to-upper-field2',
				'cartlassi_custom_data' => __( 'Recommended before the top of the footer widgets.', Cartlassi_Constants::TEXT_DOMAIN ),
			)
		);

		add_settings_field(
			Cartlassi_Constants::INCLUDE_IP_IN_CART_ID_FIELD_NAME, 
			__( 'Hashed customer IP address', Cartlassi_Constants::TEXT_DOMAIN ),
			array($this, 'cartlassi_field_include_ip_in_cart_id_cb'),
			Cartlassi_Constants::DATA_SECTION_PAGE,
			Cartlassi_Constants::DATA_SECTION_NAME,
			array(
				'label_for'         => Cartlassi_Constants::INCLUDE_IP_IN_CART_ID_FIELD_NAME,
				'class'             => Cartlassi_Constants::OPTIONS_ROW_CLASS_NAME,
				'cartlassi_custom_data' => __( 'Mandatory, can\'t uncheck.', Cartlassi_Constants::TEXT_DOMAIN ),
			)
		);

		add_settings_field(
			Cartlassi_Constants::INCLUDE_EMAIL_IN_CART_ID_FIELD_NAME, 
			__( 'Hashed customer email', Cartlassi_Constants::TEXT_DOMAIN ),
			array($this, 'cartlassi_field_include_email_in_cart_id_cb'),
			Cartlassi_Constants::DATA_SECTION_PAGE,
			Cartlassi_Constants::DATA_SECTION_NAME,
			array(
				'label_for'         => Cartlassi_Constants::INCLUDE_EMAIL_IN_CART_ID_FIELD_NAME,
				'class'             => Cartlassi_Constants::OPTIONS_ROW_CLASS_NAME,
				'cartlassi_custom_data' => __('Recommended as it improves performance of the widget. Check this if it adheres with your privacy policy', Cartlassi_Constants::TEXT_DOMAIN ),
			)
		);

		add_settings_field(
			Cartlassi_Constants::EXTRA_ENCRYPTION_FIELD_NAME, 
			__( 'Add extra encryption layer', Cartlassi_Constants::TEXT_DOMAIN ),
			array($this, 'cartlassi_field_extra_encryption_cb'),
			Cartlassi_Constants::DATA_SECTION_PAGE,
			Cartlassi_Constants::DATA_SECTION_NAME,
			array(
				'label_for'         => Cartlassi_Constants::EXTRA_ENCRYPTION_FIELD_NAME,
				'class'             => Cartlassi_Constants::OPTIONS_ROW_CLASS_NAME,
				'cartlassi_custom_data' => __('Will further encrypt the hashed IP address and/or email address before sending it over the network. Will cost a little extra CPU on your end and ours. Your call.', Cartlassi_Constants::TEXT_DOMAIN ),
			)
		);

		add_settings_field(
			Cartlassi_Constants::API_KEY_FIELD_NAME, 
			__( 'Your Cartlassi API Key', Cartlassi_Constants::TEXT_DOMAIN ),
			array($this, 'cartlassi_field_api_key_cb'),
			Cartlassi_Constants::API_SECTION_PAGE,
			Cartlassi_Constants::API_SECTION_NAME,
			array(
				'label_for'         => Cartlassi_Constants::API_KEY_FIELD_NAME,
				'class'             => Cartlassi_Constants::OPTIONS_ROW_CLASS_NAME,
				'cartlassi_custom_data' => 'custom',
			)
		);

		add_settings_field(
			Cartlassi_Constants::API_SECRET_FIELD_NAME, 
			__( 'Your Cartlassi API Secret', Cartlassi_Constants::TEXT_DOMAIN ),
			array($this, 'cartlassi_field_api_secret_cb'),
			Cartlassi_Constants::API_SECTION_PAGE,
			Cartlassi_Constants::API_SECTION_NAME,
			array(
				'label_for'         => Cartlassi_Constants::API_SECRET_FIELD_NAME,
				'class'             => Cartlassi_Constants::OPTIONS_ROW_CLASS_NAME,
				'cartlassi_custom_data' => 'custom',
			)
		);

		add_settings_field(
			Cartlassi_Constants::PAYMENT_METHOD_FIELD_NAME, 
			__( 'Payment Method', Cartlassi_Constants::TEXT_DOMAIN ),
			array($this, 'cartlassi_field_payment_method_cb'),
			Cartlassi_Constants::PAYMENTS_SECTION_PAGE,
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
			Cartlassi_Constants::PAYMENTS_SECTION_PAGE,
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
		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'Here you can configure what data your shop shares with Cartlassi.', Cartlassi_Constants::TEXT_DOMAIN ); ?></p>
		<p id="<?php echo esc_attr( $args['id'] ); ?>"><?php esc_html_e( 'FYI we never allow any of the raw data to leave your site. We hash the data before we send it to our servers. Here is what your own hash looks like:', Cartlassi_Constants::TEXT_DOMAIN ); ?></p>
		<input id="cartlassi-demo-hash" type="text" disabled value="<?php $options = get_option(Cartlassi_Constants::DATA_OPTIONS_NAME); echo $this->utils->demo_cart_id( isset($options[Cartlassi_Constants::INCLUDE_EMAIL_IN_CART_ID_FIELD_NAME] ), isset($options[Cartlassi_Constants::EXTRA_ENCRYPTION_FIELD_NAME] ) );?>">
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
		$options = get_option( Cartlassi_Constants::APPEARANCE_OPTIONS_NAME );
		?>
		<select
				id="<?php echo esc_attr( $args['label_for'] ); ?>"
				data-custom="<?php echo esc_attr( $args['cartlassi_custom_data'] ); ?>"
				name="<?php echo esc_attr( Cartlassi_Constants::APPEARANCE_OPTIONS_NAME ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>]">
				<option value="" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], '' , false ) ) : ( '' ); ?>>
					<?php echo __( 'Please Select', Cartlassi_Constants::TEXT_DOMAIN ); ?>
				</option>
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
				<option value="do-not-show" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], 'do-not-show' , false ) ) : ( '' ); ?>>
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
		$options = get_option( Cartlassi_Constants::APPEARANCE_OPTIONS_NAME );
		?>
		<input type="radio" id="<?php echo esc_attr( $args['label_for'] ); ?>"
				data-custom="<?php echo esc_attr( $args['cartlassi_custom_data'] ); ?>"
				name="<?php echo esc_attr( Cartlassi_Constants::APPEARANCE_OPTIONS_NAME ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>]"
				<?php checked( $options[ $args['label_for'] ], Cartlassi_Constants::OTHER_PAGES_OPTION_SHOW_EXCEPT , true ) ?>
				value="<?php echo esc_attr(Cartlassi_Constants::OTHER_PAGES_OPTION_SHOW_EXCEPT)?>"> <?php echo __('Show on all other pages except'); ?>
		<input type="radio" id="<?php echo esc_attr( $args['label_for'] ); ?>"
				class="cartlassi-push-right"
				data-custom="<?php echo esc_attr( $args['cartlassi_custom_data'] ); ?>"
				name="<?php echo esc_attr( Cartlassi_Constants::APPEARANCE_OPTIONS_NAME ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>]"
				<?php checked( $options[ $args['label_for'] ], Cartlassi_Constants::OTHER_PAGES_OPTION_DONT_SHOW_BUT , true ) ?>
				value="<?php echo esc_attr(Cartlassi_Constants::OTHER_PAGES_OPTION_DONT_SHOW_BUT)?>"> <?php echo __('Don\'t show on any other page but'); ?>
		
		<?php
	}

	function cartlassi_field_before_sidebar_other_pages_pages_cb( $args ) {
		$options = get_option( Cartlassi_Constants::APPEARANCE_OPTIONS_NAME );
		?>
		
		<input type="text"
			id="<?php echo esc_attr( $args['label_for'] ); ?>"
			name="<?php echo esc_attr( Cartlassi_Constants::APPEARANCE_OPTIONS_NAME ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>]"
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
		$options = get_option( Cartlassi_Constants::API_OPTIONS_NAME );
		?>
		<input type="text"
				readonly
				id="<?php echo esc_attr( $args['label_for'] ); ?>"
				name="<?php echo esc_attr( Cartlassi_Constants::API_OPTIONS_NAME ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>]"
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

	function cartlassi_field_api_secret_cb( $args ) {
		// Get the value of the setting we've registered with register_setting()
		$options = get_option( Cartlassi_Constants::API_OPTIONS_NAME );
		?>
		<input type="text"
				readonly
				id="<?php echo esc_attr( $args['label_for'] ); ?>"
				name="<?php echo esc_attr( Cartlassi_Constants::API_OPTIONS_NAME ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>]"
				value="<?php echo isset( $options[ $args['label_for'] ] ) ? $options[ $args['label_for'] ] : '' ; ?>"
				class="regular-text"
				>
		<p class="description">
			<?php esc_attr_e( 'This is the secret your shop uses to create stronger signatures against Cartlassi servers. If you beleive your secret was tempered with, you can regenarate the API Secret by clicking the Regenerate button below', Cartlassi_Constants::TEXT_DOMAIN ); ?>
		</p>
		<button
			id="regenerate-api-secret-button"
			class="button button-secondary"
		><?php esc_html_e( 'Regenerate API Secret', Cartlassi_Constants::TEXT_DOMAIN ); ?></button>
		<?php
	}

	function cartlassi_field_payment_method_cb( $args ) {
		$data = $this->utils->get_payment_method(false); // $use_cache = false
		
		if ($data->brand && $data->last4) {
			?>
			<span class="cartlassi-payment-method"><?php echo "{$data->brand} {$data->last4} &nbsp;"; ?></span>
			<button
				id="cancel-payment-method-button"
				class="cartlassi-payment-method button button-secondary"
			><?php esc_html_e( 'Remove', Cartlassi_Constants::TEXT_DOMAIN ); ?></button>
			<?php
		} else {
			?>
	
			<button type="submit"
				id="pay-button"
				class="button button-secondary"
			><?php esc_html_e( 'Add Payment Method', Cartlassi_Constants::TEXT_DOMAIN ); ?></button>
			<?php
		}
	}

	function cartlassi_field_payout_method_cb( $args ) {
		$data = $this->utils->get_payout_method(false); // $use_cache = false
		if ($data->stripeConnectAccountId && $data->stripeConnectConnected) {
			?>
			<span class="cartlassi-payment-method"><?php esc_html_e( 'Connected via Stripe Connect &nbsp;', Cartlassi_Constants::TEXT_DOMAIN ); ?></span>
			<button
				id="cancel-payout-method-button"
				class="cartlassi-payment-method button button-secondary"
			><?php esc_html_e( 'Remove', Cartlassi_Constants::TEXT_DOMAIN ); ?></button>
			<?php
		} else {
			?>
	
			<button type="submit"
				id="payout-button"
				class="button button-secondary"
			><?php esc_html_e( 'Add Payout Method', Cartlassi_Constants::TEXT_DOMAIN ); ?></button>
			<?php
		}
	}

	function cartlassi_field_include_ip_in_cart_id_cb ( $args ) {
		$options = get_option( Cartlassi_Constants::DATA_OPTIONS_NAME );
		?>
			<input type="checkbox"
				disabled
				id="<?php echo esc_attr( $args['label_for'] ); ?>"
				name="<?php echo esc_attr( Cartlassi_Constants::DATA_OPTIONS_NAME ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>]"
				value="<?php echo isset( $options[ $args['label_for'] ] ) ? true : false ; ?>"
				checked
			>
			<p class="description">
				<?php echo $args['cartlassi_custom_data']; ?>
			</p>
		<?php 
	}

	function cartlassi_field_include_email_in_cart_id_cb ( $args ) {
		$options = get_option( Cartlassi_Constants::DATA_OPTIONS_NAME );
		?>
			<input type="checkbox"
				id="<?php echo esc_attr( $args['label_for'] ); ?>"
				name="<?php echo esc_attr( Cartlassi_Constants::DATA_OPTIONS_NAME ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>]"
				value="<?php echo isset( $options[ $args['label_for'] ] ) ? true : false ; ?>"
				<?php echo isset( $options[ $args['label_for'] ] ) ? 'checked' : '' ; ?>
			>
			<p class="description">
				<?php echo $args['cartlassi_custom_data']; ?>
			</p>
		<?php 
	}

	function cartlassi_field_extra_encryption_cb ( $args ) {
		$options = get_option( Cartlassi_Constants::DATA_OPTIONS_NAME );
		?>
			<input type="checkbox"
				id="<?php echo esc_attr( $args['label_for'] ); ?>"
				name="<?php echo esc_attr( Cartlassi_Constants::DATA_OPTIONS_NAME ); ?>[<?php echo esc_attr( $args['label_for'] ); ?>]"
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

		$hook = add_submenu_page(
			Cartlassi_Constants::TOP_MENU_SLUG,
			'Stats',
			'Stats',
			'manage_options',
			'cartlassi-stats',
			array($this, 'cartlassi_stats_page_html')
		);
		add_action( "admin_print_scripts-$hook", [ $this, 'enqueue_chart_scripts' ] );
	}

	function cartlassi_wc_options_page() {
		// wc_admin_connect_page(
		// 	array(
		// 		'id'        => $this->plugin_name,
		// 		'screen_id' => 'woocommerce_page_mailchimp-woocommerce',
		// 		'title'     => __( 'Mailchimp for WooCommerce', 'mailchimp-for-woocommerce' ),
		// 	)
		// );
		// Add woocommerce menu subitem
		// add_submenu_page(
		// 	'woocommerce',
		// 	__( 'Cartlassi for WooCommerce', 'cartlassi-for-woocommerce' ),
		// 	__( 'Cartlassi', 'cartlassi-for-woocommerce' ),
		// 	'manage_options',
		// 	$this->plugin_name,
		// 	array( $this, 'display_plugin_setup_page' )
		// );
		wc_admin_connect_page(
			array(
				'id'        => $this->plugin_name,
				'screen_id' => 'woocommerce_page_cartlassi',
				'title'     => __( 'Cartlassi for WooCommerce', 'cartlassi-for-woocommerce' ),
			)
		);
		// if (
		// 	! method_exists( Menu::class, 'add_plugin_item' ) ||
		// 	! method_exists( Menu::class, 'add_plugin_category' ) ||
		// 	! Features::is_enabled( 'navigation' )
		// ) {
		// 	return;
		// }
		
		// Menu::add_plugin_item(
		// 	array(
		// 		'id'         => 'cartlassi-for-woocommerce',
		// 		'title'      => __( 'Cartlassi', 'cartlassi-for-woocommerce' ),
		// 		'capability' => 'manage_options',
		// 		'url'        => $this->plugin_name,
		// 	)
		// );


		// Menu::add_plugin_category(
		// 	array(
		// 		'id'     => 'cartlassi-settings',
		// 		'title'  => 'Cartlassi Settings',
		// 		'parent' => 'woocommerce',
		// 	)
		// );

		// Menu::add_plugin_category(
		// 	array(
		// 		'id'     => 'cartlassi-sales',
		// 		'title'  => 'Cartlassi Sales',
		// 		'parent' => 'woocommerce',
		// 	)
		// );

		// Menu::add_plugin_item(
		// 	array(
		// 		'id'         => 'cartlassi-sales-page',
		// 		'title'      => 'Cartlassi Sales',
		// 		'capability' => 'manage_woocommerce',
		// 		'url'        => 'my-extension-slug-cat-page',
		// 		'parent'     => 'cartlassi-sales',
		// 	)
		// );

		// Menu::add_plugin_category(
		// 	array(
		// 		'id'     => 'cartlassi-commissions',
		// 		'title'  => 'Cartlassi Commisions',
		// 		'parent' => 'woocommerce',
		// 	)
		// );

		// Menu::add_plugin_category(
		// 	array(
		// 		'id'     => 'cartlassi-stats',
		// 		'title'  => 'Cartlassi Stats',
		// 		'parent' => 'woocommerce',
		// 	)
		// );

		// Menu::add_plugin_item(
		// 	array(
		// 		'id'         => 'my-extension-cat-page',
		// 		'title'      => 'My Extension Cat Page',
		// 		'capability' => 'manage_woocommerce',
		// 		'url'        => 'my-extension-slug-cat-page',
		// 		'parent'     => 'my-extension-category',
		// 	)
		// );



		
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
										$this->sales_list->prepare_items();
										$this->sales_list->display(); 
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
										$this->commissions_list->prepare_items();
										$this->commissions_list->display(); 
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

	function cartlassi_stats_page_html() {
		?>
			<div class="wrap">

				<div id="poststuff">
					<div id="post-body" class="metabox-holder columns-2">
						<div id="post-body-content">
							<div class="meta-box-sortables ui-sortable">
								<div id="sales-chart"></div>
								<div id="promoter-chart"></div>
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
	
		$stripe_session_id = isset( $_GET['session_id'] ) ? $_GET['session_id'] : false;
		if ($stripe_session_id) {
			$args = array(
				'method' => 'POST',
				'body'			=> array(
					'session_id' => $stripe_session_id
				),
			);
			$response = $this->api->request("/shops/complete-stripe", $args );
		}

		if ( isset( $_GET['account-connected'] )) {
			// a stripe connect process has just ended, need to process it.
			$args = array(
				'method' => 'POST',
			);
			$response = $this->api->request("/shops/complete-stripe-connect", $args );
		}

		$welcome = isset( $_GET['welcome'] );
	
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

			<?php
				$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'appearance';
			?>
			<h2 class="nav-tab-wrapper">
				<a href="?page=<?php esc_html_e(Cartlassi_Constants::OPTIONS_PAGE); ?>&tab=appearance<?php if($welcome) { echo '&welcome=true'; } ?>" class="nav-tab <?php echo $active_tab == 'appearance' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Appearance', Cartlassi_Constants::TEXT_DOMAIN ); ?></a>
				<a href="?page=<?php esc_html_e(Cartlassi_Constants::OPTIONS_PAGE); ?>&tab=data<?php if($welcome) { echo '&welcome=true'; } ?>" class="nav-tab <?php echo $active_tab == 'data' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Data Sharing', Cartlassi_Constants::TEXT_DOMAIN ); ?></a>
				<a href="?page=<?php esc_html_e(Cartlassi_Constants::OPTIONS_PAGE); ?>&tab=api<?php if($welcome) { echo '&welcome=true'; } ?>" class="nav-tab <?php echo $active_tab == 'api' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'API Settings', Cartlassi_Constants::TEXT_DOMAIN ); ?></a>
				<a href="?page=<?php esc_html_e(Cartlassi_Constants::OPTIONS_PAGE); ?>&tab=billing<?php if($welcome) { echo '&welcome=true'; } ?>" class="nav-tab <?php echo $active_tab == 'billing' ? 'nav-tab-active' : ''; ?>"><?php esc_html_e( 'Payment & Payout', Cartlassi_Constants::TEXT_DOMAIN ); ?></a>
			</h2>

			<form action="options.php" method="post">
				<?php
				switch($active_tab) {
					case 'data':
						settings_fields( Cartlassi_Constants::DATA_SECTION_NAME );
						do_settings_sections( Cartlassi_Constants::DATA_SECTION_PAGE );
						break;
					case 'api':
						settings_fields( Cartlassi_Constants::API_SECTION_NAME );
						do_settings_sections( Cartlassi_Constants::API_SECTION_PAGE );
						break;
					case 'billing':
						settings_fields( Cartlassi_Constants::PAYMENTS_SECTION_NAME );
						do_settings_sections( Cartlassi_Constants::PAYMENTS_SECTION_PAGE );
						break;
					case 'appearance':
					default:
						settings_fields( Cartlassi_Constants::APPEARANCE_SECTION_NAME );
						do_settings_sections( Cartlassi_Constants::APPEARANCE_SECTION_PAGE );
						break;
	
				}
				// output security fields for the registered setting "cartlassi"
				// output setting sections and their fields
				// (sections are registered for "cartlassi", each field is registered to a specific section)
				// do_settings_sections( Cartlassi_Constants::OPTIONS_PAGE );
				// output save settings button
				submit_button( 'Save Settings' );
				?>
			</form>
		</div>
		<?php
	}

	function regenerate_api_key () {
		check_ajax_referer(Cartlassi_Constants::NONCE_ADMIN_NAME, 'nonce');

		$args = array(
			'method' => 'POST'
		);
		$data = $this->api->request("/shops/regenerate-api-key", $args);
		$options = get_option(Cartlassi_Constants::API_OPTIONS_NAME);
		$options[Cartlassi_Constants::API_KEY_FIELD_NAME] = $data->apiKey;
		update_option(Cartlassi_Constants::API_OPTIONS_NAME, $options);
		echo json_encode(array('apiKey' => $data->apiKey));
		wp_die();
	}

	function regenerate_api_secret () {
		check_ajax_referer(Cartlassi_Constants::NONCE_ADMIN_NAME, 'nonce');

		$args = array(
			'method' => 'POST'
		);
		$data = $this->api->request("/shops/regenerate-api-secret", $args);
		$options = get_option(Cartlassi_Constants::API_OPTIONS_NAME);
		$options[Cartlassi_Constants::API_SECRET_FIELD_NAME] = $data->apiSecret;
		update_option(Cartlassi_Constants::API_OPTIONS_NAME, $options);
		echo json_encode(array('apiSecret' => $data->apiSecret));
		wp_die();
	}

	function demo_hash () {
		check_ajax_referer(Cartlassi_Constants::NONCE_ADMIN_NAME, 'nonce');
		$hash = $this->utils->demo_cart_id(filter_var($_POST['include_email'], FILTER_VALIDATE_BOOLEAN), filter_var($_POST['extra_encryption'], FILTER_VALIDATE_BOOLEAN));
		echo wp_json_encode(array('hash' => $hash));
		wp_die();
	}

	public function add_action_links($links, $file){
		if ($file == 'cartlassi/cartlassi.php') {
			$my_links = array(
			 '<a href="' . admin_url( 'admin.php?page=cartlassi' ) . '">Settings</a>',
			);
			return array_merge( $my_links , $links );
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
		
		$this->sales_list = new Sales_List($this->config, $this->api);
	}

	public function screen_option_commissions() {

		$option = 'per_page';
		$args = [
		'label' => 'Commissions',
		'default' => 10,
		'option' => 'commissions_per_page'
		];
		
		add_screen_option( $option, $args );
		
		$this->commissions_list = new Commissions_List($this->config, $this->api);
	}

	public function init_chart_scripts() {
		$google_api = 'https://www.gstatic.com/charts/loader.js';
        wp_register_script( 'cartlassi_google_api', $google_api, array('jquery'), null, false );
		wp_register_script( 'cartlassi_chart_script', plugin_dir_url( __FILE__ ) . 'js/charts.js', array( 'jquery' ), $this->version, false );
	}

	public function enqueue_chart_scripts () {

		$data = $this->api->request('/shops/stats');
        wp_enqueue_script( 'cartlassi_google_api' );
		wp_enqueue_script( 'cartlassi_chart_script');
		wp_localize_script( 'cartlassi_chart_script', 'cartlassi_chart', 
            array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( Cartlassi_Constants::NONCE_ADMIN_NAME ),
				'data'	=> $data,
            ) );

    }

	public function activation_redirect($plugin) {
		if( $plugin == Cartlassi_Constants::PLUGIN_FILE ) {
			exit( wp_redirect( admin_url( "admin.php?page=".Cartlassi_Constants::OPTIONS_PAGE."&welcome=true" ) ) );
		}
	}

	protected function admin_notice_no_payout_method() {
		?>
			<div data-dismissible="disable-done-notice-forever" class="notice notice-warning is-dismissible">
				<p><Strong>Cartlassi:<strong> We're able to use your shop's abandoned carts data to generate extra revenue for you. However, until you add a payout method, we can't really pay you, can we? Please <a href="<?php echo admin_url( 'admin.php?page=cartlassi&tab=billing') ?>">add a payout method</a></p>
			</div>
		<?php    
	}

	protected function admin_notice_no_payment_method() {
		?>
			<div data-dismissible="disable-done-notice-forever" class="notice notice-warning is-dismissible">
				<p><Strong>Cartlassi:<strong> We're not showing the Cartlassi widget on your shop and you're missing on sales. Please <a href="<?php echo admin_url( 'admin.php?page=cartlassi&tab=billing') ?>">add a payment method</a> to start showing your widget.</p>
			</div>
		<?php    
	}

	protected function admin_notice_no_appearance_setting() {
		?>
			<div data-dismissible="disable-done-notice-forever" class="notice notice-warning is-dismissible">
				<p><Strong>Cartlassi:<strong> We're not showing the Cartlassi widget on your shop and you're missing on sales. Please <a href="<?php echo admin_url( 'admin.php?page=cartlassi&tab=appearance') ?>">configure the widget appearance</a> to start showing your widget.</p>
			</div>
		<?php    
	}

	protected function admin_notice_welcome() {
		$payment_method = $this->utils->get_payment_method(false);
		$payout_method = $this->utils->get_payout_method(false);
		$is_collecting_data = true;
		$is_payment_method = ($payment_method->brand && $payment_method->last4) || isset($_GET['session_id']);
		$is_payout_method = ($payout_method->stripeConnectAccountId && $payout_method->stripeConnectConnected) || isset($_GET['account-connected']);
		$is_appearance_set = !!get_option( Cartlassi_Constants::APPEARANCE_OPTIONS_NAME );

		$is_displaying_widget = $is_appearance_set && $is_payment_method;
		?>
			<div data-dismissible="disable-done-notice-forever" class="notice notice-success is-dismissible">
				<h2><?php _e('Welcome to Cartlassi.')?></h2>
				<h3><?php _e('Please take a few minutes to complete the setup. Hopefully by the end of it you\'ll have all boxes checked.'); ?></h3> 
				<ul>
					<li><input disabled type="checkbox" id="is-collecting-data" <?php checked($is_collecting_data, true)?>><?php _e('Your shop is collecting data and monetizing your abandoned carts')?></li>
					<li><input disabled type="checkbox" id="is-displaying-widget" <?php checked($is_displaying_widget, true)?>><?php _e('Cartlassi widget is displaying on your shop driving more sales')?></li>
					<?php if (!$is_payment_method) { ?>
						<ul class="cartlassi-admin-checkbox-reasons">
							<li>Paymet method not set.</li>
						</ul>
					<?php } ?>
					<?php if (!$is_appearance_set) { ?>
						<ul class="cartlassi-admin-checkbox-reasons">
							<li>Appearance settings not set.</li>
						</ul>
					<?php } ?>
					<li><input disabled type="checkbox" id="is-payout" <?php checked($is_payout_method, true)?>><?php _e('You\'ve established a payout method so we can pay you your earnings with us.')?></li>
					<?php if (!$is_payout_method) { ?>
						<ul id="" class="cartlassi-admin-checkbox-reasons">
							<li>Payout method not set</li>
						</ul>
					<?php } ?>
				</ul>
			</div>
		<?php    
	}

	public function display_admin_notices() {
		// in welcome mode we're not showing other notices, only the welcome notice
		$welcome = isset( $_GET['welcome'] );
		if ($welcome) {
			$this->admin_notice_welcome();
			return;
		}

		// if no stripe connect, notice that we can't pay
		if (!get_option(Cartlassi_Constants::APPEARANCE_OPTIONS_NAME)) {
			$this->admin_notice_no_appearance_setting();
		}

		$payment_method = $this->utils->get_payment_method();
		if ( !($payment_method->brand && $payment_method->last4) ) {
			$this->admin_notice_no_payment_method();
		}

		$payout_method = $this->utils->get_payout_method();
		if ( !($payout_method->stripeConnectAccountId && $payout_method->stripeConnectConnected) ) {
			$this->admin_notice_no_payout_method();
		}
	}

	public function cancel_payment_method() {
		check_ajax_referer(Cartlassi_Constants::NONCE_ADMIN_NAME, 'nonce');

		$args = array(
			'method' => 'DELETE'
		);
		$data = $this->api->request("/shops/payment-method", $args);
		echo json_encode(array());
		wp_die();

	}

	public function cancel_payout_method() {
		check_ajax_referer(Cartlassi_Constants::NONCE_ADMIN_NAME, 'nonce');

		$args = array(
			'method' => 'DELETE'
		);
		$data = $this->api->request("/shops/payout-method", $args);
		echo json_encode(array());
		wp_die();

	}
}
