<?php
// Direct access security
if (!defined('TM_EPO_PLUGIN_SECURITY')){
	die();
}
if (!isset($fieldtype)){
	$fieldtype="tmcp-field";
}
?>
<li class="tmcp-field-wrap">
	<?php include('_quantity_start.php'); ?>
	<?php 

	$date_format='dd/mm/yy';
	$date_placeholder='dd/mm/yyyy';
	$date_mask='00/00/0000';

	switch ($format){
	case "0":
		$date_format='dd/mm/yy';
		$date_placeholder='dd/mm/yyyy';
		$date_mask='00/00/0000';
		break;
	case "1":
		$date_format='mm/dd/yy';
		$date_placeholder='mm/dd/yyyy';
		$date_mask='00/00/0000';
		break;
	case "2":
		$date_format='dd.mm.yy';
		$date_placeholder='dd.mm.yyyy';
		$date_mask='00.00.0000';
		break;
	case "3":
		$date_format='mm.dd.yy';
		$date_placeholder='mm.dd.yyyy';
		$date_mask='00.00.0000';
		break;
	case "4":
		$date_format='dd-mm-yy';
		$date_placeholder='dd-mm-yyyy';
		$date_mask='00-00-0000';
		break;
	case "5":
		$date_format='mm-dd-yy';
		$date_placeholder='mm-dd-yyyy';
		$date_mask='00-00-0000';
		break;
	}
	if ($style!="picker"){
		if (isset($_GET[$name]) && empty($_POST)){
			$unixtime = strtotime($_GET[$name]);
			$_POST[$name."_day"]=date('d', $unixtime);
			$_POST[$name."_month"]=date('m', $unixtime);
			$_POST[$name."_year"]=date('Y', $unixtime);
		}

		$selectArray = array("class"=>"tmcp-date-select tmcp-date-day","id"=>$id."_day","name"=>$name."_day","extra"=>"data-tm-date='".$id."'");
		$select_options = array();
		$tranlation_day = (!empty($tranlation_day))?$tranlation_day:__( 'Day', TM_EPO_TRANSLATION );
		$select_options[]=array("text"=>$tranlation_day,"value"=>"");
		for($i=1; $i!= 31+1; $i += 1){
			$select_options[]=array("text"=>$i,"value"=>$i);
	    }
		$day_html=TM_EPO_HTML()->tm_make_select($selectArray , $select_options, $selectedvalue=isset($_POST[$name."_day"])?$_POST[$name."_day"]:"",1,0);

		$selectArray = array("class"=>"tmcp-date-select tmcp-date-month","id"=>$id."_month","name"=>$name."_month","extra"=>"data-tm-date='".$id."'");
		$select_options = array();
		$tranlation_month = (!empty($tranlation_month))?$tranlation_month:__( 'Month', TM_EPO_TRANSLATION );
		$select_options[]=array("text"=>$tranlation_month,"value"=>"");
		for($i=1; $i!= 12+1; $i += 1){
			$select_options[]=array("text"=>$i,"value"=>$i);
	    }
		$month_html=TM_EPO_HTML()->tm_make_select($selectArray , $select_options, $selectedvalue=isset($_POST[$name."_month"])?$_POST[$name."_month"]:"",1,0);

		$selectArray = array("class"=>"tmcp-date-select tmcp-date-year","id"=>$id."_year","name"=>$name."_year","extra"=>"data-tm-date='".$id."'");
		$select_options = array();
		$tranlation_year = (!empty($tranlation_year))?$tranlation_year:__( 'Year', TM_EPO_TRANSLATION );
		$select_options[]=array("text"=>$tranlation_year,"value"=>"");
		for($i=intval($end_year); $i!= intval($start_year) - 1; $i -= 1){
			$select_options[]=array("text"=>$i,"value"=>$i);
	    }
		$year_html=TM_EPO_HTML()->tm_make_select($selectArray , $select_options, $selectedvalue=isset($_POST[$name."_year"])?$_POST[$name."_year"]:"",1,0);
		
		switch ($format){
		case "0":
		case "2":
		case "4":
			echo $day_html.$month_html.$year_html;
			break;
		case "1":
		case "3":
		case "5":
			echo $month_html.$day_html.$year_html;
			break;
		}
	}
	$input_type="text";
	$showon="button";
	$mask='data-mask="'.$date_mask.'" data-mask-placeholder="'.$date_placeholder.'" ';
	if ($style==""){
		$input_type="hidden";
		$showon="focus";
		$mask='';
	}
	if (isset($textbeforeprice) && $textbeforeprice!=''){
		$textbeforeprice = '<span class="before-amount'.(!empty($hide_amount)?" ".$hide_amount:"").'">'.$textbeforeprice.'</span>';
	}
	if (isset($textafterprice) && $textafterprice!=''){
		$textafterprice = '<span class="after-amount'.(!empty($hide_amount)?" ".$hide_amount:"").'">'.$textafterprice.'</span>';
	}
	if (!empty($class)){
		$fieldtype .=" ".$class;
	}	
	?>
	<label for="<?php echo $id; ?>">
	<input type="<?php echo $input_type; ?>" class="<?php echo $fieldtype;?> tm-epo-field tmcp-date tm-epo-datepicker" 
	data-date-showon="<?php echo $showon; ?>" 
	<?php echo $mask; ?> 
	data-start-year="<?php echo $start_year; ?>" 
	data-end-year="<?php echo $end_year; ?>" 
	data-min-date="<?php echo $min_date; ?>" 
	data-max-date="<?php echo $max_date; ?>" 
	data-disabled-dates="<?php echo $disabled_dates; ?>" 
	data-enabled-only-dates="<?php echo $enabled_only_dates; ?>" 
	data-disabled-weekdays="<?php echo $disabled_weekdays; ?>" 
	data-date-format="<?php echo $date_format; ?>"
	data-date-theme="<?php echo $date_theme; ?>"
	data-date-theme-size="<?php echo $date_theme_size; ?>"
	data-date-theme-position="<?php echo $date_theme_position; ?>"
	data-price="" data-rules="<?php echo $rules; ?>" data-rulestype="<?php echo $rules_type; ?>" 
	id="<?php echo $id; ?>" tabindex="<?php echo $tabindex; ?>" 
	value="<?php 
	if (isset($_POST[$name])){
		echo esc_attr(($_POST[$name]));
	}elseif (isset($_GET[$name])){
		echo esc_attr(stripslashes($_GET[$name]));
	}
	?>" 
	name="<?php echo $name; ?>" /> 
	</label>
	<?php echo $textbeforeprice; ?>
	<span class="amount<?php if (!empty($hide_amount)){echo " ".$hide_amount;} ?>"><?php echo $amount; ?></span>
	<?php echo $textafterprice; ?>
	<?php include('_quantity_end.php'); ?>
</li>