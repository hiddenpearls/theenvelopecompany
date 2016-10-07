<?php
/**
 * Plugin Name: WooCommerce Extra Product Options Pro
 * Description: Design woocommerce Product form in your own way, customize Product fields(Add, Edit, Delete and re arrange fields).
 * Author:      ThemeHiGH
 * Version:     2.1.2
 * Author URI:  http://www.themehigh.com
 * Plugin URI:  http://www.themehigh.com
 * Text Domain: woocommerce-extra-product-options-pro
 * Domain Path: /languages
 */
 
if(!defined('ABSPATH')){ exit; }

if(in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {		
	if(!class_exists('WEPO_Extra_Product_Options')){	
		class WEPO_Extra_Product_Options {	
			public function __construct(){
				add_action('init', array($this, 'init'));
			}
			
			public function init() {
				global $supress_field_modification;
				$supress_field_modification = false;			
				
				$this->load_plugin_textdomain();
				
				!defined('TH_WEPO_PATH') && define('TH_WEPO_PATH', plugin_dir_path( __FILE__ ));
				!defined('TH_WEPO_URL') && define('TH_WEPO_URL', plugins_url( '/', __FILE__ ));
				!defined('TH_WEPO_ASSETS_URL') && define('TH_WEPO_ASSETS_URL', TH_WEPO_URL . 'assets/');
				!defined('TH_WEPO_WOO_ASSETS_URL') && define('TH_WEPO_WOO_ASSETS_URL', WC()->plugin_url() . '/assets/');
				
				include_once( TH_WEPO_PATH . 'classes/class-wepo-autoloader.php' );
				require_once( TH_WEPO_PATH . 'classes/class-wepo-settings.php' );
				WEPO_Settings::instance();						
			}
			
			public function load_plugin_textdomain(){	
				$domain = 'woocommerce-extra-product-options-pro';
				$locale = apply_filters('plugin_locale', get_locale(), $domain);
				
				load_textdomain($domain, WP_LANG_DIR.'/woocommerce-extra-product-options-pro/'.$domain.'-'.$locale.'.mo');
				load_plugin_textdomain($domain, FALSE, dirname(plugin_basename( __FILE__ )) . '/languages/');
			}
		}	
	}
	
	$thwepopro = new WEPO_Extra_Product_Options();
}