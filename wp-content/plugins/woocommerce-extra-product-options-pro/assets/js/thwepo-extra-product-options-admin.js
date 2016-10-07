var thwepo_settings = (function($, window, document) {
	/*var MSG_INVALID_NAME = 'NAME/ID must begin with a letter ([A-Za-z]) and may be followed by any number of letters, digits ([0-9]), hyphens ("-"), underscores ("_"), colons (":"), and periods ("."))';	*/

	var MSG_INVALID_NAME = 'NAME/ID must begin with a lowercase letter ([a-z]) and may be followed by any number of lowercase letters, digits ([0-9]) and underscores ("_")';
	
	var OPTION_ROW_HTML  = '<tr>';
        OPTION_ROW_HTML += '<td style="width:190px;"><input type="text" name="i_options_key[]" placeholder="Option Value" style="width:180px;"/></td>';
		OPTION_ROW_HTML += '<td style="width:190px;"><input type="text" name="i_options_text[]" placeholder="Option Text" style="width:180px;"/></td>';
		OPTION_ROW_HTML += '<td style="width:75px;"><input type="text" name="i_options_price[]" placeholder="Price" style="width:65px;"/></td>';
		OPTION_ROW_HTML += '<td style="width:120px;"><select name="i_options_price_type[]" style="width:110px;">';
		OPTION_ROW_HTML += '<option selected="selected" value="">Normal</option><option value="percentage">Percentage</option></select></td>';
		OPTION_ROW_HTML += '<td style="width:30px;"><a href="javascript:void(0)" onclick="thwepoAddNewOptionRow(this)" class="add_link" title="Add new option">+</a></td>';
		OPTION_ROW_HTML += '<td><a href="javascript:void(0)" onclick="thwepoRemoveOptionRow(this)" class="remove_link"  title="Remove option">x</a></td>';
		OPTION_ROW_HTML += '</tr>';
		
	/* used to holds next request's data (most likely to be transported to server) */
	this.request = null;
	/* used to holds last operation's response from server */
	this.response = null;
	/* to prevetn Ajax conflict. */
	this.ajaxFlaQ = true;
		
   /*------------------------------------
	*---- ON-LOAD FUNCTIONS - SATRT -----
	*------------------------------------*/
	$(function() {
		$( "#thwepo_new_section_form_pp" ).dialog({
			modal: true,
			width: 900,
			//height: 400,
			resizable: false,
			autoOpen: false,
			buttons: [
				{
					text: "Cancel",
					click: function() { $( this ).dialog( "close" ); }	
				},
				{
					text: "Save",
					click: function() {
						var form = $("#thwepo_new_section_form");
						var result = wepo_validate_section_form( form );
						if(result){ form.submit(); }
					}
				}
			]
		});	
		$( "#thwepo_edit_section_form_pp" ).dialog({
			modal: true,
			width: 900,
			resizable: false,
			autoOpen: false,
			buttons: [
				{
					text: "Cancel",
					click: function() { $( this ).dialog( "close" ); }	
				},
				{
					text: "Save",
					click: function() {
						var form = $("#thwepo_edit_section_form");
						var result = wepo_validate_section_form( form );
						if(result){
							form.submit();
						}
					}
				}
			]
		});
		
		$( "#thwepo_new_field_form_pp" ).dialog({
			modal: true,
			width: 900,
			resizable: false,
			autoOpen: false,
			buttons: [
				{
					text: "Cancel",
					click: function() { $( this ).dialog( "close" ); }	
				},
				{
					text: "Add New Field",
					click: function() {
						var form = $("#thwepo_field_editor_form_new");
						var result = wepo_add_new_row( form );
						if(result){
							$( this ).dialog( "close" );
						}
					}
				}
			]
		});	
		$( "#thwepo_edit_field_form_pp" ).dialog({
			modal: true,
			width: 900,
			resizable: false,
			autoOpen: false,
			buttons: [
				{
					text: "Cancel",
					click: function() { $( this ).dialog( "close" ); }	
				},
				{
					text: "Edit Field",
					click: function() {
						var form = $("#thwepo_field_editor_form_edit");
						var result = wepo_update_row( form );
						if(result){
							$( this ).dialog( "close" );
						}
					}
				}
			]
		});
			
		$('#thwepo_product_fields tbody').sortable({
			items:'tr',
			cursor:'move',
			axis:'y',
			handle: 'td.sort',
			scrollSensitivity:40,
			helper:function(e,ui){
				ui.children().each(function(){
					$(this).width($(this).width());
				});
				ui.css('left', '0');
				return ui;
			}		
		});	
		$("#thwepo_product_fields tbody").on("sortstart", function( event, ui ){
			ui.item.css('background-color','#f6f6f6');										
		});
		$("#thwepo_product_fields tbody").on("sortstop", function( event, ui ){
			ui.item.removeAttr('style');
			wepo_prepare_field_order_indexes();
		});		
	});
   /*------------------------------------
	*---- ON-LOAD FUNCTIONS - END -------
	*------------------------------------*/
	 
   /*------------------------------------
	*---- COMMON FUNCTIONS - START ------
	*------------------------------------*/
	function setup_popup_tabs(form){
		$(".thwepo-tabs-menu a").click(function(event) {
			event.preventDefault();
			$(this).parent().addClass("current");
			$(this).parent().siblings().removeClass("current");
			var tab = $(this).attr("href");
			$(".thwepo-tab-content").not(tab).css("display", "none");
			$(tab).fadeIn();
		});
	}
	
	function openFormTab(elm, tab_id, form_type){
		var tabs_container = $("#thwepo-tabs-container_"+form_type);
		
		$(elm).parent().addClass("current");
		$(elm).parent().siblings().removeClass("current");
		var tab = $("#"+tab_id+"_"+form_type);
		tabs_container.find(".thwepo-tab-content").not(tab).css("display", "none");
		$(tab).fadeIn();
	}
	
	function setup_color_picker(form){
		//form.find('.thwepo-color-picker').wpColorPicker();
		
		// Color picker
		form.find('.thwepo-colorpick').iris({
			change: function( event, ui ) {
				$( this ).parent().find( '.thwepo-colorpickpreview' ).css({ backgroundColor: ui.color.toString() });
			},
			hide: true,
			border: true
		}).click( function() {
			$('.iris-picker').hide();
			$(this ).closest('td').find('.iris-picker').show();
		});
	
		$('body').click( function() {
			$('.iris-picker').hide();
		});
	
		$('.thwepo-colorpick').click( function( event ) {
			event.stopPropagation();
		});
	}
	
	function setup_enhanced_multi_select(form){
		form.find('select.thwepo-enhanced-multi-select').each(function(){
			if(!$(this).hasClass('enhanced')){
				$(this).select2({
					minimumResultsForSearch: 10,
					allowClear : true,
					placeholder: $(this).data('placeholder')
				}).addClass('enhanced');
			}
		});
	}
	
	function wepo_prepare_field_order_indexes() {
		$('#thwepo_product_fields tbody tr').each(function(index, el){
			$('input.f_order', el).val( parseInt( $(el).index('#thwepo_product_fields tbody tr') ) );
		});
	};
	 	 
	function isHtmlIdValid(id) {
		//var re = /^[A-Za-z]+[\w\-\:\.]*$/;
		//var re = /^[a-z]+[\w]*$/;
		var re = /^[a-z]+[a-z0-9\_]*$/;
		return re.test(id.trim());
	}
	
	/**
	 * Function that will check if value is a valid HEX color.
	 */
	function isValidHexColor( $value ) {      
		if ( preg_match( '/^#[a-f0-9]{6}$/i', $value ) ) { // if user insert a HEX color with #     
			return true;
		}     
		return false;
	}
	 
	_selectAllFields = function selectAllFields(elm){
		var checkAll = $(elm).prop('checked');
		$('#thwepo_product_fields tbody input:checkbox[name=select_field]').prop('checked', checkAll);
	}
   /*------------------------------------
	*---- COMMON FUNCTIONS - END --------
	*------------------------------------*/


   /*------------------------------------
	*---- SECTION FUNCTIONS - SATRT -----
    *------------------------------------*/	
	var SECTION_FORM_FIELDS = {
		name 	   : {name : 'name', label : 'Name/ID', type : 'text', required : 1},
		position   : {name : 'position', label : 'Display Position', type : 'select', value : 'woo_before_add_to_cart_button', required : 1},
		//box_type : {name : 'box_type', label : 'Box Type', type : 'select'},
		cssclass   : {name : 'cssclass', label : 'CSS Class', type : 'text'},
		show_title : {name : 'show_title', label : 'Show section title in product page.', type : 'checkbox', value : 'yes', checked : true},
		
		title 		: {name : 'title', label : 'Title', type : 'text'},
		//title_position : {name : 'title_position', label : 'Title Position', type : 'select'},
		title_type 	: {name : 'title_type', label : 'Title Type', type : 'select', value : 'h3'},
		title_color : {name : 'title_color', label : 'Title Color', type : 'colorpicker'},
		title_class : {name : 'title_class', label : 'Title Class', type : 'text'},
		
		subtitle 	   : {name : 'subtitle', label : 'Subtitle', type : 'text'},
		//subtitle_position : {name : 'subtitle_position', label : 'Subtitle Position', type : 'select'},
		subtitle_type  : {name : 'subtitle_type', label : 'Subtitle Type', type : 'select', value : 'h3'},
		subtitle_color : {name : 'subtitle_color', label : 'Subtitle Color', type : 'colorpicker'},
		subtitle_class : {name : 'subtitle_class', label : 'Subtitle Class', type : 'text'},
	};
	
	_openNewSectionForm = function openNewSectionForm(){
		var form = $("#thwepo_new_section_form");
		clear_section_form(form);		
	  	$("#thwepo_new_section_form_pp").dialog( "open" );
		setup_color_picker(form);
	}
	
	_openEditSectionForm = function openEditSectionForm(sectionJson){
		var form = $("#thwepo_edit_section_form");
		populate_section_form(form, sectionJson);				
		$("#thwepo_edit_section_form_pp").dialog( "open" );
		setup_color_picker(form);
	}
	
	_removeSection = function removeSection(elm){
		var form = $(elm).closest('form');
		if(form){ form.submit(); }
	}
	
	function set_form_field_values(form, fields, valuesJson){
		$.each( fields, function( fname, field ) {
			var ftype = field['type'];								  
			var fvalue = valuesJson ? valuesJson[fname] : field['value'];
			
			switch(ftype) {
				case 'select':
					form.find("select[name=i_"+fname+"]").val(fvalue);
					break;
					
				case 'checkbox':
					var checked = false;
					if(valuesJson){
						checked = fvalue == 1 ? true : false;
					}else{
						checked = field['checked'] ? true : false;
					}
					form.find("input[name=i_"+fname+"]").prop('checked', checked);
					break;
					
				case 'colorpicker':
					var bg_color = fvalue ? { backgroundColor: fvalue } : {}; 
					form.find("input[name=i_"+fname+"]").val(fvalue);
					form.find("."+fname+"_preview").css(bg_color);
					break;
					
				default:
					form.find("input[name=i_"+fname+"]").val(fvalue);
			}
		});
	}
	
	function clear_section_form(form){
		form.find('.err_msgs').html('');
		set_form_field_values(form, SECTION_FORM_FIELDS, false);
	}
	
	function populate_section_form(form, sectionJson){
		form.find('.err_msgs').html('');
		set_form_field_values(form, SECTION_FORM_FIELDS, sectionJson);
		
		form.find("input[name=i_name]").prop("readonly", true);
		form.find("select[name=i_position_old]").val(sectionJson.position);
		
		setTimeout(function(){form.find("select[name=i_position]").focus();}, 1);
	}
	
	function wepo_validate_section_form(form){
		var name  = form.find("input[name=i_name]").val();
		var title = form.find("input[name=i_title]").val();
		var position = form.find("select[name=i_position]").val();
		
		var err_msgs = '';
		if(name.trim() == ''){
			err_msgs = 'Name/ID is required';
		}else if(!isHtmlIdValid(name)){
			err_msgs = MSG_INVALID_NAME;
		}else if(title.trim() == ''){
			err_msgs = 'Title is required';
		}else if(position == ''){
			err_msgs = 'Please select a position';
		}		
		
		if(err_msgs != ''){
			form.find('.err_msgs').html(err_msgs);
			return false;
		}		
		return true;
	}	 
   /*-----------------------------------
	*---- SECTION FUNCTIONS - END ------
	*-----------------------------------*/
	 
   /*------------------------------------
	*---- PRODUCT FIELDS - SATRT --------
	*------------------------------------*/
	var OPTION_PROPERTY_FORM_FIELDS = {
		name  : {name : 'name', type : 'text'},
		type  : {name : 'type', type : 'select'},
		value : {name : 'value', type : 'text'},
		placeholder : {name : 'placeholder', type : 'text'},
		validate    : {name : 'validate', type : 'select', multiple : 1 },
		cssclass    : {name : 'cssclass', type : 'text'},
		title          : {name : 'title', type : 'text'},
		title_type     : {name : 'title_type', type : 'select'},
		title_color    : {name : 'title_color', type : 'text'},
		title_position : {name : 'title_position', type : 'select'},
		title_class    : {name : 'title_class', type : 'text'},
		subtitle          : {name : 'subtitle', type : 'text'},
		subtitle_type     : {name : 'subtitle_type', type : 'select'},
		subtitle_color    : {name : 'subtitle_color', type : 'text'},
		subtitle_position : {name : 'subtitle_position', type : 'select'},
		subtitle_class    : {name : 'subtitle_class', type : 'text'},
		//Price Properties
		is_price_field  : {name : 'is_price_field', type : 'checkbox'},
		price  : {name : 'price', type : 'text'},
		price_unit  : {name : 'price_unit', type : 'text'},
		price_type  : {name : 'price_type', type : 'select', change : 1},
		price_prefix  : {name : 'price_prefix', type : 'text'},
		price_sufix  : {name : 'price_sufix', type : 'text'},
		
		required  : {name : 'required', type : 'checkbox'},
		enabled  : {name : 'enabled', type : 'checkbox'},
		//Date Picker Properties
		default_date : {name : 'default_date', type : 'text'},
		date_format  : {name : 'date_format', type : 'text'},
		min_date   : {name : 'min_date', type : 'text'},
		max_date   : {name : 'max_date', type : 'text'},
		year_range : {name : 'year_range', type : 'text'},
		number_of_months : {name : 'number_of_months', type : 'text'},
		//Time Picker Properties
		min_time  : {name : 'min_time', type : 'text'},
		max_time  : {name : 'max_time', type : 'text'},
		time_step  : {name : 'time_step', type : 'text'},
		time_format  : {name : 'time_format', type : 'select'},
	};
	
	var OPTION_PROPERTY_DISPLAY_FIELDS = {
		name  		: {name : 'name', type : 'text'},
		type  		: {name : 'type', type : 'select'},
		title 		: {name : 'title', type : 'text'},
		placeholder : {name : 'placeholder', type : 'text'},
		validate 	: {name : 'validate', type : 'select', multiple : 1},
		required 	: {name : 'required', type : 'checkbox', status : 1},
		enabled  	: {name : 'enabled', type : 'checkbox', status : 1},
	};
	
	function get_property_field_value(form, type, name){
		var value = '';
		
		switch(type) {
			case 'select':
				value = form.find("select[name=i_"+name+"]").val();
				value = value == null ? '' : value;
				break;
				
			case 'checkbox':
				value = form.find("input[name=i_"+name+"]").prop('checked');
				value = value ? 1 : 0;
				break;
				
			default:
				value = form.find("input[name=i_"+name+"]").val();
				value = value == null ? '' : value;
		}	
		
		return value;
	}
	
	function set_property_field_value(form, type, name, value, multiple){
		switch(type) {
			case 'select':
				if(multiple == 1){
					value = value.split(",");
				}
				form.find("select[name=i_"+name+"]").val(value);
				break;
				
			case 'checkbox':
				value = value == 1 ? true : false;
				form.find("input[name=i_"+name+"]").prop('checked', value);
				break;
				
			default:
				form.find("input[name=i_"+name+"]").val(value);
		}	
	}
	
	function clear_field_form_general( form ){
		form.find('.err_msgs').html('');
		form.find("input[name=i_name]").val('');
		form.find("select[name=i_type]").prop('selectedIndex',0);
	}
	
	/*function clear_field_form(form){
		$.each( OPTION_PROPERTY_FORM_FIELDS, function( name, field ) {
			if(name == 'name' || name == 'type' || name == 'validate') {
				return true;
			}
	
			set_property_field_value(form, field['type'], name, field['value'], field['multiple']);
		});
		
		form.find("select[name=i_validator] option:selected").removeProp('selected');
		
		form.find("input[name=i_options_key]").val('');
		form.find("input[name=i_options_text]").val('');
		form.find("input[name=i_options_price]").val('');
		form.find("input[name=i_options_price_type]").val('');
		
		form.find("select[name=i_price_type]").prop('selectedIndex',0);
		form.find("select[name=i_time_format]").prop('selectedIndex',0);
	}*/
	
	function validate_option_properties_form(containerId, form){
		var err_msgs = '';
		
		var fname  = get_property_field_value(form, 'text', 'name');
		var ftype  = get_property_field_value(form, 'select', 'type');
		var ftitle = get_property_field_value(form, 'text', 'title');
		
		if(ftype == 'heading'){
			if(fname == ''){
				err_msgs = 'Name is required';
			}else if(!isHtmlIdValid(fname)){
				err_msgs = MSG_INVALID_NAME;
			}else if(ftitle == ''){
				err_msgs = 'Title is required';
			}		
		}else{
			if(ftype == ''){
				err_msgs = 'Type is required';
			}else if(fname == ''){
				err_msgs = 'Name is required';
			}else if(!isHtmlIdValid(fname)){
				err_msgs = MSG_INVALID_NAME;
			}
		}	
		
		if(err_msgs != ''){
			form.find('.err_msgs').html(err_msgs);
			$("#"+containerId).find('.thwepo_tab_general_link').click();
			return false;
		}
		return true;
	}
	
	_openNewFieldForm = function openNewFieldForm(tabName){
		tabName = tabName != '' ? tabName+'_' : tabName;
		
		var popup = $("#thwepo_new_field_form_pp");
		var form  = $("#thwepo_field_editor_form_new");
		
		clear_field_form_general(form);
		form.find("select[name=i_type]").change();	
		//clear_field_form(form);
		
	  	popup.dialog("open");
		popup.find('.thwepo_tab_general_link').click();
	}
	
	_fieldTypeChangeListner = function fieldTypeChangeListner(elm){
		var type = $(elm).val();
		var form = $(elm).closest('form');
		
		type = type == null ? 'default' : type;
		form.find('.thwepo_field_form_tab_general_placeholder').html($('#thwepo_field_form_id_'+type).html());
		setup_enhanced_multi_select(form);	
		setup_color_picker(form);
	}
	
	function wepo_add_new_row(form){
		var index = $('#thwepo_product_fields tbody tr').size();
		
		var valid = validate_option_properties_form('thwepo_new_field_form_pp', form);
		if(!valid){
			return false;
		}
		
		var newRowHidden = '';
		$.each( OPTION_PROPERTY_FORM_FIELDS, function( name, field ) {
			var type  = field['type'];								  
			var value = get_property_field_value(form, type, name);
			
			newRowHidden += '<input type="hidden" name="f_'+name+'['+index+']" class="f_'+name+'" value="'+value+'" />';
		});
		
		var newRowCells = '';
		$.each( OPTION_PROPERTY_DISPLAY_FIELDS, function( name, field ) {
			var type  = field['type'];								  
			var value = get_property_field_value(form, type, name);
			
			if(field['status'] == 1){
				var statusHtml = value == 1 ? '<span class="status-enabled tips">Yes</span>' : '-';
				newRowCells += '<td class="td_'+name+' status">'+statusHtml+'</td>';
			}else{
				newRowCells += '<td class="td_'+name+'">'+value+'</td>';
			}
		});
		
		var options_json = get_options(form);
		
		var rules_action = form.find("select[name=i_rules_action]").val();
		var rules_json = get_conditional_rules(form, false);
		
		var ajax_rules_action = form.find("select[name=i_rules_action_ajax]").val();
		var ajax_rules_json = get_conditional_rules(form, true);
		
		newRowHidden += '<input type="hidden" name="f_order['+index+']" class="f_order" value="'+index+'" />';
		newRowHidden += '<input type="hidden" name="f_deleted['+index+']" class="f_deleted" value="0" />';
		newRowHidden += '<input type="hidden" name="f_options['+index+']" class="f_options" value="'+options_json+'" />';
			
		newRowHidden += '<input type="hidden" name="f_rules_action['+index+']" class="f_rules_action" value="'+rules_action+'" />';
		newRowHidden += '<input type="hidden" name="f_rules['+index+']" class="f_rules" value="'+rules_json+'" />';
		
		newRowHidden += '<input type="hidden" name="f_rules_action_ajax['+index+']" class="f_rules_action_ajax" value="'+ajax_rules_action+'" />';
		newRowHidden += '<input type="hidden" name="f_rules_ajax['+index+']" class="f_rules_ajax" value="'+ajax_rules_json+'" />'
		
		var newRow = '<tr class="row_'+index+'">';
		newRow += '<td width="1%" class="sort ui-sortable-handle">'+ newRowHidden +'</td>';
		newRow += '<td class="td_select"><input type="checkbox" /></td>';		
		newRow += newRowCells;
		newRow += '<td class="td_edit" align="center"><button type="button" onclick="thwepoOpenEditFieldForm(this, '+index+')">Edit</button></td>';
		newRow += '</tr>';
		
		if(index > 0){
			$('#thwepo_product_fields tbody tr:last').after(newRow);
		}else{
			$('#thwepo_product_fields tbody').append(newRow);
		}
		
		return true;
	}
	
	_openEditFieldForm = function openEditFieldForm(elm, rowId){
		var row = $(elm).closest('tr');
		var popup = $("#thwepo_edit_field_form_pp");
		var form = $("#thwepo_field_editor_form_edit");
				
		populate_field_form_general(row, form, rowId);					
		form.find("select[name=i_type]").change();			
		populate_field_form(row, form, rowId);	
		
		popup.dialog("open");
		popup.find('.thwepo_tab_general_link').click();
	}
	
	function populate_field_form_general(row, form, rowId){
		var name = row.find(".f_name").val();
		var type = row.find(".f_type").val();
		
		form.find("input[name=i_rowid]").val(rowId);
		form.find("input[name=i_name]").val(name);
		form.find("select[name=i_type]").val(type);		
	}
	
	function populate_field_form(row, form, rowId){
		$.each( OPTION_PROPERTY_FORM_FIELDS, function( name, field ) {
			if(name == 'name' || name == 'type') {
				return true;
			}
	
			var type  = field['type'];								  
			var value = row.find(".f_"+name).val();
			
			set_property_field_value(form, type, name, value, field['multiple']);
			
			if(type == 'select'){
				if(field['multiple'] == 1 || field['change'] == 1){
					form.find("select[name=i_"+name+"]").trigger("change");
				}
			}
		});
		
		/*if(!is_price_field){
			price = '';
			price_unit = '';
			price_type = '';
			price_prefix = '';
			price_sufix = '';
		}*/
		
		var optionsJson = row.find(".f_options").val();
		populate_options_list(form, optionsJson);
		
		var rulesAction = row.find(".f_rules_action").val();
		var rulesActionAjax = row.find(".f_rules_action_ajax").val();
		
		rulesAction = rulesAction != '' ? rulesAction : 'show';
		rulesActionAjax = rulesActionAjax != '' ? rulesActionAjax : 'show';
		
		form.find("select[name=i_rules_action]").val(rulesAction);
		form.find("select[name=i_rules_action_ajax]").val(rulesActionAjax);
		
		var conditionalRules = row.find(".f_rules").val();
		var conditionalRulesAjax = row.find(".f_rules_ajax").val();
		
		populate_conditional_rules(form, conditionalRules, false);	
		populate_conditional_rules(form, conditionalRulesAjax, true);
	}
		
	function wepo_update_row(form){
		var valid = validate_option_properties_form('thwepo_edit_field_form_pp', form);
		if(!valid){
			return false;
		}
		
		var rowId = form.find("input[name=i_rowid]").val();
		var row = $('#thwepo_product_fields tbody').find('.row_'+rowId);
		
		$.each( OPTION_PROPERTY_FORM_FIELDS, function( name, field ) {
			var type  = field['type'];								  
			var value = get_property_field_value(form, type, name);
			
			row.find(".f_"+name).val(value);
		});
		
		var options_json = get_options(form);
		
		var rulesAction = form.find("select[name=i_rules_action]").val();
		var rulesActionAjax = form.find("select[name=i_rules_action_ajax]").val();
		
		var rules_json = get_conditional_rules(form, false);
		var ajax_rules_json = get_conditional_rules(form, true);
		
		row.find(".f_options").val(options_json);
		
		row.find(".f_rules_action").val(rulesAction);
		row.find(".f_rules_action_ajax").val(rulesActionAjax);
		
		row.find(".f_rules").val(rules_json);
		row.find(".f_rules_ajax").val(ajax_rules_json);
		
		$.each( OPTION_PROPERTY_DISPLAY_FIELDS, function( name, field ) {
			var type  = field['type'];								  
			var value = get_property_field_value(form, type, name);
			
			if(name == 'validate'){
				value = ""+value+"";
			}
			
			if(field['status'] == 1){
				value = value == 1 ? '<span class="status-enabled tips">Yes</span>' : '-';
			}
			row.find(".td_"+name).html(value);
		});
		
		return true;
	}
   /*------------------------------------
	*---- PRODUCT FIELDS - END ----------
	*------------------------------------*/
	
   /*------------------------------------
	*---- OPTIONS FUNCTIONS - SATRT -----
	*------------------------------------*/
	function get_options(form){
		var optionsKey  = form.find("input[name='i_options_key[]']").map(function(){ return $(this).val(); }).get();
		var optionsText = form.find("input[name='i_options_text[]']").map(function(){ return $(this).val(); }).get();
		var optionsPrice = form.find("input[name='i_options_price[]']").map(function(){ return $(this).val(); }).get();
		var optionsPriceType = form.find("select[name='i_options_price_type[]']").map(function(){ return $(this).val(); }).get();
		
		var optionsSize = optionsText.length;
		var optionsArr = [];
		
		for(var i=0; i<optionsSize; i++){
			var optionDetails = {};
			optionDetails["key"] = optionsKey[i];
			optionDetails["text"] = optionsText[i];
			optionDetails["price"] = optionsPrice[i];
			optionDetails["price_type"] = optionsPriceType[i];
			
			optionsArr.push(optionDetails);
		}
		
		var optionsJson = optionsArr.length > 0 ? JSON.stringify(optionsArr) : '';
		optionsJson = encodeURIComponent(optionsJson);
		return optionsJson;
	}
	
	function populate_options_list(form, optionsJson){
		var optionsHtml = "";
		
		if(optionsJson){
			try{
				optionsJson = decodeURIComponent(optionsJson);
				var optionsList = $.parseJSON(optionsJson);
				if(optionsList){
					jQuery.each(optionsList, function() {
						var percSelected = this.price_type === 'percentage' ? 'selected' : '';
						
						var html  = '<tr>';
						html += '<td style="width:190px;"><input type="text" name="i_options_key[]" value="'+this.key+'" placeholder="Option Value" style="width:180px;"/></td>';
						html += '<td style="width:190px;"><input type="text" name="i_options_text[]" value="'+this.text+'" placeholder="Option Text" style="width:180px;"/></td>';
						html += '<td style="width:80px;"><input type="text" name="i_options_price[]" value="'+this.price+'" placeholder="Price" style="width:70px;"/></td>';
						html += '<td style="width:130px;"><select name="i_options_price_type[]" value="'+this.price_type+'" style="width:120px;">';
						html += '<option value="">Normal</option><option value="percentage" '+percSelected+'>Percentage</option></select></td>';
						html += '<td style="width:30px;"><a href="javascript:void(0)" onclick="thwepoAddNewOptionRow(this)" class="add_link" title="Add new option">+</a></td>';
						html += '<td><a href="javascript:void(0)" onclick="thwepoRemoveOptionRow(this)" class="remove_link"  title="Remove option">x</a></td>';
						html += '</tr>';
						
						optionsHtml += html;
					});
				}
			}catch(err) {
				alert(err);
			}
		}
		
		var optionsTable = form.find(".thwepo-option-list tbody");
		if(optionsHtml){
			optionsTable.html(optionsHtml);
		}else{
			optionsTable.html(OPTION_ROW_HTML);
		}
	}
	
	addNewOptionRow = function addNewOptionRow(elm){
		var ptable = $(elm).closest('table');
		var optionsSize = ptable.find('tbody tr').size();
			
		if(optionsSize > 0){
			ptable.find('tbody tr:last').after(OPTION_ROW_HTML);
		}else{
			ptable.find('tbody').append(OPTION_ROW_HTML);
		}
	}
	
	removeOptionRow = function removeOptionRow(elm){
		var ptable = $(elm).closest('table');
		$(elm).closest('tr').remove();
		var optionsSize = ptable.find('tbody tr').size();
			
		if(optionsSize == 0){
			ptable.find('tbody').append(OPTION_ROW_HTML);
		}
	}
   /*------------------------------------
	*---- OPTIONS FUNCTIONS - END -------
	*------------------------------------*/
	
	show_subtitle_options = function show_subtitle_options(elm){
		var show = $(elm).prop('checked');
		if(show){
			$('tr.thwepo_subtitle_row').show();
		}else{
			$('tr.thwepo_subtitle_row').hide();
		}		
	}

   /*----------------------------------------
	*---- PRICE FIELD FUNCTIONS - START -----
	*----------------------------------------*/
	priceTypeChangeListener = function priceTypeChangeListener(elm){
		var row = $(elm).closest('tr');
		var priceType = $(elm).val();
		
		if(priceType === 'dynamic' || priceType === 'dynamic-excl-base-price'){
			row.find("input[name=i_price]").css('width','100px');
			row.find('.thwepo-dynamic-price-field').show();	
		}else{
			row.find("input[name=i_price]").css('width','250px');
			row.find('.thwepo-dynamic-price-field').hide();	
		}
	}
	
	show_price_fields = function show_price_fields(elm){
		var show = $(elm).prop('checked');
		if(show){
			$('tr.thwepo_price_row').show();
		}else{
			$('tr.thwepo_price_row').hide();
		}		
	}
   /*--------------------------------------
	*---- PRICE FIELD FUNCTIONS - END -----
	*--------------------------------------*/
	
   /*----------------------------------------------
	*---- CONDITIONAL RULES FUNCTIONS - SATRT -----
	*----------------------------------------------*/
	var RULE_OPERATOR_SET = {"equals" : "Equals to/ In", "not_equals" : "Not Equals to/ Not in"};
	var RULE_OPERAND_TYPE_SET = {"product" : "Product", "category" : "Category"};
	
	var OP_AND_HTML  = '<label class="thpl_logic_label">AND</label>';
		OP_AND_HTML += '<a href="javascript:void(0)" onclick="thwepoRemoveRuleRow(this)" class="thpl_delete_icon" title="Remove"></a>';
	var OP_OR_HTML   = '<tr class="thpl_logic_label_or"><td colspan="4" align="center">OR</td></tr>';
	
	var OP_HTML  = '<a href="javascript:void(0)" class="thpl_logic_link" onclick="thwepoAddNewConditionRow(this, 1)" title="">AND</a>';
		OP_HTML += '<a href="javascript:void(0)" class="thpl_logic_link" onclick="thwepoAddNewConditionRow(this, 2)" title="">OR</a>';
		OP_HTML += '<a href="javascript:void(0)" class="thpl_delete_icon" onclick="thwepoRemoveRuleRow(this)" title="Remove"></a>';
	
	var CONDITION_HTML = '', CONDITION_SET_HTML = '', CONDITION_SET_HTML_WITH_OR = '', RULE_HTML = '', RULE_SET_HTML = '';
	
	$(function() {
	    CONDITION_HTML  = '<tr class="thwepo_condition">';
		CONDITION_HTML += '<td width="25%">'+ prepareRuleOperandTypeSet('') +'</td>';	
		CONDITION_HTML += '<td width="25%">'+ prepareRuleOperatorSet('') +'</td>';
		CONDITION_HTML += '<td width="25%" class="thpladmin_rule_operand"><input type="text" name="i_rule_operand" style="width:200px;"/></td>';
		CONDITION_HTML += '<td>'+ OP_HTML +'</td></tr>';
		
	    CONDITION_SET_HTML  = '<tr class="thwepo_condition_set_row"><td>';
		CONDITION_SET_HTML += '<table class="thwepo_condition_set" width="100%" style=""><tbody>'+CONDITION_HTML+'</tbody></table>';
		CONDITION_SET_HTML += '</td></tr>';
		
	    CONDITION_SET_HTML_WITH_OR  = '<tr class="thwepo_condition_set_row"><td>';
		CONDITION_SET_HTML_WITH_OR += '<table class="thwepo_condition_set" width="100%" style=""><thead>'+OP_OR_HTML+'</thead><tbody>'+CONDITION_HTML+'</tbody></table>';
		CONDITION_SET_HTML_WITH_OR += '</td></tr>';
	
	    RULE_HTML  = '<tr class="thwepo_rule_row"><td>';
		RULE_HTML += '<table class="thwepo_rule" width="100%" style=""><tbody>'+CONDITION_SET_HTML+'</tbody></table>';
		RULE_HTML += '</td></tr>';	
		
	    RULE_SET_HTML  = '<tr class="thwepo_rule_set_row"><td>';
		RULE_SET_HTML += '<table class="thwepo_rule_set" width="100%"><tbody>'+RULE_HTML+'</tbody></table>';
		RULE_SET_HTML += '</td></tr>';
	});
	
	function prepareRuleOperandTypeSet(value){
		var html = '<select name="i_rule_operand_type" style="width:200px;" onchange="thwepoRuleOperandTypeChangeListner(this)" value="'+ value +'">';
		html += '<option value=""></option>';
		for(var index in RULE_OPERAND_TYPE_SET) {
			var selected = index === value ? "selected" : "";
			html += '<option value="'+index+'" '+selected+'>'+RULE_OPERAND_TYPE_SET[index]+'</option>';
		}
		html += '</select>';
		return html;
	}
	
	function prepareRuleOperatorSet(value){
		var html = '<select name="i_rule_operator" style="width:200px;" value="'+ value +'">';
		html += '<option value=""></option>';
		for(var index in RULE_OPERATOR_SET) {
			var selected = index === value ? "selected" : "";
			html += '<option value="'+index+'" '+selected+'>'+RULE_OPERATOR_SET[index]+'</option>';
		}
		html += '</select>';
		return html;
	}
	
	function prepareRuleOperandSet(operand_type, operand){
		var html = '<input type="hidden" name="i_rule_operand_hidden" value="'+operand+'"/>';
		if(operand_type === "product"){
			html += $("#thwepo_product_select").html();
			
		}else if(operand_type === "category"){
			html += $("#thwepo_product_cat_select").html();
			
		}else{
			html += '<input type="text" name="i_rule_operand" style="width:200px;" value="'+value+'"/>';
		}
		return html;
	}
	
	function is_valid_condition(condition){
		if(condition["operand_type"] && condition["operator"]){
			return true;
		}
		return false;
	}
	
	this.ruleOperandTypeChangeListner = function(elm){
		var operand_type = $(elm).val();
		var condition_row = $(elm).closest("tr.thwepo_condition");
		var target = condition_row.find("td.thpladmin_rule_operand");
		
		if(operand_type === 'category'){
			target.html( $("#thwepo_product_cat_select").html() );
		}else{
			target.html( $("#thwepo_product_select").html() );
		}	
		setup_enhanced_multi_select(condition_row);		
	}
	
	/*this.ruleOperandTypeChangeListner = function(elm){
		e.data.prepareRequest( "GET", $(elm).val(), "" );
		e.data.dock( $(this).val(), $(this) );		
	}*/
	
	_add_new_rule_row = function add_new_rule_row(elm, op){
		var condition_row = $(elm).closest('tr');
		condition = {};
		condition["operand_type"] = condition_row.find("select[name=i_rule_operand_type]").val();
		condition["operator"] = condition_row.find("select[name=i_rule_operator]").val();
		condition["operand"] = condition_row.find("select[name=i_rule_operand]").val();
		if(!is_valid_condition(condition)){
			alert('Please provide a valid condition.');
			return;
		}
		
		if(op == 1){
			var conditionSetTable = $(elm).closest('.thwepo_condition_set');
			var conditionSetSize  = conditionSetTable.find('tbody tr.thwepo_condition').size();
			
			if(conditionSetSize > 0){
				$(elm).closest('td').html(OP_AND_HTML);
				conditionSetTable.find('tbody tr.thwepo_condition:last').after(CONDITION_HTML);
			}else{
				conditionSetTable.find('tbody').append(CONDITION_HTML);
			}
		}else if(op == 2){
			var ruleTable = $(elm).closest('.thwepo_rule');
			var ruleSize  = ruleTable.find('tbody tr.thwepo_condition_set_row').size();
			
			if(ruleSize > 0){
				ruleTable.find('tbody tr.thwepo_condition_set_row:last').after(CONDITION_SET_HTML_WITH_OR);
			}else{
				ruleTable.find('tbody').append(CONDITION_SET_HTML);
			}
		}	
	}
	
	_remove_rule_row = function remove_rule_row(elm){
		var ctable = $(elm).closest('table.thwepo_condition_set');
		var rtable = $(elm).closest('table.thwepo_rule');
		
		$(elm).closest('tr.thwepo_condition').remove();
		
		var cSize = ctable.find('tbody tr.thwepo_condition').size();
		if(cSize == 0){
			ctable.closest('tr.thwepo_condition_set_row').remove();
		}
		
		rSize = rtable.find('tbody tr.thwepo_condition_set_row').size();
		if(cSize == 0 && rSize == 0){
			rtable.find('tbody').append(CONDITION_SET_HTML);
		}
	}
	
	function get_conditional_rules(elm, ajaxFlag){
		var rulesTable;
		if(ajaxFlag){
			rulesTable = $(elm).find(".thwepo_conditional_rules_ajax tbody");
		}else{
			rulesTable = $(elm).find(".thwepo_conditional_rules tbody");	
		}
		
		var conditionalRules = [];
		rulesTable.find("tr.thwepo_rule_set_row").each(function() {
			var ruleSet = [];
			$(this).find("table.thwepo_rule_set tbody tr.thwepo_rule_row").each(function() {
				var rule = [];															 
				$(this).find("table.thwepo_rule tbody tr.thwepo_condition_set_row").each(function() {
					var conditions = [];
					$(this).find("table.thwepo_condition_set tbody tr.thwepo_condition").each(function() {
						condition = {};
						if(ajaxFlag){
							condition["operand_type"] = $(this).find("input[name=i_rule_operand_type]").val();
							condition["value"] = $(this).find("input[name=i_rule_value]").val();
						}else{
							condition["operand_type"] = $(this).find("select[name=i_rule_operand_type]").val();	
						}
						condition["operator"] = $(this).find("select[name=i_rule_operator]").val();
						condition["operand"] = $(this).find("select[name=i_rule_operand]").val();
						
						if(is_valid_condition(condition)){
							conditions.push(condition);
						}
					});
					if(conditions.length > 0){
						rule.push(conditions);
					}
				});
				if(rule.length > 0){
					ruleSet.push(rule);
				}
			});
			if(ruleSet.length > 0){
				conditionalRules.push(ruleSet);
			}
		});
		
		var conditionalRulesJson = conditionalRules.length > 0 ? JSON.stringify(conditionalRules) : '';
		conditionalRulesJson = encodeURIComponent(conditionalRulesJson);
		//conditionalRulesJson = conditionalRulesJson.replace(/"/g, "'");
		
		return conditionalRulesJson;
	}
		
	function populate_conditional_rules(form, conditionalRulesJson, ajaxFlag){
		var conditionalRulesHtml = "";
		if(conditionalRulesJson){
			try{
				conditionalRulesJson = decodeURIComponent(conditionalRulesJson);
				var conditionalRules = $.parseJSON(conditionalRulesJson);
				if(conditionalRules){
					jQuery.each(conditionalRules, function() {
						var ruleSet = this;	
						var rulesHtml = '';
						
						jQuery.each(ruleSet, function() {
							var rule = this;
							var conditionSetsHtml = '';
							
							var y=0;
							var ruleSize = rule.length;
							jQuery.each(rule, function() {
								var conditions = this;								   	
								var conditionsHtml = '';
								
								var x=1;
								var size = conditions.length;
								jQuery.each(conditions, function() {
									var lastRow = (x==size) ? true : false;
									var conditionHtml = populate_condition_html(this, lastRow, ajaxFlag);
									if(conditionHtml){
										conditionsHtml += conditionHtml;
									}
									x++;
								});
								
								var firstRule = (y==0) ? true : false;
								var conditionSetHtml = populate_condition_set_html(conditionsHtml, firstRule);
								if(conditionSetHtml){
									conditionSetsHtml += conditionSetHtml;
								}
								y++;
							});
							
							var ruleHtml = populate_rule_html(conditionSetsHtml);
							if(ruleHtml){
								rulesHtml += ruleHtml;
							}
						});
						
						var ruleSetHtml = populate_rule_set_html(rulesHtml);
						if(ruleSetHtml){
							conditionalRulesHtml += ruleSetHtml;
						}
					});
				}
			}catch(err) {
				alert(err);
			}
		}
		
		var conditionalRulesTable;
		if(ajaxFlag){
			conditionalRulesTable = form.find(".thwepo_conditional_rules_ajax tbody");
		}else{
			conditionalRulesTable = form.find(".thwepo_conditional_rules tbody");
		}
		
		if(conditionalRulesHtml){
			conditionalRulesTable.html(conditionalRulesHtml);
			setup_enhanced_multi_select(conditionalRulesTable);
			
			conditionalRulesTable.find('tr.thwepo_condition').each(function(){
				var operantVal = $(this).find("input[name=i_rule_operand_hidden]").val();	
				operantVal = operantVal.split(",");
				$(this).find("select[name=i_rule_operand]").val(operantVal).trigger("change");
			});
			
			conditionalRulesTable.find("select[name=i_rule_operator]").change();
		}else{
			if(ajaxFlag){
				conditionalRulesTable.html(RULE_SET_HTML_AJAX);
			}else{
				conditionalRulesTable.html(RULE_SET_HTML);
			}
			setup_enhanced_multi_select(conditionalRulesTable);
		}
	}
	
	function populate_rule_set_html(ruleHtml){
		var html = '';
		if(ruleHtml){
			html += '<tr class="thwepo_rule_set_row"><td><table class="thwepo_rule_set" width="100%"><tbody>';
			html += ruleHtml;
			html += '</tbody></table></td></tr>';
		}
		return html;
	}
	
	function populate_rule_html(conditionSetHtml){
		var html = '';
		if(conditionSetHtml){
			html += '<tr class="thwepo_rule_row"><td><table class="thwepo_rule" width="100%" style=""><tbody>';
			html += conditionSetHtml;
			html += '</tbody></table></td></tr>';
		}
		return html;
	}
	
	function populate_condition_set_html(conditionsHtml, firstRule){
		var html = '';
		if(conditionsHtml){
			if(firstRule){
				html += '<tr class="thwepo_condition_set_row"><td><table class="thwepo_condition_set" width="100%" style=""><tbody>';
				html += conditionsHtml;
				html += '</tbody></table></td></tr>';
			}else{
				html += '<tr class="thwepo_condition_set_row"><td><table class="thwepo_condition_set" width="100%" style=""><thead>'+OP_OR_HTML+'</thead><tbody>';
				html += conditionsHtml;
				html += '</tbody></table></td></tr>';
			}
		}
		return html;
	}
	
	function populate_condition_html(condition, lastRow, ajaxFlag){
		var html = '';
		if(condition){
			if(ajaxFlag){
				var actionsHtml = lastRow ? OP_HTML_AJAX : OP_AND_HTML_AJAX;
				
				html += '<tr class="thwepo_condition">';
				html += '<td width="25%">'+ prepareRuleOperandSetAjax(condition.operand) +'</td>';
				html += '<td width="25%">'+ prepareRuleOperatorSetAjax(condition.operator) +'</td>';
				html += '<td width="25%">'+ prepareRuleValueSetAjax(condition.value) +'</td>';
				html += '<td>'+ actionsHtml+'</td></tr>';
			}else{
				var actionsHtml = lastRow ? OP_HTML : OP_AND_HTML;
			
				html += '<tr class="thwepo_condition">';
				html += '<td width="25%">'+ prepareRuleOperatorSet(condition.operator) +'</td>';
				html += '<td width="25%">'+ prepareRuleOperandTypeSet(condition.operand_type) +'</td>';
				html += '<td width="25%" class="thpladmin_rule_operand">'+ prepareRuleOperandSet(condition.operand_type, condition.operand) +'</td>';
				html += '<td>'+ actionsHtml+'</td></tr>';			
			}
		}
		return html;
	}	
		
   /*----------------------------------------------
	*---- CONDITIONAL RULES FUNCTIONS - END -------
	*----------------------------------------------*/
	
   /*---------------------------------------------------
	*---- CONDITIONAL RULES FUNCTIONS AJAX - SATRT -----
	*---------------------------------------------------*/
	var RULE_OPERATOR_SET_AJAX = {
		"empty" : "Is empty", "not_empty" : "Is not empty",
		"value_eq" : "Value equals to", "value_ne" : "Value not equals to", "value_gt" : "Value greater than", "value_le" : "Value less than",
		"checked" : "Is checked", "not_checked" : "Is not checked"
	};
	
	var OP_AND_HTML_AJAX  = '<label class="thpl_logic_label">AND</label>';
		OP_AND_HTML_AJAX += '<a href="javascript:void(0)" onclick="thwepoRemoveRuleRowAjax(this)" class="thpl_delete_icon" title="Remove"></a>';
	
	var OP_HTML_AJAX  = '<a href="javascript:void(0)" class="thpl_logic_link" onclick="thwepoAddNewConditionRowAjax(this, 1)" title="">AND</a>';
		OP_HTML_AJAX += '<a href="javascript:void(0)" class="thpl_logic_link" onclick="thwepoAddNewConditionRowAjax(this, 2)" title="">OR</a>';
		OP_HTML_AJAX += '<a href="javascript:void(0)" class="thpl_delete_icon" onclick="thwepoRemoveRuleRowAjax(this)" title="Remove"></a>';
	
	var CONDITION_HTML_AJAX = '', CONDITION_SET_HTML_AJAX = '', CONDITION_SET_HTML_WITH_OR_AJAX = '', RULE_HTML_AJAX = '', RULE_SET_HTML_AJAX = '';
	
	$(function() {
	    CONDITION_HTML_AJAX  = '<tr class="thwepo_condition">';
		CONDITION_HTML_AJAX += '<td width="25%">'+ prepareRuleOperandSetAjax('') +'</td>';
		CONDITION_HTML_AJAX += '<td width="25%">'+ prepareRuleOperatorSetAjax('') +'</td>';	
		CONDITION_HTML_AJAX += '<td width="25%"><input type="text" name="i_rule_value" style="width:200px;"/></td>';
		CONDITION_HTML_AJAX += '<td>'+ OP_HTML_AJAX +'</td></tr>';
		
	    CONDITION_SET_HTML_AJAX  = '<tr class="thwepo_condition_set_row"><td>';
		CONDITION_SET_HTML_AJAX += '<table class="thwepo_condition_set" width="100%" style=""><tbody>'+CONDITION_HTML_AJAX+'</tbody></table>';
		CONDITION_SET_HTML_AJAX += '</td></tr>';
		
	    CONDITION_SET_HTML_WITH_OR_AJAX  = '<tr class="thwepo_condition_set_row"><td>';
		CONDITION_SET_HTML_WITH_OR_AJAX += '<table class="thwepo_condition_set" width="100%" style=""><thead>'+OP_OR_HTML+'</thead><tbody>'+CONDITION_HTML_AJAX+'</tbody></table>';
		CONDITION_SET_HTML_WITH_OR_AJAX += '</td></tr>';
	
	    RULE_HTML_AJAX  = '<tr class="thwepo_rule_row"><td>';
		RULE_HTML_AJAX += '<table class="thwepo_rule" width="100%" style=""><tbody>'+CONDITION_SET_HTML_AJAX+'</tbody></table>';
		RULE_HTML_AJAX += '</td></tr>';	
		
	    RULE_SET_HTML_AJAX  = '<tr class="thwepo_rule_set_row"><td>';
		RULE_SET_HTML_AJAX += '<table class="thwepo_rule_set" width="100%"><tbody>'+RULE_HTML_AJAX+'</tbody></table>';
		RULE_SET_HTML_AJAX += '</td></tr>';
	});
	
	function prepareRuleOperatorSetAjax(value){
		var html = '<select name="i_rule_operator" style="width:200px;" value="'+ value +'" onchange="thwepoRuleOperatorChangeListnerAjax(this)" >';
		html += '<option value=""></option>';
		for(var index in RULE_OPERATOR_SET_AJAX) {
			var selected = index === value ? "selected" : "";
			html += '<option value="'+index+'" '+selected+'>'+RULE_OPERATOR_SET_AJAX[index]+'</option>';
		}
		html += '</select>';
		return html;
	}
	
	function prepareRuleOperandSetAjax(value){
		var html = '<input type="hidden" name="i_rule_operand_type" value="field"/>';
		html += '<input type="hidden" name="i_rule_operand_hidden" value="'+value+'"/>';
		html += $("#thwepo_product_fields_select").html();
		return html;
	}
	
	function prepareRuleValueSetAjax(value){
		var html = '<input type="text" name="i_rule_value" style="width:200px;" value="'+value+'" />';
		return html;
	}
	
	function isValidConditionAjax(condition){
		if(condition["operand_type"] && condition["operator"]){
			return true;
		}
		return false;
	}
	
	_add_new_rule_row_ajax = function addNewRuleRowAjax(elm, op){
		var condition_row = $(elm).closest('tr');
		
		condition = {};
		condition["operand_type"] = condition_row.find("input[name=i_rule_operand_type]").val();
		condition["operator"] = condition_row.find("select[name=i_rule_operator]").val();
		condition["operand"] = condition_row.find("select[name=i_rule_operand]").val();
		condition["value"] = condition_row.find("input[name=i_rule_value]").val();
		
		if(!isValidConditionAjax(condition)){
			alert('Please provide a valid condition.');
			return;
		}
		
		if(op == 1){
			var conditionSetTable = $(elm).closest('.thwepo_condition_set');
			var conditionSetSize  = conditionSetTable.find('tbody tr.thwepo_condition').size();
			
			if(conditionSetSize > 0){
				$(elm).closest('td').html(OP_AND_HTML_AJAX);
				conditionSetTable.find('tbody tr.thwepo_condition:last').after(CONDITION_HTML_AJAX);
			}else{
				conditionSetTable.find('tbody').append(CONDITION_HTML_AJAX);
			}
			
			setup_enhanced_multi_select(conditionSetTable);
			
		}else if(op == 2){
			var ruleTable = $(elm).closest('.thwepo_rule');
			var ruleSize  = ruleTable.find('tbody tr.thwepo_condition_set_row').size();
			
			if(ruleSize > 0){
				ruleTable.find('tbody tr.thwepo_condition_set_row:last').after(CONDITION_SET_HTML_WITH_OR_AJAX);
			}else{
				ruleTable.find('tbody').append(CONDITION_SET_HTML_AJAX);
			}
			
			setup_enhanced_multi_select(ruleTable);
		}
	}
	
	_remove_rule_row_ajax = function removeRuleRowAjax(elm){
		var ctable = $(elm).closest('table.thwepo_condition_set');
		var rtable = $(elm).closest('table.thwepo_rule');
		
		$(elm).closest('tr.thwepo_condition').remove();
		
		var cSize = ctable.find('tbody tr.thwepo_condition').size();
		if(cSize == 0){
			ctable.closest('tr.thwepo_condition_set_row').remove();
		}
		
		rSize = rtable.find('tbody tr.thwepo_condition_set_row').size();
		if(cSize == 0 && rSize == 0){
			rtable.find('tbody').append(CONDITION_SET_HTML_AJAX);
		}
		
		setup_enhanced_multi_select(rtable);
	}
		
	this.ruleOperatorChangeListnerAjax = function(elm){
		var operator = $(elm).val();
		var condition_row = $(elm).closest("tr.thwepo_condition");
		var ruleValuElm = condition_row.find("input[name=i_rule_value]");
		
		if(operator === 'empty' || operator === 'not_empty' || operator === 'checked' || operator === 'not_checked'){
			ruleValuElm.val('');
			ruleValuElm.prop("readonly", true);
		}else{
			ruleValuElm.prop("readonly", false);
		}	
	}
   /*---------------------------------------------------
	*---- CONDITIONAL RULES FUNCTIONS AJAX - END -------
	*---------------------------------------------------*/
	
   /*---------------------------------------
	* Remove fields functions - START
	*----------------------------------------*/
	_removeSelectedFields = function removeSelectedFields(){
		$('#thwepo_product_fields tbody tr').removeClass('strikeout');
		$('#thwepo_product_fields tbody input:checkbox[name=select_field]:checked').each(function () {
			var row = $(this).closest('tr');
			if(!row.hasClass("strikeout")){
				row.addClass("strikeout");
			}
			row.find(".f_deleted").val(1);
			row.find(".f_edit_btn").prop('disabled', true);
	  	});	
	}
   /*---------------------------------------
	* Remove fields functions - END
	*----------------------------------------*/
	
   /*---------------------------------------
	* Enable or Disable fields functions - START
	*----------------------------------------*/
	_enableDisableSelectedFields = function enableDisableSelectedFields(enabled){
		$('#thwepo_product_fields tbody input:checkbox[name=select_field]:checked').each(function(){
			var row = $(this).closest('tr');
			if(enabled == 0){
				if(!row.hasClass("thwepo-disabled")){
					row.addClass("thwepo-disabled");
				}
			}else{
				row.removeClass("thwepo-disabled");				
			}
			
			row.find(".f_edit_btn").prop('disabled', enabled == 1 ? false : true);
			row.find(".td_enabled").html(enabled == 1 ? '<span class="status-enabled tips">Yes</span>' : '-');
			row.find(".f_enabled").val(enabled);
	  	});	
	}
   /*-----------------------------------------
	* Enable or Disable fields functions - END
	*----------------------------------------*/	
	
   /*-------------------------
	* Ajax Services - SATRT
	*------------------------*/
	this.reloadHtml = function( _where ) {
		_where.html( this.response.payload );
	}
	
	/* convert string to url slug */
	this.sanitizeStr = function( str ) {
		return str.toLowerCase().replace(/[^\w ]+/g,'').replace(/ +/g,'_');
	};	 
	
	this.escapeQuote = function( str ) {
		str = str.replace( /[']/g, '&#39;' );
		str = str.replace( /["]/g, '&#34;' );
		return str;
	}
	
	this.unEscapeQuote = function( str ) {
		str = str.replace( '&#39;', "'" );
		str = str.replace( '&#34;', '"' );
		return str;
	}
	
	this.prepareRequest = function( _request, _context, _payload ) {
		this.request = {
			request : _request,
			context : _context,
			payload : _payload
		};
	};
	
	this.prepareResponse = function( _status, _msg, _data ) {
		this.response = {
			status : _status,
			message : _msg,
			payload : _data
		};
	};
	
	this.dock = function( _action, _target ) {		
		var me = this;
		/* see the ajax handler is free */
		if( !this.ajaxFlaQ ) {
			return;
		}		
		
		$.ajax({  
			type       : "POST",  
			data       : { action : "WEPO_Ajax_Listener", THWEPO_AJAX_PARAM : JSON.stringify(this.request)},  
			dataType   : "json",  
			url        : wepo_var.ajaxurl,  
			beforeSend : function(){  				
				/* enable the ajax lock - actually it disable the dock */
				me.ajaxFlaQ = false;				
			},  
			success    : function(data) {				
				/* disable the ajax lock */
				me.ajaxFlaQ = true;				
				me.prepareResponse( data.status, data.message, data.data );		               

				/* handle the response and route to appropriate target */
				if( me.response.status ) {
					me.responseHandler( _action, _target );
				} else {
					/* alert the user that some thing went wrong */
					//me.responseHandler( _action, _target );
				}				
			},  
			error      : function(jqXHR, textStatus, errorThrown) {                    
				/* disable the ajax lock */
				me.ajaxFlaQ = true;
			}  
		});		
	};
		
	this.responseHandler = function( _action, _target ){		
		if( _action == "product" ) {
			this.reloadHtml( _target.closest("tr.thwepo_condition").find("td.thpladmin_rule_operand") );
		} else if( _action == "product_cat" ) {
			this.reloadHtml( _target.closest("tr.thwepo_condition").find("td.thpladmin_rule_operand") );
		}	
	};
   /*------------------------
	* Ajax Services - END
	*------------------------*/
				
	return {
		openNewSectionForm : _openNewSectionForm,
		openEditSectionForm : _openEditSectionForm,
		removeSection : _removeSection,
		openNewFieldForm : _openNewFieldForm,
		openEditFieldForm : _openEditFieldForm,
		removeSelectedFields : _removeSelectedFields,
		enableDisableSelectedFields : _enableDisableSelectedFields,
		fieldTypeChangeListner : _fieldTypeChangeListner,
		ruleOperandTypeChangeListner : ruleOperandTypeChangeListner,
		ruleOperatorChangeListnerAjax : ruleOperatorChangeListnerAjax,
		selectAllFields : _selectAllFields,
		add_new_rule_row : _add_new_rule_row,
		remove_rule_row : _remove_rule_row,
		add_new_rule_row_ajax : _add_new_rule_row_ajax,
		remove_rule_row_ajax : _remove_rule_row_ajax,
		show_subtitle_options : show_subtitle_options,
		show_price_fields : show_price_fields,
		openFormTab : openFormTab,
		addNewOptionRow : addNewOptionRow,
		removeOptionRow : removeOptionRow,
		priceTypeChangeListener : priceTypeChangeListener,
   	};
}(window.jQuery, window, document));	

function thwepoOpenNewSectionForm(){
	thwepo_settings.openNewSectionForm();		
}

function thwepoOpenEditSectionForm(section){
	thwepo_settings.openEditSectionForm(section);		
}

function thwepoRemoveSection(elm){
	thwepo_settings.removeSection(elm);	
}

function thwepoOpenNewFieldForm(tabName){
	thwepo_settings.openNewFieldForm(tabName);		
}

function thwepoOpenEditFieldForm(elm, rowId){
	thwepo_settings.openEditFieldForm(elm, rowId);		
}
	
function thwepoRemoveSelectedFields(){
	thwepo_settings.removeSelectedFields();
}

function thwepoEnableSelectedFields(){
	thwepo_settings.enableDisableSelectedFields(1);
}

function thwepoDisableSelectedFields(){
	thwepo_settings.enableDisableSelectedFields(0);
}

function thwepoFieldTypeChangeListner(elm){	
	thwepo_settings.fieldTypeChangeListner(elm);
}
	
function thwepoSelectAllProductFields(elm){
	thwepo_settings.selectAllFields(elm);
}

function thwepoRuleOperandTypeChangeListner(elm){
	thwepo_settings.ruleOperandTypeChangeListner(elm);
}

function thwepoAddNewConditionRow(elm, op){
	thwepo_settings.add_new_rule_row(elm, op);
}

function thwepoRemoveRuleRow(elm){
	thwepo_settings.remove_rule_row(elm);
}

function thwepoAddNewConditionRowAjax(elm, op){
	thwepo_settings.add_new_rule_row_ajax(elm, op);
}
function thwepoRemoveRuleRowAjax(elm){
	thwepo_settings.remove_rule_row_ajax(elm);
}

function thwepoRuleOperatorChangeListnerAjax(elm){
	thwepo_settings.ruleOperatorChangeListnerAjax(elm);
}

function thwepo_show_subtitle_options(elm){
	thwepo_settings.show_subtitle_options(elm);
}

function thwepo_show_price_fields(elm){
	thwepo_settings.show_price_fields(elm);
}

function thwepoOpenFormTab(elm, tab, form_type){
	thwepo_settings.openFormTab(elm, tab, form_type);	
}

function thwepoAddNewOptionRow(elm){
	thwepo_settings.addNewOptionRow(elm);
}
function thwepoRemoveOptionRow(elm){
	thwepo_settings.removeOptionRow(elm);
}

function thwepoPriceTypeChangeListener(elm){
	thwepo_settings.priceTypeChangeListener(elm);
}