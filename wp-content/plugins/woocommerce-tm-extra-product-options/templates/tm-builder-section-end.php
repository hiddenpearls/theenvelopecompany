<?php
// Direct access security
if (!defined('TM_EPO_PLUGIN_SECURITY')){
	die();
}
if ($style=="box"){
	echo '</div>';
}
if ($style=="collapse" || $style=="collapseclosed" || $style=="accordion"){
	echo '</div></div>';
}	
if (isset($sections_type) && $sections_type=="popup"){
	echo '</div>';
}
if(!empty($description) && !empty($description_position) && $description_position=="below" ){
	echo'<div';
	if(!empty($description_color)){
		echo ' style="color:'.$description_color.'"';
	}
	echo' class="tm-description">'.do_shortcode($description).'</div>';
}
?>
</div>