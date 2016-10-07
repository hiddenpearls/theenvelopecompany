<?php
/**
 * WooCommerce Checkout Field Editor Pro - Settings
 *
 * @author      ThemeHiGH
 * @category    Admin
 */
 
if(!defined('ABSPATH')){ exit; }

if(!class_exists('WCFE_Settings')) :

class WCFE_Settings {
	protected static $_instance = null;	
	public $admin_instance = null;
	public $frontend_instance = null;
	
	/**
	 * Constructor
	 */
	public function __construct() {		
		add_action('admin_menu', array($this, 'admin_menu'));
		add_filter('woocommerce_screen_ids', array($this, 'add_screen_id'));
		
		$this->init();
	}
	
	public static function instance() {
		if(is_null(self::$_instance)){
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
	public function init() {		
		if(!is_admin() || (defined( 'DOING_AJAX' ) && DOING_AJAX)){
			$this->frontend_instance = new WCFE_Checkout_Field_Editor_Frontend();
		}else if(is_admin()){
			$admin_utils = WCFE_Checkout_Fields_Admin_Utils::instance();
			$admin_utils->init();
				
			$this->admin_instance = WCFE_Checkout_Field_Editor_Settings::instance();
		}		
	}
			
	function admin_menu() {
		$this->screen_id = add_submenu_page('woocommerce', __('WooCommerce Checkout Field Editor Pro', 'woocommerce-checkout-field-editor-pro'), 
		__('Checkout Form', 'woocommerce-checkout-field-editor-pro'), 'manage_woocommerce', 'th_checkout_field_editor_pro', array($this, 'output_settings'));

		//add_action('admin_print_scripts-'. $this->screen_id, array($this, 'enqueue_admin_scripts'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
	}

	function add_screen_id($ids){
		$ids[] = 'woocommerce_page_th_checkout_field_editor_pro';
		$ids[] = strtolower(__('WooCommerce', 'woocommerce-checkout-field-editor-pro')) .'_page_th_checkout_field_editor_pro';

		return $ids;
	}
	
	function output_settings() {
		$tab  = isset( $_GET['tab'] ) ? esc_attr( $_GET['tab'] ) : 'fields';
		if($tab === 'fields'){			
			$admin_instance = WCFE_Checkout_Field_Editor_Settings::instance();	
			$admin_instance->output_page();			
		}else if($tab === 'advanced_settings'){			
			$advanced_settings = WCFE_Checkout_Field_Editor_Advanced_Settings::instance();	
			$advanced_settings->output_page();			
		}
	}

	function enqueue_admin_scripts($hook) {
		if('woocommerce_page_th_checkout_field_editor_pro' != $hook){
        	return;
    	}
		
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_style('thwcfe-admin-style', plugins_url('/assets/css/thwcfe-checkout-field-editor-admin.css', dirname(__FILE__)));
		
		//wp_register_script('thwcfe-select2', TH_WCFE_ASSETS_URL.'js/select2.min.js', array('jquery'));
		wp_enqueue_script('thwcfe-admin-script', plugins_url('/assets/js/thwcfe-checkout-field-editor-admin.js', dirname(__FILE__)), 
		array('jquery', 'jquery-ui-dialog', 'jquery-ui-sortable', 'woocommerce_admin', 'select2', 'wp-color-picker', 'jquery-tiptip'));
	
        $wcfe_var = array(
            'admin_url' => admin_url(),
            'ajaxurl'   => admin_url( 'admin-ajax.php' ),
        );
		wp_localize_script('thwcfe-admin-script', 'wcfe_var', $wcfe_var);
	}	
}

endif;