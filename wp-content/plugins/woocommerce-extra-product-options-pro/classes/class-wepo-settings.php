<?php
/**
 * Woo Extra Product Options Settings
 *
 * @author      ThemeHiGH
 * @category    Admin
 */
 
if(!defined('ABSPATH')){ exit; }

if(!class_exists('WEPO_Settings')) :

class WEPO_Settings {
	protected static $_instance = null;	
	public $admin_instance = null;
	public $frontend_instance = null;
	
	/**
	 * Constructor
	 */
	public function __construct() {		
		$required_classes = apply_filters('th_wepo_require_class', array(
			'common' => array(
				'classes/fe/class-wepo-extra-product-options-utils.php',
				'classes/fe/rules/class-wepo-condition.php',
				'classes/fe/rules/class-wepo-condition-set.php',
				'classes/fe/rules/class-wepo-rule.php',
				'classes/fe/rules/class-wepo-rule-set.php',
				'classes/fe/class-wepo-section.php',
				'classes/fe/fields/class-wepo-field.php',
				'classes/fe/fields/class-wepo-field-inputtext.php',
				'classes/fe/fields/class-wepo-field-password.php',
				'classes/fe/fields/class-wepo-field-textarea.php',				
				'classes/fe/fields/class-wepo-field-select.php',
				'classes/fe/fields/class-wepo-field-multiselect.php',
				'classes/fe/fields/class-wepo-field-radio.php',
				'classes/fe/fields/class-wepo-field-checkbox.php',
				'classes/fe/fields/class-wepo-field-datepicker.php',
				'classes/fe/fields/class-wepo-field-timepicker.php',
				'classes/fe/fields/class-wepo-field-heading.php',
				'classes/fe/fields/class-wepo-field-label.php',
				'classes/fe/fields/class-wepo-field-factory.php',
			),
			'admin' => array(
				'classes/class-wepo-settings-page.php',
				'classes/fe/class-wepo-extra-product-options-settings.php',
				//'classes/fe/class-wepo-extra-product-options-export-handler.php',
			),
			'frontend' => array(
				'classes/fe/class-wepo-extra-product-options-frontend.php',
			),
		));
		
		//$this->include_required( $required_classes );
	
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
	
	protected function include_required( $required_classes ) {
		foreach($required_classes as $section => $classes ) {
			foreach( $classes as $class ){
				if('common' == $section  || ('frontend' == $section && !is_admin() || ( defined('DOING_AJAX') && DOING_AJAX) ) 
					|| ('admin' == $section && is_admin()) && file_exists( TH_WEPO_PATH . $class )){
					require_once( TH_WEPO_PATH . $class );
				}
			}
		}
	}
	
	public function init() {		
		if(!is_admin() || (defined( 'DOING_AJAX' ) && DOING_AJAX)){
			$this->frontend_instance = new WEPO_Extra_Product_Options_Frontend();
		}else if(is_admin()){
			$this->admin_instance = WEPO_Extra_Product_Options_Settings::instance();
		}		
	}
			
	function admin_menu() {
		$this->screen_id = add_submenu_page('edit.php?post_type=product', __('WooCommerce Extra Product Option', 'woocommerce-extra-product-options-pro'), 
		__('Extra Product Option', 'woocommerce-extra-product-options-pro'), 'manage_woocommerce', 'th_extra_product_options_pro', array($this, 'output_settings'));

		add_action('admin_print_scripts-'. $this->screen_id, array($this, 'enqueue_admin_scripts'));
	}
	
	function add_screen_id($ids){
		$ids[] = 'woocommerce_page_th_extra_product_options_pro';
		$ids[] = strtolower(__('WooCommerce', 'woocommerce-extra-product-options-pro')) .'_page_th_extra_product_options_pro';

		return $ids;
	}
	
	function output_settings() {
		$tab  = isset( $_GET['tab'] ) ? esc_attr( $_GET['tab'] ) : 'options';
		if($tab === 'options'){			
			$admin_instance = WEPO_Extra_Product_Options_Settings::instance();	
			$admin_instance->output_page();			
		}
	}
	
	function enqueue_admin_scripts() {
		//wp_enqueue_style (array('woocommerce_admin_styles', 'jquery-ui-style', 'wp-color-picker'));
		wp_enqueue_style ('jquery-ui-style', '//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css?ver=1.11.4');
		wp_enqueue_style ('woocommerce_admin_styles', TH_WEPO_WOO_ASSETS_URL.'css/admin.css');
		wp_enqueue_style ('wp-color-picker');
		wp_enqueue_style ('thwepo-admin-style', plugins_url('/assets/css/thwepo-extra-product-options-admin.css', dirname(__FILE__)));
		wp_enqueue_style ('thwepo-colorpicker-style', plugins_url('/assets/colorpicker/spectrum.css', dirname(__FILE__)));
		
		//wp_register_script('thwepo-colorpicker', plugins_url('/assets/colorpicker/spectrum.js', dirname(__FILE__)), array(), false, true);		
		wp_enqueue_script('thwepo-admin-script', plugins_url('/assets/js/thwepo-extra-product-options-admin.js', dirname(__FILE__)), 
		array('jquery', 'jquery-ui-dialog', 'jquery-ui-sortable', 'jquery-tiptip', 'woocommerce_admin', 'wc-enhanced-select', 'select2', 'wp-color-picker'), false, true);	
		//wp_enqueue_script('thwepo-colorpicker-script', plugins_url('/assets/colorpicker/spectrum.js', dirname(__FILE__)), array(), false, true);
        
        $wepo_var = array(
            'admin_url' => admin_url(),
            'ajaxurl'   => admin_url( 'admin-ajax.php' ),
        );
		wp_localize_script('thwepo-admin-script', 'wepo_var', $wepo_var);
	}	
}

endif;