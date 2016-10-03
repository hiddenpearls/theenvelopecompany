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

?>
<li class="tmcp-field-wrap">
	<?php include('_quantity_start.php'); ?>
	<label for="<?php echo $id; ?>"></label>
	<textarea<?php 
	if (isset($placeholder)){
		echo ' placeholder="'.$placeholder.'"';
	}
	if (isset($min_chars) && $min_chars!=''){
		echo ' maxlength="'.$min_chars.'"';
	}
	if (isset($max_chars) && $max_chars!=''){
		echo ' maxlength="'.$max_chars.'"';
	}
	?> class="<?php echo $fieldtype;?> tm-epo-field tmcp-textarea" name="<?php echo $name; ?>" data-price="" data-rules="<?php echo $rules; ?>" data-rulestype="<?php echo $rules_type; ?>" id="<?php echo $id; ?>" tabindex="<?php echo $tabindex; ?>" rows="5" cols="20"><?php 
    if(isset($_POST[$name])){
    	echo stripslashes($_POST[$name]);
    }elseif (isset($_GET[$name])){
		echo esc_attr(stripslashes($_GET[$name]));
	}else{
		echo $default_value;
	}
	?></textarea>
	<?php echo $textbeforeprice; ?>
	<span class="amount<?php if (!empty($hide_amount)){echo " ".$hide_amount;} ?>"><?php echo $amount; ?></span>
	<?php echo $textafterprice; ?>
	<?php include('_quantity_end.php'); ?>
</li>