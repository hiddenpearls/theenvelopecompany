<?php
// Direct access security
if (!defined('TM_EPO_PLUGIN_SECURITY')){
	die();
}
if (!isset($fieldtype)){
	$fieldtype="tmcp-field";
}
?>
<li class="tmcp-field-wrap">
	<?php include('_quantity_start.php'); ?>
	<?php 
	
   	$time_placeholder=$time_format;
	$time_mask=$time_format;
	$time_mask=str_replace('H', '0', $time_mask);
	$time_mask=str_replace('m', '0', $time_mask);
	$time_mask=str_replace('s', '0', $time_mask);
	if (is_rtl()){
		$time_mask=strrev($time_mask);
	}
	
	$input_type="text";

	$mask='data-mask="'.$time_mask.'" data-mask-placeholder="'.$time_placeholder.'" ';

	if (isset($textbeforeprice) && $textbeforeprice!=''){
		$textbeforeprice = '<span class="before-amount'.(!empty($hide_amount)?" ".$hide_amount:"").'">'.$textbeforeprice.'</span>';
	}
	if (isset($textafterprice) && $textafterprice!=''){
		$textafterprice = '<span class="after-amount'.(!empty($hide_amount)?" ".$hide_amount:"").'">'.$textafterprice.'</span>';
	}
	if (!empty($class)){
		$fieldtype .=" ".$class;
	}

	$get_default_value="";
if (TM_EPO()->tm_epo_global_reset_options_after_add=="no" && isset($_POST[$name])){
	$get_default_value= esc_attr(stripslashes($_POST[$name]));
}elseif (isset($_GET[$name])){
	$get_default_value= esc_attr(stripslashes($_GET[$name]));
}
$get_default_value=apply_filters('wc_epo_default_value',$get_default_value,isset($tm_element_settings)?$tm_element_settings:array());
	?>
	<label for="<?php echo $id; ?>" class="tm-epo-timepicker-label-container">
	<input type="<?php echo $input_type; ?>" class="<?php echo $fieldtype;?> tm-epo-field tmcp-time tm-epo-timepicker" 
	<?php echo $mask; ?> 
	data-min-time="<?php echo $min_time; ?>" 
	data-max-time="<?php echo $max_time; ?>" 
	data-time-format="<?php echo $time_format; ?>"
	data-time-theme="<?php echo $time_theme; ?>"
	data-time-theme-size="<?php echo $time_theme_size; ?>"
	data-time-theme-position="<?php echo $time_theme_position; ?>"
	data-price="" data-rules="<?php echo $rules; ?>" data-original-rules="<?php echo $original_rules; ?>" data-rulestype="<?php echo $rules_type; ?>" 
	id="<?php echo $id; ?>" tabindex="<?php echo $tabindex; ?>" 
	value="<?php echo $get_default_value; ?>" 
	name="<?php echo $name; ?>" /> 
	</label>
	<?php include('_price.php'); ?>
	<?php include('_quantity_end.php'); ?>
	<?php do_action( 'tm_after_element' , isset($tm_element_settings)?$tm_element_settings:array() ); ?>
</li>