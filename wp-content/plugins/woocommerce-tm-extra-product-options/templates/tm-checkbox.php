<?php
// Direct access security
//@youareheremedia.com - page customized
if (!defined('TM_EPO_PLUGIN_SECURITY')){
	die();
}

if (!isset($fieldtype)){
	$fieldtype="tmcp-field";
}
?>
<?php
if (!isset($border_type)){
	$border_type="";
}
$use="";
if (!empty($use_images)){
	switch ($use_images){
	case "images":
		$use=" use_images";
		if (!empty($image)){
			$swatch="";
			$swatch_class="";
			if ($swatchmode=='swatch'){
				$swatch_class=" tm-tooltip";
				$swatch=' '.'data-tm-tooltip-swatch="on"';
			}
			elseif ($swatchmode=='swatch_desc'){
			  $swatch_class=" tm-tooltip";
			  $swatch=' '.'data-tm-tooltip-swatch-desc="on"';
			}
			elseif ($swatchmode=='swatch_lbl_desc'){
			  $swatch_class=" tm-tooltip";
			  $swatch=' '.'data-tm-tooltip-swatch-lbl-desc="on"';
			}
			elseif ($swatchmode=='swatch_img'){
			  $swatch_class=" tm-tooltip";
			  $swatch=' '.'data-tm-tooltip-swatch-img="on"';
			}
			elseif ($swatchmode=='swatch_img_lbl'){
			  $swatch_class=" tm-tooltip";
			  $swatch=' '.'data-tm-tooltip-swatch-img-lbl="on"';
			}
			elseif ($swatchmode=='swatch_img_desc'){
			  $swatch_class=" tm-tooltip";
			  $swatch=' '.'data-tm-tooltip-swatch-img-desc="on"';
			}
			elseif ($swatchmode=='swatch_img_lbl_desc'){
			  $swatch_class=" tm-tooltip";
			  $swatch=' '.'data-tm-tooltip-swatch-img-lbl-desc="on"';
			}
			if ($tm_epo_no_lazy_load=='no'){
				$altsrc='data-original="'.$image.'"';
			}else{
				$altsrc='src="'.$image.'"';
			}
			if(!empty($use_lightbox) && $use_lightbox=="lightbox"){
				$swatch_class .= " tc-lightbox-image";
			}
			$label='<img class="tmlazy '.$border_type.' checkbox_image'.$swatch_class.'" alt="'.esc_attr($label).'" '.$altsrc.$swatch.' />'.'<span class="tc-label checkbox_image_label">'.$label.'</span>';
		}else{
			// check for hex color
			$search_for_color = $label;
			if (isset($color)){
				$search_for_color = $color;
				if(empty($search_for_color)){
					$search_for_color = 'transparent';
				}
			}
			if($search_for_color == 'transparent' || preg_match('/#([a-f]|[A-F]|[0-9]){3}(([a-f]|[A-F]|[0-9]){3})?\b/', $search_for_color)){ //hex color is valid
				$swatch="";
				$swatch_class="";
				if ($swatchmode=='swatch'){
					$swatch_class=" tm-tooltip";
					$swatch=' '.'data-tm-tooltip-swatch="on"';
				}
				if($search_for_color == 'transparent'){
					$swatch_class .=" tm-transparent-swatch";
				}
				$label='<span class="tmhexcolorimage '.$border_type.' checkbox_image'.$swatch_class.'" alt="'.esc_attr((!isset($color))?$search_for_color:'').'" '.$swatch.'></span>'.'<span class="tc-label checkbox_image_label">'.((!isset($color))?$search_for_color:'').'</span>';
			}
		}
		break;
	case "start":
		if (!empty($image)){
			if ($tm_epo_no_lazy_load=='no'){
				$altsrc='data-original="'.$image.'"';
			}else{
				$altsrc='src="'.$image.'"';
			}
			$label='<img class="tmlazy tc-checkbox-image" alt="'.esc_attr($label).'" '.$altsrc.' /><span class="tc-label">'.$label.'</span>';
		}
		break;
	
	case "end":
		if (!empty($image)){
			if ($tm_epo_no_lazy_load=='no'){
				$altsrc='data-original="'.$image.'"';
			}else{
				$altsrc='src="'.$image.'"';
			}
			$label='<span class="tc-label">'.$label.'</span><img class="tmlazy tc-checkbox-image" alt="'.esc_attr($label).'" '.$altsrc.' />';
		}
		break;
	}
}
if (!empty($li_class)){
	$li_class =" ".$li_class;
}else{
	$li_class = "";
}
if (!empty($items_per_row)){
	$li_class .=" tm-per-row";
}

