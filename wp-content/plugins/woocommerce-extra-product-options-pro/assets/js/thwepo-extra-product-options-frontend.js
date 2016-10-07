jQuery(document).ready(function($) {	
	$('select.thwepo-enhanced-multi-select').select2({
		minimumResultsForSearch: 10,
		allowClear : true,
		placeholder: $(this).data('placeholder')
	}).addClass('enhanced');

	//$.datepicker.setDefaults($.datepicker.regional[thwepo_extra_product_options.language]);
	$('.thwepo-date-picker').each(function(){
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
		$(this).datepicker("option", $.datepicker.regional[thwepo_extra_product_options.language]);
		$(this).datepicker("option", "dateFormat", dateFormat);
		//$(this).datepicker("option", "beforeShowDay", noWeekends);
	});
			
	$('.thwepo-time-picker').each(function(){
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
			'lang': thwepo_extra_product_options.lang,
			'disableTextInput' : true
		}		
		$(this).timepicker(args);
	});
	
	$('.thwepo-date-picker').prop('readonly', true);
	
   /******************************************
	***** DISABLE DATE FUNCTIONS - START *****
	******************************************/
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
		var noSunday = noSundays(date);
		if (noSunday[0]) {
			return noChristmas(date);
		} else {
			return noSunday;
		}
	}
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
	
   /*********************************************
    ------- CONDITIONAL FIELD SETUP - START -----
	*********************************************/
	$('.thwepo-conditional-field').each(function(){
		validate_field_condition($(this), true);										 
	});
	
	function validate_field_condition(cfield, needSetup){
		var conditionalRules = cfield.data("rules");	
		var conditionalRulesAction = cfield.data("rules-action");
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
					hide_field(cfield);
				}else{
					show_field(cfield);	
				}
			}else{
				if(valid){
					show_field(cfield);	
				}else{
					hide_field(cfield);
				}	
			}
		}
	}
	
	function hide_field(cfield){
		//cfield.find(":input").val('');
		cfield.find(":input").prop('disabled', true);
		cfield.hide();	
		thwepo_calculate_extra_cost();
	}
	function show_field(cfield){
		var cinput = cfield.find(":input");
		
		cinput.prop('disabled', false);
		cfield.show();
		cinput.change();
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
	// Fired when the user selects all the required dropdowns / attributes and a final variation is selected / shown
	$( ".single_variation_wrap" ).on( "show_variation", function ( event, variation ) {
		thwepo_calculate_extra_cost();
	});
	
	thwepo_prepare_extra_cost_for_option_fields();
	thwepo_calculate_extra_cost();
	
	$('.thwepo-price-field').change(function(){
		var ftype = $(this).getType();	
		if(ftype == "select" || ftype == "radio"){
			thwepo_prepare_extra_cost_from_selected_option($(this), ftype);
		}
		thwepo_calculate_extra_cost();
        return false;
    });
	
	function thwepo_prepare_extra_cost_for_option_fields(){
		$('.thwepo-price-option-field').each(function(){										 
			thwepo_prepare_extra_cost_from_selected_option($(this), 'select');
		});
	}
	
	function thwepo_prepare_extra_cost_from_selected_option(elm, ftype){
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
	
	function thwepo_display_new_price(displayPrice, isVariableProduct){
		if(displayPrice){
			if(isVariableProduct){
				var priceElm = $('.woocommerce-variation-price .price');
				if(priceElm.length){
					priceElm.html(displayPrice);
				}
			}else{
				$('.price').html(displayPrice);	
			}
		}
	}
	
	function thwepo_calculate_extra_cost(){
		var form = $("form.cart");
		var isVariableProduct = false;
		if(form.hasClass('variations_form')){
			isVariableProduct = true;
		}
		
		var priceInfoArr = {};
		//var prodPrice = $('input[name=wepo-product-price]').val();
		//prodPrice = parseInt(prodPrice);
		var productId = $('input[name=add-to-cart]').val();
		var variationId = $('input[name=variation_id]').val();
		
		$('.thwepo-price-field').each(function(){	
			if($(this).is(":enabled")){
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
				var priceUnit = $(this).data("price-unit");
				
				if(value && value != '' && name && price){
					var priceInfo = {};
					priceInfo['label'] = label;
					priceInfo['price'] = price;
					priceInfo['price_type'] = priceType;
					priceInfo['price_unit'] = priceUnit;
					priceInfo['value'] = value;
					priceInfo['multiple'] = multiple;
					
					priceInfoArr[name] = priceInfo;
				}
			}
		});
		
		var requestData = {};
		requestData['product_id'] = productId;
		requestData['price_info'] = priceInfoArr;
		requestData['is_variable_product'] = isVariableProduct;
		if(variationId){
			requestData['variation_id'] = variationId;
		}
		
		var data = {
            action: 'thwepo_calculate_extra_cost',
			price_info: JSON.stringify(requestData)
        };

        $.ajax({
            type: 'POST',
            url : thwepo_extra_product_options.ajax_url,
            data: data,
            success: function(data){
				if(data.code === 'E000'){
					$result = data.result;
					if($result){
						thwepo_display_new_price($result.display_price, isVariableProduct);
						//$('.price').html($result.display_price);
					}
				}else if(data.code === 'E002'){
					thwepo_display_new_price(data.result, isVariableProduct);
					//$('.price').html(data.result);
				}
            }
        });
	}
	
	
	
	/*function thwepo_calculate_field_extra_cost(prodPrice, price, priceType, value, multiple){
		var fprice = 0;
		if(multiple == 1){
			var priceArr = price.split(",");
			var priceTypeArr = priceType.split(",");
			
			$.each( priceArr, function( index, oprice ) {
				var opriceType = priceTypeArr[index]; // ? $price_type_arr[$index] : 'normal';
				
				fprice = fprice + thwepo_calculate_item_extra_cost(prodPrice, oprice, value, opriceType);
			});
		}else{
			fprice = thwepo_calculate_item_extra_cost(prodPrice, price, value, priceType);
		}	
		return fprice;
	}*/
	
	/*function thwepo_calculate_item_extra_cost(prodPrice, price, value, priceType){
		var fprice = 0;
		
		if(value){
			if(priceType === 'percentage'){
				if($.isNumeric(price) && $.isNumeric(prodPrice)){
					fprice = (price/100)*prodPrice;
				}
			}else{
				if($.isNumeric(price)){
					fprice = price;
				}
			}
		}
		
		return fprice;
	}*/
   /****************************************
    ------- EXTRA COST FIELD - END ---------
	****************************************/
});