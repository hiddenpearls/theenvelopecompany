<?php
/**
 * Plugin Name: WooCommerce Checkout Field Editor Pro
 * Description: Design woocommerce checkout form in your own way, customize checkout fields(Add, Edit, Delete and re arrange fields).
 * Author:      ThemeHiGH
 * Version:     2.6.2
 * Author URI:  http://www.themehigh.com
 * Plugin URI:  http://www.themehigh.com/product/woocommerce-checkout-field-editor-pro/
 * Text Domain: woocommerce-checkout-field-editor-pro
 * Domain Path: /languages
 */
 
if(!defined('ABSPATH')){ exit; }

if(in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {		
	if(!class_exists('WCFE_Checkout_Field_Editor')){	
		class WCFE_Checkout_Field_Editor {	
			public function __construct(){
				add_action('init', array($this, 'init'));
			}
			
			public function init() {
				//global $supress_field_modification;
				//$supress_field_modification = false;			
				
				$this->load_plugin_textdomain();
				
				!defined('TH_WCFE_PATH') && define('TH_WCFE_PATH', plugin_dir_path( __FILE__ ));
				!defined('TH_WCFE_URL') && define('TH_WCFE_URL', plugins_url( '/', __FILE__ ));
				!defined('TH_WCFE_ASSETS_URL') && define('TH_WCFE_ASSETS_URL', TH_WCFE_URL . 'assets/');
				!defined('TH_WEPE_WOO_ASSETS_URL') && define('TH_WEPE_WOO_ASSETS_URL', WC()->plugin_url() . '/assets/');
				
				$this->include_required_classes();
				$this->init_hooks();
				
				require_once( TH_WCFE_PATH . 'classes/class-wcfe-settings.php' );
				WCFE_Settings::instance();	
			}
			
			public function load_plugin_textdomain(){	
				$domain = 'woocommerce-checkout-field-editor-pro';
				$locale = apply_filters('plugin_locale', get_locale(), $domain);
				
				load_textdomain($domain, WP_LANG_DIR.'/woocommerce-checkout-field-editor-pro/'.$domain.'-'.$locale.'.mo');
				load_plugin_textdomain($domain, FALSE, dirname(plugin_basename( __FILE__ )) . '/languages/');
			}
			
			public function include_required_classes(){
				$required_classes = apply_filters('th_wcfe_require_class', array(
					'common' => array(
						'classes/class-wcfe-settings.php',
						'classes/fe/class-wcfe-checkout-field-editor-utils.php',
						'classes/fe/rules/class-wcfe-condition.php',
						'classes/fe/rules/class-wcfe-condition-set.php',
						'classes/fe/rules/class-wcfe-rule.php',
						'classes/fe/rules/class-wcfe-rule-set.php',
						'classes/fe/class-wcfe-section.php',
						'classes/fe/fields/class-wcfe-field.php',
						'classes/fe/fields/class-wcfe-field-inputtext.php',
						'classes/fe/fields/class-wcfe-field-hidden.php',
						'classes/fe/fields/class-wcfe-field-password.php',
						'classes/fe/fields/class-wcfe-field-textarea.php',				
						'classes/fe/fields/class-wcfe-field-select.php',
						'classes/fe/fields/class-wcfe-field-multiselect.php',
						'classes/fe/fields/class-wcfe-field-radio.php',
						'classes/fe/fields/class-wcfe-field-checkbox.php',
						'classes/fe/fields/class-wcfe-field-datepicker.php',
						'classes/fe/fields/class-wcfe-field-timepicker.php',
						'classes/fe/fields/class-wcfe-field-heading.php',
						'classes/fe/fields/class-wcfe-field-label.php',
						'classes/fe/fields/class-wcfe-field-country.php',
						'classes/fe/fields/class-wcfe-field-email.php',
						'classes/fe/fields/class-wcfe-field-state.php',
						'classes/fe/fields/class-wcfe-field-tel.php',
						'classes/fe/fields/class-wcfe-field-factory.php',
						'classes/includes/class-wcfe-install.php',
					),
					'admin' => array(
						'classes/fe/class-wcfe-checkout-field-editor-utils-admin.php',
						'classes/class-wcfe-settings-page.php',
						'classes/fe/class-wcfe-checkout-field-editor-settings.php',
						'classes/fe/class-wcfe-checkout-field-editor-settings-advanced.php',
					),
					'frontend' => array(
						'classes/fe/class-wcfe-checkout-field-editor-frontend.php',
					),
				));
				
				$this->include_required( $required_classes );
			}
			
			protected function include_required( $required_classes ) {
				foreach($required_classes as $section => $classes ) {
					foreach( $classes as $class ){
						if('common' == $section  || ('frontend' == $section && !is_admin() || ( defined('DOING_AJAX') && DOING_AJAX) ) 
							|| ('admin' == $section && is_admin()) && file_exists( TH_WCFE_PATH . $class )){
							require_once( TH_WCFE_PATH . $class );
						}
					}
				}
				
				//WooCommerce Zapier Support
				if(class_exists('WC_Zapier') && class_exists('WC_Zapier_Trigger') && $this->get_global_settings('enable_wc_zapier_support') === 'yes'){
					require_once( TH_WCFE_PATH . 'classes/includes/class-wcfe-wc-zapier-handler.php' );
					new WCFE_WC_Zapier_Handler();
				}
			}
			
			private function init_hooks() {
				WCFE_Install::install();
				register_activation_hook( __FILE__, array( 'WCFE_Install', 'install' ) );
				//register_deactivation_hook( __FILE__, array( 'WCFE_Install', 'uninstall' ));
			}
			
			/*public function plugin_activated(){
				$admin_utils = WCFE_Legacy_Support::instance();
				$admin_utils->init();
			}*/
			
			public function get_global_settings($key){
				$settings = get_option('thwcfe_advanced_settings');
				if(is_array($settings) && isset($settings[$key])){
					return $settings[$key];
				}
				return '';
			}
			
		}	
	}
	
	$thwcfepro = new WCFE_Checkout_Field_Editor();
}