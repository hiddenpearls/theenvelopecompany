<?php
// Direct access security
if (!defined('TM_EPO_PLUGIN_SECURITY')){
	die();
}
?>
<li class="tmcp-field-wrap">
<?php echo '<'.$title_size;
	if(!empty($title_color)){
		echo ' style="color:'.$title_color.'"';
	}
	echo ' class="tm-epo-field-label">'
	.$title;
	if($required){
		echo '<span class="tm-epo-required">*</span>';
	}
	echo '</'.$title_size.'>';
?><?php 
if(!empty($description)){
echo'<div'; 
	if(!empty($description_color)){
		echo ' style="color:'.$description_color.'"';
	}
echo' class="tm-description">'.$description.'</div>';
}?>
</li>