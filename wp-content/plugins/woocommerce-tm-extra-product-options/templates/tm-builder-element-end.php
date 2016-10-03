<?php
// Direct access security
if (!defined('TM_EPO_PLUGIN_SECURITY')){
	die();
}
if (!in_array($element,array('header','divider')) && empty(TM_EPO()->tm_builder_elements[$tm_element_settings['type']]["no_frontend_display"]) ){
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