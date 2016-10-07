<?php
/**
 * Woo Extra Product Options Editor
 *
 * @author      ThemeHiGH
 * @category    Admin
 */

if(!defined('ABSPATH')){ exit; }

if(!class_exists('WEPO_Extra_Product_Options_Settings')):

class WEPO_Extra_Product_Options_Settings extends WEPO_Settings_Page {
	protected static $_instance = null;
	private $field_factory = NULL;
	
	private $cell_props = array();
	private $left_cell_props = array();
	private $right_cell_props = array();
	
	private $section_fields = array();
	private $option_fields = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->page_id    = 'options';
		$this->section_id = 'default';
		$this->sections = $this->get_sections();
		
		$this->field_factory = new WEPO_Product_Field_Factory();
		$this->init_constants();
		
		add_filter('woocommerce_attribute_label', array($this, 'woo_attribute_label'), 10, 2 );
		
		add_filter('thwepo_load_products', array($this, 'load_products'));
		add_filter('thwepo_load_products_cat', array($this, 'load_products_cat'));
		
		//add_action('woocommerce_admin_order_data_after_order_details', array($this, 'woo_admin_order_data_after_order_details'), 20, 1);
		//add_action('woocommerce_admin_order_data_after_billing_address', array($this, 'woo_admin_order_data_after_billing_address'), 20, 1);
		//add_action('woocommerce_admin_order_data_after_shipping_address', array($this, 'woo_admin_order_data_after_shipping_address'), 20, 1);
	}
	
	public static function instance() {
		if(is_null(self::$_instance)){
			self::$_instance = new self();
		}
		return self::$_instance;
	}	
	
	public function init_constants(){
		$this->left_cell_props = array( 'label_cell_width' => '13%', 'input_cell_width' => '34%', 'input_width' => '250px' );
		$this->right_cell_props = array( 'label_cell_width' => '13%', 'input_cell_width' => '34%', 'input_width' => '250px' );
		$this->cell_props = array( 'input_width' => '250px' );
		
		$this->section_fields = $this->get_section_form_fields();
		$this->option_fields = $this->get_option_form_fields();
	}
	
	public function woo_attribute_label( $label, $key ) {
		if(!empty($label)){
			$name_title_map = $this->get_options_name_title_map();
			if($name_title_map){
				if(array_key_exists($label, $name_title_map)) {
					$label = $name_title_map[$label];
				}
			}
		}
		return $label;
	}
		
	public function load_products(){
		$args = array( 'post_type' => 'product', 'order' => 'ASC', 'posts_per_page' => -1 );
		$products = get_posts( $args );
		$productsList = array();
		
		if(count($products) > 0){
			foreach($products as $product){				
				$productsList[] = array("id" => $product->ID, "title" => $product->post_title);
			}
		}		
		return $productsList;
	}
	
	public function load_products_cat(){
		$product_cat = array();
		$pcat_terms = get_terms('product_cat', 'orderby=count&hide_empty=0');
		
		foreach($pcat_terms as $pterm){
			$product_cat[] = array("id" => $pterm->slug, "title" => $pterm->name);
		}		
		return $product_cat;
	}	
	
	public function get_field_types(){
		return array('inputtext' => 'Text', 'password' => 'Password', 'textarea' => 'Textarea', 'select' => 'Select', 'multiselect' => 'Multiselect', 
			'radio' => 'Radio', 'checkbox' => 'Checkbox', 'datepicker' => 'Date Picker', 'timepicker' => 'Time Picker', 'heading' => 'Heading', 'label' => 'Label');
	}
	
	public function get_html_text_tags(){
		return array( 'h1' => 'H1', 'h2' => 'H2', 'h3' => 'H3', 'h4' => 'H4', 'h5' => 'H5', 'h6' => 'H6', 'p'  => 'p', 'div' => 'div', 'span' => 'span', 'label' => 'label');
	}
	
	public function get_available_positions(){
		return array(
			'woo_before_add_to_cart_button'			=> 'Before Add To Cart Button',
			'woo_after_add_to_cart_button'			=> 'After Add To Cart Button',
			
			/*'woo_single_product_before_title' 		=> 'Before Title',
			'woo_single_product_after_title' 		=> 'After Title',
			'woo_single_product_before_rating' 		=> 'Before Rating',
			'woo_single_product_after_rating' 		=> 'After Rating',
			'woo_single_product_before_price' 		=> 'Before Price',
			'woo_single_product_after_price' 		=> 'After Price',
			'woo_single_product_before_excerpt' 	=> 'Before Excerpt',
			'woo_single_product_after_excerpt' 		=> 'After Excerpt',
			'woo_single_product_before_add_to_cart' => 'Before Add To Cart',
			'woo_single_product_after_add_to_cart'  => 'After Add To Cart',			
			'woo_single_product_before_meta' 		=> 'Before Meta',
			'woo_single_product_after_meta' 		=> 'After Meta',
			'woo_single_product_before_sharing' 	=> 'Before Sharing',
			'woo_single_product_after_sharing' 		=> 'After Sharing',*/
		);
	}
	
	/*
	 * Override
	 */
	public function output_sections() {
		$result = false;
		if(isset($_POST['s_action']) && $_POST['s_action'] == 'new')
			$result = $this->create_section();	
			
		if(isset($_POST['s_action']) && $_POST['s_action'] == 'edit')
			$result = $this->edit_section();	
			
		if(isset($_POST['s_action']) && $_POST['s_action'] == 'remove')
			$result = $this->remove_section();
			
		$current_section = $this->get_current_section();
		$sections = $this->get_sections();
					
		if(empty($sections)){
			return;
		}
		
		$array_keys = array_keys( $sections );
				
		echo '<ul class="thwepo-sections">';
		foreach( $sections as $name => $section ){
			$url = $this->get_admin_url($this->page_id, sanitize_title($name));	
			
			echo '<li><a href="'. $url .'" class="'. ($current_section == $name ? 'current' : '') .'">'. $this->__wepo($section->get_property('title')) .'</a></li>';
			if($section->is_custom_section()){
				?>
                <li>
                	<a href="javascript:void(0)" onclick='thwepoOpenEditSectionForm(<?php echo json_encode($section); ?>)' class="edit_section" 
                		title="<?php $this->_ewepo('Edit Section') ?>">
                		<img src="<?php echo TH_WEPO_ASSETS_URL; ?>/css/edit.png" />
                	</a>
                </li>
				<li>
                    <form method="post" action="">
                        <input type="hidden" name="s_action" value="remove" />
                        <input type="hidden" name="i_name" value="<?php echo $name; ?>" />
                        <a href="javascript:void(0)" onclick="thwepoRemoveSection(this)" class="delete_section" title="<?php $this->_ewepo('Delete Section'); ?>">
                        	<img src="<?php echo TH_WEPO_ASSETS_URL; ?>/css/delete.png" />
                        </a>
					</form>
                </li>
                <?php
			}
			echo '<li>';
			echo(end( $array_keys ) == $name ? '' : ' | ');
			echo '</li>';
		}
		echo '<li><a href="javascript:void(0)" onclick="thwepoOpenNewSectionForm()" class="add_link">+ '. $this->__wepo( 'Add new section' ) .'</a></li>';
		echo '</ul>';		
		
		if($result){
			echo $result;
		}
	}
	
   /*---------------------------------------
	*------ SECTION FUNCTIONS - START ------
	*---------------------------------------*/
	public function get_section_form_fields(){
		$positions = $this->get_available_positions();
		$html_text_tags = $this->get_html_text_tags();
		
		/*$box_types = array(
			'' 				 => 'Normal (clear)',
			'box' 			 => 'Box',
			'collapse' 		 => 'Expand and Collapse (start opened)',
			'collapseclosed' => 'Expand and Collapse (start closed)',
			'accordion' 	 => 'Accordion',
		);*/
		//$title_positions = array( '' => 'Above field', 'left' => 'Left of the field', 'right' => 'Right of the field', 'disable' => 'Disable' );
		
		return array(
			'name' 		 => array('name'=>'name', 'label'=>'Name/ID', 'type'=>'text', 'required'=>1),
			'position' 	 => array('name'=>'position', 'label'=>'Display Position', 'type'=>'select', 'options'=>$positions, 'required'=>1),
			//'box_type' 	 => array('name'=>'box_type', 'label'=>'Box Type', 'type'=>'select', 'options'=>$box_types),
			'cssclass' 	 => array('name'=>'cssclass', 'label'=>'CSS Class', 'type'=>'text'),
			'show_title' => array('name'=>'show_title', 'label'=>'Show section title in product page.', 'type'=>'checkbox', 'value'=>'yes', 'checked'=>1),
			
			'title' 		   => array('name'=>'title', 'label'=>'Title', 'type'=>'text'),
			//'title_position' => array('name'=>'title_position', 'label'=>'Title Position', 'type'=>'select', 'options'=>$title_positions),
			'title_type' 	   => array('name'=>'title_type', 'label'=>'Title Type', 'type'=>'select', 'value'=>'h3', 'options'=>$html_text_tags),
			'title_color' 	   => array('name'=>'title_color', 'label'=>'Title Color', 'type'=>'colorpicker'),
			'title_class' 	   => array('name'=>'title_class', 'label'=>'Title Class', 'type'=>'text'),
			
			'subtitle' 			  => array('name'=>'subtitle', 'label'=>'Subtitle', 'type'=>'text'),
			//'subtitle_position' => array('name'=>'subtitle_position', 'label'=>'Subtitle Position', 'type'=>'select', 'options'=>$title_positions),
			'subtitle_type' 	  => array('name'=>'subtitle_type', 'label'=>'Subtitle Type', 'type'=>'select', 'value'=>'h3', 'options'=>$html_text_tags),
			'subtitle_color' 	  => array('name'=>'subtitle_color', 'label'=>'Subtitle Color', 'type'=>'colorpicker'),
			'subtitle_class' 	  => array('name'=>'subtitle_class', 'label'=>'Subtitle Class', 'type'=>'text'),
		);
	}
					
	public function create_section(){
		$section = new WEPO_Product_Page_Section();
		
		foreach($this->get_section_form_fields() as $fname => $field){
			$fvalue = !empty($_POST['i_'.$fname]) ? $_POST['i_'.$fname] : '';
			$section->set_property($field['name'], $fvalue);
		}
	
		$name     = $section->get_property('name');
		$title 	  = $section->get_property('title');
		$position = $section->get_property('position');
		
		$name = is_numeric($name) ? "s_".$name : $name;
		
		if(!$name || !$title || !$position){
			return;
		}
		
		$show_title = $section->get_property('show_title');
		$show_title = ( !empty($show_title) && $show_title === 'yes' ) ? 1 : 0;
		
		$section->set_property('id', $name);
		$section->set_property('name', $name);
		$section->set_property('show_title', $show_title);
		$section->prepare_properties();
		
		$result = $this->update_section($section);
						
		if ($result == true) {			
			return '<div class="updated"><p>'. $this->__wepo('New section added successfully.') .'</p></div>';
		} else {
			return '<div class="error"><p> '. $this->__wepo('New section not added due to an error.') .'</p></div>';
		}	
	}
	
	public function edit_section(){
		$name  	 = !empty($_POST['i_name']) ? $_POST['i_name'] : '';
		$section = $this->get_section($name);
		$fields  = $this->get_section_form_fields();
		unset($fields['i_name']);
		
		foreach($fields as $fname => $field){
			$fvalue = !empty($_POST['i_'.$fname]) ? $_POST['i_'.$fname] : '';
			$section->set_property($field['name'], $fvalue);
		}
	
		$title 	  = $section->get_property('title');
		$position = $section->get_property('position');
		
		if(!$name || !$title || !$position){
			return;
		}
		
		$show_title = $section->get_property('show_title');
		$show_title = ( !empty($show_title) && $show_title === 'yes' ) ? 1 : 0;

		$section->set_property('show_title', $show_title);
		$section->prepare_properties();
				
		$old_position = !empty($_POST['i_position_old']) ? $_POST['i_position_old'] : '';
		if($old_position && $position && ($old_position != $position)){			
			$this->remove_section_from_hook($position_old, $name);
		}
		
		$result = $this->update_section($section);
						
		if ($result == true) {			
			return '<div class="updated"><p>'. $this->__wepo('Section details updated successfully.') .'</p></div>';
		} else {
			return '<div class="error"><p> '. $this->__wepo('Section details not updated due to an error.') .'</p></div>';
		}	
	}
			
	public function remove_section(){
		$section_name = !empty($_POST['i_name']) ? $_POST['i_name'] : false;		
		if($section_name){	
			$result = $this->delete_section($section_name);			
										
			if ($result == true) {
				return '<div class="updated"><p>'. $this->__wepo('Section removed successfully.') .'</p></div>';
			} else {
				return '<div class="error"><p> '. $this->__wepo('Section not removed due to an error.') .'</p></div>';
			}
		}
	}
	
	private function output_add_section_form_pp(){		
		$fields = $this->get_section_form_fields();
		?>
        <div id="thwepo_new_section_form_pp" title="Create New Section" class="thwepo_popup_wrapper">
          	<form method="post" id="thwepo_new_section_form" action="">
          		<input type="hidden" name="s_action" value="new" />            
                <table width="100%" border="0">
                    <?php
                    $this->output_section_info_form($fields);
                    $this->output_h_separator();
                    $this->output_title_form($fields, true);
                    $this->output_h_separator();
                    $this->output_rule_form($fields, true);
                    ?>    
                </table>
          	</form>
        </div>
        <?php
	}
	
	private function output_edit_section_form_pp(){	
		$fields = $this->get_section_form_fields();	
		?>
        <div id="thwepo_edit_section_form_pp" title="Edit Section" class="thwepo_popup_wrapper">
          	<form method="post" id="thwepo_edit_section_form" action="">
          		<input type="hidden" name="s_action" value="edit" />
            	<input type="hidden" name="s_name" value="" />
            	<input type="hidden" name="i_position_old" value="" />                
          		<table width="100%" border="0">
                	<?php
                    $this->output_section_info_form($fields);
                    $this->output_h_separator();
                    $this->output_title_form($fields, true);
                    $this->output_h_separator();
                    $this->output_rule_form($fields, true);
                    ?> 
            	</table>
          	</form>
        </div>
        <?php
	}
	
	private function output_section_info_form($fields){
		$available_positions = $this->get_available_positions();
		
		$args_L = array( 'label_cell_width' => '13%', 'input_cell_width' => '34%', 'input_width' => '250px' );
		$args_R = array( 'label_cell_width' => '13%', 'input_cell_width' => '34%', 'input_width' => '250px' );
		$args   = array( 'input_width' => '250px' );
		
		?>
        <tr>                
            <td colspan="6" class="err_msgs"></td>
        </tr>            	
        <tr>                
            <?php
			$this->render_form_field_element($fields['name'], $args_L);
			$this->render_form_field_element($fields['position'], $args_R);
			?>
        </tr>  
        <tr>                
            <?php 
			$this->render_form_field_element($fields['cssclass'], $args);
			$this->render_form_field_blank();
			?>
        </tr> 
        <?php
	}
	
	private function output_title_form($fields, $show_subtitle = false){
		$html_text_tags = $this->get_html_text_tags();
		
		$args_L = array( 'label_cell_width' => '13%', 'input_cell_width' => '34%', 'input_width' => '250px' );
		$args_R = array( 'label_cell_width' => '13%', 'input_cell_width' => '34%', 'input_width' => '250px' );
		$args   = array( 'input_width' => '250px' );
		?>
        <tr>  
        	<?php $this->render_form_field_element($fields['show_title'], array( 'input_cell_colspan' => '5' )); ?>
        </tr>
        <?php $this->output_h_separator(false); ?>
        <tr>                
        	<?php
			$this->render_form_field_element($fields['title'], $args);
			$this->render_form_field_element($fields['title_type'], $args);
			//$this->render_form_field_element($fields['title_position'], $args);
			?>
        </tr>
        <tr>                
        	<?php
			$this->render_form_field_element($fields['title_color']);
			$this->render_form_field_element($fields['title_class'], $args);
			?>
        </tr>
        
        <?php
		if($show_subtitle){
			$this->output_h_separator(false);
		?>
        <tr> 
        	<?php
			$this->render_form_field_element($fields['subtitle'], $args);
			$this->render_form_field_element($fields['subtitle_type'], $args);
			//$this->render_form_field_element($fields['subtitle_position'], $args);
			?>
        </tr>  
        <tr>  
        	<?php
			$this->render_form_field_element($fields['subtitle_color']);
			$this->render_form_field_element($fields['subtitle_class'], $args);
			?>
        </tr>
        <?php
		}
	}
	
	private function output_rule_form(){
	
	}
	
	private function output_h_separator($show_line = true){
		$style = '';
		if($show_line){
			$style .= 'margin: 5px 0; border-bottom: 1px dashed #ccc';
		}
		echo '<tr><td colspan="6" style="'.$style.'">&nbsp;</td></tr>';
	}
   /*---------------------------------------
	*------ SECTION FUNCTIONS - START ------
	*---------------------------------------*/
	
   /*---------------------------------------
	*----- PRODUCT FIELDS FORMS - START ----
	*---------------------------------------*/
	public function get_option_form_fields(){
		$html_text_tags = $this->get_html_text_tags();
		$field_types = $this->get_field_types();
		
		$price_types = array(
			'normal' => 'Normal',
			'percentage' => 'Percentage',
			'dynamic' => 'Dynamic',
			'dynamic-excl-base-price' => 'Dynamic - Exclude base price ',
		);
		
		$validators = array(
			'email' => 'Email',
			'number' => 'Number',
		);
		
		$title_positions = array(
			'left' => 'Left of the field',
			'above' => 'Above field',
		);
		
		$time_formats = array(
			'h:i A' => '12-hour format',
			'H:i' => '24-hour format',
		);
		
		$hint_default_date = "Specify a date in the current dateFormat, or number of days from today (e.g. +7) or a string of values and periods ('y' for years, 'm' for months, 'w' for weeks, 'd' for days, e.g. '+1m +7d'), or leave empty for today.";
		$hint_date_format = "The format for parsed and displayed dates.";
		$hint_min_date = "The minimum selectable date. Specify a date in the current dateFormat, or number of days from today (e.g. -7) or a string of values and periods ('y' for years, 'm' for months, 'w' for weeks, 'd' for days, e.g. '-1m -7d'), or leave empty for no minimum limit.";
		$hint_max_date = "The maximum selectable date. Specify a date in the current dateFormat, or number of days from today (e.g. +7) or a string of values and periods ('y' for years, 'm' for months, 'w' for weeks, 'd' for days, e.g. '+1m +7d'), or leave empty for no maximum limit.";
		$hint_year_range = "The range of years displayed in the year drop-down: either relative to today's year ('-nn:+nn' e.g. -5:+3), relative to the currently selected year ('c-nn:c+nn' e.g. c-10:c+10), absolute ('nnnn:nnnn' e.g. 2002:2012), or combinations of these formats ('nnnn:+nn' e.g. 2002:+3). Note that this option only affects what appears in the drop-down, to restrict which dates may be selected use the minDate and/or maxDate options.";
		$hint_number_of_months = "The number of months to show at once";
		
		return array(
			'name' 		  => array('name'=>'name', 'label'=>'Name', 'type'=>'text', 'required'=>1),
			'type' 		  => array('name'=>'type', 'label'=>'Field Type', 'type'=>'select', 'options'=>$field_types, 'required'=>1),
			'value' 	  => array('name'=>'value', 'label'=>'Default Value', 'type'=>'text'),
			'placeholder' => array('name'=>'placeholder', 'label'=>'Placeholder', 'type'=>'text'),
			'validate' 	  => array('name'=>'validate', 'label'=>'Validation', 'type'=>'multiselect', 'options'=>$validators, 'placeholder'=>'Select validations'),
			'cssclass'    => array('name'=>'cssclass', 'label'=>'CSS Class', 'type'=>'text'),
			
			'price'        => array('name'=>'price', 'label'=>'Price', 'type'=>'text', 'placeholder'=>'Price'),
			'price_unit'   => array('name'=>'price_unit', 'label'=>'Unit', 'type'=>'text', 'placeholder'=>'Unit'),
			'price_type'   => array('name'=>'price_type', 'label'=>'Price Type', 'type'=>'select', 'options'=>$price_types),
			//'price_prefix' => array('name'=>'price_prefix', 'label'=>'Price Prefix', 'type'=>'text'),
			//'price_suffix' => array('name'=>'price_suffix', 'label'=>'Price Suffix', 'type'=>'text'),
			
			'required' => array('name'=>'required', 'label'=>'Required', 'type'=>'checkbox', 'value'=>'yes', 'checked'=>1),
			'enabled'  => array('name'=>'enabled', 'label'=>'Enabled', 'type'=>'checkbox', 'value'=>'yes', 'checked'=>1),
			
			'title'          => array('name'=>'title', 'label'=>'Title', 'type'=>'text'),
			'title_position' => array('name'=>'title_position', 'label'=>'Title Position', 'type'=>'select', 'options'=>$title_positions),
			'title_type'     => array('name'=>'title_type', 'label'=>'Title Type', 'type'=>'select', 'value'=>'label', 'options'=>$html_text_tags),
			'title_color'    => array('name'=>'title_color', 'label'=>'Title Color', 'type'=>'colorpicker'),
			'title_class'    => array('name'=>'title_class', 'label'=>'Title Class', 'type'=>'text', 'placeholder'=>'Seperate classes with comma'),
			
			'subtitle'       => array('name'=>'subtitle', 'label'=>'Subtitle', 'type'=>'text'),
			'subtitle_type'  => array('name'=>'subtitle_type', 'label'=>'Subtitle Type', 'type'=>'select', 'value'=>'label', 'options'=>$html_text_tags),
			'subtitle_color' => array('name'=>'subtitle_color', 'label'=>'Subtitle Color', 'type'=>'colorpicker'),
			'subtitle_class' => array('name'=>'subtitle_class', 'label'=>'Subtitle Class', 'type'=>'text', 'placeholder'=>'Seperate classes with comma'),
						
			'default_date' => array('name'=>'default_date', 'label'=>'Default Date','type'=>'text','placeholder'=>"Leave empty for today's date",'hint_text'=>$hint_default_date),
			'date_format'  => array('name'=>'date_format', 'label'=>'Date Format', 'type'=>'text', 'value'=>'dd/mm/yy', 'hint_text'=>$hint_date_format),
			'min_date'     => array('name'=>'min_date', 'label'=>'Min. Date', 'type'=>'text', 'placeholder'=>'The minimum selectable date', 'hint_text'=>$hint_min_date),
			'max_date'     => array('name'=>'max_date', 'label'=>'Max. Date', 'type'=>'text', 'placeholder'=>'The maximum selectable date', 'hint_text'=>$hint_max_date),
			'year_range'   => array('name'=>'year_range', 'label'=>'Year Range', 'type'=>'text', 'value'=>'-100:+1', 'hint_text'=>$hint_year_range),
			'number_of_months' => array('name'=>'number_of_months', 'label'=>'Number Of Months', 'type'=>'text', 'value'=>'1', 'hint_text'=>$hint_number_of_months),
			
			'min_time'    => array('name'=>'min_time', 'label'=>'Min. Time', 'type'=>'text', 'value'=>'12:00am', 'sub_label'=>'ex: 12:30am'),
			'max_time'    => array('name'=>'max_time', 'label'=>'Max. Time', 'type'=>'text', 'value'=>'11:30pm', 'sub_label'=>'ex: 11:30pm'),
			'time_step'   => array('name'=>'time_step', 'label'=>'Time Step', 'type'=>'text', 'value'=>'30', 'sub_label'=>'In minutes, ex: 30'),
			'time_format' => array('name'=>'time_format', 'label'=>'Time Format', 'type'=>'select', 'value'=>'h:i A', 'options'=>$time_formats),
		);
	}
	
	public function get_option_display_fields(){
		return array(
			'name'  => array('name'=>'name', 'type'=>'text'),
			'type'  => array('name'=>'type', 'type'=>'select'),
			'title' => array('name'=>'title', 'type'=>'text'),
			'placeholder' => array('name'=>'placeholder', 'type'=>'text'),
			'validate' => array('name'=>'validate', 'type'=>'text'),
			'required' => array('name'=>'required', 'type'=>'checkbox', 'status'=>1),
			'enabled'  => array('name'=>'enabled', 'type'=>'checkbox', 'status'=>1),
		);
	}
	
	private function output_add_field_form_pp(){
		?>
        <div id="thwepo_new_field_form_pp" title="New Product Field" class="thwepo_popup_wrapper">
          <?php $this->output_popup_form_fields('new'); ?>
        </div>
        <?php
	}
		
	private function output_edit_field_form_pp(){		
		?>
        <div id="thwepo_edit_field_form_pp" title="Edit Product Field" class="thwepo_popup_wrapper">
          <?php $this->output_popup_form_fields('edit'); ?>
        </div>
        <?php
	}
	 
	/*---------------------------------------
	 *----- PRODUCT FIELDS FORMS - END ------
	 *---------------------------------------*/
	
	public function output_page(){
		/*$memory_limit = ini_get('memory_limit');
		ini_set('memory_limit', '256M');*/
		
		if(isset($_POST['reset_fields']))
			echo $this->reset_to_default();	
	
		$this->output_tabs();
		$this->output_sections();
		$this->output_content();
		
		//ini_set('memory_limit', $memory_limit);
	}
			
	private function output_fields_table_heading(){
		?>
		<th class="sort"></th>
		<th class="check-column" style="padding-left:0px !important;"><input type="checkbox" style="margin-left:7px;" onclick="thwepoSelectAllProductFields(this)"/></th>
		<th class="name"><?php $this->_ewepo('Name'); ?></th>
		<th class="id"><?php $this->_ewepo('Type'); ?></th>
		<th><?php $this->_ewepo('Label'); ?></th>
		<th><?php $this->_ewepo('Placeholder'); ?></th>
		<th><?php $this->_ewepo('Validation Rules'); ?></th>
        <th class="status"><?php $this->_ewepo('Required'); ?></th>
		<th class="status"><?php $this->_ewepo('Enabled'); ?></th>	
        <th><?php $this->_ewepo('Edit'); ?></th>	
        <?php
	}
	
	private function output_actions_row($section){
		?>
        <th colspan="5">
            <button type="button" class="button button-primary" onclick="thwepoOpenNewFieldForm('<?php echo $section->get_property('name'); ?>')">
				<?php $this->_ewepo('+ Add field'); ?>
            </button>
            <button type="button" class="button" onclick="thwepoRemoveSelectedFields()"><?php  $this->_ewepo('Remove'); ?></button>
            <button type="button" class="button" onclick="thwepoEnableSelectedFields()"><?php  $this->_ewepo('Enable'); ?></button>
            <button type="button" class="button" onclick="thwepoDisableSelectedFields()"><?php $this->_ewepo('Disable'); ?></button>
        </th>
        <th colspan="5">
        	<input type="submit" name="save_fields" class="button-primary" value="<?php $this->_ewepo('Save changes') ?>" style="float:right" />
            <input type="submit" name="reset_fields" class="button" value="<?php $this->_ewepo('Reset to default fields') ?>" style="float:right; margin-right: 5px;" />
        </th>  
    	<?php 
	}
			    
    private function output_content(){
    	$section_name = $this->get_current_section();
		$section = $this->get_section($section_name);
				
		if(!$this->is_valid_section($section)){
			$section = new WEPO_Product_Page_Section();
			$section->set_default_section();
		}
				
		if(isset($_POST['save_fields']))
			echo $this->save_options( $section );
			
		$section = $this->get_section($section_name);
				
		if(!$this->is_valid_section($section)){
			$section = new WEPO_Product_Page_Section();
			$section->set_default_section();
		}
		
		$option_form_fields = $this->get_option_form_fields();
							
		?>            
        <div class="wrap woocommerce"><div class="icon32 icon32-attributes" id="icon-woocommerce"><br /></div>                
		    <form method="post" id="thwepo_product_fields_form" action="">
            <table id="thwepo_product_fields" class="wc_gateways widefat" cellspacing="0">
                <thead>
                    <tr><?php $this->output_actions_row($section); ?></tr>
                    <tr><?php $this->output_fields_table_heading(); ?></tr>						
                </thead>
                <tfoot>
                    <tr><?php $this->output_fields_table_heading(); ?></tr>
                    <tr><?php $this->output_actions_row($section); ?></tr>
                </tfoot>
                <tbody class="ui-sortable">
                <?php 
                $i=0;												
                foreach( $section->fields as $field ) :	
                    $name = $field->get_property('name');
					$is_enabled = $field->get_property('enabled') ? 1 : 0;                
                ?>
                    <tr class="row_<?php echo $i; echo($is_enabled == 1 ? '' : ' thwepo-disabled') ?>">
                        <td width="1%" class="sort ui-sortable-handle">
                            <?php
							foreach( $option_form_fields as $pname => $property ){
								$pvalue = $field->get_property($pname);
								$pvalue = esc_attr($pvalue);
								
								if($property['type'] == 'checkbox'){
									$pvalue = $pvalue ? 1 : 0;
								}
								?>
								<input type="hidden" name="f_<?php echo $pname; ?>[<?php echo $i; ?>]" class="f_<?php echo $pname; ?>" value="<?php echo $pvalue; ?>" />
                                <?php
							}
							
							$price_field = $field->get_property('price_field') ? 1 : 0;
							$options_json = htmlspecialchars($field->get_property('options_json'));
							
							$rules_action = $field->get_property('rules_action');
							$rules_action_ajax = $field->get_property('rules_action_ajax');
							
							$rules_json = htmlspecialchars($field->get_property('conditional_rules_json'));
							$rules_json_ajax = htmlspecialchars($field->get_property('conditional_rules_ajax_json'));
							?>
                            
                            <input type="hidden" name="f_order[<?php echo $i; ?>]" class="f_order" value="<?php echo $i; ?>" />
                            <input type="hidden" name="f_deleted[<?php echo $i; ?>]" class="f_deleted" value="0" />
                            
                            <input type="hidden" name="f_price_field[<?php echo $i; ?>]" class="f_price_field" value="<?php echo $price_field; ?>" />
                            <input type="hidden" name="f_options[<?php echo $i; ?>]" class="f_options" value="<?php echo $options_json; ?>" /> 
                            
                            <input type="hidden" name="f_rules_action[<?php echo $i; ?>]" class="f_rules_action" value="<?php echo $rules_action; ?>" />
                            <input type="hidden" name="f_rules_action_ajax[<?php echo $i; ?>]" class="f_rules_action_ajax" value="<?php echo $rules_action_ajax; ?>" />                            
                            <input type="hidden" name="f_rules[<?php echo $i; ?>]" class="f_rules" value="<?php echo $rules_json; ?>" />
                            <input type="hidden" name="f_rules_ajax[<?php echo $i; ?>]" class="f_rules_ajax" value="<?php echo $rules_json_ajax; ?>" />
                        </td>
                        <td class="td_select"><input type="checkbox" name="select_field"/></td>
                        
                        <?php
						$option_display_fields = $this->get_option_display_fields();
						
						foreach( $option_display_fields as $pname => $property ){
							$pvalue = $field->get_property($pname);
							$pvalue = esc_attr($pvalue);
							
							if($property['type'] == 'checkbox'){
								$pvalue = $pvalue ? 1 : 0;
							}
							
							if(isset($property['status']) && $property['status'] == 1){
								$statusHtml = $pvalue == 1 ? '<span class="status-enabled tips">'.$this->__wepo('Yes').'</span>' : '-';
								?>
                                <td class="td_<?php echo $pname; ?> status"><?php echo $statusHtml; ?></td>
                                <?php
							}else{
								?>
                                <td class="td_<?php echo $pname; ?>"><?php echo $pvalue; ?></td>
                                <?php
							}
						}
						?>

                        <td class="td_edit" align="center">
                            <button type="button" class="f_edit_btn" <?php echo($is_enabled == 1 ? '' : 'disabled') ?> 
                            onclick="thwepoOpenEditFieldForm(this, <?php echo $i; ?>)"><?php $this->_ewepo('Edit'); ?></button>
                        </td>
                    </tr>						
                <?php $i++; endforeach; ?>
                </tbody>
            </table> 
            </form>
            <?php
            $this->output_add_field_form_pp();
			$this->output_edit_field_form_pp();
			$this->output_add_section_form_pp();
			$this->output_edit_section_form_pp();
			?>
    	</div>
    <?php
    }
	
	private function save_options($section) {	
		$f_names = !empty( $_POST['f_name'] ) ? $_POST['f_name'] : array();	
		if(empty($f_names)){
			echo '<div class="error"><p> '. $this->__wepo('Your changes were not saved due to no fields found.') .'</p></div>';
			return;
		}
		
		$option_form_fields = $this->get_option_form_fields();
		
		/*----- Recieve POST data START ----*/
		$field_values = array();
		foreach( $option_form_fields as $pname => $property ){
			$values = !empty( $_POST['f_'.$pname] ) ? $_POST['f_'.$pname] : array();
			$field_values[$pname] = $values;
		}
		
		$f_options = !empty( $_POST['f_options'] ) ? $_POST['f_options'] : array();
		$f_order   = !empty( $_POST['f_order'] ) ? $_POST['f_order'] : array();	
		$f_deleted = !empty( $_POST['f_deleted'] ) ? $_POST['f_deleted'] : array();
		
		$f_rules_action = !empty( $_POST['f_rules_action'] ) ? $_POST['f_rules_action'] : array();
		$f_rules_action_ajax = !empty( $_POST['f_rules_action_ajax'] ) ? $_POST['f_rules_action_ajax'] : array();
		
		$f_rules = !empty( $_POST['f_rules'] ) ? $_POST['f_rules'] : array();
		$f_rules_ajax = !empty( $_POST['f_rules_ajax'] ) ? $_POST['f_rules_ajax'] : array();
		/*----- Recieve POST data END ----*/
		
		$section->clear_fields();
		
		$max = max( array_map( 'absint', array_keys( $f_names ) ) );
		for($i = 0; $i <= $max; $i++) {
			if(isset($f_deleted[$i]) && $f_deleted[$i] == 1){
				continue;
			}
			
			$types = isset($field_values['type']) ? $field_values['type'] : array();
			$type  = isset($types[$i]) ? trim(stripslashes($types[$i])) : '';
			
			$field = $this->field_factory->create_field($type); 
			
			foreach( $option_form_fields as $pname => $property ){
				$pvalues = isset($field_values[$pname]) ? $field_values[$pname] : array();
				$pvalue = '';
				if($type == 'checkbox'){
					$pvalue = isset($pvalues[$i]) ? $pvalues[$i] : 0;
				}else{
					$pvalue  = isset($pvalues[$i]) ? trim(stripslashes($pvalues[$i])) : '';
				}
				
				$field->set_property($pname, $pvalue);
			}
			
			if($type === 'select' || $type === 'multiselect' || $type === 'radio'){
				if(isset($f_options[$i])){
					$field->set_property('options_json', isset($f_options[$i]) ? trim(stripslashes($f_options[$i])) : '');
					$field->set_property('options', $this->prepare_options_array($field->get_property('options_json')));
				}
			}
			
			$field->set_property('order', isset($f_order[$i]) ? trim(stripslashes($f_order[$i])) : 0);
			
			$field->set_property('rules_action', isset($f_rules_action[$i]) ? trim(stripslashes($f_rules_action[$i])) : '');
			$field->set_property('rules_action_ajax', isset($f_rules_action_ajax[$i]) ? trim(stripslashes($f_rules_action_ajax[$i])) : '');
			
			$field->set_property('conditional_rules_json', isset($f_rules[$i]) ? trim(stripslashes($f_rules[$i])) : '');
			$field->set_property('conditional_rules', $this->prepare_conditional_rules($field->get_property('conditional_rules_json')));
			
			$field->set_property('conditional_rules_ajax_json', isset($f_rules_ajax[$i]) ? trim(stripslashes($f_rules_ajax[$i])) : '');
			$field->set_property('conditional_rules_ajax', $this->prepare_conditional_rules($field->get_property('conditional_rules_ajax_json')));
			
			$field->prepare_properties();
			
			$section->add_field($field);
		}
		$section->sort_fields();
		
		$result1 = $this->update_section($section);
		$result2 = $this->update_options_name_title_map();
		
		if ($result1 == true) {
			echo '<div class="updated"><p>'. $this->__wepo('Your changes were saved.') .'</p></div>';
		} else {
			echo '<div class="error"><p>'. $this->__wepo('Your changes were not saved due to an error (or you made none!).') .'</p></div>';
		}
	}
	
	public function prepare_options_array($options_json){
		$options_json = urldecode($options_json);
		$options_arr = json_decode($options_json, true);
		$options = array();
		
		if($options_arr){
			foreach($options_arr as $option){
				$options[$option['key']] = $option;
			}
		}
		return $options;
	}
		
	public function prepare_conditional_rules($conditional_rules){
		$condition_rule_sets = array();	
		if(!empty($conditional_rules)){
			$conditional_rules = urldecode($conditional_rules);
			$rule_sets = json_decode($conditional_rules, true);
				
			if(is_array($rule_sets)){
				foreach($rule_sets as $rule_set){
					if(is_array($rule_set)){
						$condition_rule_set_obj = new WEPO_Condition_Rule_Set();
						$condition_rule_set_obj->set_logic('and');
												
						foreach($rule_set as $condition_sets){
							if(is_array($condition_sets)){
								$condition_rule_obj = new WEPO_Condition_Rule();
								$condition_rule_obj->set_logic('or');
														
								foreach($condition_sets as $condition_set){
									if(is_array($condition_set)){
										$condition_set_obj = new WEPO_Condition_Set();
										$condition_set_obj->set_logic('and');
													
										foreach($condition_set as $condition){
											if(is_array($condition)){
												$condition_obj = new WEPO_Condition();
												/*$condition_obj->set_subject(isset($condition['subject']) ? $condition['subject'] : '');
												$condition_obj->set_comparison(isset($condition['comparison']) ? $condition['comparison'] : '');
												$condition_obj->set_value(isset($condition['cvalue']) ? $condition['cvalue'] : '');*/
												
												$condition_obj->set_operand_type(isset($condition['operand_type']) ? $condition['operand_type'] : '');
												$condition_obj->set_operand(isset($condition['operand']) ? $condition['operand'] : '');
												$condition_obj->set_operator(isset($condition['operator']) ? $condition['operator'] : '');
												$condition_obj->set_value(isset($condition['value']) ? $condition['value'] : '');
												
												$condition_set_obj->add_condition($condition_obj);
											}
										}										
										$condition_rule_obj->add_condition_set($condition_set_obj);	
									}								
								}
								$condition_rule_set_obj->add_condition_rule($condition_rule_obj);
							}
						}
						$condition_rule_sets[] = $condition_rule_set_obj;
					}
				}	
			}
		}
		return $condition_rule_sets;
	}
			
	public function reset_to_default() {
		delete_option('thwepo_custom_sections');
		delete_option('thwepo_section_hook_map');
		delete_option('thwepo_options_name_title_map');
		
		echo '<div class="updated"><p>'. $this->__wepo('Product fields successfully reset') .'</p></div>';
	}
	
   /*******************************************
	*-------- SECTION FUNCTIONS - START -------
	*******************************************/	 
	public function save_section_hook_map($section_hook_map){
		$result = update_option('thwepo_section_hook_map', $section_hook_map);		
		return $result;
	}
	 
	public function update_section_hook_map($section){
		$section_name = $section->name;
		$hook_name 	  = $section->position;
				
	 	if(isset($hook_name) && isset($section_name) && !empty($hook_name) && !empty($section_name)){	
			$hook_map = $this->get_section_hook_map();
			if(isset($hook_map[$hook_name])){
				$hooked_sections = $hook_map[$hook_name];
				if(!in_array($section_name, $hooked_sections)){
					$hooked_sections[] = $section_name;
					$hook_map[$hook_name] = $hooked_sections;
					$this->save_section_hook_map($hook_map);
				}
			}else{
				$hooked_sections = array();
				$hooked_sections[] = $section_name;
				$hook_map[$hook_name] = $hooked_sections;
				$this->save_section_hook_map($hook_map);
			}					
		}
	}
	
	private function remove_section_from_hook($hook_name, $section_name){
		if(isset($hook_name) && isset($section_name) && !empty($hook_name) && !empty($section_name)){	
			$hook_map = $this->get_section_hook_map();
			if(isset($hook_map[$hook_name])){
				$hooked_sections = $hook_map[$hook_name];
				if(!in_array($section_name, $hooked_sections)){
					unset($hooked_sections[$section_name]);				
					$hook_map[$hook_name] = $hooked_sections;
					$this->save_section_hook_map($hook_map);
				}
			}				
		}
	}
	 	 
	 public function get_section($section_name){
	 	if(isset($section_name) && !empty($section_name)){	
			$sections = $this->get_sections();
			if(is_array($sections)){
				$section = $sections[$section_name];	
				if($this->is_valid_section($section)){
					return $section;
				} 
			}
		}
		return false;
	 }
	 
	 public function update_section($section){
	 	if($this->is_valid_section($section)){	
			$sections = $this->get_sections();
			$sections = (isset($sections) && is_array($sections)) ? $sections : array();
			
			$sections[$section->name] = $section;
			
			$result1 = update_option('thwepo_custom_sections', $sections);
			$result2 = $this->update_section_hook_map($section);
	
			return $result1;
		}
		return false;
	 }
	 
	 public function delete_section($section_name){
	 	if(isset($section_name) && !empty($section_name)){	
			$sections = $this->get_sections();
			if(is_array($sections) && isset($sections[$section_name])){
				$section   = $sections[$section_name];
				$hook_name = $section->get_position();
				$this->remove_section_from_hook($hook_name, $section_name);
				
				unset($sections[$section_name]);			
				$result = update_option('thwepo_custom_sections', $sections);		
				return $result;
			}
		}
		return false;
	 }	 
	 
	 public function update_options_name_title_map(){
	 	$name_title_map = array();
	 	$sections = $this->get_sections();
		if($sections && is_array($sections)){
			foreach($sections as $section_name => $section){
				if($this->is_valid_section($section)){					
					$fields = $section->get_fields();					
					if($fields){
						foreach($fields as $field_name => $field){
							if($this->is_valid_field($field) && $field->get_property('enabled')){
								$name_title_map[$field_name] = $field->get_display_label();
							}
						}
					}
				}
			}
		}
	 
		$result = update_option('thwepo_options_name_title_map', $name_title_map);
		return $result;
	 }	 
	/**-----------------------------------------
	 *-------- SECTION FUNCTIONS - START -------
	 *------------------------------------------*/
	 
	 
   /*******************************************
	*-------- HTML FORM FRAGMENTS - START -----
	*******************************************/
	
	private function output_popup_form_fields($form_type){
		?>
		<form>
        	<div id="thwepo-tabs-container_<?php echo $form_type ?>">
                <ul class="thwepo-tabs-menu">
                    <li class="first current"><a class="thwepo_tab_general_link" href="javascript:void(0)" 
                    onclick="thwepoOpenFormTab(this, 'thwepo-tab-general', '<?php echo $form_type ?>')">General Properties</a></li>
                    <li><a class="thwepo_tab_rules_link" href="javascript:void(0)" 
                    onclick="thwepoOpenFormTab(this, 'thwepo-tab-rules', '<?php echo $form_type ?>')">Conditional Rules</a></li>
                </ul>
                <div id="thwepo_field_editor_form_<?php echo $form_type ?>" class="thwepo-tab thwepo_popup_wrapper">
                    <div id="thwepo-tab-general_<?php echo $form_type ?>" class="thwepo-tab-content">
						<?php $this->render_field_form_fragment_general($form_type); ?>
                        <table class="thwepo_field_form_tab_general_placeholder" width="100%"></table>
                    </div>
                    <div id="thwepo-tab-rules_<?php echo $form_type ?>" class="thwepo-tab-content">
                    	<table class="thwepo_field_form_tab_rules_placeholder" width="100%" style="margin-top: 10px;">
                    	<?php 
							$this->render_field_form_fragment_rules(); 
							$this->render_field_form_fragment_rules_ajax();
						?>
                        </table>
                    </div>
                </div>
        	</div>
        </form>
        <?php
		$this->render_form_field_inputtext();
		//$this->render_form_field_hidden();
		$this->render_form_field_password();		
		$this->render_form_field_textarea();
		$this->render_form_field_select();
		$this->render_form_field_multiselect();		
		$this->render_form_field_radio();
		$this->render_form_field_checkbox();
		//$this->render_form_field_checkboxgroup();
		$this->render_form_field_datepicker();
		$this->render_form_field_timepicker();		
		$this->render_form_field_heading();
		$this->render_form_field_label();
		$this->render_form_field_default();
		
		$this->render_field_form_fragment_product_list();
		$this->render_field_form_fragment_category_list();
		$this->render_field_form_fragment_fields_wrapper();
	}	
	
	private function render_form_field_inputtext(){
		?>
        <table id="thwepo_field_form_id_inputtext" class="thwepo_field_form_table" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_field_form_fragment_value();
            	$this->render_form_field_element($this->option_fields['placeholder'], $this->right_cell_props);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_field_form_fragment_validate();
            	$this->render_form_field_element($this->option_fields['cssclass'], $this->right_cell_props);
				?>
            </tr>
            <?php 
				$this->render_field_form_fragment_price();
				$this->render_field_form_fragment_h_spacing(); 
			?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_field_form_fragment_required();
				$this->render_field_form_fragment_enabled();
				//$this->render_field_form_fragment_is_price_field();
				?>
                </td>
            </tr>      
            <?php 
				$this->render_field_form_fragment_h_separator();
				$this->render_field_form_fragment_title(true);
				//$this->render_field_form_fragment_h_separator();
				//$this->render_field_form_fragment_rules();   
			?>   
        </table>
        <?php   
	}
	
	private function render_form_field_password(){
		?>
        <table id="thwepo_field_form_id_password" class="thwepo_field_form_table" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_form_field_element($this->option_fields['placeholder'], $this->left_cell_props);
            	$this->render_form_field_element($this->option_fields['cssclass'], $this->right_cell_props);
				?>
            </tr>
            <?php 
				$this->render_field_form_fragment_price();
				$this->render_field_form_fragment_h_spacing(); 
			?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_field_form_fragment_required();
				$this->render_field_form_fragment_enabled();
				?>
                </td>
            </tr>      
            <?php 
				$this->render_field_form_fragment_h_separator();
				$this->render_field_form_fragment_title(true);
			?>   
        </table>
        <?php   
	}
	
	private function render_form_field_textarea(){
		?>
        <table id="thwepo_field_form_id_textarea" class="thwepo_field_form_table" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_field_form_fragment_value();
            	$this->render_form_field_element($this->option_fields['placeholder'], $this->right_cell_props);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->option_fields['cssclass'], $this->left_cell_props);
				$this->render_form_field_blank();
				?>
            </tr>
            <?php 
				$this->render_field_form_fragment_price();
				$this->render_field_form_fragment_h_spacing(); 
			?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_field_form_fragment_required();
				$this->render_field_form_fragment_enabled();
				?>
                </td>
            </tr>      
            <?php 
				$this->render_field_form_fragment_h_separator();
				$this->render_field_form_fragment_title(true);
			?>   
        </table>
        <?php   
	}
	
	private function render_form_field_select(){
		?>
        <table id="thwepo_field_form_id_select" class="thwepo_field_form_table" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_field_form_fragment_value();
				$this->render_form_field_element($this->option_fields['cssclass'], $this->right_cell_props);
				?>
            </tr>
            <?php $this->render_field_form_fragment_h_spacing(); ?>
            <tr>
            	<?php $this->render_field_form_fragment_options(); ?>
            </tr>
            <?php $this->render_field_form_fragment_h_spacing(); ?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_field_form_fragment_required();
				$this->render_field_form_fragment_enabled();
				?>
                </td>
            </tr>   
            <?php 
				$this->render_field_form_fragment_h_separator();
				$this->render_field_form_fragment_title(true);
			?>           
        </table>
        <?php   
	}
	
	private function render_form_field_multiselect(){
		?>
        <table id="thwepo_field_form_id_multiselect" class="thwepo_field_form_table" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_field_form_fragment_value();
            	$this->render_form_field_element($this->option_fields['placeholder'], $this->right_cell_props);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->option_fields['cssclass'], $this->left_cell_props);
				$this->render_form_field_blank();
				?>
            </tr>
            <?php $this->render_field_form_fragment_h_spacing(); ?>
            <tr>
            	<?php $this->render_field_form_fragment_options(); ?>
            </tr>
            <?php $this->render_field_form_fragment_h_spacing(); ?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_field_form_fragment_required();
				$this->render_field_form_fragment_enabled();
				?>
                </td>
            </tr>   
            <?php 
				$this->render_field_form_fragment_h_separator();
				$this->render_field_form_fragment_title(true);
			?>           
        </table>
        <?php   
	}
	
	private function render_form_field_radio(){
		?>
        <table id="thwepo_field_form_id_radio" class="thwepo_field_form_table" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_field_form_fragment_value();
            	$this->render_form_field_element($this->option_fields['cssclass'], $this->right_cell_props);
				?>
            </tr>
            <tr>
            	<?php $this->render_field_form_fragment_options(); ?>
            </tr>
            <?php $this->render_field_form_fragment_h_spacing(); ?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_field_form_fragment_required();
				$this->render_field_form_fragment_enabled();
				?>
                </td>
            </tr>   
            <?php 
				$this->render_field_form_fragment_h_separator();
				$this->render_field_form_fragment_title(true);
			?>           
        </table>
        <?php   
	}
	
	private function render_form_field_checkbox(){
		?>
        <table id="thwepo_field_form_id_checkbox" class="thwepo_field_form_table" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_field_form_fragment_value(true);
				$this->render_form_field_element($this->option_fields['cssclass'], $this->right_cell_props);
				?>
            </tr>
            <?php 
				$this->render_field_form_fragment_price();
				$this->render_field_form_fragment_h_spacing(); 
			?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_field_form_fragment_required();
				$this->render_field_form_fragment_enabled();
				?>
                </td>
            </tr>   
            <?php 
				$this->render_field_form_fragment_h_separator();
				$this->render_field_form_fragment_title(true);
			?>           
        </table>
        <?php   
	}
	
	private function render_form_field_datepicker(){
		?>
        <table id="thwepo_field_form_id_datepicker" class="thwepo_field_form_table" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_form_field_element($this->option_fields['placeholder'], $this->left_cell_props);
            	$this->render_form_field_element($this->option_fields['cssclass'], $this->right_cell_props);
				?>
            </tr>
            <?php 
				$this->render_field_form_fragment_price();
			?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_field_form_fragment_required();
				$this->render_field_form_fragment_enabled();
				?>
                </td>
            </tr>      
            <?php 
				$this->render_field_form_fragment_h_separator();
				$this->render_field_form_fragment_title(true);
				
				$this->render_field_form_fragment_h_spacing(); 
				$this->render_field_form_fragment_h_separator();
				$this->render_field_form_fragment_datepicker();
				$this->render_field_form_fragment_h_spacing(); 
			?>   
        </table>
        <?php   
	}
	
	private function render_form_field_timepicker(){
		?>
        <table id="thwepo_field_form_id_timepicker" class="thwepo_field_form_table" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_field_form_fragment_value();
            	$this->render_form_field_element($this->option_fields['placeholder'], $this->right_cell_props);
				?>
            </tr>
            <tr>
            	<?php
            	$this->render_form_field_element($this->option_fields['cssclass'], $this->left_cell_props);
				$this->render_form_field_blank();
				?>
            </tr>
            <?php 
				 $this->render_field_form_fragment_price();
			?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_field_form_fragment_required();
				$this->render_field_form_fragment_enabled();
				?>
                </td>
            </tr>      
            <?php 
				$this->render_field_form_fragment_h_separator();
				$this->render_field_form_fragment_title(true);
				
				$this->render_field_form_fragment_h_spacing(); 
				$this->render_field_form_fragment_h_separator();
				$this->render_field_form_fragment_timepicker();
				$this->render_field_form_fragment_h_spacing();
			?>   
        </table>
        <?php   
	}
	
	private function render_form_field_heading(){
		?>
        <table id="thwepo_field_form_id_heading" class="thwepo_field_form_table" width="100%" style="display:none;">
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
				$this->render_field_form_fragment_enabled();
				?>
                </td>
            </tr>      
            <?php 
				$this->render_field_form_fragment_title(true);
			?>   
        </table>
        <?php   
	}
	
	private function render_form_field_label(){
		?>
        <table id="thwepo_field_form_id_label" class="thwepo_field_form_table" width="100%" style="display:none;">
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
				$this->render_field_form_fragment_enabled();
				?>
                </td>
            </tr>      
            <?php 
				$this->render_field_form_fragment_title(true);
			?>     
        </table>
        <?php   
	}
	
	private function render_form_field_default(){
		?>
        <table id="thwepo_field_form_id_default" class="thwepo_field_form_table" width="100%" style="display:none;">
            <tr>
            	<?php
            	$this->render_field_form_fragment_value();
				$this->render_form_field_element($this->option_fields['placeholder'], $this->right_cell_props);
				?>
            </tr>
            <tr>
            	<?php
				$this->render_field_form_fragment_validate();
				$this->render_form_field_element($this->option_fields['cssclass'], $this->right_cell_props);
				?>
            </tr>
            <?php 
				$this->render_field_form_fragment_price();
				$this->render_field_form_fragment_h_spacing(); 
			?>
            <tr>
            	<td colspan="2">&nbsp;</td>
            	<td colspan="4">
            	<?php
            	$this->render_field_form_fragment_required();
				$this->render_field_form_fragment_enabled();
				?>
                </td>
            </tr>      
            <?php 
				$this->render_field_form_fragment_h_separator();
				$this->render_field_form_fragment_title(true);
			?>   
        </table>
        <?php   
	}
	
	private function render_field_form_fragment_general($form_type, $input_field = true){
		$field_types = $this->get_field_types();
		
		$field_name_label = $input_field ? $this->__wepo('Name') : $this->__wepo('ID');
		?>
        <table width="100%">
            <tr>                
                <td colspan="6" class="err_msgs"></td>
            </tr>            	         
            <tr>      
                <td width="13%"><?php echo $field_name_label; ?><abbr class="required" title="required">*</abbr></td>
                <?php $this->render_field_form_fragment_tooltip_empty(); ?>
                <td width="34%">
                	<input type="text" name="i_name" style="width:250px;"/>
                    <?php if($form_type === 'edit'){ ?>
                        <input type="hidden" name="i_rowid" value="" />
                    <?php } ?>
                </td>
                    
                <td width="13%"><?php $this->_ewepo('Field Type'); ?><abbr class="required" title="required">*</abbr></td>
                <?php $this->render_field_form_fragment_tooltip_empty(); ?>
                <td width="34%">
                    <select name="i_type" style="width:250px;" onchange="thwepoFieldTypeChangeListner(this)">
                    <?php foreach($field_types as $value=>$label){ ?>
                        <option value="<?php echo trim($value); ?>"><?php echo $label; ?></option>
                    <?php } ?>
                    </select>
                </td>
            </tr> 
        </table>  
        <?php
	}
			
	private function render_field_form_fragment_value($is_value = false){
		$label = $is_value ? $this->__wepo('Value') : $this->__wepo('Default Value');
		?>
        <td width="13%"><?php echo $label; ?></td>
        <?php $this->render_field_form_fragment_tooltip_empty(); ?>
        <td width="34%"><input type="text" name="i_value" style="width:250px;"/></td>
        <?php
	}
	
    private function render_field_form_fragment_options(){
		?>
        <td width="13%" valign="top"><?php $this->_ewepo('Options'); ?></td>
        <?php $this->render_field_form_fragment_tooltip_empty(); ?>
        <td colspan="4">
        	<table width="99%" border="0" cellpadding="0" cellspacing="0" class="thwepo-option-list"><tbody>
            	<tr>
            		<td style="width:190px;"><input type="text" name="i_options_key[]" placeholder="Option Value" style="width:180px;"/></td>
                    <td style="width:190px;"><input type="text" name="i_options_text[]" placeholder="Option Text" style="width:180px;"/></td>
                    <td style="width:75px;"><input type="text" name="i_options_price[]" placeholder="Price" style="width:65px;"/></td>
                    <td style="width:120px;">    
                        <select name="i_options_price_type[]" style="width:110px;">
                            <option selected="selected" value="">Normal</option>
                            <option value="percentage">Percentage</option>
                        </select>
                    </td>
                    <td style="width:30px;"><a href="javascript:void(0)" onclick="thwepoAddNewOptionRow(this)" class="add_link" title="Add new option">+</a></td>
                    <td><a href="javascript:void(0)" onclick="thwepoRemoveOptionRow(this)" class="remove_link" title="Remove option">x</a></td>
        		</tr>
        	</tbody></table>            	
        </td>
        <?php
	}
	
	private function render_field_form_fragment_validate(){
		?>
        <td width="13%"><?php $this->_ewepo('Validation'); ?></td>
        <?php $this->render_field_form_fragment_tooltip_empty(); ?>
        <td width="34%">
            <select multiple="multiple" name="i_validate" placeholder="Select validations" class="thwepo-enhanced-multi-select" style="width: 250px; height:30px;">
                <option value="email"><?php $this->_ewepo('Email'); ?></option>
                <option value="number"><?php $this->_ewepo('Number'); ?></option>
            </select>
        </td>
        <?php
	}
	
	private function render_field_form_fragment_required(){
		?>
        <input type="checkbox" id="a_frequired" name="i_required" value="yes" checked />
        <label for="a_frequired" style="margin-right: 40px;" ><?php $this->_ewepo('Required'); ?></label>
        <?php
	}
	
	private function render_field_form_fragment_enabled(){
		?>                              
        <input type="checkbox" id="a_fenabled" name="i_enabled" value="yes" checked />
        <label for="a_fenabled" style="margin-right: 40px;" ><?php $this->_ewepo('Enabled'); ?></label>
        <?php
	}
			
	private function render_field_form_fragment_title($show_subtitle = false){
		?>
        <tr>      
        	<?php          
        	$this->render_form_field_element($this->option_fields['title'], $this->left_cell_props);
			$this->render_form_field_element($this->option_fields['title_position'], $this->right_cell_props);
			?>
        </tr>  
        <tr>   
        	<?php          
        	$this->render_form_field_element($this->option_fields['title_type'], $this->left_cell_props);
			$this->render_form_field_element($this->option_fields['title_color'], $this->right_cell_props);
			?>
        </tr>
        <tr>  
        	<?php          
        	$this->render_form_field_element($this->option_fields['title_class'], $this->left_cell_props);
			$this->render_field_form_fragment_show_subtitle_checkbox();
			?>            
        </tr>
        <?php
		if($show_subtitle){
			$this->output_h_separator(false);
		?>
        <tr class="thwepo_subtitle_row">    
        	<?php          
        	$this->render_form_field_element($this->option_fields['subtitle'], $this->left_cell_props);
			$this->render_form_field_element($this->option_fields['subtitle_type'], $this->right_cell_props);
			?>
        </tr>  
        <tr class="thwepo_subtitle_row"> 
        	<?php          
        	$this->render_form_field_element($this->option_fields['subtitle_color'], $this->left_cell_props);
			$this->render_form_field_element($this->option_fields['subtitle_class'], $this->right_cell_props);
			?>
        </tr>
        <?php
		}
	}
	
	private function render_field_form_fragment_show_subtitle_checkbox(){
		?>                              
        <td colspan="3">
        <input type="checkbox" id="a_fshowsubtitle" name="i_showsubtitle" onchange="thwepo_show_subtitle_options(this)"/>
        <label for="a_fshowsubtitle" style="margin-right: 40px;" ><?php $this->_ewepo('Add subtitle'); ?></label>
        </td>
        <?php
	}
	
	private function render_field_form_fragment_datepicker(){
		?>
        <tr>     
        	<?php          
			$this->render_form_field_element($this->option_fields['date_format'], $this->left_cell_props);
			$this->render_form_field_element($this->option_fields['default_date'], $this->right_cell_props);
			?> 
        </tr>  
        <tr>     
        	<?php          
        	$this->render_form_field_element($this->option_fields['min_date'], $this->left_cell_props);
			$this->render_form_field_element($this->option_fields['max_date'], $this->right_cell_props);
			?> 
        </tr>  
        <tr> 
        	<?php          
        	$this->render_form_field_element($this->option_fields['year_range'], $this->left_cell_props);
			$this->render_form_field_element($this->option_fields['number_of_months'], $this->right_cell_props);
			?> 
        </tr>
        <?php
    }
	
	private function render_field_form_fragment_timepicker(){
		?>
        <tr>       
        	<?php          
        	$this->render_form_field_element($this->option_fields['min_time'], $this->left_cell_props);
			$this->render_form_field_element($this->option_fields['max_time'], $this->right_cell_props);
			?> 
        </tr>  
        <tr>       
        	<?php          
        	$this->render_form_field_element($this->option_fields['time_step'], $this->left_cell_props);
			$this->render_form_field_element($this->option_fields['time_format'], $this->right_cell_props);
			?> 
        </tr>
        <?php
    }
	
	private function render_field_form_fragment_is_price_field(){
		?>
        <input type="checkbox" id="a_is_price_field" name="i_is_price_field" value="1" onchange="thwepo_show_price_fields(this)"/>
        <label for="a_is_price_field"><?php $this->_ewepo('Is Price Field'); ?></label>
        <?php
	}
	
	private function render_field_form_fragment_price(){
		?>
        <tr>        
            <td width="13%"><?php $this->_ewepo('Price'); ?></td>
            <?php $this->render_field_form_fragment_tooltip_empty(); ?>
            <td width="34%">
            	<input type="text" name="i_price" placeholder="Price" style="width:250px;" class="thwepo-price-field"/>
                <label class="thwepo-dynamic-price-field" style="display:none">per</label>
                <input type="text" name="i_price_unit" placeholder="Unit" style="width:80px; display:none" class="thwepo-dynamic-price-field"/>
                <label class="thwepo-dynamic-price-field" style="display:none">unit</label>
            </td>
            
            <td width="13%"><?php $this->_ewepo('Price Type'); ?></td>
            <?php $this->render_field_form_fragment_tooltip_empty(); ?>
            <td width="34%">
                <select name="i_price_type" style="width:250px;" onchange="thwepoPriceTypeChangeListener(this)">
                    <option selected="selected" value="">Normal</option>
                    <option value="percentage">Percentage</option>
                    <option value="dynamic">Dynamic</option>
                    <option value="dynamic-excl-base-price">Dynamic - Exclude base price </option>
                </select>
            </td>
        </tr>  
        <?php
	}
	
	private function render_field_form_fragment_rules(){
		?>
        <tr>
        	<td style="padding-left: 12px;">
                <select name="i_rules_action" style="width:80px;">
                    <option value="show">Show</option>
                    <option value="hide">Hide</option>
                </select>
                field if all below conditions are met.
            </td>
        </tr>
        <tr>                
            <td colspan="6">
            	<table class="thwepo_conditional_rules" width="100%"><tbody>
                    <tr class="thwepo_rule_set_row">                
                        <td>
                            <table class="thwepo_rule_set" width="100%"><tbody>
                                <tr class="thwepo_rule_row">
                                    <td>
                                        <table class="thwepo_rule" width="100%" style=""><tbody>
                                            <tr class="thwepo_condition_set_row">
                                                <td>
                                                    <table class="thwepo_condition_set" width="100%" style=""><tbody>
                                                        <tr class="thwepo_condition">
                                                            <td width="25%">
                                                                <select name="i_rule_operand_type" style="width:200px;" onchange="thwepoRuleOperandTypeChangeListner(this)">
                                                                    <option value=""></option>
                                                                    <option value="product">Product</option>
                                                                    <option value="category">Category</option>
                                                                </select>
                                                            </td>
                                                            <td width="25%">
                                                                <select name="i_rule_operator" style="width:200px;">
                                                                    <option value=""></option>
                                                                    <option value="equals">Equals to/ In</option>
                                                                    <option value="not_equals">Not Equals to/ Not in</option>
                                                                </select>
                                                            </td>
                                                            <td width="25%" class="thpladmin_rule_operand"><input type="text" name="i_rule_operand" style="width:200px;"/></td>
                                                            <td>
                                                                <a href="javascript:void(0)" class="thpl_logic_link" onclick="thwepoAddNewConditionRow(this, 1)" title="">AND</a>
                                                                <a href="javascript:void(0)" class="thpl_logic_link" onclick="thwepoAddNewConditionRow(this, 2)" title="">OR</a>
                                                                <a href="javascript:void(0)" class="thpl_delete_icon" onclick="thwepoRemoveRuleRow(this)" title="Remove"></a>
                                                            </td>
                                                        </tr>
                                                    </tbody></table>
                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </td>
                                </tr>
                            </tbody></table>            	
                        </td>            
                    </tr> 
        		</tbody></table>
        	</td>
        </tr>
        <?php
	}
	
	private function render_field_form_fragment_rules_ajax(){
		?>
        <tr><td style="border-top: 1px dashed #e6e6e6;">&nbsp;</td></tr>
        <tr>
        	<td style="padding-left: 12px;">
                <select name="i_rules_action_ajax" style="width:80px;">
                    <option value="show">Show</option>
                    <option value="hide">Hide</option>
                </select>
                field if all below conditions are met.
            </td>
        </tr>
        <tr>                
            <td>
            	<table class="thwepo_conditional_rules_ajax" width="100%"><tbody>
                    <tr class="thwepo_rule_set_row">                
                        <td>
                            <table class="thwepo_rule_set" width="100%"><tbody>
                                <tr class="thwepo_rule_row">
                                    <td>
                                        <table class="thwepo_rule" width="100%" style=""><tbody>
                                            <tr class="thwepo_condition_set_row">
                                                <td>
                                                    <table class="thwepo_condition_set" width="100%" style=""><tbody>
                                                        <tr class="thwepo_condition">
                                                        	<td width="25%" class="thpladmin_rule_operand">
                                                            	<input type="hidden" name="i_rule_operand_type" value="field" />
                                                            	<?php $this->render_field_form_fragment_fields_select(); ?>
                                                            </td>
                                                            <td width="25%">
                                                                <select name="i_rule_operator" style="width:200px;" onchange="thwepoRuleOperatorChangeListnerAjax(this)">
                                                                    <option value="">Please select an operator</option>
                                                                    <option value="empty">Is empty</option>
                                                                    <option value="not_empty">Is not empty</option>
                                                                    <option value="value_eq">Value equals to</option>
                                                                    <option value="value_ne">Value not equals to</option>
                                                                    <option value="value_gt">Value greater than</option>
                                                                    <option value="value_le">Value less than</option>
                                                                    <option value="checked">Is checked</option>
                                                                    <option value="not_checked">Is not checked</option>
                                                                </select>
                                                            </td>
                                                            <td width="25%"><input type="text" name="i_rule_value" style="width:200px;"/></td>
                                                            <td>
                                                              <a href="javascript:void(0)" class="thpl_logic_link" onclick="thwepoAddNewConditionRowAjax(this, 1)" title="">AND</a>
                                                              <a href="javascript:void(0)" class="thpl_logic_link" onclick="thwepoAddNewConditionRowAjax(this, 2)" title="">OR</a>
                                                              <a href="javascript:void(0)" class="thpl_delete_icon" onclick="thwepoRemoveRuleRowAjax(this)" title="Remove"></a>
                                                            </td>
                                                        </tr>
                                                    </tbody></table>
                                                </td>
                                            </tr>
                                        </tbody></table>
                                    </td>
                                </tr>
                            </tbody></table>            	
                        </td>            
                    </tr> 
        		</tbody></table>
        	</td>
        </tr>
        <?php
	}
	
	private function render_field_form_fragment_product_list(){
		$products = apply_filters( "thwepo_load_products", array() );
		array_unshift( $products , array( "id" => "-1", "title" => "All Products" ));
		?>
        <div id="thwepo_product_select" style="display:none;">
        <select multiple="multiple" name="i_rule_operand" class="thwepo-enhanced-multi-select" style="width:200px;" value="">
			<?php 	
                foreach($products as $product){
                    echo '<option value="'. $product["id"] .'" >'. $product["title"] .'</option>';
                }
            ?>
        </select>
        </div>
        <?php
	}
	private function render_field_form_fragment_category_list(){		
		$categories = apply_filters( "thwepo_load_products_cat", array() );
		array_unshift( $categories , array( "id" => "-1", "title" => "All Categories" ));
		?>
        <div id="thwepo_product_cat_select" style="display:none;">
        <select multiple="multiple" name="i_rule_operand" class="thwepo-enhanced-multi-select" style="width:200px;" value="">
			<?php 	
                foreach($categories as $category){
                    echo '<option value="'. $category["id"] .'" >'. $category["title"] .'</option>';
                }
            ?>
        </select>
        </div>
        <?php
	}
	private function render_field_form_fragment_fields_wrapper(){		
		?>
        <div id="thwepo_product_fields_select" style="display:none;">
			<?php $this->render_field_form_fragment_fields_select(); ?>
        </div>
        <?php
	}
	private function render_field_form_fragment_fields_select(){		
		$sections = $this->get_sections();	
		?>
        <select multiple="multiple" name="i_rule_operand" data-placeholder="Click to select field(s)" class="thwepo-enhanced-multi-select" style="width:200px;" value="">
			<?php 
			if($sections && is_array($sections)){	
				foreach($sections as $sname => $section){	
					if($section && $this->is_valid_section($section)){
						$fields = $section->get_fields();
						if($fields && is_array($fields)){	
							echo '<optgroup label="'. $section->get_property('title') .'">';
							foreach($fields as $name => $field){
								if($field && $this->is_valid_field($field)){
									$label = $field->get_property('title');
									$label = empty($label) ? $name : $label;
									echo '<option value="'. $name .'" >'. $label .'</option>';
								}
							}
							echo '</optgroup>';
						}
					}
				}
			}
            ?>
        </select>
        <?php
	}
	
	private function render_field_form_fragment_tooltip($msg){
		?>
        <td style="width: 16px; padding:0px;">
			<a href="javascript:void(0)" title="<?php echo $msg; ?>" class="thwepo_tooltip"><img src="<?php echo TH_WEPO_ASSETS_URL; ?>/css/help.png" title=""/></a>
        </td>
        <?php
	}
	
	private function render_field_form_fragment_tooltip_empty(){
		?>
        <td style="width: 16px; padding:0px;"></td>
        <?php
	}
	
	private function render_field_form_fragment_h_separator($padding = 5){
		?>
        <tr><td colspan="6" style="border-bottom: 1px dashed #e6e6e6; padding-top: <?php echo $padding ?>px;"></td></tr>
        <?php
	}
	
	private function render_field_form_fragment_h_spacing($padding = 5){
		?>
        <tr><td colspan="6" style="padding-top:<?php echo $padding ?>px;"></td></tr>
        <?php
	}
	
	
	private function render_form_field_element_($field, $atts=array()){
		if($field && is_array($field)){
			$args = shortcode_atts( array(
				'label_cell_width' => '',
				'input_cell_colspan' => '',
				'label_cell_colspan' => '',
				'input_cell_width' => '',
				'input_width' => '',
				'input_name_prefix' => 'i_'
			), $atts );
		
			$ftype  = $field['type'];
			$fname  = $args['input_name_prefix'].$field['name'];
			$flabel = $this->__wepo($field['label']);
			$fvalue = isset($field['value']) ? $field['value'] : '';
			
			$input_width = $args['input_width'] ? 'width:'.$args['input_width'].';' : '';
			$field_props = 'name="'. $fname .'" value="'. $fvalue .'" style="'. $input_width .'"';
			
			$required_html = ( isset($field['required']) && $field['required'] ) ? '<abbr class="required" title="required">*</abbr>' : '';
			$field_html = '';
			
			if($ftype == 'text'){
				$field_html = '<input type="text" '. $field_props .' />';
				
			}else if($ftype == 'select'){
				$field_html = '<select '. $field_props .' >';
				foreach($field['options'] as $value=>$label){
					$selected = $value === $fvalue ? 'selected' : '';
					$field_html .= '<option value="'. trim($value) .'" '.$selected.'>'. $this->__wepo($label) .'</option>';
				}
				$field_html .= '</select>';
				
			}else if($ftype == 'colorpicker'){
				$field_html  = '<span class="thwepo-colorpickpreview '.$field['name'].'_preview" style=""></span>';
                $field_html .= '<input type="text" '. $field_props .' class="thwepo-colorpick"/>';              
            
			}else if($ftype == 'checkbox'){
				$fid = 'a_f'. $field['name'];
				$field_props .= $field['checked'] ? ' checked' : '';
				
				$field_html  = '<input type="checkbox" id="'. $fid .'" '. $field_props .' />';
				$field_html .= '<label for="'. $fid .'" > '. $flabel .'</label>';
				
				$flabel = '&nbsp;';
			}
			
			$label_cell_props = !empty($args['label_cell_width']) ? 'width="'.$args['label_cell_width'].'"' : '';
			$input_cell_props = !empty($args['input_cell_width']) ? 'width="'.$args['input_cell_width'].'"' : '';
			
			$label_cell_props .= !empty($args['label_cell_colspan']) ? 'colspan="'.$args['label_cell_colspan'].'"' : '';
			$input_cell_props .= !empty($args['input_cell_colspan']) ? 'colspan="'.$args['input_cell_colspan'].'"' : '';
			
			?>
			<td <?php echo $label_cell_props ?> ><?php echo $flabel; echo $required_html; ?></td>
            <td <?php echo $input_cell_props ?> ><?php echo $field_html; ?></td>
            <?php
		}
	}
	
	
	
	private function render_form_field_element($field, $atts=array()){
		if($field && is_array($field)){
			$args = shortcode_atts( array(
				'label_cell_width' => '',
				'input_cell_colspan' => '',
				'label_cell_colspan' => '',
				'input_cell_width' => '',
				'input_width' => '',
				'input_name_prefix' => 'i_'
			), $atts );
		
			$ftype  = $field['type'];
			$fname  = $args['input_name_prefix'].$field['name'];
			$flabel = $this->__wepo($field['label']);
			$fvalue = isset($field['value']) ? $field['value'] : '';
			
			$input_width  = $args['input_width'] ? 'width:'.$args['input_width'].';' : '';
			$field_props  = 'name="'. $fname .'" value="'. $fvalue .'" style="'. $input_width .'"';
			$field_props .= ( isset($field['placeholder']) && !empty($field['placeholder']) ) ? ' placeholder="'.$field['placeholder'].'"' : '';
			
			$required_html = ( isset($field['required']) && $field['required'] ) ? '<abbr class="required" title="required">*</abbr>' : '';
			$field_html = '';
			
			if($ftype == 'text'){
				$field_html = '<input type="text" '. $field_props .' />';
				
			}else if($ftype == 'select'){
				$field_html = '<select '. $field_props .' >';
				foreach($field['options'] as $value=>$label){
					$selected = $value === $fvalue ? 'selected' : '';
					$field_html .= '<option value="'. trim($value) .'" '.$selected.'>'. $this->__wepo($label) .'</option>';
				}
				$field_html .= '</select>';
				
			}else if($ftype == 'colorpicker'){
				$field_html  = '<span class="thwepo-colorpickpreview '.$field['name'].'_preview" style=""></span>';
                $field_html .= '<input type="text" '. $field_props .' class="thwepo-colorpick"/>';              
            
			}else if($ftype == 'checkbox'){
				$fid = 'a_f'. $field['name'];
				$field_props .= $field['checked'] ? ' checked' : '';
				
				$field_html  = '<input type="checkbox" id="'. $fid .'" '. $field_props .' />';
				$field_html .= '<label for="'. $fid .'" > '. $flabel .'</label>';
				
				$flabel = '&nbsp;';
			}
			
			$label_cell_props = !empty($args['label_cell_width']) ? 'width="'.$args['label_cell_width'].'"' : '';
			$input_cell_props = !empty($args['input_cell_width']) ? 'width="'.$args['input_cell_width'].'"' : '';
			
			$label_cell_props .= !empty($args['label_cell_colspan']) ? 'colspan="'.$args['label_cell_colspan'].'"' : '';
			$input_cell_props .= !empty($args['input_cell_colspan']) ? 'colspan="'.$args['input_cell_colspan'].'"' : '';
			
			?>
			<td <?php echo $label_cell_props ?> >
				<?php echo $flabel; echo $required_html; 
				if(isset($field['sub_label']) && !empty($field['sub_label'])){
					?>
                    <br /><span class="thwepo-subtitle"><?php $this->_ewepo($field['sub_label']); ?></span>
					<?php
				}
				?>
            </td>
            <?php 
				$tooltip = ( isset($field['hint_text']) && !empty($field['hint_text']) ) ? $field['hint_text'] : '';
				if($tooltip){
					$this->render_field_form_fragment_tooltip($tooltip);
				}else{
					$this->render_field_form_fragment_tooltip_empty(); 
				}
			?>
            <td <?php echo $input_cell_props ?> ><?php echo $field_html; ?></td>
            <?php
		}
	}
	
	
	
	
	private function render_form_field_blank($colspan=3){
		?>
        <td colspan="<?php echo $colspan; ?>">&nbsp;</td>  
        <?php
	}
	
	private function prepare_atts( $pairs, $atts ) {
		$atts = (array)$atts;
		$out = array();
		foreach ($pairs as $name => $default) {
			if ( array_key_exists($name, $atts) )
				$out[$name] = $atts[$name];
			else
				$out[$name] = $default;
		}
		
		return $out;
	}
   /*******************************************
 	*-------- HTML FORM FRAGMENTS - END -------
 	*******************************************/
}

endif;