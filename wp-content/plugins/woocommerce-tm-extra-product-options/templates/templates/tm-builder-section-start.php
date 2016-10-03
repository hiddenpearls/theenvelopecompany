<?php

// Direct access security

if (!defined('TM_EPO_PLUGIN_SECURITY')){

	die();

}

if (isset($sections_type) && $sections_type=="popup"){

	$sections_class .=" section_popup";

}

if (!$haslogic){

	$logic="";

}

$tm_product_id_class="";

if (!empty($tm_product_id)){

	$tm_product_id_class=" tm-product-id-".$tm_product_id;

}

if ($sections_type=="slider"){

	$column .=" tm-owl-slider";

}

?>

<div data-uniqid="<?php echo $uniqid;?>" 

	data-logic="<?php echo $logic;?>" 

	data-haslogic="<?php echo $haslogic;?>" 

	class="cpf-section tm-row tm-cell <?php echo $column;?> <?php echo $sections_class.$tm_product_id_class;?>">

<?php



if (isset($sections_type) && $sections_type=="popup"){

	$_popuplinkitle=(!empty(TM_EPO()->tm_epo_additional_options_text))?TM_EPO()->tm_epo_additional_options_text:__( 'Additional options', TM_EPO_TRANSLATION );

	if (!empty ($title)){

		$_popuplinkitle=$title;

	}

	$_popuplink='<a class="tm-section-link" href="#" data-title="'.esc_attr($_popuplinkitle).'" data-sectionid="'.$uniqid.'">'.$_popuplinkitle.'</a>';

	echo $_popuplink.'<div class="tm-section-pop">';

}



$icon='';

$toggler='';

if ($style=="box"){

	echo '<div class="tm-box">';

}

if ($style=="collapse" || $style=="collapseclosed" || $style=="accordion"){

	echo '<div class="tm-collapse'.($style=="accordion"?' tmaccordion':'').'">';

	$icon='<span class="fa fa-angle-down tm-arrow"></span>';

	$toggler=' tm-toggle';

	if ($title==''){

		$title='&nbsp;';

	}

}





	if ($title!=''){

		echo '<'.$title_size;

		if(!empty($title_color)){

			echo ' style="color:'.$title_color.'"';

		}

		echo ' class="tm-epo-field-label tm-section-label'.$toggler.'">'.$title;

		

		echo $icon.'</'.$title_size.'>';

	}

	if(!empty($description)){

		echo'<div'; 

		if(!empty($description_color)){

			echo ' style="color:'.$description_color.'"';

		}

		echo' class="tm-description">'.do_shortcode($description).'</div>';

	}

	echo $divider;

if ($style=="collapse"){

	echo '<div class="tm-collapse-wrap">';

}

if ($style=="collapseclosed"){

	echo '<div class="tm-collapse-wrap closed">';

}

if ($style=="accordion"){

	echo '<div class="tm-collapse-wrap closed">';

}

?>