if (!empty($class)){
	$fieldtype .=" ".$class;
}
if (!empty($changes_product_image)){
	$fieldtype .=" tm-product-image";
}
if (!empty($changes_product_image) && $changes_product_image=="images"){
	$imagep = '';
}

if (empty($limit)){
	$limit="";
}

if (isset($textbeforeprice) && $textbeforeprice!=''){
	$textbeforeprice = '<span class="before-amount'.(!empty($hide_amount)?" ".$hide_amount:"").'">'.$textbeforeprice.'</span>';
}else{
	$textbeforeprice='';
}

if (isset($textafterprice) && $textafterprice!=''){
	$textafterprice = '<span class="after-amount'.(!empty($hide_amount)?" ".$hide_amount:"").'">'.$textafterprice.'</span>';
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
if (empty($image)){
	$image = '';
}
if (empty($imagep) || empty($changes_product_image)){
	$imagep = '';
}

$labelclass='';
$labelclass_start='';
$labelclass_end='';
if (TM_EPO()->tm_epo_css_styles=="on" && (empty($use_images) || (isset($use_images) && $use_images!="images" )) ){
	$labelclass=' class="tc-label tm-epo-style '.TM_EPO()->tm_epo_css_styles_style.'"';
	$labelclass_start='<span class="tm-epo-style-wrapper '.TM_EPO()->tm_epo_css_styles_style.'">';
	$labelclass_end='</span>';
}

?>
<li class="tmcp-field-wrap<?php echo $grid_break.$li_class;?>">
	<?php include('_quantity_start.php'); ?>
	<?php echo $labelclass_start; ?>
	<input class="<?php echo $fieldtype;?> tm-epo-field tmcp-checkbox<?php echo $use; ?>" 
	name="<?php echo $name; ?>" 
	data-limit="<?php echo $limit; ?>" 
	data-exactlimit="<?php echo $exactlimit; ?>" 
	data-minimumlimit="<?php echo $minimumlimit; ?>" 
	data-image="<?php echo $image; ?>" 
	data-imagep="<?php echo $imagep; ?>" 
	data-imagel="<?php echo $imagel; ?>" 
	data-price="" 
	data-rules="<?php echo $rules; ?>" 
	data-original-rules="<?php echo $original_rules; ?>" 
	data-rulestype="<?php echo $rules_type; ?>" 
	value="<?php echo $value; ?>" 
	id="<?php echo $id; ?>" 
	tabindex="<?php echo $tabindex; ?>" 
	type="checkbox" 
	<?php echo $element_data_attr_html; ?>
	<?php checked( $checked, true ); ?> />
	<?php 
	if (empty($use_images) || (isset($use_images) && $use_images!="images" )){
		if (!empty($labelclass)){
			echo '<label'.$labelclass.' for="'.$id.'"></label>';	
		}		
		echo $labelclass_end;
		if ($label!==''){
			echo '<label for="'.$id.'"><span class="tc-label tm-label">'.$label.'</span></label>';
		}
	}else{
		if ($label!==''){
			echo '<label'.$labelclass.' for="'.$id.'">'.$label.'</label>';
		}		
		echo $labelclass_end;
	}
	?>
	<?php include('_price.php'); ?>
	<?php include('_quantity_end.php'); ?>
	<?php do_action( 'tm_after_element' , isset($tm_element_settings)?$tm_element_settings:array() ); ?>
</li>