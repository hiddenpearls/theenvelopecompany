<?php
// Direct access security
if (!defined('TM_EPO_PLUGIN_SECURITY')){
	die();
}
if (!isset($fieldtype)){
	$fieldtype="tmcp-field";
}
?>
<?php
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
			if (!empty($show_label)){
				switch ($show_label) {
					case 'hide':
						$swatch_class .=" tm-hide-label";
						break;
					case 'bottom':
						$swatch_class .=" tm-bottom-label";
						break;
					case 'inside':
						$swatch_class .=" tm-inside-label";
						break;					
					case 'tooltip':
						$swatch_class .=" tm-tooltip";
						$swatch=' '.'data-tm-tooltip-swatch="on"';
						break;					
				}
			}
			if ($tm_epo_no_lazy_load=='no'){
				$altsrc='data-original="'.$image.'"';
			}else{
				$altsrc='src="'.$image.'"';
			}
			$label='<img class="tmlazy '.$border_type.' radio_image'.$swatch_class.'" alt="" '.$altsrc.$swatch.' />'.'<span class="checkbox_image_label">'.$label.'</span>';
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
				if (!empty($show_label)){
					switch ($show_label) {
						case 'hide':
							$swatch_class .=" tm-hide-label";
							break;
						case 'bottom':
							$swatch_class .=" tm-bottom-label";
							break;
						case 'inside':
							$swatch_class .=" tm-inside-label";
							break;					
						case 'tooltip':
							$swatch_class .=" tm-tooltip";
							$swatch=' '.'data-tm-tooltip-swatch="on"';
							break;					
					}
				}
				$label='<span class="tmhexcolorimage '.$border_type.' checkbox_image'.$swatch_class.'" alt="" '.$swatch.'></span>'.'<span class="checkbox_image_label">'.((!isset($color))?$search_for_color:$label).'</span>';
			}
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

if (!empty($use_url)){
	switch ($use_url){
	case "url":
		$url=' data-url="'.$url.'"';
		break;
	}
}else{
	$url="";
}

$selected_value='';
if (isset($_POST[$name]) ){
	$selected_value=$_POST[$name];
}
elseif (isset($_GET[$name]) ){
	$selected_value=$_GET[$name];
}
elseif (empty($_POST)){
	$selected_value=-1;
}

$checked=false;

if($selected_value==-1){
	if (( empty($_POST) || !isset($_POST[$name]) ) && isset($default_value)){
		if ($default_value){
			$checked=true;
		}
	}
}else{
	if (esc_attr(stripcslashes($selected_value))==esc_attr( ( $value ) ) ){
		$checked=true;
	}
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
if (empty($imagep)){
	$imagep = '';
}
$labelclass='';
$labelclass_start='';
$labelclass_end='';
if (TM_EPO()->tm_epo_css_styles=="on" && empty($use_images)){
	$labelclass=' class="tm-epo-style '.TM_EPO()->tm_epo_css_styles_style.'"';
	$labelclass_start='<span class="tm-epo-style-wrapper '.TM_EPO()->tm_epo_css_styles_style.'">';
	$labelclass_end='</span>';
}
?>
<li class="tmcp-field-wrap<?php echo $grid_break.$li_class;?>">
	<?php include('_quantity_start.php'); ?>
	<?php echo $labelclass_start; ?>
	<input class="<?php echo $fieldtype;?> tm-epo-field tmcp-radio<?php echo $use; ?>" style="display:none;visibility:hidden;" 
	name="<?php echo $name; ?>" 
	data-price="" 
	data-rules="<?php echo $rules; ?>" 
	data-rulestype="<?php echo $rules_type; ?>" 
	data-image="<?php echo $image; ?>" 
	data-imagep="<?php echo $imagep; ?>" <?php echo $element_data_attr_html; ?>
	value="<?php echo $value; ?>" 
	id="<?php echo $id; ?>" 
	tabindex="<?php echo $tabindex; ?>" 
	type="radio" <?php checked( $checked, true ); echo $url; ?> />
	<?php 
	if (empty($use_images)){
		echo '<label'.$labelclass.' for="'.$id.'"></label>';
		echo $labelclass_end;
		echo '<span class="tm-label">'.$label.'</span>';
	}else{
		echo '<label'.$labelclass.' for="'.$id.'">'.$label.'</label>';
		echo $labelclass_end;
	}
	?>
	<?php echo $textbeforeprice; ?>
	<span class="amount<?php if (!empty($hide_amount)){echo " ".$hide_amount;} ?>"><?php echo $amount; ?></span>
	<?php echo $textafterprice; ?>
	<?php include('_quantity_end.php'); ?>
</li>