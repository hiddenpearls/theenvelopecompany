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
	$textafterprice = '<span class="after-amount">'.$textafterprice.'</span>';
}
if (!empty($class)){
	$fieldtype .=" ".$class;
}
?>
<li class="tmcp-field-wrap">
	<?php include('_quantity_start.php'); ?>
	<?php 
	$upload_text="";
	switch ($style){
	case "":
		$style='';
		break;
	case "button":
		$style=' class="cpf-upload-container"';
		$upload_text='<span>'.__( 'Select file', 'woocommerce-tm-extra-product-options' ).'</span>';
		break;
	}
	?>
	<label for="<?php echo $id; ?>"<?php echo $style; ?>><?php echo $upload_text; ?>
	<input type="file" class="<?php echo $fieldtype;?> tm-epo-field tmcp-upload" 
	data-price="" 
	data-rules="<?php echo $rules; ?>" 
	data-original-rules="<?php echo $original_rules; ?>" 
	data-rulestype="<?php echo $rules_type; ?>" 
	id="<?php echo $id; ?>" 
	tabindex="<?php echo $tabindex; ?>"
	name="<?php echo $name; ?>" /> 
	</label>
	<small><?php echo sprintf( __( '(max file size %s)', 'woocommerce-tm-extra-product-options' ), $max_size ) ?></small>
	<?php include('_price.php'); ?>
	<?php include('_quantity_end.php'); ?>
	<?php do_action( 'tm_after_element' , isset($tm_element_settings)?$tm_element_settings:array() ); ?>
</li>