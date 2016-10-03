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
if (!isset($default_value)){
	$default_value="";
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
<li class="tmcp-field-wrap">
	<?php include('_quantity_start.php'); ?>
	<label for="<?php echo $id; ?>"></label>
	<input<?php 
	if (isset($placeholder)){
		echo ' placeholder="'.$placeholder.'"';
	}
	if (isset($min_chars) && $min_chars!=''){
		echo ' minlength="'.$min_chars.'"';
	}
	if (isset($max_chars) && $max_chars!=''){
		echo ' maxlength="'.$max_chars.'"';
	}
	?> class="<?php echo $fieldtype;?> tm-epo-field tmcp-textfield" 
	name="<?php echo $name; ?>" 
	data-price="" 
	data-rules="<?php echo $rules; ?>" 
	data-original-rules="<?php echo $original_rules; ?>" 
	data-rulestype="<?php echo $rules_type; ?>" 
	data-freechars="<?php echo $freechars; ?>" 
	value="<?php echo $get_default_value; ?>"  
	id="<?php echo $id; ?>" 
	tabindex="<?php echo $tabindex; ?>" 
	type="<?php echo $input_type; ?>" />
	<?php include('_price.php'); ?>
	<?php include('_quantity_end.php'); ?>
	<?php do_action( 'tm_after_element' , isset($tm_element_settings)?$tm_element_settings:array() ); ?>
</li>