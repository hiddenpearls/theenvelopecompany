<?php
/**
 * Product Field Propertoes
 *
 * @author      ThemeHiGH
 * @category    Admin
 */

if(!defined('ABSPATH')){ exit; }

if(!class_exists('WEPO_Autoloader')):

class WEPO_Autoloader {
	private $include_path = '';
	
	private $class_path = array(
				'WEPO_Extra_Product_Options_Utils' => 'classes/fe/class-wepo-extra-product-options-utils.php',
				
				'WEPO_Condition' => 'classes/fe/rules/class-wepo-condition.php',
				'WEPO_Condition_Set' => 'classes/fe/rules/class-wepo-condition-set.php',
				'WEPO_Condition_Rule' => 'classes/fe/rules/class-wepo-rule.php',
				'WEPO_Condition_Rule_Set' => 'classes/fe/rules/class-wepo-rule-set.php',
				
				'WEPO_Product_Page_Section' => 'classes/fe/class-wepo-section.php',
				'WEPO_Product_Field' => 'classes/fe/fields/class-wepo-field.php',
				'WEPO_Product_Field_InputText' => 'classes/fe/fields/class-wepo-field-inputtext.php',
				'WEPO_Product_Field_Password' => 'classes/fe/fields/class-wepo-field-password.php',
				'WEPO_Product_Field_Textarea' => 'classes/fe/fields/class-wepo-field-textarea.php',				
				'WEPO_Product_Field_Select' => 'classes/fe/fields/class-wepo-field-select.php',
				'WEPO_Product_Field_Multiselect' => 'classes/fe/fields/class-wepo-field-multiselect.php',
				'WEPO_Product_Field_Radio' => 'classes/fe/fields/class-wepo-field-radio.php',
				'WEPO_Product_Field_Checkbox' => 'classes/fe/fields/class-wepo-field-checkbox.php',
				'WEPO_Product_Field_DatePicker' => 'classes/fe/fields/class-wepo-field-datepicker.php',
				'WEPO_Product_Field_TimePicker' => 'classes/fe/fields/class-wepo-field-timepicker.php',
				'WEPO_Product_Field_Heading' => 'classes/fe/fields/class-wepo-field-heading.php',
				'WEPO_Product_Field_Label' => 'classes/fe/fields/class-wepo-field-label.php',
				'WEPO_Product_Field_Factory' => 'classes/fe/fields/class-wepo-field-factory.php',
			
				'WEPO_Settings' 	 => 'classes/class-wepo-settings.php',
				'WEPO_Settings_Page' => 'classes/class-wepo-settings-page.php',
				'WEPO_Extra_Product_Options_Settings' => 'classes/fe/class-wepo-extra-product-options-settings.php',
			
				'WEPO_Extra_Product_Options_Frontend' => 'classes/fe/class-wepo-extra-product-options-frontend.php',
		);

	public function __construct() {
		if ( function_exists( "__autoload" ) ) {
			spl_autoload_register( "__autoload" );
		}

		spl_autoload_register( array( $this, 'autoload' ) );

		$this->include_path = untrailingslashit(TH_WEPO_PATH).'/classes/';
	}

	/** Include a class file. */
	private function load_file( $path ) {
		if ( $path && is_readable( $path ) ) {
			require_once( $path );
			return true;
		}
		return false;
	}
	
	public function autoload( $class ) {
		/*print_r($class);
		echo '<br/>';*/
		
		if(isset($this->class_path[$class])){
			$file = $this->class_path[$class];
			$this->load_file( TH_WEPO_PATH.$file );
		}
	}
	
	/** Class name to file name. */
	/*private function get_file_name_from_class( $class ) {
		return 'class-' . str_replace( '_', '-', $class ) . '.php';
	}
	
	public function autoload( $class ) {
		$class = strtolower( $class );
		$file  = $this->get_file_name_from_class( $class );
		$path  = '';

		if ( strpos( $class, 'wepo_ajax' ) === 0 ) {
			$path = $this->include_path . 'fe/ajax/';
		} elseif ( strpos( $class, 'wc_gateway_' ) === 0 ) {
			$path = $this->include_path . 'fe/ajax/';
		}

		if ( empty( $path ) || ( ! $this->load_file( $path . $file ) && strpos( $class, 'wepo_' ) === 0 ) ) {
			$this->load_file( $this->include_path . $file );
		}
	}*/
}

endif;

new WEPO_Autoloader();
