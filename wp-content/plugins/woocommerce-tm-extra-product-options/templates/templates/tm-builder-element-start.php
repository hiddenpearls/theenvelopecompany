<?php
// Direct access security
if (!defined('TM_EPO_PLUGIN_SECURITY')){
	die();
}
if ( !empty( $class ) ) {
	$class=" ".$class;
	$divclass=$class."-div";
	$ulclass=$class."-ul";
}else{
	$class="";
	$divclass="";
	$ulclass="";
}

if (!$haslogic){
	$logic="";
}
if (!empty($exactlimit)){
	$exactlimit =" ".$exactlimit;
}else{
	$exactlimit="";
}
if (!empty($minimumlimit)){
	$minimumlimit =" ".$minimumlimit;
}else{
	$minimumlimit="";
}
$tm_product_id_class="";
if (!empty($tm_product_id)){
	$tm_product_id_class=" tm-product-id-".$tm_product_id;
}
?>
<div data-uniqid="<?php echo $uniqid;?>" 
	data-logic="<?php echo $logic;?>" 
	data-haslogic="<?php echo $haslogic;?>" 
	class="cpf_hide_element tm-cell <?php echo $column; ?> cpf-type-<?php echo $type.$divclass.$tm_product_id_class; ?>">
<?php
$use=" ".$class_id;
if (!empty($use_images)){
	switch ($use_images){
	case "images":
		$use .=" use_images_containter";
		break;
	}
}
if (!empty($use_url)){
	switch ($use_url){
	case "url":
		$use .=" use_url_containter";
		break;
	}
}
if ($tm_element_settings['type']=='radio'){
	switch (TM_EPO()->tm_epo_global_radio_undo_button) {
		case 'enable':
			$clear_options='yes';
			break;
		case 'disable':
			$clear_options='';
			break;
	}	
}

if (!empty($tm_undo_button) || !empty($clear_options)){
	$class .=" ".'tm-has-undo-button';
}
if($required){
	$class .=" ".'tm-has-required';
}
if (empty($title) && !empty($required)){
	$title='&nbsp;';
}
if ($element!="divider"){
	if ((!empty($title) && $title_position!="disable") || !empty($required) || !empty($clear_options)){
		echo '<'.$title_size;
		if(!empty($title_color)){
			echo ' style="color:'.$title_color.'"';
		}
		if ( $element=='header' && !empty( $class ) ) {
			$class=" ".$class;
		}
		if(!empty($description) && $description_position=="tooltip"){
			$class=" tm-tooltip";
		}
		if (!empty($title_position)){
			$class .=" tm-".$title_position;
		}
		if(!empty($description) && !empty($description_position) && $description_position=="tooltip"){
			echo ' data-tm-tooltip-swatch="on"';
		}		
		echo ' class="tm-epo-field-label'.$class.'">';
		
		if($required && !empty(TM_EPO()->tm_epo_global_required_indicator) && TM_EPO()->tm_epo_global_required_indicator_position=='left'){
			echo '<span class="tm-epo-required">'.TM_EPO()->tm_epo_global_required_indicator.'</span>&nbsp;';
		}
		if(!empty($title) && $title_position!="disable"){
			echo $title;
		}else{
			echo "&nbsp;";
		}
		if($required && !empty(TM_EPO()->tm_epo_global_required_indicator) && TM_EPO()->tm_epo_global_required_indicator_position=='right'){
			echo '&nbsp;<span class="tm-epo-required">'.TM_EPO()->tm_epo_global_required_indicator.'</span>';
		}
		
		if (!empty($tm_undo_button)){
			echo $tm_undo_button;
		}
		if (!empty($clear_options)){
			echo '<span class="tm-epo-reset-radio">'.apply_filters('tm_undo_radio_text','<i class="fa fa-undo"></i>').'</span>';
		}
		echo '</'.$title_size.'>';
	}
	if(!empty($description) && (empty($description_position) || $description_position=="tooltip") ){
		echo'<div'; 
		if(!empty($description_color)){
			echo ' style="color:'.$description_color.'"';
		}
		echo' class="tm-description'.($description_position=="tooltip"?" tm-tip-html":"").'">'.do_shortcode($description).'</div>';
	}

}
echo $divider;
if (!in_array($element,array('header','divider'))){
?>
	<div class="tm-extra-product-options-container">
        <ul data-rules="<?php echo $rules;?>" 
        	data-rulestype="<?php echo $rules_type; ?>" 
        	<?php if(!empty($tm_validation)){?>data-tm-validation="<?php echo $tm_validation; ?>" <?php } ?>
        	class="tmcp-ul-wrap tmcp-elements tm-extra-product-options-<?php echo $use.$ulclass.$exactlimit.$minimumlimit; ?>">
<?php 
}
?>