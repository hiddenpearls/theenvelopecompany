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
?>
<li class="tmcp-field-wrap">
	<?php include('_quantity_start.php'); ?>
	<label for="<?php echo $id; ?>"></label>
	<input<?php 
	if (isset($placeholder)){
		echo ' placeholder="'.$placeholder.'"';
	}
	?> class="<?php echo $fieldtype;?> tm-color-picker tm-epo-field tmcp-textfield" 
	name="<?php echo $name; ?>" 
	data-show-input="true" 
    data-show-initial="true" 
    data-allow-empty="true" 
    data-show-alpha="false"
  	data-show-palette="false"
  	data-clickout-fires-change="true"
  	data-show-buttons="false"
  	data-preferred-format="hex"
	data-price="" 
	data-rules="<?php echo $rules; ?>" 
	data-rulestype="<?php echo $rules_type; ?>" 
	value="<?php 
	if (isset($_POST[$name])){
		echo esc_attr(stripslashes($_POST[$name]));
	}elseif (isset($_GET[$name])){
		echo esc_attr(stripslashes($_GET[$name]));
	}elseif(isset($default_value)){
		echo esc_attr($default_value);
	}
	?>" 
	id="<?php echo $id; ?>" 
	tabindex="<?php echo $tabindex; ?>" 
	type="text" />
	<?php echo $textbeforeprice; ?>
	<span class="amount<?php if (!empty($hide_amount)){echo " ".$hide_amount;} ?>"><?php echo $amount; ?></span>
	<?php echo $textafterprice; ?>
	<?php include('_quantity_end.php'); ?>
</li>