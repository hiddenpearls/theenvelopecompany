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
?>
</div>