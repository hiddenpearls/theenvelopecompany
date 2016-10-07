jQuery(document).ready(function($) {	
	var weekDays = ["sun", "mon", "tue", "wed", "thu", "fri", "sat"];
	//var holidays = [[1, 1, 'uk'], [12, 25, 'uk'], [12, 26, 'uk']];
	//var holidays = ["2016-06-15", "2016-07-05"];
	
	$('select.thwcfe-enhanced-select').select2({
		minimumResultsForSearch: 10,
		allowClear : true,
		placeholder: $(this).data('placeholder')
	}).addClass('enhanced');
		
	$('select.thwcfe-enhanced-multi-select').select2({
		minimumResultsForSearch: 10,
		allowClear : true,
		placeholder: $(this).data('placeholder')
	}).addClass('enhanced');

	$('.thwcfe-checkout-date-picker').each(function(){
		var dateFormat = $(this).data("date-format");		
		var defaultDate = $(this).data("default-date");
		var maxDate = $(this).data("max-date");
		var minDate = $(this).data("min-date");
		var yearRange = $(this).data("year-range");
		var numberOfMonths = $(this).data("number-months");
						
		dateFormat = dateFormat == '' ? 'dd/mm/yy' : dateFormat;
		defaultDate = defaultDate == '' ? null : defaultDate;
		maxDate = maxDate == '' ? null : maxDate;
		minDate = minDate == '' ? null : minDate;
		yearRange = yearRange == '' ? '-100:+1' : yearRange;
		numberOfMonths = numberOfMonths > 0 ? numberOfMonths : 1;
		
		var value = $(this).val();
		if(value.trim()){
			defaultDate = value;
		}
		
		//minDate = new Date().getHours() >= 2 ? 1 : 0;
		
		$(this).datepicker({
			defaultDate: defaultDate,
			maxDate: maxDate,
			minDate: minDate,
			yearRange: yearRange,
			numberOfMonths: numberOfMonths,
			showButtonPanel: true,
			changeMonth: true,
			changeYear: true			
		});
		$(this).datepicker("option", $.datepicker.regional[wcfe_checkout_fields.language]);
		$(this).datepicker("option", "dateFormat", dateFormat);
		$(this).datepicker("option", "beforeShowDay", disableDates);
		$(this).datepicker("setDate", defaultDate);
	});
	$('.thwcfe-checkout-date-picker').prop('readonly', true);
	
	$('.thwcfe-checkout-time-picker').each(function(){
		var minTime = $(this).data("min-time");		
		var maxTime = $(this).data("max-time");
		var step    = $(this).data("step");
		var format  = $(this).data("format");
						
		minTime = minTime ? minTime : '12:00am';
		maxTime = maxTime ? maxTime : '11:30pm';
		step 	= step ? step : '30';
		format 	= format ? format : 'h:i A';
		
		var args = {
			'minTime': minTime,
			'maxTime': maxTime,
			'step': step,
			'timeFormat': format,
			'forceRoundTime': true,
			//'showDuration':true,
			'disableTextInput' : true,
			'lang': wcfe_checkout_fields.lang
		}		
		$(this).timepicker(args);
		//$(this).timepicker('option', 'minTime', tpMinTime(format, step, minTime, maxTime));
	});
	
	function tpMinTime(format, step, minTime, maxTime){
		var suffixMinTime = thwcfeGetTimeSuffix(minTime);
		var suffixMaxTime = thwcfeGetTimeSuffix(maxTime);
		
		var _minTime = thwcfeSplitTimeString(minTime, suffixMinTime);
		var _maxTime = thwcfeSplitTimeString(maxTime, suffixMaxTime);
		
		if(_minTime.length > 1){
			var currTime = new Date();
			
			var minHour = _minTime[0];
			var minMin  = _minTime[1];
			
			var dateMin = new Date();
			dateMin.setHours(minHour, minMin);
			
			if(currTime >= dateMin){
				var maxHour = _maxTime[0];
				var maxMin  = _maxTime[1];
				
				var currHour = currTime.getHours();
				var currMin  = currTime.getMinutes();
			
				if(step < 60){
					var ns = Math.floor(currMin/step);
					currMin = (ns+1)*step;
					
					if(currMin >= 60){
						currHour += 1;
						currMin = 00;
					}
				}
				
				/*if(currHour > maxHour){
					maxHour = maxHour;
					currMin = maxMin;
				}else if(currHour = maxHour && currMin > maxMin){
					currMin = maxMin;
				}*/
				if(currHour > maxHour || (currHour == maxHour && currMin > maxMin)){
					return minTime;
				}
			
				currHour = padZero(currHour, 2);
				currMin  = padZero(currMin, 2);
				
				minTime  = currHour+":"+currMin;
			}
		}
		
		return minTime;
	}
	
	function thwcfeSplitTimeString(time, ampm){
		time = time.replace(/pm/gi, "");
		time = time.replace(/am/gi, "");
		var timeArr = time.split(":");
		
		var hours = parseInt(timeArr[0]);
		var minutes = parseInt(timeArr[1]);
		
		if(ampm == "pm" && hours < 12){
			hours = hours + 12;
		}else if(ampm == "am" && hours == 12){
			hours = hours - 12;
		}
		
		return [hours, minutes];
	}
	
	function thwcfeGetTimeSuffix(time){
		time = time.toLowerCase();
		var suffix = "";
		if(time.indexOf("am") != -1){
			suffix = "am";
		}else if(time.indexOf("pm") != -1){
			suffix = "pm";
		}
		return suffix;
	}
	
   /******************************************
	***** DISABLE DATE FUNCTIONS - START *****
	******************************************/
	// start weeks from monday
	//$(".selector").datepicker({ firstDay: 1 });
	
	/*function noSpecificDays(date, dayIndex) {
		var day = date.getDay();
		return [day != dayIndex, ''];
	}*/
	function noSpecificDays(date, disableDays) {
		var day = date.getDay();
		var daystring = weekDays[day];
    	return [ disableDays.indexOf(daystring) == -1, '' ];
	}
	function noSpecificDates(date, datestring) {
		var day = date.getDate();
		var month = date.getMonth() + 1;
		var year = date.getFullYear();
		
		var dateArr = datestring.split("-");
		if(dateArr.length == 3){
			var matchYear = isInt(dateArr[0]) ? dateArr[0] == year : true;
			var matchMonth = isInt(dateArr[1]) ? dateArr[1] == month : true;
			var matchDay = isInt(dateArr[2]) ? dateArr[2] == day : true;
			
			if(isInt(dateArr[0]) || isInt(dateArr[1]) || isInt(dateArr[2])){
				return [!(matchYear && matchMonth && matchDay), ''];
			}else{
				return [true, ''];
			}
		}else{
			return [true, ''];
		}
		
		/*var matchYear = matchMonth = matchDay = false;
		if( isInt(dateArr[0]) && dateArr[0] === year ){
			matchYear = true;
		}
		if( isInt(dateArr[1]) && dateArr[1] === month ){
			matchMonth = true;
		}
		if( isInt(dateArr[2]) && dateArr[2] === day ){
			matchDay = true;
		}*/
		
		
		
		//var datestring = $.datepicker.formatDate('yy-mm-dd', date);
    	//return [ disableDates.indexOf(datestring) == -1, '' ];
	}
	
	
	
	function noSpecificDates1(date, disableDates) {
		var datestring = $.datepicker.formatDate('yy-mm-dd', date);
    	return [ disableDates.indexOf(datestring) == -1, '' ];
	}
	
	function noSundays(date) {
		var day = date.getDay();
		return [day != 0, ''];
	}
	function noSaturdays(date) {
		var day = date.getDay();
		return [day != 6, ''];
	}
	function noWeekends(date) {
		return $.datepicker.noWeekends(date);
	}
	function noChristmas(date) {
		var day = date.getDate();
		var month = date.getMonth() + 1;
		return [!(day === 25 && month === 12), ''];
	}
	function noNewYearsDay(date) {
		var day = date.getDate();
		var month = date.getMonth() + 1;
		return [!(day === 1 && month === 1), ''];
	}
	function noHolidays(date) {
		var datestring = $.datepicker.formatDate('yy-mm-dd', date);
    	return [ holidays.indexOf(datestring) == -1, '' ];
	}
	
	function noWeekendsOrHolidays(date) {
		var noWeekend = $.datepicker.noWeekends(date);
		if (noWeekend[0]) {
			return noHolidays(date);
		} else {
			return noWeekend;
		}
	}
	
	function disableDates(date){
		var disabledDays = $(this).data("disabled-days");
		
		if(disabledDays && disabledDays.length > 0){
			var daysArr = disabledDays.split(",");
			var disabledDay = noSpecificDays(date, daysArr);
			
			if(!disabledDay[0]) {
				return disabledDay;
			}
			
			/*if(daysArr.length > 0){
				for (i = 0; i < daysArr.length; i++) { 
					var dayIndex = weekDays.indexOf(daysArr[i].trim());
					
					var disabled = noSpecificDays(date, dayIndex);
					if(!disabled[0]) {
						return disabled;
					}
				}
			}*/
		}
		
		var disabledDates = $(this).data("disabled-dates");
		if(disabledDates && disabledDates.length > 0){
			var datesArr = disabledDates.split(",");
			/*var disabledDate = noSpecificDates(date, datesArr);
			
			if(!disabledDate[0]) {
				return disabledDate;
			}*/
			if(datesArr.length > 0){
				for (i = 0; i < datesArr.length; i++) { 
					var disabledDate = noSpecificDates(date, datesArr[i].trim());
					//alert(datesArr[i].trim()+":::"+disabledDate[0]);
					if(!disabledDate[0]) {
						return disabledDate;
					}
				}
			}
		}
		
		return [true, ''];
	}
	
	/*function disableDates(date){
		var noSunday = noSundays(date);
		if (noSunday[0]) {
			return noChristmas(date);
		} else {
			return noSunday;
		}
	}*/
   /******************************************
	***** DISABLE DATE FUNCTIONS - END *******
	******************************************/
	
	$.fn.getType = function(){
		try{
			return this[0].tagName == "INPUT" ? this[0].type.toLowerCase() : this[0].tagName.toLowerCase(); 
		}catch(err) {
			return 'E001';
		}
	}
	
	function padZero(s, len, c){
		s = ""+s;
		var c = c || '0';
		while(s.length< len) s= c+ s;
		return s;
	}
	
	function isInt(value) {
	  	return !isNaN(value) && parseInt(Number(value)) == value && !isNaN(parseInt(value, 10));
	}
	
   /*********************************************
    ------- CONDITIONAL FIELD SETUP - START -----
	*********************************************/
	$('.thwcfe-conditional-field').each(function(){
		validate_field_condition($(this), true);										 
	});
	
	function validate_field_condition(cfield, needSetup){
		var conditionalRules = cfield.data("rules");	
		var conditionalRulesAction = cfield.data("rules-action");
		var validations = cfield.data("validations");
		var valid = true;
		
		if(conditionalRules){
			try{
				jQuery.each(conditionalRules, function() {
					var ruleSet = this;	
					
					jQuery.each(ruleSet, function() {
						var rule = this;
						var validRS = false;
						
						jQuery.each(rule, function() {
							var conditions = this;								   	
							var validCS = true;
							
							jQuery.each(conditions, function() {
								validCS = validate_condition(this, validCS, needSetup, cfield);
							});
							
							validRS = validRS || validCS;
						});
						valid = valid && validRS;
					});
				});
			}catch(err) {
				alert(err);
			}
			
			if(conditionalRulesAction === 'hide'){
				if(valid){
					hide_field(cfield, validations);
				}else{
					show_field(cfield, validations);
				}
			}else{
				if(valid){
					show_field(cfield, validations);
				}else{
					hide_field(cfield, validations);
				}	
			}
		}
	}
	
	function hide_field(cfield, validations){
		//var cinput = cfield.find(":input");
		var cinput = cfield.find(":input.thwcfe-input-field");
		var ftype = cinput.getType();
		
		cfield.hide();	
		//cinput.val('');
		
		var fid = cinput.prop('id');
		if(ftype == "radio"){
			fid = cinput.prop('name');
		}
		
		var disabled_fnames = $('#thwcfe_disabled_fields').val();
		var disabled_fnames_x = disabled_fnames.split(",");
		
		disabled_fnames_x.push(fid); 
		disabled_fnames = disabled_fnames_x.toString();
		
		$('#thwcfe_disabled_fields').val(disabled_fnames);
		
		//cfield.find(":input").val('mockvalthXXX'); //mockvalthXXX
		
		/**** COUNTRY field conditions issue workaround START ****/
		/*var cfieldId = cfield.prop('id');
		if(cfieldId == 'billing_country_field' || cfieldId == 'shipping_country_field'){
			var name = cfield.find("select").prop('id');
			if(name){
				var cssclass = name+'_hidden_field';
				if( cfield.find('.'+cssclass).length == 0 ){
					cfield.append( '<input type="hidden" id="'+name+'" name="'+name+'" value="mockvalthXXX" class="'+cssclass+'" />' );
				}
			}
		}*/
		/**** COUNTRY field conditions issue workaround END ****/
					
		//disable_field_ajax(cfield.find(":input").prop("name"));
		if(validations) {
			cfield.removeClass(validations);
			cfield.removeClass('woocommerce-validated woocommerce-invalid woocommerce-invalid-required-field');
		}
	}
	function show_field(cfield, validations){
		//var cinput = cfield.find(":input");
		var cinput = cfield.find(":input.thwcfe-input-field");
		var ftype = cinput.getType();
		
		cfield.show();	
		//cfield.find(":input").val('');
		
		var fid = cinput.prop('id');
		if(ftype == "radio"){
			fid = cinput.prop('name');
		}
		
		var disabled_fnames = $('#thwcfe_disabled_fields').val();
		var disabled_fnames_x = disabled_fnames.split(",");
		
		disabled_fnames_x = jQuery.grep(disabled_fnames_x, function(value) {
		  	return value != fid; 
		});
		
		disabled_fnames = disabled_fnames_x.toString();
		
		$('#thwcfe_disabled_fields').val(disabled_fnames);
		
		/**** COUNTRY field conditions issue workaround START ****/
		/*var cfieldId = cfield.prop('id');
		if(cfieldId == 'billing_country_field' || cfieldId == 'shipping_country_field'){
			var name = cfield.find("select").prop('id');
			if(name){
				cfield.find('.'+name+'_hidden_field').remove();
			}
		}*/
		/**** COUNTRY field conditions issue workaround END ****/
		
		//enable_field_ajax(cfield.find(":input").prop("name"));
		if(validations) {
			cfield.addClass(validations);
		}
	}

	function validate_condition(condition, valid, needSetup, cfield){
		if(condition){
			var operand_type = condition.operand_type;
			var operand = condition.operand;
			var operator = condition.operator;
			var cvalue = condition.value;
			
			if(operand_type === 'field' && operand){
				jQuery.each(operand, function() {
					var field = $('#'+this);
					var value = field.val();
					
					var ftype = field.getType();
		            if(ftype == "radio" || ftype == "E001"){
						field = $("input[type='radio'][name='"+this+"']");
						if(field){
							value = $("input[type='radio'][name='"+this+"']:checked").val();
						}
					}
					
					if(operator === 'empty' && value != ''){
						valid = false;
						
					}else if(operator === 'not_empty' && value == ''){
						valid = false;
						
					}if(operator === 'value_eq' && value != cvalue){
						valid = false;
						
					}if(operator === 'value_ne' && value == cvalue){
						valid = false;
						
					}if(operator === 'value_gt'){
						if($.isNumeric(value) && $.isNumeric(cvalue)){
							valid = (Number(value) <= Number(cvalue)) ? false : valid;
						}else{
							valid = false;
						}
						
					}if(operator === 'value_le'){
						if($.isNumeric(value) && $.isNumeric(cvalue)){
							valid = (Number(value) >= Number(cvalue)) ? false : valid;
						}else{
							valid = false;
						}
						
					}if(operator === 'checked'){
						var checked = field.prop('checked');
						valid = checked ? valid : false;
						
					}if(operator === 'not_checked'){
						var checked = field.prop('checked');
						valid = checked ? false : valid;
					}
					
					if(needSetup){
						var depFields = field.data("fields");
						if(depFields){
							var depFieldsArr = depFields.split(",");
							depFieldsArr.push(cfield.prop('id'));
							depFields = depFieldsArr.toString();
						}else{
							depFields = cfield.prop('id');
						}
						field.data("fields", depFields);
						add_field_value_change_handler(field);
					}
				});
			}
		}
		return valid;
	}
	
	function add_field_value_change_handler(field){
		field.change(function(event) {
			var depFields = $(this).data("fields");
			var depFieldsArr = depFields.split(",");
			
			jQuery.each(depFieldsArr, function() {
				if(this.length > 0){	
					var cfield = $('#'+this);
					validate_field_condition(cfield, false);	
				}
			});							 
		});
	}
   /*********************************************
    ------- CONDITIONAL FIELD SETUP - END -------
	*********************************************/
	
   /****************************************
    ------- EXTRA COST FIELD - START -------
	****************************************/
	thwcfe_prepare_extra_cost_for_option_fields();
	thwcfe_calculate_extra_cost();
	
	$('.thwcfe-price-field').change(function(){
		var ftype = $(this).getType();	
		if(ftype == "select" || ftype == "radio"){
			thwcfe_prepare_extra_cost_from_selected_option($(this), ftype);
		}
		thwcfe_calculate_extra_cost();
        return false;
    });
	
	function thwcfe_prepare_extra_cost_for_option_fields(){
		$('.thwcfe-price-option-field').each(function(){										 
			thwcfe_prepare_extra_cost_from_selected_option($(this), 'select');
		});
	}
	
	function thwcfe_prepare_extra_cost_from_selected_option(elm, ftype){
		var option = elm.find(':selected');
		var oPrice = '';
		var oPriceType = '';
		
		if(elm.attr("multiple")){
			elm.find('option:selected').each(function(){
				oprice = $(this).data('price');
				opriceType = $(this).data('price-type');	
				if(oprice){
					opriceType = opriceType ? opriceType : 'normal';
					
					if(oPrice.trim()){
						oPrice += ',';
					}
					
					if(oPriceType.trim()){
						oPriceType += ',';
					}
					
					oPrice += oprice;
					oPriceType += opriceType;
				}
			});
		}else{
			oPrice = option.data('price');
			oPriceType = option.data('price-type');
			oPriceType = oPriceType ? oPriceType : 'normal';
		}
		
		if(oPrice){
			elm.data("price", oPrice);		
			elm.data("price-type", oPriceType);
		}else{
			if(ftype == "select"){
				elm.data("price", "");		
				elm.data("price-type", "");
			}
		}
	}
	
	function thwcfe_calculate_extra_cost(){
		var priceInfoArr = {};									 	
		$('.thwcfe-price-field').each(function(){		
			var ftype = $(this).getType();
			var multiple = 0;
			
			var value = $(this).val();
			if(ftype == 'radio' || ftype == 'checkbox'){
				value = $(this).is(':checked') ? value : '';
			}else if(ftype == "select"){
				if($(this).attr("multiple")){
					multiple = 1;
				}
			}
			
			var name = $(this).prop("id");
			var label = $(this).data("price-label");
			var price = $(this).data("price");		
			var priceType = $(this).data("price-type");
			
			if(value && name && price){
				var priceInfo = {};
				priceInfo['name'] = name;
				priceInfo['label'] = label+' ('+value+')';
				priceInfo['price'] = price;
				priceInfo['price_type'] = priceType;
				priceInfo['multiple'] = multiple;
				
				priceInfoArr[name] = priceInfo;
			}
		});
		
		var data = {
            action: 'thwcfe_calculate_extra_cost',
			price_info: JSON.stringify(priceInfoArr)
        };

        $.ajax({
            type: 'POST',
            url : wcfe_checkout_fields.ajax_url,
            data: data,
            success: function(code){
            	$('body').trigger('update_checkout');
            }
        });
	}
   /****************************************
    ------- EXTRA COST FIELD - END ---------
	****************************************/
		
});