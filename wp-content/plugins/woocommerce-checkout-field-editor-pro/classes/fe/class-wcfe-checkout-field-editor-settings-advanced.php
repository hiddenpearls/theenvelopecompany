<?php
/**
 * WooCommerce Checkout Field Editor Advanced Settings
 *
 * @author      ThemeHiGH
 * @category    Admin
 */

if(!defined('ABSPATH')){ exit; }

if(!class_exists('WCFE_Checkout_Field_Editor_Advanced_Settings')):

class WCFE_Checkout_Field_Editor_Advanced_Settings extends WCFE_Settings_Page {
	protected static $_instance = null;
	
	private $settings_options = NULL;
	private $left_cell_props = array();
	private $right_cell_props = array();
	private $checkbox_cell_props = array();

	public function __construct() {
		parent::__construct();
		
		$this->page_id    = 'advanced_settings';
		$this->init_constants();
	}
	
	public static function instance() {
		if(is_null(self::$_instance)){
			self::$_instance = new self();
		}
		return self::$_instance;
	} 	
	
	public function init_constants(){
		//$this->left_cell_props  = array( 'label_cell_width' => '13%', 'input_width' => '250px', 'label_cell_th' => true, 'label_cell_style' => 'width:13%', 'input_style' => 'width:250px' );
		$this->left_cell_props  = array( 
			'label_cell_props' => 'class="titledesc" scope="row" style="width: 20%;"', 
			'input_cell_props' => 'class="forminp"', 
			'input_width' => '250px', 
			'label_cell_th' => true 
		);
		
		$this->right_cell_props = array( 'label_cell_width' => '13%', 'input_cell_width' => '34%', 'input_width' => '250px' );
		$this->checkbox_cell_props = array( 'cell_props' => 'colspan="3"' );
		$this->settings_fields  = $this->get_advanced_settings_fields();
	}
	
	public function get_advanced_settings_fields(){
		$fields_position_email = array(
			'woocommerce_email_order_meta_fields' => 'Above customer details',
			'woocommerce_email_customer_details_fields' => 'Below customer details',
		);
		
		return array(
			'custom_fields_position_email' => array(
				'name'=>'custom_fields_position_email', 'label'=>'Custom fields display position in email', 'type'=>'select', 
				'value'=>'woocommerce_email_order_meta_fields', 'options'=>$fields_position_email
			),
			'custom_validators' => array(
				'name'=>'custom_validators', 'label'=>'Custom validators', 'type'=>'dynamic_options'
			),
			'wp_memory_limit' => array('name'=>'wp_memory_limit', 'label'=>'WP Memory Limit', 'type'=>'text'),
			'lazy_load_products' => array(
				'name'=>'lazy_load_products', 'label'=>'Lazy load products used in conditional rules', 'type'=>'checkbox', 'value'=>'yes', 'checked'=>0
			),
			'lazy_load_categories' => array(
				'name'=>'lazy_load_categories', 'label'=>'Lazy load categories used in conditional rules', 'type'=>'checkbox', 'value'=>'yes', 'checked'=>0
			),
			'enable_conditions_country' => array(
				'name'=>'enable_conditions_country', 'label'=>'Enable conditional rules based on country selected.', 'type'=>'checkbox', 'value'=>'yes', 'checked'=>0
			),
			'enable_conditions_state' => array(
				'name'=>'enable_conditions_state', 'label'=>'Enable conditional rules based on State/ Province.', 'type'=>'checkbox', 'value'=>'yes', 'checked'=>0
			),
			'enable_wc_zapier_support' => array(
				'name'=>'enable_wc_zapier_support', 'label'=>'Enable Zapier support.', 'type'=>'checkbox', 'value'=>'yes', 'checked'=>0
			),
		);
	}
	
	public function save_advanced_settings($settings){
		$result = update_option(self::OPTION_KEY_ADVANCED_SETTINGS, $settings);
		return $result;
	}
	
