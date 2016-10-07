<?php
/**
 * WooCommerce Checkout Field Editor Pro - Settings Page
 *
 * @author      ThemeHiGH
 * @category    Admin
 */

if(!defined('ABSPATH')){ exit; }

if(!class_exists('WCFE_Settings_Page')) :

abstract class WCFE_Settings_Page extends WCFE_Checkout_Fields_Admin_Utils{
	protected $page_id    = '';	
	protected $section_id = '';
	
	protected $tabs = '';
	protected $sections = '';

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->tabs = array( 'fields' => 'Checkout Fields', 'advanced_settings' => 'Advanced Settings');
	}
	
	public function get_tabs(){
		return $this->tabs;
	}

	public function get_current_tab(){
		return $this->page_id;
	}
	
	public function get_current_section(){
		return isset( $_GET['section'] ) ? esc_attr( $_GET['section'] ) : $this->section_id;
	}
	
	public function output_tabs(){
		$current_tab = $this->get_current_tab();
		$tabs = $this->get_tabs();

		if(empty($tabs)){
			return;
		}
		
		echo '<h2 class="nav-tab-wrapper woo-nav-tab-wrapper">';
		foreach( $tabs as $id => $label ){
			$active = ( $current_tab == $id ) ? 'nav-tab-active' : '';
			$label  = $this->__wcfe($label);
			echo '<a class="nav-tab '.$active.'" href="'. $this->get_admin_url($id) .'">'.$label.'</a>';
		}
		echo '</h2>';		
	}
	
	public function output_sections() {
		$current_section = $this->get_current_section();
		$sections = $this->get_sections();

		if(empty($sections)){
			return;
		}
		
		$array_keys = array_keys( $sections );
		
		echo '<ul class="thpladmin-sections">';
		foreach( $sections as $id => $label ){
			$label = $this->__wcfe($label);
			$url = $this->get_admin_url($this->page_id, sanitize_title($id));	
			echo '<li><a href="'. $url .'" class="'. ( $current_section == $id ? 'current' : '' ) .'">'. $label .'</a> '. (end( $array_keys ) == $id ? '' : '|') .' </li>';
		}		
		echo '</ul>';
	}	
	
	public function get_admin_url($tab = false, $section = false){
		$url = 'admin.php?page=th_checkout_field_editor_pro';
		if($tab && !empty($tab)){
			$url .= '&tab='. $tab;
		}
		if($section && !empty($section)){
			$url .= '&section='. $section;
		}
		return admin_url($url);
	}
	
   /*******************************************
	*-------- HTML FORM FRAGMENTS - START -----
	*******************************************/
	
	public function render_form_element_tooltip($tooltip){
		$tooltip_html = '';
		
		if($tooltip){
			$icon = TH_WCFE_ASSETS_URL.'/css/help.png';
			$tooltip_html = '<a href="javascript:void(0)" title="'. $tooltip .'" class="thpladmin_tooltip"><img src="'. $icon .'" alt="" title=""/></a>';
		}
		?>
        <td style="width: 16px; padding:0px;"><?php echo $tooltip_html; ?></td>
        <?php
	}
	
	public function render_form_element_empty_cell(){
		?>
		<td width="13%">&nbsp;</td>
        <?php $this->render_form_element_tooltip(false); ?>
        <td width="34%">&nbsp;</td>
        <?php
	}
	
	public function render_form_element_h_separator($padding = 5, $colspan = 6){
		?>
        <tr><td colspan="<?php echo $colspan; ?>" style="border-bottom: 1px dashed #e6e6e6; padding-top: <?php echo $padding ?>px;"></td></tr>
        <?php
	}
	
	public function render_form_element_h_spacing($padding = 5, $colspan = 6){
		?>
        <tr><td colspan="<?php echo $colspan; ?>" style="padding-top:<?php echo $padding ?>px;"></td></tr>
        <?php
	}
	
	public function render_form_field_element($field, $atts=array(), $render_cell=true){
		if($field && is_array($field)){
			$ftype = isset($field['type']) ? $field['type'] : 'text';
			
			if($ftype == 'checkbox'){
				$this->render_form_field_element_checkbox($field, $atts, $render_cell);
				return true;
			}
		
			$args = shortcode_atts( array(
				'label_cell_props' => '',
				'input_cell_props' => '',
				'label_cell_th' => false,
				'input_width' => '',
				'input_name_prefix' => 'i_'
			), $atts );
			
			$fname  = $args['input_name_prefix'].$field['name'];
			$flabel = $this->__wcfe($field['label']);
			$fvalue = isset($field['value']) ? $field['value'] : '';
						
			$input_width  = $args['input_width'] ? 'width:'.$args['input_width'].';' : '';
			$field_props  = 'name="'. $fname .'" value="'. $fvalue .'" style="'. $input_width .'"';
			$field_props .= ( isset($field['placeholder']) && !empty($field['placeholder']) ) ? ' placeholder="'.$field['placeholder'].'"' : '';
			
			$required_html = ( isset($field['required']) && $field['required'] ) ? '<abbr class="required" title="required">*</abbr>' : '';
			$field_html = '';
			
			if(isset($field['onchange']) && !empty($field['onchange'])){
				$field_props .= ' onchange="'.$field['onchange'].'"';
			}
			
			if($ftype == 'text'){
				$field_html = '<input type="text" '. $field_props .' />';
				
			}else if($ftype == 'select'){
				$field_html = '<select '. $field_props .' >';
				foreach($field['options'] as $value=>$label){
					$selected = $value === $fvalue ? 'selected' : '';
					$field_html .= '<option value="'. trim($value) .'" '.$selected.'>'. $this->__wcfe($label) .'</option>';
				}
				$field_html .= '</select>';
				
			}else if($ftype == 'multiselect'){
				$field_html = '<select multiple="multiple" '. $field_props .' class="thwcfe-enhanced-multi-select" >';
				foreach($field['options'] as $value=>$label){
					//$selected = $value === $fvalue ? 'selected' : '';
					$field_html .= '<option value="'. trim($value) .'" >'. $this->__wcfe($label) .'</option>';
				}
				$field_html .= '</select>';
				
			}else if($ftype == 'colorpicker'){
				$field_html  = '<span class="thpladmin-colorpickpreview '.$field['name'].'_preview" style=""></span>';
                $field_html .= '<input type="text" '. $field_props .' class="thpladmin-colorpick"/>';              
            
			}
			
			$label_cell_props = !empty($args['label_cell_props']) ? ' '.$args['label_cell_props'] : '';
			$input_cell_props = !empty($args['input_cell_props']) ? ' '.$args['input_cell_props'] : '';
			?>
            
			<td <?php echo $label_cell_props ?> > <?php 
				echo $flabel; echo $required_html; 
				
				if(isset($field['sub_label']) && !empty($field['sub_label'])){
					?>
                    <br /><span class="thpladmin-subtitle"><?php $this->_ewcfe($field['sub_label']); ?></span>
					<?php
				}
				?>
            </td>
            
            <?php 
			$tooltip = ( isset($field['hint_text']) && !empty($field['hint_text']) ) ? $field['hint_text'] : false;
			$this->render_form_element_tooltip($tooltip);
			?>
            
            <td <?php echo $input_cell_props ?> ><?php echo $field_html; ?></td>
            
            <?php
		}
	}
	
	public function render_form_field_element_checkbox($field, $atts=array(), $render_cell=false){
		$args = shortcode_atts( array( 'cell_props'  => '', 'input_props' => '', 'label_props' => '', 'name_prefix' => 'i_', 'id_prefix' => 'a_f' ), $atts );
		
		$fid    = $args['id_prefix'].$field['name'];
		$fname  = $args['name_prefix'].$field['name'];
		$fvalue = isset($field['value']) ? $field['value'] : '';
		$flabel = $this->__wcfe($field['label']);
		
		$field_props  = 'id="'. $fid .'" name="'. $fname .'"';
		$field_props .= !empty($fvalue) ? ' value="'. $fvalue .'"' : '';
		$field_props .= $field['checked'] ? ' checked' : '';
		$field_props .= $args['input_props'];
		$field_props .= isset($field['onchange']) && !empty($field['onchange']) ? ' onchange="'.$field['onchange'].'"' : '';
		
		$field_html  = '<input type="checkbox" '. $field_props .' />';
		$field_html .= '<label for="'. $fid .'" '. $args['label_props'] .' > '. $flabel .'</label>';
		
		if($render_cell){
		?>
			<td <?php echo $args['cell_props']; ?> ><?php echo $field_html; ?></td>
		<?php 
		}else{
		?>
			<?php echo $field_html; ?>
		<?php 
		}
	}
	
   /*******************************************
	*-------- HTML FORM FRAGMENTS - END   -----
	*******************************************/
}

endif;