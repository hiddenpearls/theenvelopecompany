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
?>
<div data-cart-id="<?php echo $forcart;?>" data-product-id="<?php echo $product_id;?>" class="tc-extra-product-options tm-extra-product-options tm-custom-prices tm-product-id-<?php echo $product_id;?> <?php echo $classcart;?>" id="tm-extra-product-options<?php echo $form_prefix;?>">
    <div class="tm-extra-product-options-inner">
        <ul id="tm-extra-product-options-fields" class="tm-extra-product-options-fields">                            