	private function reset_settings(){
		delete_option(self::OPTION_KEY_ADVANCED_SETTINGS);
		echo '<div class="updated"><p>'. $this->__wcfe('Settings successfully reset') .'</p></div>';		
	}
	
	private function save_settings(){
		$settings = array();
		
		//print_r($_POST);
		
		foreach( $this->settings_fields as $name => $field ) {
			if($field['type'] === 'dynamic_options'){
				$vnames = !empty( $_POST['i_validator_name'] ) ? $_POST['i_validator_name'] : array();
				$vlabels = !empty( $_POST['i_validator_label'] ) ? $_POST['i_validator_label'] : array();
				$vpatterns = !empty( $_POST['i_validator_pattern'] ) ? $_POST['i_validator_pattern'] : array();
				$vmessages = !empty( $_POST['i_validator_message'] ) ? $_POST['i_validator_message'] : array();
				
				$validators = array();
				$max = max( array_map( 'absint', array_keys( $vnames ) ) );
				for($i = 0; $i <= $max; $i++) {
					$vname = isset($vnames[$i]) ? stripslashes(trim($vnames[$i])) : '';
					$vlabel = isset($vlabels[$i]) ? stripslashes(trim($vlabels[$i])) : '';
					$vpattern = isset($vpatterns[$i]) ? stripslashes(trim($vpatterns[$i])) : '';
					$vmessage = isset($vmessages[$i]) ? stripslashes(trim($vmessages[$i])) : '';
					
					if(!empty($vname) && !empty($vpattern)){
						$vlabel = empty($vlabel) ? $vname : $vlabel;
						
						$validator = array();
						//$validator['name'] = $vname;
						$validator['label'] = $vlabel;
						$validator['pattern'] = $vpattern;
						$validator['message'] = $vmessage;
						
						$validators[$vname] = $validator;
					}
				}
				$settings[$name] = $validators;
			}else{
				$value = '';
				
				if($field['type'] === 'checkbox'){
					$value = !empty( $_POST['i_'.$name] ) ? $_POST['i_'.$name] : '';
				}else{
					$value = !empty( $_POST['i_'.$name] ) ? $_POST['i_'.$name] : '';
				}
				
				$settings[$name] = $value;
			}
		}
				
		$result = $this->save_advanced_settings($settings);
		if ($result == true) {
			echo '<div class="updated"><p>'. $this->__wcfe('Your changes were saved.') .'</p></div>';
		} else {
			echo '<div class="error"><p>'. $this->__wcfe('Your changes were not saved due to an error (or you made none!).') .'</p></div>';
		}	
	}
	
	public function output_page(){
		$this->output_tabs();
		$this->output_content();
	}
	
