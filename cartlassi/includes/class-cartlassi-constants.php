<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Cartlassi
 * @subpackage Cartlassi/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Cartlassi
 * @subpackage Cartlassi/includes
 * @author     Your Name <email@example.com>
 */
class Cartlassi_Constants {

	const PLUGIN_NAME				= 'cartlassi'; 
	const OPTIONS_NAME 				= 'cartlassi_options';
	
	const APPEARANCE_OPTIONS_NAME 	= 'cartlassi_options_appearance';
	const DATA_OPTIONS_NAME 		= 'cartlassi_options_data';
	const API_OPTIONS_NAME 			= 'cartlassi_options_api';
	const PAYMENTS_OPTIONS_NAME 	= 'cartlassi_options_payments';
	
	const OPTIONS_PAGE 				= 'cartlassi';
	const OPTION_GROUP				= 'cartlassi';
	const TOP_MENU_SLUG				= 'cartlassi';
	const TEXT_DOMAIN 				= 'cartlassi';
	
	const API_KEY_FIELD_NAME 		= 'cartlassi_field_api_key';
	const API_SECRET_FIELD_NAME		= 'cartlassi_field_api_secret';
	const PAYMENT_METHOD_FIELD_NAME	= 'cartlassi_field_payment_method';
	const PAYOUT_METHOD_FIELD_NAME	= 'cartlassi_field_payout_method';
	const BEFORE_SIDEBAR_FIELD_NAME	= 'cartlassi_field_before_sidebar';
	const EXTRA_ENCRYPTION_FIELD_NAME = 'cartlassi_field_extra_encryption';

	const SIDEBAR_ID 				= 'sidebar-cartlassi';
	const ORDER_ITEM_CART_ITEM_KEY 	= '_cartlassi_cart_item_key';
	const CURRENT_MAP_NAME			= 'cartlassi_current_map';
	const NONCE_ADMIN_NAME			= 'cartlassi-admin-nonce';
	const NONCE_PUBLIC_NAME			= 'cartlassi-public-nonce';
	
	const DEFAULT_SECTION_NAME 		= 'cartlassi_section_default';
	const APPEARANCE_SECTION_NAME 	= 'cartlassi_section_appearance';
	const DATA_SECTION_NAME 		= 'cartlassi_section_data';
	const API_SECTION_NAME 			= 'cartlassi_section_api';
	const PAYMENTS_SECTION_NAME 		= 'cartlassi_section_payments';
	
	const APPEARANCE_SECTION_PAGE 	= 'cartlassi_page_appearance';
	const DATA_SECTION_PAGE 		= 'cartlassi_page_data';
	const API_SECTION_PAGE 			= 'cartlassi_page_api';
	const PAYMENTS_SECTION_PAGE 		= 'cartlassi_page_payments';
	
	const OPTIONS_ROW_CLASS_NAME 	= 'cartlassi_row';	
	const BEFORE_SIDEBAR_SHOP_FIELD_NAME 				= 'cartlassi_field_before_sidebar_shop';
	const BEFORE_SIDEBAR_CATEGORY_FIELD_NAME 			= 'cartlassi_field_before_sidebar_category';
	const BEFORE_SIDEBAR_PRODUCT_TAG_FIELD_NAME 		= 'cartlassi_field_before_sidebar_product_tag';
	const BEFORE_SIDEBAR_PRODUCT_FIELD_NAME 			= 'cartlassi_field_before_sidebar_product';
	const BEFORE_SIDEBAR_OTHER_PAGES_FIELD_NAME		 	= 'cartlassi_field_before_sidebar_other_pages';
	const BEFORE_SIDEBAR_OTHER_PAGES_PAGES_FIELD_NAME 	= 'cartlassi_field_before_sidebar_other_pages_pages';
	const BEFORE_SIDEBAR_OTHER_PAGES_STRATEGY_FIELD_NAME = 'cartlassi_field_before_sidebar_other_pages_strategy';
	const INCLUDE_EMAIL_IN_CART_ID_FIELD_NAME			= 'cartlassi_field_include_email_in_cart_id';
	const INCLUDE_IP_IN_CART_ID_FIELD_NAME			= 'cartlassi_field_include_ip_in_cart_id';
	const OTHER_PAGES_OPTION_SHOW_EXCEPT		= 'show_except';
	const OTHER_PAGES_OPTION_DONT_SHOW_BUT		= 'dont_show_but';
	const COMMISSION = 0.15;
	const FEE = 0.015;
	const PLUGIN_FILE = 'cartlassi/cartlassi.php';
	const PAYMENT_METHOD_TRANSIENT = 'cartlassi_payment_method';
	const PAYOUT_METHOD_TRANSIENT = 'cartlassi_payout_method';
	const WIDGET_CACHE_NAME = 'cartlassi_widget_cache';
	

}
