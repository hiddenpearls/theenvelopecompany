<?php
// Direct access security
if (!defined('TM_EPO_PLUGIN_SECURITY')){
	die();
}
$forcart="main";
$classcart="tm-cart-main";
$classtotalform="tm-totals-form-main";
$form_prefix_id=str_replace("_", "", $form_prefix);
if (!empty($form_prefix)){
	$forcart=$form_prefix_id;
	$classcart="tm-cart-".$form_prefix_id;
	$classtotalform="tm-totals-form-".$form_prefix_id;
}
?>
<div class="<?php echo $classtotalform;?>" data-product-id="<?php echo $product_id;?>"><input 
type="hidden" 
value="<?php echo $price;?>" 
name="cpf_product_price<?php echo $form_prefix;?>" 
class="cpf-product-price" />
<div 
id="tm-epo-totals<?php echo $form_prefix;?>" 
class="tm-epo-totals tm-custom-prices-total<?php echo $hidden;?> <?php echo $classcart;?>" 
data-theme-name="<?php echo $theme_name;?>"
data-cart-id="<?php echo $forcart;?>"
data-is-subscription="<?php echo $is_subscription;?>" 
data-is-sold-individually="<?php echo $is_sold_individually;?>" 
data-type="<?php echo $type;?>" 
data-price="<?php echo $price;?>"
data-product-price-rules="<?php echo $product_price_rules;?>" 
data-fields-price-rules="<?php echo $fields_price_rules;?>" 
data-tm-epo-dpd-prefix="<?php echo $tm_epo_dpd_prefix;?>" 
data-tm-epo-dpd-suffix="<?php echo $tm_epo_dpd_suffix;?>" 
data-force-quantity="<?php echo $force_quantity;?>" 
data-taxable="<?php echo $taxable;?>" 
data-tax-rate="<?php echo $tax_rate;?>" 
data-tax-string="<?php echo $tax_string;?>" 
data-tax-display-mode="<?php echo $tax_display_mode;?>" 
data-prices-include-tax="<?php echo $prices_include_tax;?>" 
data-subscription-sign-up-fee="<?php echo $subscription_sign_up_fee;?>" 
data-variations-subscription-sign-up-fee="<?php echo $variations_subscription_sign_up_fee;?>" 
data-subscription-period="<?php echo $subscription_period;?>" 
data-variations-subscription-period="<?php echo $variations_subscription_period;?>" 
data-variations="<?php echo $variations;?>"></div></div>