	private function output_content(){
		if(isset($_POST['reset_settings']))
			echo $this->reset_settings();	
			
		if(isset($_POST['save_settings']))
			echo $this->save_settings();
			
		
		/*$testvals = array('max-mustermann-str. 34b', 'abcd 1', 'abcd 1b', 'abcd 1b c / o me in office', 'abc 1 ', 'abc1', 'abc 1', 'abc66abc', 'max-mustermann-str.', '34b', '34b4');
		foreach($testvals as $testval){
			//if(preg_match("/^[a-zA-Z]+(?=.*\d).*$/", $testval) === 0) {
			if(preg_match("/^(?=.*[a-zA-Z])(?=.*\d).*$/", $testval) === 0) {
				echo 'Invalid<br/>';
			}else{
				echo 'Valid<br/>';
			}
		}*/
		
		
			
		$settings = $this->get_advanced_settings();
		?>            
        <div style="padding-left: 30px;">               
		    <form method="post" action="">
                <!--<h2>Custom Fields Display Settings</h2>
                <p>The following options affect how prices are displayed on the frontend.</p>-->
                <table class="form-table thpladmin-form-table">
                    <tbody>
                    <?php foreach( $this->settings_fields as $name => $field ) { ?>
                        <tr valign="top">
                            <?php 
								if($field['type'] === 'dynamic_options'){
									?>
									<td><?php echo $field['label']; ?></td>
                                    <?php $this->render_form_element_tooltip(''); ?>
									<td>
                                        <table border="0" cellpadding="0" cellspacing="0" class="thwcfe-validations-list thpladmin-dynamic-row-table"><tbody>
                                        	<?php
											$custom_validators = is_array($settings) && isset($settings[$name]) ? $settings[$name] : array();
											if(is_array($custom_validators) && !empty($custom_validators)){
												foreach( $custom_validators as $vname => $validator ) {
													$vlabel = isset($validator['label']) ? $validator['label'] : '';
													$vpattern = isset($validator['pattern']) ? $validator['pattern'] : '';
													$vmessage = isset($validator['message']) ? $validator['message'] : '';
													?>
                                                    <tr>
                                                        <td style="width:190px;"><input type="text" name="i_validator_name[]" value="<?php echo $vname; ?>" placeholder="Validator Name" style="width:180px;"/></td>
                                                        <td style="width:190px;"><input type="text" name="i_validator_label[]" value="<?php echo $vlabel; ?>" placeholder="Validator Label" style="width:180px;"/></td>
                                                        <td style="width:190px;"><input type="text" name="i_validator_pattern[]" value="<?php echo $vpattern; ?>" placeholder="Validator Pattern" style="width:180px;"/></td>
                                                        <td style="width:190px;"><input type="text" name="i_validator_message[]" value="<?php echo $vmessage; ?>" placeholder="Validator Message" style="width:180px;"/></td>
                                                        <td class="action-cell"><a href="javascript:void(0)" onclick="thwcfeAddNewValidatorRow(this)" class="btn btn-blue" title="Add new validator">+</a></td>
                                                        <td class="action-cell"><a href="javascript:void(0)" onclick="thwcfeRemoveValidatorRow(this)" class="btn btn-red" title="Remove validator">x</a></td>
                                                    </tr>
                                                    <?php
												}
											}else{
												?>
												<tr>
													<td style="width:190px;">
                                                    	<input type="text" name="i_validator_name[]" placeholder="Validator Name" style="width:180px;"/>
                                                    </td>
													<td style="width:190px;">
                                                    	<input type="text" name="i_validator_label[]" placeholder="Validator Label" style="width:180px;"/>
                                                    </td>
													<td style="width:190px;">
                                                    	<input type="text" name="i_validator_pattern[]" placeholder="Validator Pattern" style="width:180px;"/>
                                                    </td>
                                                    <td style="width:190px;">
                                                    	<input type="text" name="i_validator_message[]" placeholder="Validator Message" style="width:180px;"/>
                                                    </td>
													<td class="action-cell">
                                                    	<a href="javascript:void(0)" onclick="thwcfeAddNewValidatorRow(this)" class="btn btn-blue" title="Add new validator">+</a>
                                                    </td>
													<td class="action-cell">
                                                    	<a href="javascript:void(0)" onclick="thwcfeRemoveValidatorRow(this)" class="btn btn-red" title="Remove validator">x</a>
                                                    </td>
												</tr>
												<?php
											}
											?>
                                        </tbody></table>            	
                                    </td>
                                    <?php
								}else{
									$cell_props = $this->left_cell_props;
									
									if(is_array($settings) && isset($settings[$name])){
										if($field['type'] === 'checkbox'){
											if($field['value'] === $settings[$name]){
												$field['checked'] = 1;
											}
											$cell_props = $this->checkbox_cell_props;
										}else{
											$field['value'] = $settings[$name];
										}
									}
									
									$this->render_form_field_element($field, $cell_props); 
								}
							?>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table> 
                <p class="submit">
					<input type="submit" name="save_settings" class="button-primary" value="Save changes">
                    <input type="submit" name="reset_settings" class="button" value="Reset to default">
            	</p>
            </form>
    	</div>       
    	<?php
	}
	
   /*******************************************
	*-------- HTML FORM FRAGMENTS - START -----
	*******************************************/
	
	public function render_form_element_tooltip1($msg){
		?>
        <td style="width: 16px; padding:0px;">
			<a href="javascript:void(0)" title="<?php echo $msg; ?>" class="thwcfe_tooltip"><img src="<?php echo TH_WCFE_ASSETS_URL; ?>/css/help.png" title=""/></a>
        </td>
        <?php
	}
	
	public function render_form_element_tooltip_empty1(){
		?>
        <td style="width: 16px; padding:0px;"></td>
        <?php
	}
	
	public function render_form_field_element1($field, $atts=array()){
		if($field && is_array($field)){
			$args = shortcode_atts( array(
				'label_cell_props' => '',
				'input_cell_props' => '',
				'label_cell_th' => false,
				'input_width' => '',
				'input_name_prefix' => 'i_'
			), $atts );
		
			$ftype  = $field['type'];
			$fname  = $args['input_name_prefix'].$field['name'];
			$flabel = $this->__wcfe($field['label']);
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
					$field_html .= '<option value="'. trim($value) .'" '.$selected.'>'. $this->__wcfe($label) .'</option>';
				}
				$field_html .= '</select>';
				
			}else if($ftype == 'colorpicker'){
				$field_html  = '<span class="thwcfe-colorpickpreview '.$field['name'].'_preview" style=""></span>';
                $field_html .= '<input type="text" '. $field_props .' class="thwcfe-colorpick"/>';              
            
			}else if($ftype == 'checkbox'){
				$fid = 'a_f'. $field['name'];
				
				$field_props  = 'name="'. $fname .'" value="'. $fvalue .'"';
				$field_props .= $field['checked'] ? ' checked' : '';
				
				$field_html  = '<input type="checkbox" id="'. $fid .'" '. $field_props .' />';
				$field_html .= '<label for="'. $fid .'" > '. $flabel .'</label>';
				
				$flabel = '&nbsp;';
			}
			
			/*$label_cell_props  = !empty($args['label_cell_width']) ? 'width="'.$args['label_cell_width'].'"' : '';
			$label_cell_props .= !empty($args['label_cell_colspan']) ? ' colspan="'.$args['label_cell_colspan'].'"' : '';
			$label_cell_props .= !empty($args['label_cell_colspan']) ? ' colspan="'.$args['label_cell_colspan'].'"' : '';
			
			$input_cell_props  = !empty($args['input_cell_width']) ? 'width="'.$args['input_cell_width'].'"' : '';
			$input_cell_props .= !empty($args['input_cell_colspan']) ? 'colspan="'.$args['input_cell_colspan'].'"' : '';
			$input_cell_props .= !empty($args['input_cell_style']) ? ' style="'.$args['input_cell_style'].'"' : '';*/
			
			$label_cell_props = !empty($args['label_cell_props']) ? ' '.$args['label_cell_props'] : '';
			$input_cell_props = !empty($args['input_cell_props']) ? ' '.$args['input_cell_props'] : '';
			
			?>
			<th <?php echo $label_cell_props ?> >
				<?php echo $flabel; echo $required_html; 
				if(isset($field['sub_label']) && !empty($field['sub_label'])){
					?>
                    <br /><span class="thwcfe-subtitle"><?php $this->_ewcfe($field['sub_label']); ?></span>
					<?php
				}
				?>
            </th>
            <?php 
				$tooltip = ( isset($field['hint_text']) && !empty($field['hint_text']) ) ? $field['hint_text'] : '';
				if($tooltip){
					$this->render_form_element_tooltip($tooltip);
				}else{
					$this->render_form_element_tooltip_empty(); 
				}
			?>
            <td <?php echo $input_cell_props ?> ><?php echo $field_html; ?></td>
            <?php
		}
	}
	
   /*******************************************
	*-------- HTML FORM FRAGMENTS - END   -----
	*******************************************/
	
} 

endif;