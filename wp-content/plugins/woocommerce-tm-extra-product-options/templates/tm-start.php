<?php
// Direct access security
if (!defined('TM_EPO_PLUGIN_SECURITY')){
	die();
}
$forcart="main";
$classcart="tm-cart-main";
$form_prefix=str_replace("_", "", $form_prefix);
if (!empty($form_prefix)){
	$forcart=$form_prefix;
	$classcart="tm-cart-".$form_prefix;
}
$isfromshortcode="";
if (!empty($is_from_shortcode)){
	$isfromshortcode = " tc-shortcode";
}
?>
<div 
data-epo-id="<?php echo $epo_internal_counter;?>" 
data-cart-id="<?php echo $forcart;?>" 
data-product-id="<?php echo $product_id;?>" 
class="tc-extra-product-options tm-extra-product-options tm-custom-prices tc-clearfix tm-product-id-<?php echo $product_id;?> <?php echo $classcart;?><?php echo $isfromshortcode;?>" 
id="tm-extra-product-options<?php echo $form_prefix;?>">
    <div class="tm-extra-product-options-inner">
        <ul id="tm-extra-product-options-fields" class="tm-extra-product-options-fields">                            