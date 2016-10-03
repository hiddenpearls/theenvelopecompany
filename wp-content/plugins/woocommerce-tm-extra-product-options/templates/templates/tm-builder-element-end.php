<?php
// Direct access security
if (!defined('TM_EPO_PLUGIN_SECURITY')){
	die();
}
if (!in_array($element,array('header','divider'))){
?>

</ul></div>
<?php 
	if(!empty($description) && !empty($description_position) && $description_position=="below" ){
		echo'<div';
		if(!empty($description_color)){
			echo ' style="color:'.$description_color.'"';
		}
		echo' class="tm-description">'.do_shortcode($description).'</div>';
	}
}
?></div>