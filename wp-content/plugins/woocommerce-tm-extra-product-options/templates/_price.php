<?php
// Direct access security
//@youareheremedia.com - page customized
if (!defined('TM_EPO_PLUGIN_SECURITY')){
	die();
}

if (isset($textbeforeprice) && isset($textafterprice) && isset($hide_amount) && isset($amount) && isset($original_amount)){

	echo $textbeforeprice;

	echo '<span id="yahm-tm-price-'.$name.'" class="price tc-price';

	if (!empty($hide_amount)){
		echo " ".$hide_amount;
	}
	echo '"><span id="yahm-tm-price-'.$name.'" class="amount">'.$amount.'</span></span>';

	echo $textafterprice;

	if(isset($tm_element_settings) & isset($field_counter)){
		if(!empty($tm_element_settings['cdescription'][$field_counter]) || (count($tm_element_settings['cdescription'])>1 && $tm_element_settings['type']=='select') ){
			echo '<i data-tm-tooltip-html="'.esc_attr(do_shortcode($tm_element_settings['cdescription'][$field_counter])).'" class="tm-tooltip tc-tooltip tcfa tcfa-question-circle"></i>';
		}
	}

}