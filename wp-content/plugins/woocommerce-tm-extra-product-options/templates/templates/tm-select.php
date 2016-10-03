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
}else{
	$textbeforeprice='';
}
if (isset($textafterprice) && $textafterprice!=''){
	$textafterprice = '<span class="after-amount'.(!empty($hide_amount)?" ".$hide_amount:"").'">'.$textafterprice.'</span>';
}
if (!empty($class)){
	$fieldtype .=" ".$class;
}
if (!empty($changes_product_image)){
	$fieldtype .=" tm-product-image";
}
$li_class="";
if (!empty($li_class)){
	$li_class =" ".$li_class;
}
$element_data_attr_html = array();
if (!empty($element_data_attr) && is_array($element_data_attr)){
	foreach ($element_data_attr as $k => $v) {
		$element_data_attr_html[] = $k.'="'.esc_attr($v).'"';
	}
}
if (!empty($element_data_attr_html)){
	$element_data_attr_html = " ". implode(" ", $element_data_attr_html)." ";
}else{
	$element_data_attr_html = "";
}
?>
<li class="tmcp-field-wrap<?php echo $li_class;?>">
	<?php include('_quantity_start.php'); ?>
	<label for="<?php echo $id; ?>"></label>
	<select class="<?php echo $fieldtype;?> tm-epo-field tmcp-select" 
		name="<?php echo $name; ?>" 
		data-price="" 
		data-rules="" <?php echo $element_data_attr_html; ?>
		id="<?php echo $id; ?>" 
		tabindex="<?php echo $tabindex; ?>"  >
	<?php echo $options; ?>
	</select>
	<?php echo $textbeforeprice; ?>
	<span class="amount<?php if (!empty($hide_amount)){echo " ".$hide_amount;} ?>"><?php echo $amount; ?></span>
	<?php echo $textafterprice; ?>
	<?php include('_quantity_end.php'); ?>
</li>