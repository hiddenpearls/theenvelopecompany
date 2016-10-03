<?php
// Direct access security
if (!defined('TM_EPO_PLUGIN_SECURITY')){
	die();
}
if (!isset($fieldtype)){
	$fieldtype="tmcp-field";
}
if (isset($textbeforeprice) && $textbeforeprice!=''){
	$textbeforeprice = '<span class="before-amount'.(!empty($hide_amount)?" ".$hide_amount:"").'">'.$textbeforeprice.'</span>';
}
if (isset($textafterprice) && $textafterprice!=''){
	$textafterprice = '<span class="after-amount'.(!empty($hide_amount)?" ".$hide_amount:"").'">'.$textafterprice.'</span>';
}
if (!empty($class)){
	$fieldtype .=" ".$class;
}

if ($min!='' && $default_value==''){
	$default_value=$min;
}

$get_default_value="";
if (TM_EPO()->tm_epo_global_reset_options_after_add=="no" && isset($_POST[$name])){
	$get_default_value= esc_attr(stripslashes($_POST[$name]));
}elseif (isset($_GET[$name])){
	$get_default_value= esc_attr(stripslashes($_GET[$name]));
}else{
	$get_default_value= $default_value;
}
$get_default_value=apply_filters('wc_epo_default_value',$get_default_value,isset($tm_element_settings)?$tm_element_settings:array());
?>
<li class="tmcp-field-wrap<?php if(!empty($show_picker_value))echo " tm-show-picker-".$show_picker_value; ?>">
	<?php include('_quantity_start.php'); ?>
	<div 
	class="tm-range-picker<?php if ($pips=="yes")echo " pips"; ?>" 
	data-min="<?php echo $min; ?>" 
	data-max="<?php echo $max; ?>" 
	data-step="<?php echo $step; ?>" 
	data-pips="<?php echo $pips; ?>" 
	data-noofpips="<?php echo $noofpips; ?>" 
	data-show-picker-value="<?php echo $show_picker_value; ?>" 
	data-field-id="<?php echo $id; ?>" 
	data-start="<?php 
	if (isset($_POST[$name])){
		echo esc_attr(($_POST[$name]));
	}elseif (isset($_GET[$name])){
		echo esc_attr(stripslashes($_GET[$name]));
	}elseif(isset($default_value)){
		echo $default_value;
	}else{
		echo $min;
	}
	?>" 
	></div>
	<label class="tm-show-picker-value" for="<?php echo $id; ?>"></label>
	<input<?php 
	if (isset($placeholder)){
		echo ' placeholder="'.$placeholder.'"';
	}
	if (isset($max_chars) && $max_chars!=''){
		echo ' maxlength="'.$max_chars.'"';
	}
	?> class="<?php echo $fieldtype;?> tm-epo-field tmcp-textfield tmcp-range" 
	name="<?php echo $name; ?>" 
	data-price="" 
	data-rules="<?php echo $rules; ?>" 
	data-original-rules="<?php echo $original_rules; ?>" 
	data-rulestype="<?php echo $rules_type; ?>" 
	value="<?php echo $get_default_value; ?>" 
	id="<?php echo $id; ?>" 
	tabindex="<?php echo $tabindex; ?>" 
	type="hidden" />
	<?php include('_price.php'); ?>
	<?php include('_quantity_end.php'); ?>
	<?php do_action( 'tm_after_element' , isset($tm_element_settings)?$tm_element_settings:array() ); ?>
</li>