<?php
/**
 * Checkout Field - Date Picker
 *
 * @author      ThemeHiGH
 * @category    Admin
 */

if(!defined('ABSPATH')){ exit; }

if(!class_exists('WCFE_Checkout_Field_DatePicker')):

class WCFE_Checkout_Field_DatePicker extends WCFE_Checkout_Field{
	public $pattern = array(			
			'/d/', '/j/', '/l/', '/z/', '/S/', //day (day of the month, 3 letter name of the day, full name of the day, day of the year, )			
			'/F/', '/M/', '/n/', '/m/', //month (Month name full, Month name short, numeric month no leading zeros, numeric month leading zeros)			
			'/Y/', '/y/' //year (full numeric year, numeric year: 2 digit)
		);
		
	public $replace = array(
			'dd','d','DD','o','',
			'MM','M','m','mm',
			'yy','y'
		);
		
	public $default_date = '';
	public $date_format = '';
	public $min_date = '';
	public $max_date = '';
	public $year_range = '';
	public $number_of_months = '';
	public $disabled_days = array();
	public $disabled_dates = array();
	
	public function __construct() {
		$this->type = 'datepicker';
	}	
	
	public function prepare_field($name, $field){
		if(!empty($field) && is_array($field)){
			parent::prepare_field($name, $field);
			
			$this->set_property('default_date', isset($field['default_date']) ? $field['default_date'] : '');
			$this->set_property('date_format', isset($field['date_format']) ? $field['date_format'] : '');
			$this->set_property('min_date', isset($field['min_date']) ? $field['min_date'] : '');
			$this->set_property('max_date', isset($field['max_date']) ? $field['max_date'] : '');
			$this->set_property('year_range', isset($field['year_range']) ? $field['year_range'] : '');
			$this->set_property('number_months', isset($field['number_months']) ? $field['number_months'] : '');
			$this->set_property('disabled_days', isset($field['disabled_days']) ? $field['disabled_days'] : array());
			$this->set_property('disabled_dates', isset($field['disabled_dates']) ? $field['disabled_dates'] : '');
		}
	}
		
	/*public function get_html(){
		$html = '';
		if($this->is_enabled()){
			$html .= '<tr class="'. $this->get_cssclass_str() .'">';
			$html .= '<td class="label '.$this->get_title_position().'">'.$this->get_title_html().'</td">';
			$html .= '<td class="value '.$this->get_title_position().'">';
			$html .= '<input type="text" id="'.$this->get_name().'" name="'.$this->get_name().'" placeholder="'.$this->get_placeholder().'" value="'.$this->get_value().'" ';
			$html .= 'class="thwepo-date-picker" data-date-format="'.$this->get_date_format().'" data-default-date="'. $this->get_value() .'" ';
			$html .= 'data-min-date="'. $this->get_min_date() .'" data-max-date="'. $this->get_max_date() .'" ';
			$html .= 'data-year-range="'. $this->get_year_range() .'" data-number-months="'. $this->get_number_of_months() .'"/>';
			$html .= '</td>';
			$html .= '</tr>';
		}	
		return $html;
	}
	
	public function render_field(){
		echo $this->get_html();
	}*/	
	
	public function get_jquery_date_format($woo_date_format){				
		$woo_date_format = !empty($woo_date_format) ? $woo_date_format : wc_date_format();
		return preg_replace($this->pattern, $this->replace, $woo_date_format);	
	}
	
   /**********************************
	**** Setters & Getters - START ****
	***********************************/
		
	/* Getters */
	/*public function get_default_date(){
		return $this->default_date;
	}
	public function get_date_format(){
		return empty($this->date_format) ? $this->get_jquery_date_format(wc_date_format()) : $this->date_format;
	}
	public function get_min_date(){
		return $this->min_date;
	}
	public function get_max_date(){
		return $this->max_date;
	}
	public function get_year_range(){
		return empty($this->year_range) ? '-100:+1' : $this->year_range;
	}
	public function get_number_of_months(){
		return empty($this->number_of_months) ? 1 : $this->number_of_months;
	}*/
}

endif;