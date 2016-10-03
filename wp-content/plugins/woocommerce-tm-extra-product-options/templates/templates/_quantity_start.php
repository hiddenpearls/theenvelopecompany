<?php
// Direct access security
if (!defined('TM_EPO_PLUGIN_SECURITY')){
	die();
}
if (!empty($quantity)){
	$_quantity=1;
	if (isset($_POST[$name.'_quantity'])){
		$_quantity= esc_attr(stripslashes($_POST[$name.'_quantity']));
	}elseif (isset($_GET[$name.'_quantity'])){
		$_quantity= esc_attr(stripslashes($_GET[$name.'_quantity']));
	}
	echo '<div class="tm-quantity tm-'.$quantity.'">
			<input type="button" value="-" class="minus">
			<input type="number" min="1" step="1" name="'. $name.'_quantity" value="'.$_quantity.'" title="'.__('quantity', TM_EPO_TRANSLATION).'" class="tm-qty" size="4">
			<input type="button" value="+" class="plus">
			</div>';
	echo '<div class="tm-field-display">';
}