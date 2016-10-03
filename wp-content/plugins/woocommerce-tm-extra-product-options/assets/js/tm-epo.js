(function($) {
    "use strict";

    function getSystemDecimalSeparator() {
        var n = 1.1;
        n = /^1(.+)1$/.exec(n.toLocaleString())[1]
        return n;
    }

    String.prototype.tmtoFloat = function(){
        var a=accounting.unformat(this,local_input_decimal_separator),
            n=parseFloat(a);
        if (isNaN(n)){
            return a;
        }
        return n;
    };
    String.prototype.tm_isNumeric = function () {
        return !isNaN(parseFloat(this)) && isFinite(this);
    };

    var local_input_decimal_separator = (tm_epo_js.tm_epo_global_input_decimal_separator=="")?tm_epo_js.currency_format_decimal_sep:getSystemDecimalSeparator(),
        local_decimal_separator = (tm_epo_js.tm_epo_global_displayed_decimal_separator=="")?tm_epo_js.currency_format_decimal_sep:getSystemDecimalSeparator(),
        local_thousand_separator = (tm_epo_js.tm_epo_global_displayed_decimal_separator=="")?tm_epo_js.currency_format_thousand_sep:(getSystemDecimalSeparator()==","?".":","),
        global_variation_object=false,
        late_variation_event = [],
        tm_lazyload_container=false,
        epo_selector = '.tc-extra-product-options',
        add_to_cart_selector = 'input[name="add-to-cart"]',
        tc_add_to_cart_selector = 'input.tc-add-to-cart',
        body=$("body"),
        variations_form_is_loaded=false,
        native_product_price_selector='.woocommerce div.product p.price',
        native_product_price_selector_initial_html=$(native_product_price_selector).length>0?$(native_product_price_selector).html():'',
        _window=$(window),
        _document=$(document),
        template_engine = {
            'price'                 : wp.template( 'tc-price' ),
            'sale_price'            : wp.template( 'tc-sale-price' ),
            'tc_section_pop_link'   : wp.template( 'tc-section-pop-link' ),
            'tc_floating_box'       : wp.template( 'tc-floating-box' ),
            'tc_floating_box_nks'   : wp.template( 'tc-floating-box-nks' ),
            'tc_chars_remanining'   : wp.template( 'tc-chars-remanining' ),
            'tc_final_totals'       : wp.template( 'tc-final-totals' ),
            'tc_lightbox'           : wp.template( 'tc-lightbox' ),
            'tc_lightbox_zoom'      : wp.template( 'tc-lightbox-zoom' ),
        };

    $.extend($.tc_validator.messages, {
        required: tm_epo_js.tm_epo_global_validator_messages["required"],
        email: tm_epo_js.tm_epo_global_validator_messages["email"],
        url: tm_epo_js.tm_epo_global_validator_messages["url"],
        number: tm_epo_js.tm_epo_global_validator_messages["number"],
        digits: tm_epo_js.tm_epo_global_validator_messages["digits"],
        maxlength: $.tc_validator.format( tm_epo_js.tm_epo_global_validator_messages["maxlength"] ),
        minlength: $.tc_validator.format( tm_epo_js.tm_epo_global_validator_messages["minlength"] ),
        max: $.tc_validator.format( tm_epo_js.tm_epo_global_validator_messages["max"] ),
        min: $.tc_validator.format( tm_epo_js.tm_epo_global_validator_messages["min"] )
    });

    // variations checker
    $.fn.tm_find_matching_variations = function( product_variations, settings ) {
        var matching = [];

        if (product_variations){
            for ( var i = 0; i < product_variations.length; i++ ) {
                var variation = product_variations[i];
                var variation_id = variation.variation_id;

                if ( $.fn.tm_variations_match( variation.attributes, settings ) ) {
                    matching.push( variation );
                }
            }
        }        

        return matching;
    };

    $.fn.tm_variations_match = function( attrs1, attrs2 ) {
        var match = true;

        for ( var attr_name in attrs1 ) {
            if ( attrs1.hasOwnProperty( attr_name ) ) {
                var val1 = attrs1[ attr_name ];
                var val2 = attrs2[ attr_name ];

                if ( val1 !== undefined && val2 !== undefined && val1.length !== 0 && val2.length !== 0 && val1 !== val2 ) {
                    match = false;
                }
            }
        }

        return match;
    };

    // tc-lightbox
    if (!$().tclightbox) {
        $.fn.tclightbox = function() {
            var elements = this;
            
            if (elements.length==0){
                return;
            }

            return elements.each(function(){
                var $this=$(this);
                if ($this.is(".tcinit")){
                    return;
                }
                var _imgsrc = $this.attr("src") || $this.attr('data-original'),
                    _label = $this.closest("label"),
                    _input = _label.closest(".tmcp-field-wrap").find(".tm-epo-field[id='"+_label.attr("for") +"']");
                    _imgsrc= _input.attr("data-imagel") || _input.attr("data-imagep") || _input.attr("data-image") || _imgsrc;
                
                if (!_imgsrc){
                    return;
                }
                
                $this.addClass("tcinit").before( $.fn.tm_template(template_engine.tc_lightbox,{}) );
                var tclightboxwrap=$this.prev();
                tclightboxwrap.prepend($this);
                var _img_button = tclightboxwrap.find('.tc-lightbox-button');
                
                _img_button.addClass('tcinit').on("click.tclightbox",function(){
                    if ($('.tc-closing.tc-lightbox').length>0){
                        return;
                    }
                    var o = $.tm_getPageSize(),
                        _img = $('<img>').addClass('tc-lightbox-img').attr('src',_imgsrc).css('maxHeight',o[3]+'px'),                    
                    temp_floatbox = $("body").tm_floatbox({
                        "fps": 1,
                        "ismodal": false,
                        "refresh": "fixed",
                        "width": "auto",
                        "height": "auto",
                        "top": "0%",
                        "left": "0%",
                        "classname": "flasho tc-lightbox",
                        "animateIn":'tc-lightbox-zoomIn',
                        "animateOut":'tc-lightbox-zoomOut',
                        "data": $.fn.tm_template(template_engine.tc_lightbox_zoom,{'img':_img[0].outerHTML}),
                        "zIndex":102001
                    });
                    $('.tc-lightbox-img, .tc-lightbox-button-close').on('click',function(){
                        temp_floatbox.cancelfunc();
                    });                
                });

            });

        };
        _document.ready(function(){
            $('.tc-lightbox-image').not(".tm-extra-product-options-variations .radio_image").tclightbox();
        });
    }

    // Start Section popup
    if (!$().tmsectionpoplink) {
        $.fn.tmsectionpoplink = function() {
            var elements = this;
            
            if (elements.length==0){
                return;
            }

            return elements.each(function(){
                var $this       = $(this),
                    id          = $this.attr('data-sectionid'),
                    title       = $this.attr('data-title')?$this.attr('data-title'):tm_epo_js.i18n_addition_options,
                    section     = $('.cpf-section[data-uniqid="'+id+'"]'),
                    html_clone  = section.tm_clone(),
                    clicked     = false,
                    _ovl        = $('<div class="fl-overlay"></div>').css({
                        zIndex: ($this.zIndex - 1),
                        opacity: .8
                    });

                var cancelfunc=function(){
                    var pop=$('#tm-section-pop-up');
                    pop.parents().removeClass('noanimated');
                    
                    _ovl.unbind().remove();
                    pop.after(section);
                    pop.remove();

                    section.find('.tm-section-link').show();
                    section.find('.tm-section-pop').hide();
                }

                $this.on("click.tmsectionpoplink",function(e){
                    
                    e.preventDefault();
                    clicked=false;
                    _ovl.appendTo("body").click(cancelfunc);
                    
                    section.before($.fn.tm_template(template_engine.tc_section_pop_link,{'title':title,'close':tm_epo_js.i18n_close}));
                    var pop=$('#tm-section-pop-up');
                    pop.find('.float_editbox').prepend(section);

                    section.find('.tm-section-link').hide();
                    section.find('.tm-section-pop').show();
                    
                    pop.parents().addClass('noanimated');
                    
                    pop.find(".details_cancel").click(function() {
                        if (clicked){
                            return;
                        }
                        clicked=true;
                        cancelfunc();                        
                    });
                    _window.trigger("tmlazy");
                });
                
            });
        };
    }// End Section popup

    // Start Conditional logic
    if (!$().cpfdependson) {
            
        $.fn.cpfdependson = function(fields, toggle, what,refresh) {
            var elements    = this,
                matches     = 0;
            
            if (elements.length==0 || !typeof fields =="object"){
                return;
            }

            if (!toggle){
                toggle="show";
            }
            if (!what){
                what="all";
            }
            
            $.each(fields,function(i,field){
                if (!typeof fields == "object"){
                    return true;
                }
                var get_element=get_element_from_field(field.element);
                if (get_element && get_element.length>0){
                    get_element.each(function(i,element){
                        if (element && (!$(element).data('tmhaslogicevents') || refresh) ){;
                            if ($(element).is(".tm-epo-variation-element")){
                                
                                add_variation_event('found_variation.tmlogic', false, function(event, variation) {
                                    run_cpfdependson();
                                    _window.trigger('tm-do-epo-update');
                                });
                                add_variation_event('hide_variation.tmlogic', false, function(event, variation) {
                                    run_cpfdependson();
                                    _window.trigger('tm-do-epo-update');
                                });

                            }else{
                                var _events="change.cpflogic";
                                if ($(element).is(":text") || $(element).is("textarea")){
                                    _events="change.cpflogic keyup.cpflogic";
                                }
                                $(element).off(_events).on(_events,function(e){
                                    run_cpfdependson();
                                });                        
                            }
                            $(element).data('tmhaslogicevents',1);
                        }
                    });

                    matches++;
                }

            });
            
            elements.each(function(i,el){
                var $this   = $(this),
                    show    = false;
                $this.data("matches",matches)
                    .data("toggle",toggle)
                    .data("what",what)
                    .data("fields",fields);
                
                switch (toggle){
                    case "show":
                        show=false;
                    break;
                    case "hide":
                        show=true;
                    break;
                }
                if (show){
                    $this.show();
                }else{
                    $this.hide();
                }                
                $this.data('isactive',show);
            });

            elements.addClass('iscpfdependson').data('iscpfdependson',1);
            return elements.each(function(){
                $(this).addClass("is-epo-depend");
            });
        };

        $.fn.run_cpfdependson = function() {
            run_cpfdependson();
        }
    }
    
    $.tcepo = {
        temp_floatbox : false,

        tm_init_epo : function(main_product,is_quickview,product_id,epo_id){
            tm_init_epo(main_product,is_quickview,product_id,epo_id);
        }
    };

    function field_is_active(field){
        var hide_element;
        if (!$(field).is('.cpf_hide_element')){
            hide_element=$(field).closest('.cpf_hide_element');
        }else{
            hide_element=$(field);
        }
        
        if ($(hide_element).data('isactive')!==false && $(hide_element).closest('.cpf-section').data('isactive')!==false){
            $(field).prop('disabled',false);
            return true;
        }
        if (!$(field).is('.cpf_hide_element')){
            $(field).prop('disabled',true);
        }
        return false;
    }

    function is_per_product_pricing(p){
        if (p==true || p=="yes"){
            return true;
        }
        return false;
    }
    
    function run_cpfdependson(obj){
        if (!$(obj).length){
            obj="body";
        }
        var iscpfdependson = $(obj).find('.iscpfdependson');
        iscpfdependson.each(function(i,elements){
            $(elements).each(function(j,el){                
                tm_check_rules($(el));
            });            
        });
        iscpfdependson.each(function(i,elements){
            $(elements).each(function(j,el){                
                tm_check_rules($(el),'cpflogic');                
            });            
        });
        iscpfdependson.each(function(i,elements){
            $(elements).each(function(j,o){
                o=$(o);
                if (o.is('.cpf-section')){
                    o=o.find(".cpf_hide_element");
                }
                o.each(function(theindex,theelement){
                         
                    var f=field_is_active($(theelement).find('.tm-epo-field'));
                           
                });                    
            });            
        });
        if ($().selectric){
            $('.tm-extra-product-options select').selectric('refresh');
        }
        $('.tm-owl-slider').each(function(){
            $(this).trigger('refresh.owl.carousel');
        });
        _window.trigger("cpflogicrun");
        _window.trigger("tmlazy");
    }

    function tm_check_rules(o,theevent){
        o.each(function(theindex,theelement){
            var $this   = $(this),
                matches = $this.data("matches"),
                toggle  = $this.data("toggle"),
                what    = $this.data("what"),
                fields  = $this.data("fields"),
                checked = 0,
                show    = false;

            switch (toggle){
                case "show":
                    show=false;
                break;
                case "hide":
                    show=true;
                break;
            }

            $.each(fields,function(i,field){
                var fia=true;
                if (theevent=='cpflogic'){
                    fia=field_is_active($(field.element));
                }
                if (fia && tm_check_field_match(field)){
                    checked++;
                }
            });

            if (what=="all"){
                if (matches==checked){
                    show=!show;
                }
            }else{
                if (checked>0){
                    show=!show;
                }

            }
            if (show){
                $this.show();
            }else{
                $this.hide();
            }
            $this.data('isactive',show);
        });
    }

    function tm_check_section_match(elements,operator){
        var all_checked=true,val;
        var all_elements=elements.find(".cpf_hide_element");
        $(all_elements).each(function(j,element){
            element=$(element);
            if(field_is_active(element)){
                var _class  = element.attr("class").split(' ')
                    .map(function(cls) {
                        if (cls.indexOf("cpf-type-", 0) !== -1) {
                            return cls;
                        }
                    })
                    .filter(function(v, k, el) {
                        if (v !== null && v !== undefined) {
                            return v;
                        }
                    });

                if (_class.length>0){
                    _class=_class[0];
                    switch (_class){
                        case "cpf-type-radio" :
                            var radio           = element.find("input.tm-epo-field.tmcp-radio"),
                                radio_checked   = element.find("input.tm-epo-field.tmcp-radio:checked");

                            if (operator=='isnotempty'){
                                all_checked = all_checked && radio_checked.length>0
                            }else if (operator=='isempty'){
                                all_checked = all_checked && radio_checked.length==0
                            }
                            break;
                        case "cpf-type-checkbox" :
                            var checkbox            = element.find("input.tm-epo-field.tmcp-checkbox"),
                                checkbox_checked    = element.find("input.tm-epo-field.tmcp-checkbox:checked");

                            if (operator=='isnotempty'){
                                all_checked = all_checked && checkbox_checked.length>0
                            }else if (operator=='isempty'){
                                all_checked = all_checked && checkbox_checked.length==0
                            } 
                            break;
                        case "cpf-type-select" :
                            var select = element.find("select.tm-epo-field.tmcp-select"),
                                options = element.find("select.tm-epo-field.tmcp-select").children('option'),
                                selected = element.find("select.tm-epo-field.tmcp-select").children('option:selected');
                            var eq=options.index(selected);
                                
                            if (options.eq(0).val()==="" && options.eq(0).attr('data-rulestype')===""){
                                eq=eq-1;
                            }

                            var builder_addition="_"+eq;

                            builder_addition=builder_addition.length;
                            val=element.find("select.tm-epo-field.tmcp-select").val();
                            if(val){
                                val=val.slice(0,-builder_addition); 
                            }

                            break;
                        case "cpf-type-textarea" :
                            val=element.find("textarea.tm-epo-field.tmcp-textarea").val();

                            break;
                        case "cpf-type-textfield" :
                            val=element.find("input.tm-epo-field.tmcp-textfield").val();
                            break;
                        case "cpf-type-range" :
                            val=element.find("input.tm-epo-field.tmcp-range").val();
                            break;

                        }
                    all_checked = all_checked && tm_check_match(val,'',operator);

                }else{
                    all_checked = all_checked && false;
                }
            }          
        });
        return all_checked;
    
    }

    function tm_check_field_match(f){
        var element     = $(f.element),
            operator    = f.operator,
            value       = f.value,
            val;
        if (!element.length){
            return false;
        }
        if (element.is('.cpf-section')){
            return tm_check_section_match(element,operator);
        }
        var _class  = element.attr("class").split(' ')
            .map(function(cls) {
                if (cls.indexOf("cpf-type-", 0) !== -1) {
                    return cls;
                }
            })
            .filter(function(v, k, el) {
                if (v !== null && v !== undefined) {
                    return v;
                }
            });
                
        if (_class.length>0){
            _class=_class[0];
            switch (_class){
                case "cpf-type-radio" :
                    var radio           = element.find("input.tm-epo-field.tmcp-radio"),
                        radio_checked   = element.find("input.tm-epo-field.tmcp-radio:checked");

                    if (operator=='is' || operator=='isnot'){
                        if (radio_checked.length==0){
                            return false;
                        }
                        var eq=radio.index(radio_checked),
                            builder_addition="_"+eq;

                        builder_addition=builder_addition.length;                        
                        val=element.find("input.tm-epo-field.tmcp-radio:checked").val();
                        if(val){
                            val=val.slice(0,-builder_addition); 
                        }
                    }else if (operator=='isnotempty'){
                        return radio_checked.length>0
                    }else if (operator=='isempty'){
                        return radio_checked.length==0
                    }
                    break;
                case "cpf-type-checkbox" :
                    var checkbox            = element.find("input.tm-epo-field.tmcp-checkbox"),
                        checkbox_checked    = element.find("input.tm-epo-field.tmcp-checkbox:checked");

                    if (operator=='is' || operator=='isnot'){
                        if (checkbox_checked.length==0){
                            return false;
                        }
                        var ret=false;
                        checkbox_checked.each(function(i,el){
                            var eq                  = checkbox.index($(el)),
                                builder_addition    = "_"+eq;

                            builder_addition=builder_addition.length;
                            val=$(el).val();
                            if(val){
                                val=val.slice(0,-builder_addition); 
                            }
                            if (tm_check_match(val,value,operator)){
                                ret=true;
                            }
                        });
                        return ret;
                    }else if (operator=='isnotempty'){
                        return checkbox_checked.length>0
                    }else if (operator=='isempty'){
                        return checkbox_checked.length==0
                    } 
                    break;
                case "cpf-type-select" :
                    var select = element.find("select.tm-epo-field.tmcp-select"),
                        options = element.find("select.tm-epo-field.tmcp-select").children('option'),
                        selected = element.find("select.tm-epo-field.tmcp-select").children('option:selected');
                    var eq=options.index(selected);
                        
                    if (options.eq(0).val()==="" && options.eq(0).attr('data-rulestype')===""){
                        eq=eq-1;
                    }

                    var builder_addition="_"+eq;

                    builder_addition=builder_addition.length;
                    val=element.find("select.tm-epo-field.tmcp-select").val();
                    if(val){
                        val=val.slice(0,-builder_addition); 
                    }

                    break;
                case "cpf-type-textarea" :
                    val=element.find("textarea.tm-epo-field.tmcp-textarea").val();

                    break;
                case "cpf-type-textfield" :
                    val=element.find("input.tm-epo-field.tmcp-textfield").val();
                    break;
                case "cpf-type-range" :
                    val=element.find("input.tm-epo-field.tmcp-range").val();
                    break;

                case "cpf-type-variations" :
                    return tm_variation_check_match(element,value,operator);
                    break;


                }
            return tm_check_match(val,value,operator);

        }else{
            return false;
        }

        return false;
                    
    }

    function tm_variation_check_match(element, val2, operator){
        if (element!=null && val2!=null){
            val2 = val2 ? parseInt(val2) : -1;
        }
        var variations_form=$(element).closest(".variations_form"),
            val1,
            variation_id_selector='input[name^="variation_id"]';
        if ( variations_form.find( 'input.variation_id' ).length > 0 ){
            variation_id_selector='input.variation_id';
        }
        val1=variations_form.find( variation_id_selector ).val();
        
        switch(operator){
        case "is" :
            return (val1!="" && val1 == val2);
            break;

        case "isnot" :
            return (val1!="" && val1 != val2);
            break;

        case "isempty" :
            return ( val1 == "" );
            break;

        case "isnotempty" :
            return ( !(val1 == "") );
            break;

        case "startswith" :
            return val1.startsWith(val2);
            break;
        case "endswith" :
            return val1.endsWith(val2);
            break;
        case "greaterthan" :
            return parseFloat(val1)>parseFloat(val2);
            break;
        case "lessthan" :
            return parseFloat(val1)<parseFloat(val2);
            break;

        }
        return false;
    }
    
    function tm_check_match(val1, val2, operator){
        if (val1!=null && val2!=null){
            
            val1=encodeURIComponent(val1);

            if ($.qtranxj_split){
                val2=encodeURIComponent( $.qtranxj_split(decodeURIComponent(val2))[tm_epo_q_translate_x_clogic_js['language']] );//backwards compatible
            }else{
                val2=encodeURIComponent(decodeURIComponent(val2));//backwards compatible
            }

            val1 = val1 ? val1.toLowerCase() : "";
            val2 = val2 ? val2.toLowerCase() : "";
        }
        switch(operator){
        case "is" :
            return (val1!=null && val1 == val2);
            break;

        case "isnot" :
            return (val1!=null && val1 != val2);
            break;

        case "isempty" :
            return !( (val1 != undefined && val1!='') );
            break;

        case "isnotempty" :
            return ( (val1 != undefined && val1!='') );
            break;

        case "startswith" :
            return val1.startsWith(val2);
            break;
        case "endswith" :
            return val1.endsWith(val2);
            break;
        case "greaterthan" :
            return parseFloat(val1)>parseFloat(val2);
            break;
        case "lessthan" :
            return parseFloat(val1)<parseFloat(val2);
            break;

        }
        return false;
    }

    function get_element_from_field(element){

        if ($(element).length==0){
            return;
        }

        if ($(element).is('.cpf-section')){
            return element.find(".tm-epo-field");
        }
        
        var _class=element.attr("class").split(' ')
            .map(function(cls) {
                if (cls.indexOf("cpf-type-", 0) !== -1) {
                    return cls;
                }
            })
            .filter(function(v, k, el) {
                if (v !== null && v !== undefined) {
                    return v;
                }
            });

        if (_class.length>0){
            _class=_class[0];
            
            switch (_class){
                case "cpf-type-radio" :
                    return element.find(".tm-epo-field.tmcp-radio");
                    break;
                case "cpf-type-checkbox" :
                    return element.find(".tm-epo-field.tmcp-checkbox");
                    break;
                case "cpf-type-select" :
                    return element.find(".tm-epo-field.tmcp-select");
                    break;
                case "cpf-type-textarea" :
                    return element.find(".tm-epo-field.tmcp-textarea");
                    break;
                case "cpf-type-textfield" :
                    return element.find(".tm-epo-field.tmcp-textfield");
                    break;
                case "cpf-type-range" :
                    return element.find(".tm-epo-field.tmcp-range");
                    break;
                case "cpf-type-date" :
                    return element.find(".tm-epo-field.tmcp-date");
                    break;
                case "cpf-type-variations" :
                    return element.closest('.cpf-section').find(".tm-epo-field.tm-epo-variation-element");
                    break;
            }
            return;
        }
        return;
    }

    function validate_logic(l){
        return (typeof l =="object") && ("toggle" in l) && ("what" in l) && ("rules" in l) && (l.rules.length>0);
    }

    /* Following loops are required for the logic to work on composite products that have custom variations */
    function cpf_section_logic(obj){
        
        var root_element=$(obj),
            all_sections=root_element.find(".cpf-section"),
            search_obj;
        
        if (root_element.is('.cpf-section')){
            search_obj = false;
        }else{
            search_obj = all_sections;
        }
        
        root_element.each(function(j,obj_el){            
            var cpf_section;
            if ($(obj_el).is('.cpf-section')){
                cpf_section = $(obj_el);
            }else{
                cpf_section = $(obj_el).find(".cpf-section");
            }
            
            cpf_section.each(function(index,el){
                var sect = $(el),
                    id = sect.data("uniqid"),
                    logic = sect.data("logic"),
                    haslogic = parseInt(sect.data("haslogic")),
                    fields=[];

                if (haslogic==1 && validate_logic(logic)){

                    $.each(logic.rules,function(i,rule){
                        if (rule){
                            var section     = rule.section,
                                element     = rule.element,
                                operator    = rule.operator,
                                value       = rule.value,
                                obj_section,
                                obj_element;
                            
                            if (search_obj){
                                obj_section = search_obj.filter('[data-uniqid="'+section+'"]');
                                if (element!=section){
                                    obj_element = obj_section.find(".cpf_hide_element").eq(element);
                                }else{
                                    obj_element = obj_section;
                                }
                                
                            }else{
                                if (element!=section){
                                    obj_element = root_element.find(".cpf_hide_element").eq(element);
                                }else{
                                    obj_element = obj_section;
                                }                            
                            }

                            fields.push({
                                "element":obj_element,
                                "operator":operator,
                                "value":value
                            });                            
                        }
                    });
                    if (!sect.data('iscpfdependson')){
                        sect.data('cpfdependson-fields',fields);
                        sect.cpfdependson(fields,logic.toggle,logic.what);
                    }else{
                        sect.cpfdependson(sect.data('cpfdependson-fields'),logic.toggle,logic.what,true);
                    }
                }

            });

        });
        
    }

    function cpf_element_logic(obj){

        var root_element=$(obj), 
            all_sections=root_element.find(".cpf-section"),
            search_obj;
        
        if (root_element.is('.cpf-section')){
            search_obj = false;
        }else{
            search_obj = all_sections;
        }

        root_element.find(".cpf_hide_element").each(function(index,el){
            var current_element = $(el),
                id              = current_element.data("uniqid"),
                logic           = current_element.data("logic"),
                haslogic        = parseInt(current_element.data("haslogic"));

            if (haslogic==1 && validate_logic(logic)){
                var fields=[];
                $.each(logic.rules,function(i,rule){
                    if(rule){
                        var section     = rule.section,
                            element     = rule.element,
                            operator    = rule.operator,
                            value       = rule.value,
                            obj_section,
                            obj_element;

                        if (search_obj){
                            obj_section = search_obj.filter('[data-uniqid="'+section+'"]');
                            if (element!=section){
                                obj_element = obj_section.find(".cpf_hide_element").eq(element);
                            }else{
                                obj_element = obj_section;
                            }                        
                        }else{
                            if (element!=section){
                                obj_element = root_element.find(".cpf_hide_element").eq(element);
                            }else{
                                obj_element = obj_section;
                            }
                        }

                        fields.push({
                            "element":obj_element,
                            "operator":operator,
                            "value":value
                        });
                    }
                });
                if (!current_element.data('iscpfdependson')){
                    current_element.data('cpfdependson-fields',fields);
                    current_element.cpfdependson(fields,logic.toggle,logic.what);
                }else{
                    current_element.cpfdependson(current_element.data('cpfdependson-fields'),logic.toggle,logic.what,true);
                }
            }
        });
        
    }// End Conditional logic
    
    // Taxes setup
    function get_price_including_tax(price, _cart) {
        if (isNaN(parseFloat(price))){
            price=0;
        }
        price=price*10000;
        if (_cart){
            var taxable = _cart.attr("data-taxable"),
                tax_rate = _cart.attr("data-tax-rate"),
                base_tax_rate = _cart.attr("data-base-tax-rate"),
                tax_string = _cart.attr("data-tax-string"),
                tax_display_mode = _cart.attr("data-tax-display-mode"),
                prices_include_tax = _cart.attr("data-prices-include-tax"),
                is_vat_exempt = _cart.attr("data-is-vat-exempt"),
                non_base_location_prices = _cart.attr("data-non-base-location-prices");

            if ( taxable ) {
                if (prices_include_tax=="1"){
                    if (is_vat_exempt == "1"){
                        price = parseFloat(price)*(1-(base_tax_rate/100));
                    }else if(non_base_location_prices=="1"){
                        var the_rate = (base_tax_rate/ 100) / (1+(base_tax_rate/100));
                        var net_price = price - ( the_rate * price );
                        var tax_amount = price - net_price;

                        //var base_taxes = parseFloat(price) * (base_tax_rate/100);
                        var modded_taxes = parseFloat(price) * (tax_rate/100);
                        price = parseFloat(price)-tax_amount+modded_taxes;
                    }
                }else{
                    price = parseFloat(price)*(1+(tax_rate/100));
                }
            }

        }
        price=price/10000;

        return price;
    }

    function get_price_excluding_tax(price, _cart) {
        if (isNaN(parseFloat(price))){
            price=0;
        }
        price=price*10000;
        if (_cart){
            var taxable = _cart.attr("data-taxable"),
                tax_rate = _cart.attr("data-tax-rate"),
                tax_string = _cart.attr("data-tax-string"),
                tax_display_mode = _cart.attr("data-tax-display-mode"),
                prices_include_tax = _cart.attr("data-prices-include-tax");

            if ( taxable && prices_include_tax=="1" ) {
                price = parseFloat(price)/(1+(tax_rate/100));
            }

        }
        price=price/10000;
        return price;
    }

    function tm_set_tax_price(value, _cart) {
        if (isNaN(parseFloat(value))){
            value=0;
        }
        if (_cart){
            var taxable = _cart.attr("data-taxable"),
                tax_rate = _cart.attr("data-tax-rate"),
                tax_string = _cart.attr("data-tax-string"),
                tax_display_mode = _cart.attr("data-tax-display-mode"),
                prices_include_tax = _cart.attr("data-prices-include-tax");

            /*if ( taxable ) {
                if ( tax_display_mode== 'excl' ) {// Display without taxes
                    if (prices_include_tax=="1"){
                        value = parseFloat(value)/(1+(tax_rate/100));
                    }else{
                        
                    }                    

                } else {// Display with taxes
                    if (prices_include_tax=="1"){
                        
                    }else{
                        value = parseFloat(value)*(1+(tax_rate/100));
                    }

                }
            }*/
            value = tax_display_mode == 'incl' ? get_price_including_tax( value,_cart ) : get_price_excluding_tax( value,_cart );

        }
        return value;
    }

    // Return a formatted currency value without tax
    function tm_set_price_without_tax(value, _cart) {
        if (_cart){
            var taxable = _cart.attr("data-taxable"),
                tax_rate = _cart.attr("data-tax-rate"),
                tax_string = _cart.attr("data-tax-string"),
                tax_display_mode = _cart.attr("data-tax-display-mode"),
                prices_include_tax = _cart.attr("data-prices-include-tax");

            if ( taxable ) {
                if ( tax_display_mode== 'excl' ) {// Display without taxes
                    if (prices_include_tax=="1"){
                        //value = parseFloat(value)/(1+(tax_rate/100));
                    }else{
                        
                    }                    

                } else {// Display with taxes
                    if (prices_include_tax=="1"){
                        
                    }else{
                        value = parseFloat(value)/(1+(tax_rate/100));
                    }

                }
            }

        }
        return value;
    }

    // Return a formatted currency value
    function tm_set_price(value, _cart, notax, taxstring) {
        if(!notax){
            value=tm_set_tax_price(value, _cart);
        }
        var inc_tax_string="";
        if (_cart && taxstring){
            inc_tax_string = _cart.attr("data-tax-string")
        }
        if (inc_tax_string==undefined){
            inc_tax_string='';
        }
        return accounting.formatMoney(value, {
            symbol: tm_epo_js.currency_format_symbol,
            decimal: local_decimal_separator,
            thousand: local_thousand_separator,
            precision: tm_epo_js.currency_format_num_decimals,
            format: tm_epo_js.currency_format
        }) + inc_tax_string;
    }

    function tm_update_price(obj,price,formated_price,original_price,original_formated_price){
        var $obj=$(obj),
            $price = $obj.closest('.price'),
            w=$obj.closest('.tmcp-field-wrap'),
            $ba_amount = w.find('.before-amount,.after-amount'),
            priceobj={price:formated_price,original_price:original_formated_price};
        if($obj.length==0){
            return;
        }
        if ((tm_epo_js.tm_epo_auto_hide_price_if_zero=="yes" && price!=0) || tm_epo_js.tm_epo_auto_hide_price_if_zero!="yes"){
            var f=w.find('.tm-epo-field');
            if (f.length>0 && f.is('.tmcp-select') && f.val()===''){
                $obj.empty();
                $ba_amount.addClass('tm-hidden');
            }else{
                if (original_price && original_price!=undefined && parseFloat(original_price)!==parseFloat(price)){
                    $obj.html( $.fn.tm_template(template_engine.sale_price,{price:priceobj}) );
                }else{
                    $obj.html( $.fn.tm_template(template_engine.price,{price:priceobj}) );
                }                
                $ba_amount.removeClass('tm-hidden');
            }
        }else{
            $obj.empty();
            $ba_amount.addClass('tm-hidden');
        }
    }
        
    function add_variation_event(name,selector,func){
        late_variation_event[late_variation_event.length] = {
            "name" : name,
            "selector" : selector,
            "func" : func
        };        
    }

    function get_variation_current_settings(form){
        var current_settings={};
        form.find( '.variations select' ).each( function() {
            var attribute_name,value;
            // Get attribute name from data-attribute_name, or from input name if it doesn't exist
            if ( typeof( $( this ).data( 'attribute_name' ) ) != 'undefined' )
                attribute_name = $( this ).data( 'attribute_name' );
            else
                attribute_name = $( this ).attr( 'name' );

            // Encode entities
            value = $( this ).val();

            // Add to settings array
            current_settings[ attribute_name ] = value;

        });
        return current_settings;
    }

    function do_tm_custom_variations_update(form,all_variations){
        var check_if_all_are_not_set=[];
        form.find('.cpf-type-variations').each(function(i,el){
            check_if_all_are_not_set[i]=true;
            var t=$(el).find('.tm-epo-variation-element'),
                id,
                v,
                exists = false;

            if(t.is("select")){
                id=t.attr('data-tm-for-variation');
                v=t.val();
                if(v){
                    check_if_all_are_not_set[i]=false;
                }
                t.children('option').each(function(x,o){
    
                    exists = false;
                    form.find('#'+id).children('option').each(function(){
                        if ($(this).attr("value") == $(o).attr("value")) {
                            exists = true;
                            return false;
                        }
                    });
                    if (!exists){
                        $(o).attr("disabled","disabled").hide();
                    }else{
                        $(o).removeAttr("disabled").show();
                    }

                });

            }else{
                
                t.each(function(x,o){
                    var o=$(o),
                        li=o.closest("li"),
                        input=li.find(".tm-epo-variation-element");
                    id=o.attr('data-tm-for-variation');
                    v=o.val();
                    if (o.is(":checked")){
                        check_if_all_are_not_set[i]=false;
                    }
                    var this_settings=get_variation_current_settings(form);
                    this_settings[ 'attribute_'+id ] = v;
                    
                    var matching_variations = $.fn.tm_find_matching_variations( all_variations, this_settings );
                    var variation = matching_variations.shift();
                    
                    var is_in_stock=(variation && ("is_in_stock" in variation) && variation.is_in_stock);
                    if (!variation || !is_in_stock){
                        o.attr("disabled","disabled").addClass("tm-disabled");
                        
                        input.attr("disabled","disabled");
                        input.attr("data-tm-disabled","disabled");
                        
                        li.addClass("tm-attribute-disabled").fadeTo( "fast" , 0.5);
                        if(!is_in_stock){
                            li.find("label").off();
                        }
                    }else{
                        o.removeAttr("disabled").removeClass("tm-disabled");
                        li.removeClass("tm-attribute-disabled").fadeTo( "fast" , 1, function(){$(this).css("opacity", "");});
                        input.removeAttr("disabled");
                        input.removeAttr("data-tm-disabled");
                    }
                });

            }

        });
        if(check_if_all_are_not_set){
            var s=check_if_all_are_not_set.shift(),
                redo_check=true;
            $.each(check_if_all_are_not_set,function(i,el){
                if(el===false){
                    redo_check=false;
                    return false;
                }
            })
            if(redo_check)
            form.find('.cpf-type-variations').first().each(function(i,el){
                var t=$(el).find('.tm-epo-variation-element'),
                    id,
                    v,
                    exists = false;

                if(t.is("select")){
                }else{
                    t.each(function(x,o){
                        var o=$(o),
                            li=o.closest("li"),
                            input=li.find(".tm-epo-variation-element");
                        o.removeAttr("disabled").removeClass("tm-disabled");
                        li.removeClass("tm-attribute-disabled").stop().css("opacity", "");
                        input.removeAttr("disabled");
                        input.removeAttr("data-tm-disabled");
                    });                    
                }
            });
        }
    }

    function tm_custom_variations_update(form){
        var all_variations = form.data( 'product_variations' ),
            product_id = parseInt( form.data( 'product_id' ) );
        if (!product_id){
            product_id = form.data('tc_product_id');
        }
        // Fallback to window property if not set - backwards compat
        if ( ! all_variations && window.product_variations && window.product_variations.product_id){
            all_variations = window.product_variations.product_id;
        }
        if ( ! all_variations && window.product_variations ){
            all_variations = window.product_variations;
        }
        if ( ! all_variations && window['product_variations_' + product_id] ){
            all_variations = window['product_variations_' + product_id ];
        }
        if (!all_variations){
            if (!global_variation_object){
                var data = {
                        action: 'woocommerce_tm_get_variations_array',
                        post_id: product_id,
                    };
                $.post(tm_epo_js.ajax_url, data, function(response) {
                    global_variation_object=response;
                    do_tm_custom_variations_update(form,global_variation_object["variations"]);
                },'json');

            }else{
                do_tm_custom_variations_update(form,global_variation_object["variations"]);
            }

            return;
        }
        // may need 2.4 check for woocommerce_ajax_variation_threshold
        do_tm_custom_variations_update(form,all_variations);
    }

    function tm_fix_stock(cart,html){
        if (html==undefined){
            return false;
        }
        var cart=$(cart),
            custom_variations = cart.find('.tm-epo-variation-element'),
            section = custom_variations.closest('.tm-epo-variation-section');

        if (custom_variations.length){
            section.find('.tm-stock').remove();
            section.append('<div class="tm-stock">'+html+'</div>');
            return true;
        }else{
            cart.find('.tm-stock').remove();
            cart.find('.variations').after('<div class="tm-stock">'+html+'</div>');
            return true;
        }
        return false;
    }

    function tm_fix_stock_tmepo($this,form){
        var stock = $this.find( '.stock' ).last();
        if (stock.length){
            form.find('.tm-stock').remove();
            if (tm_fix_stock(form, stock.prop('outerHTML'))){
                stock.remove();    
            }
        }else{
            form.find('.tm-stock').remove();
        }
    }

    function tm_init_epo(main_product,is_quickview,product_id,epo_id){
        
        main_product=$(main_product);

        // Range picker setup
        function tm_set_range_pickers(obj){

            if (!obj){
                    obj=this_epo_container;
            }

            obj.find('.tm-range-picker').each(function(i,el){
                var el=$(el),
                    $decimals=el.attr('data-step').split("."),
                    $tmfid=obj.find('#'+el.attr('data-field-id')),
                    $min=parseFloat(el.attr('data-min')),
                    $max=parseFloat(el.attr('data-max')),
                    $start=parseFloat(el.attr('data-start')),
                    $step=parseFloat(el.attr('data-step')),
                    $show_picker_value=el.attr('data-show-picker-value'),
                    $show_label=el.closest("li").find(".tm-show-picker-value"),
                    $noofpips=parseFloat(el.attr('data-noofpips')),
                    $pips=null;

                if ($decimals.length==1){
                    $decimals=0;
                }else{
                    $decimals=$decimals[1].length;
                }
                if (isNaN($min)){
                    $min=0;
                }
                if (isNaN($max)){
                    $max=0;
                }
                if ($max<=$min){
                    $max++;
                }
                if (isNaN($start)){
                    $start=0;
                }
                if (isNaN($step)){
                    $step=0;
                }
                if (isNaN($noofpips)){
                    $noofpips=10;
                }
                if (el.attr('data-pips')=="yes"){
                    $pips={
                        mode: 'count',
                        values: $noofpips,
                        density: 2,
                        filter: function(value, type){
                            if ($step<=0){
                                return 0;
                            }
                            return value % 1 ? 2 : 1;
                        },
                        stepped: true,
                        format: wNumb({
                            decimals: $decimals
                        })
                    };
                }
                noUiSlider.create(el.get(0),{
                    direction:tm_epo_js.text_direction,
                    start: $start,
                    step: $step,
                    connect: 'lower',            
                    // Configure tapping, or make the selected range dragable.
                    behaviour: 'tap',            
                    // Full number format support.
                    format: wNumb({
                        mark: ".",
                        decimals: $decimals,
                        thousand: "",
                    }),            
                    // Support for non-linear ranges by adding intervals.
                    range: {
                        'min': [$min],
                        'max': [$max]
                    },
                    pips:$pips
                });
                var $tmh=el.find('.noUi-handle-lower');
                el.get(0).noUiSlider.on("slide",function(){
                    $tmh.trigger('tmmovetooltip');
                    $tmfid.trigger('change.cpf');
                });
                el.get(0).noUiSlider.on('update', function( values, handle ) {
                    handle=0;//fixes rtl issue.
                    if ($show_picker_value!="left" && $show_picker_value!="right"){
                        $tmh.attr('title',accounting.formatNumber(values[handle], {
                            decimal: local_decimal_separator,
                            thousand: local_thousand_separator,
                            precision: $decimals
                        }));
                    }                    
                    $tmfid.val(values[handle]);
                    if ($show_picker_value!=""){
                        $show_label.html(accounting.formatNumber(values[handle], {
                            decimal: local_decimal_separator,
                            thousand: local_thousand_separator,
                            precision: $decimals
                        }));
                    }
                });
                
                if ($show_picker_value!="left" && $show_picker_value!="right"){
                    $tmh.attr('title',el.attr('data-start'));
                    $.tm_tooltip($tmh);
                }
                if ($show_picker_value!=""){
                    $show_label.html($start);
                }
          
            });
            
        }
        
        // Date and time picker setup
        function tm_set_datepicker(obj){
            if (!$.tm_datepicker){
                return;
            }
            if (!obj){
                obj=this_epo_container;
            }
            var inputIds = $('input').map(function() {
                    return this.id;
                }).get().join(' ');
            
            obj.find( ".tm-epo-timepicker" ).each(function(i,el){
                var $this               = $(el),
                    _mintime            = ($this.attr('data-min-time').trim()!=="")?$this.attr('data-min-time').trim():null,
                    _maxtime            = ($this.attr('data-max-time').trim()!=="")?$this.attr('data-max-time').trim():null,
                    format              = $this.attr('data-time-format').trim(),
                    date_theme          = $this.attr('data-time-theme').trim(),
                    date_theme_size     = $this.attr('data-time-theme-size').trim(),
                    date_theme_position = $this.attr('data-time-theme-position').trim();

                $this.tm_timepicker({
                    isRTL               : tm_epo_js.isRTL,
                    hourText            : tm_epo_js.hourText,
                    minuteText          : tm_epo_js.minuteText,
                    secondText          : tm_epo_js.secondText,
                    timeFormat          : format,
                    minTime             : _mintime,
                    maxTime             : _maxtime,

                    beforeShow: function(input, inst) {
                        $(inst.dpDiv)
                        .removeClass(inputIds)
                        .removeClass("tm-datepicker-normal tm-datepicker-top tm-datepicker-bottom")
                        .addClass(this.id+ ' tm-bsbb-all tm-ui-skin-'+date_theme+' tm-timepicker tm-datepicker tm-datepicker-'+date_theme_position+' tm-datepicker-'+date_theme_size)
                        .appendTo("body");
                        
                        _document
                        .off('click','.tm-ui-dp-overlay')
                        .on('click','.tm-ui-dp-overlay',function(){$this.tm_timepicker("hide");});
                        $("body").addClass("tm-static");
                        $this.prop("readonly",true);
                        
                        _window.trigger({
                            "type":"tm-timepicker-beforeShow",
                            "input":input,
                            "inst":inst
                        });
                        
                    },
                    onClose: function(dateText, inst) {
                        $("body").removeClass("tm-static");                    
                        $this.prop("readonly",false);
                        $this.trigger("change");
                    },

                });
                $('#ui-tm-datepicker-div').hide();
            });

            obj.find( ".tm-epo-datepicker" ).each(function(i,el){
                var $this               = $(el),
                    startDate           = parseInt($this.attr('data-start-year').trim()),
                    endDate             = parseInt($this.attr('data-end-year').trim()),
                    minDate             = $this.attr('data-min-date').trim(),
                    maxDate             = $this.attr('data-max-date').trim(),
                    disabled_dates      = $this.attr('data-disabled-dates').trim(),
                    enabled_only_dates  = $this.attr('data-enabled-only-dates').trim(),
                    disabled_weekdays   = $this.attr('data-disabled-weekdays').trim().split(","),
                    format              = $this.attr('data-date-format').trim(),
                    show                = $this.attr('data-date-showon').trim(),
                    date_theme          = $this.attr('data-date-theme').trim(),
                    date_theme_size     = $this.attr('data-date-theme-size').trim(),
                    date_theme_position = $this.attr('data-date-theme-position').trim();

                if (disabled_dates!=""){
                    var $split=disabled_dates.split(','),
                        $index=disabled_dates.indexOf(',');

                    if ($index!=-1 && $split.length>0){
                        disabled_dates=$split;
                    }
                }
                if (enabled_only_dates!=""){
                    var $split=enabled_only_dates.split(','),
                        $index=enabled_only_dates.indexOf(',');

                    if ($index!=-1 && $split.length>0){
                        enabled_only_dates=$split;
                    }
                }

                if (minDate==""){
                    if(startDate==""){
                        minDate=null;
                    }else{
                        minDate=new Date(startDate, 1 - 1, 1);
                    }
                }else{
                    minDate=!minDate.tm_isNumeric() && !isNaN( new Date(minDate).getTime() ) ? new Date(minDate) : minDate;
                }
                if (maxDate==""){
                    if(endDate==""){
                        maxDate=null;
                    }else{
                        maxDate=new Date(endDate, 12 - 1, 31);
                    }
                }else{
                    maxDate=!maxDate.tm_isNumeric() && !isNaN( new Date(maxDate).getTime() ) ? new Date(maxDate) : maxDate;
                }
                $this.data('tc-enabled_only_dates',enabled_only_dates);
                $this.data('tc-disabled_weekdays',disabled_weekdays);
                $this.data('tc-disabled_dates',disabled_dates);
                $this.data('tc-format',format);

                $this.tm_datepicker({
                    monthNames          : tm_epo_js.monthNames,
                    monthNamesShort     : tm_epo_js.monthNamesShort,
                    dayNames            : tm_epo_js.dayNames,
                    dayNamesShort       : tm_epo_js.dayNamesShort,
                    dayNamesMin         : tm_epo_js.dayNamesMin,
                    isRTL               : tm_epo_js.isRTL,
                    showOtherMonths     : true,
                    selectOtherMonths   : true,
                    showOn              : show,
                    buttonText          : "",
                    showButtonPanel     : true,
                    firstDay            : tm_epo_js.first_day,
                    closeText           : tm_epo_js.closeText,
                    currentText         : tm_epo_js.currentText,
                    dateFormat          : format,
                    minDate             : minDate,
                    maxDate             : maxDate,
                    onSelect: function (dateText, inst) {
                        var input       = $(this),
                            id          = '#' + input.attr("id"),
                            format      = input.attr('data-date-format'),
                            date        = input.tm_datepicker('getDate'),
                            day         = '',
                            month       = '',
                            year        = '',
                            day_field   = obj.find(id + '_day'),
                            month_field = obj.find(id + '_month'),
                            year_field  = obj.find(id + '_year');

                        if (date){
                            day  = date.getDate();
                            month = date.getMonth() + 1;
                            year =  date.getFullYear();
                            var string = $.tm_datepicker.formatDate(format, date);
                            if (disabled_weekdays.indexOf(date.getDay().toString())!=-1 
                                || disabled_dates.indexOf(string) != -1 
                                || (enabled_only_dates!="" && enabled_only_dates.indexOf(string) == -1)
                            ){                        
                                var ld=input.data('tm-last-date');
                                if (input.data('tm-last-date')){
                                    ld=input.data('tm-last-date');
                                }else{
                                    ld='';
                                }
                                input.val(ld);
                                input.tm_datepicker('setDate',ld);
                                if (ld){
                                    date = input.tm_datepicker('getDate');
                                    day  = date.getDate();
                                    month = date.getMonth() + 1;
                                    year =  date.getFullYear();
                                }else{
                                    day='';
                                    month='';
                                    year='';
                                }
                            }

                        }

                        day_field.val(day);
                        month_field.val(month);
                        year_field.val(year);

                        input.data('tm-last-date',input.val());
                    },
                    beforeShow: function(input, inst) {
                        $(inst.dpDiv)
                        .removeClass(inputIds)
                        .removeClass("tm-datepicker-normal tm-datepicker-top tm-datepicker-bottom")
                        .addClass(this.id+ ' tm-bsbb-all tm-ui-skin-'+date_theme+' tm-datepicker tm-datepicker-'+date_theme_position+' tm-datepicker-'+date_theme_size)
                        .appendTo("body");
                        
                        _document
                        .off('click','.tm-ui-dp-overlay')
                        .on('click','.tm-ui-dp-overlay',function(){$this.tm_datepicker("hide");});
                        $("body").addClass("tm-static");
                        $this.prop("readonly",true);
                        
                        _window.trigger({
                            "type":"tm-datepicker-beforeShow",
                            "input":input,
                            "inst":inst
                        });
                        
                    },
                    onClose: function(dateText, inst) {
                        $("body").removeClass("tm-static");                    
                        $this.prop("readonly",false);
                        $this.trigger("change");
                    },
                    beforeShowDay: function(date){
                        var day = date.getDay();
                        if (enabled_only_dates!=""){
                            var string = $.tm_datepicker.formatDate(format, date);
                            return [ enabled_only_dates.indexOf(string) != -1 ,""];
                        }else{
                            
                            if (disabled_weekdays.indexOf(day.toString())!=-1){
                                return [false,""];
                            }
                            if (disabled_dates!=""){
                                var string = $.tm_datepicker.formatDate(format, date);
                                return [ disabled_dates.indexOf(string) == -1 ,""];
                            }else{
                                return [true,""];
                            }

                        }
                    }
                });
                $('#ui-tm-datepicker-div').hide();
            });
            
            function _validate_date_with_options(date,input){
                input=$(input);

                var inst=$.tm_datepicker._getInst(input[0] ),
                    enabled_only_dates= input.data('tc-enabled_only_dates'),
                    disabled_weekdays = input.data('tc-disabled_weekdays'),
                    disabled_dates = input.data('tc-disabled_dates'),
                    format = input.data('tc-format'),
                    day = date.getDay();

                if (!$.tm_datepicker._isInRange(inst,date)){
                    return false;
                }
                if (enabled_only_dates!=""){
                    var string = $.tm_datepicker.formatDate(format, date);
                    return enabled_only_dates.indexOf(string) != -1 ;
                }else{
                            
                    if (disabled_weekdays.indexOf(day.toString())!=-1){
                        return false;
                    }
                    if (disabled_dates!=""){
                        var string = $.tm_datepicker.formatDate(format, date);
                        return disabled_dates.indexOf(string) == -1 ;
                    }else{
                        return true;
                    }

                }
            }

            obj.find('.tmcp-date-select')
            .on('change.cpf',function(e){

                var id          = '#' + $(this).attr("data-tm-date"),
                    input       = obj.find(id),
                    format      = input.attr('data-date-format'),
                    day         = obj.find(id + '_day').val(),
                    month       = obj.find(id + '_month').val(),
                    year        = obj.find(id + '_year').val(),
                    dateFormat  = $.tm_datepicker.formatDate(format, new Date( year, month-1, day));
                if (day>0 && month>0 && year>0){
                    input.tm_datepicker( "setDate", dateFormat );
                    input.trigger("change");
                }else{
                    input.val("");
                    input.trigger("change.cpf");
                }

            })
            .on('focus.cpf',function(e){
                var id          = '#' + $(this).attr("data-tm-date"),
                    input       = obj.find(id),
                    format      = input.attr('data-date-format'),
                    day_select  = obj.find(id + '_day'),
                    month_select= obj.find(id + '_month'),
                    year_select = obj.find(id + '_year'),
                    day         = day_select.val(),
                    month       = month_select.val(),
                    year        = year_select.val(),
                    dateFormat  = $.tm_datepicker.formatDate(format, new Date( year, month-1, day));

                if ( 
                    ( year!=='' && month!=='' && day!=='' ) ||  
                    ( (year!=='' && month!=='') && day==='' ) || 
                    ( (day!=='' && year!=='')  && month==='' ) || 
                    ( (day!=='' && month!=='') && year==='' ) 
                ){
                    var _select = $(this);
                    _select.find("option").each(function(){
                        var $this=$(this),
                            val=$this.val();

                        var date_string = year+"-"+month+"-"+day;
                        if(_select.is(".tmcp-date-day")){
                            if(year==='' || month===''){
                                return;
                            }
                            date_string = year+"-"+month+"-"+val;
                        }else if(_select.is(".tmcp-date-month")){
                            if(year==='' || day===''){
                                return;
                            }
                            date_string = year+"-"+val+"-"+day;
                        }else if(_select.is(".tmcp-date-year")){
                            if(day==='' || month===''){
                                return;
                            }
                            date_string = val+"-"+month+"-"+day;
                        }

                        if (val!=''){
                            
                            try {

                                var d = $.tm_datepicker.parseDate( "yy-mm-dd", date_string );                            
                                if(d){
                                    if(_validate_date_with_options(d,input)){
                                        $this.prop("disabled",false);
                                    }else{
                                        $this.prop("disabled",true);    
                                    }                                    
                                }
                            } catch(e) {

                              $this.prop("disabled",true);

                            }                            
                            
                        }
                        
                    });
                }else{
                    day_select.find("option").prop("disabled",false);
                    month_select.find("option").prop("disabled",false);
                    year_select.find("option").prop("disabled",false);
                }

            });       

            $(window).on("resizestart",function() {            
                var field = $(document.activeElement);
                if (field.is('.hasDatepicker')) {
                    field.data("resizestarted",true);
                    if ($(window).width()<768){
                        field.data("resizewidth",true);
                        return;
                    }
                    field.tm_datepicker('hide');                
                }
            });
            $(window).on("resizestop",function() {            
                var field = $(document.activeElement);
                if (field.is('.hasDatepicker') && field.data("resizestarted")) {
                    if (field.data("resizewidth")){
                        field.tm_datepicker('hide');
                    }
                    field.tm_datepicker('show');                
                }
                field.data("resizestarted",false);
                field.data("resizewidth",false);
            });

        };        

        // URL replacement setup
        function tm_set_url_fields(){
            _document.on("click.cpfurl change.cpfurl tmredirect", ".use_url_containter .tmcp-radio, .use_url_containter .tmcp-radio+label", function(e) {
                var data_url=$(this).attr("data-url");
                if (data_url){
                    if (window.location!=data_url){
                        e.preventDefault();                
                        window.location=data_url;
                    }
                }
            });
            _document.on("change.cpfurl tmredirect", ".use_url_containter .tmcp-select", function(e) {
                var selected=$(this).children('option:selected'),
                    data_url=selected.attr("data-url");
                if (data_url){
                    if (window.location!=data_url){
                        e.preventDefault();                
                        window.location=data_url;
                    }
                }
            });
        }

        function tm_apply_validation(form){

            if (tm_epo_js.tm_epo_global_enable_validation=="yes"){

                var validation_rules={};
                this_epo_container.find(".tmcp-ul-wrap").each(function(loop_index,tmcpulwrap){
                    tmcpulwrap=$(tmcpulwrap);
                    var has_rules=tmcpulwrap.data('tm-validation');
                    if (has_rules && $.tmType(has_rules)==="object"){
                        var field=tmcpulwrap.find(".tm-epo-field"),
                            field_name=field.first().attr("name");
                        if (tmcpulwrap.is(".tm-extra-product-options-radio.tm-element-ul-radio")){
                            field_name=field.last().attr("name");
                        }    
                        if (tmcpulwrap.is(".tm-extra-product-options-checkbox.tm-element-ul-checkbox")){
                            field.each(function(f,fname){
                                if ("required" in has_rules){
                                    has_rules["required"]=function(elem){
                                        var len=tmcpulwrap.find("input.tm-epo-field.tmcp-checkbox:checked").length;
                                        if (len==0){
                                            if(field.last().attr("name")==$(elem).attr("name")){
                                                return true;
                                            }else{
                                                return false;
                                            }                                            
                                        }
                                        return len <= 0;
                                    };
                                }
                                validation_rules[$(fname).attr("name")]=has_rules;
                            });
                            
                        }else{
                            validation_rules[field_name]=has_rules;
                        }                        
                    }
                });
                
                form.removeData('tc_validator');
                var validator = form.tc_validate({
                        ignore          : "input.tm-qty:hidden[type='number'],input.input-text.qty,.ignore,.variations select,.tm-extra-product-options-variations input,.tm-extra-product-options-variations select",
                        rules           : validation_rules,
                        errorClass      : "tm-error",
                        validClass      : "tm-valid",
                        errorElement    : "label",
                        errorPlacement  : function(error, element) {
                            if (element.is('.tm-epo-field.tmcp-radio')){
                                error.appendTo( element.closest(".tmcp-ul-wrap").find(".tmcp-field-wrap").last() );
                            }else{
                                error.appendTo( element.closest(".tmcp-field-wrap") );
                            }
                            return false;
                        },
                        invalidHandler : function(event, validator) {
                            setTimeout(function() {
                                main_product.find('.single_add_to_cart_button').removeAttr('disabled').removeClass('fpd-disabled');
                                if ($.tcepo.temp_floatbox && "object"==typeof $.tcepo.temp_floatbox){
                                    $.tcepo.temp_floatbox.cancelfunc();    
                                }                                
                            }, 100 );
                            if (validator.errorList && validator.errorList[0] && validator.errorList[0].element){
                                goto_error_item($(validator.errorList[0].element));
                            }
                        },
                        submitHandler : function(form) {
                            main_product.find('.single_add_to_cart_button').attr('disabled','disabled');
                            return apply_submit_events();
                        }
                    });
                return true;
            }
            return false;
        }

        function tm_form_submit_event(){

            var form = get_main_form();

            if (!tm_apply_validation(form) && form_submit_events.length){
                form.on("submit",apply_submit_events);
            }
        }

        function apply_submit_events(e){
            var form_is_submit = true;
            for (var i = 0; i < form_submit_events.length; i++) {
                var form_event = form_submit_events[i],
                    type = typeof(form_event);
                if(type=="object"){
                    var trigger = typeof(form_event.trigger)=="function" || false;

                    if(trigger){
                        if (!form_event.trigger()){
                            form_is_submit = false;
                            break;
                        }
                    }
                }
            }
            for (var i = 0; i < form_submit_events.length; i++) {
                var form_event = form_submit_events[i],
                    type = typeof(form_event);
                if(type=="object"){
                    var on_true = typeof(form_event.on_true)=="function" || false,
                        on_false = typeof(form_event.on_false)=="function" || false;

                    if(form_is_submit){
                        form_event.on_true();
                    }else{
                        form_event.on_false();
                    }
                }

            }
            if(!form_is_submit){
                setTimeout(function() {
                    main_product.find('.single_add_to_cart_button').removeAttr('disabled');
                }, 100 );
            }
            return form_is_submit;
        }

        function tm_floating_totals(){

            if (!is_quickview && tm_epo_js.floating_totals_box && tm_epo_js.floating_totals_box!='disable'){

                if ( main_cart && this_epo_totals_container.length ) {
                    
                    var $tm_floating_box    = $('<div class="tm-floating-box '+tm_epo_js.floating_totals_box+'"></div>'),
                        is_nks              = false,
                        fb,
                        nks_selector        = $(".tm-floating-box-nks"),
                        alt_selector        = $(".tm-floating-box-alt"),
                        is_nks_alt          = false;
                    if (nks_selector.length>0){
                        is_nks=true;
                        $tm_floating_box.removeClass('top left right bottom').appendTo(nks_selector).show();
                    }else{
                        if (alt_selector.length>0){
                            $tm_floating_box.removeClass('top left right bottom').appendTo(alt_selector).hide();  
                        }else{
                            $tm_floating_box.appendTo("body").hide();    
                        }
                    }
                    if (nks_selector.length>0 || alt_selector.length>0){
                        is_nks_alt = true;
                    }

                    var tm_update_epo_pop_scroll = function(){
                            if ( $(window).scrollTop() > 100 || is_nks_alt) {
                                if ( ($tm_floating_box.is(":hidden") && !$tm_floating_box.is(":empty") )|| is_nks_alt ){
                                    if (!is_nks){
                                        $tm_floating_box.fadeIn();
                                    }else{
                                        $tm_floating_box.show();
                                    } 
                                }
                            }else{
                                if (!$tm_floating_box.is(":hidden")){
                                    if (!is_nks){
                                        $tm_floating_box.fadeOut();
                                    }else{
                                        $tm_floating_box.hide();
                                    } 
                                }
                            }                    
                        }

                    var tm_update_epo_pop = function(){
                        var tm_epo_totals_html          = this_epo_totals_container.data('tm-html'),
                            tm_floating_box_data        = this_epo_totals_container.data('tm-floating-box-data'),
                            values_obj=[];
                        if (tm_floating_box_data && tm_floating_box_data.length){
                            
                            $.each(tm_floating_box_data,function(i,row){
                                if(row.title==''){
                                    row.title='&nbsp;';
                                }
                                if(row.value==''){
                                    row.value='&nbsp;';
                                }
                                if (!row.title){
                                    row.title='&nbsp;';
                                }else{
                                    row.title=$('<div>'+row.title+'</div>');
                                    row.title.find('span').remove();
                                    row.title=row.title.html();
                                }

                                if (is_nks){
                                    if (row.label_show!=''){
                                        row.title='';
                                    }
                                    if (row.value_show!=''){
                                        row.value='';
                                    }
                                }
                                values_obj.push({
                                    'label_show':row.label_show,
                                    'value_show':row.value_show,
                                    'title':row.title,
                                    'value':row.value,
                                    'quantity':row.quantity,
                                    'price':tm_set_price(row.price,this_epo_totals_container,true,false),
                                });
                            });
                        }
                        
                        if ( ( tm_epo_totals_html && tm_epo_totals_html!='' ) || is_nks){
                            tm_update_epo_pop_scroll();
                        }else{
                            tm_epo_totals_html='';
                            $tm_floating_box.hide();
                        }

                        fb = $.fn.tm_template(
                        template_engine.tc_floating_box,
                        {
                            'html_before'   : tm_epo_js.floating_totals_box_html_before,
                            'html_after'    : tm_epo_js.floating_totals_box_html_after,
                            'option_label'  : tm_epo_js.i18n_option_label,
                            'option_value'  : tm_epo_js.i18n_option_value,
                            'option_qty'    : tm_epo_js.i18n_option_qty,
                            'option_price'  : tm_epo_js.i18n_option_price,
                            'values'        : values_obj,
                            'totals'        : tm_epo_totals_html
                        });

                        $tm_floating_box.html(fb);
                        
                    }
                    tm_update_epo_pop();
                    
                    main_cart.on('tm-epo-after-update',  function() {  
                        tm_update_epo_pop();
                    });

                    if (!is_nks){
                        
                        tm_update_epo_pop_scroll();

                        $(window).on( 'scroll', function () {
                            tm_update_epo_pop_scroll();
                        });

                    }

                }

            }

        }

        function get_main_cart(product,selector,id){
            return get_main_form(product,selector,id);
        }

        function get_main_form(product,selector,id){
            if (!selector){
                selector = "form";
            }
            return get_main_input_id(product,id).closest(selector);
        }

        function get_main_input_id(product,id){
            var selector = '';
            if (id){
                selector = selector + '[value="'+id+'"]'
            }
            if (!product){
                product = main_product;
            }
            var inputid = product.find(add_to_cart_selector+selector);
            if (inputid.length==0){
                inputid = product.find(tc_add_to_cart_selector+selector);
            }
            return inputid.last();
        }
        function tc_compatibility(){
            _document.tcready(function() {
                tc_compatibility_bookings();
            });
            _window.trigger('tm-epo-compatibility',{"epo":{
                'form':get_main_form(),
                'main_cart':main_cart,
                'main_epo_inside_form':main_epo_inside_form,
                'product_id_selector':product_id_selector,
                'epo_id_selector':epo_id_selector,
                'product_id':product_id,
                'this_epo_container':this_epo_container,
                'this_totals_container':this_totals_container,
                'this_epo_totals_container':this_epo_totals_container,
            }});
        }
        // Woothemes Bookings
        function tc_compatibility_bookings(){
            var form = get_main_form(),
                bookings_form = form.find('.wc-bookings-booking-form'),
                epo_trigger = bookings_form.find('.tm-epo-counter');

            if (epo_trigger.length>0){
                form.on("submit",function(){
                    form.find('.single_add_to_cart_button').addClass('disabled');
                });
                // move totals before booking button
                if (tm_epo_js.tm_epo_totals_box_placement=='woocommerce_before_add_to_cart_button'){
                    $('.wc-bookings-booking-form-button').before(this_totals_container);    
                }

                this_epo_totals_container.data('tc_is_bookings',1);
                this_epo_totals_container.data('bookings_form',bookings_form);
                this_epo_totals_container.data('bookings_form_init',0);
                this_epo_totals_container.data('price', 0);
                this_totals_container.find('.cpf-product-price').val(0);

                // find original bookings fields change handler
                var eventschange=findEventHandlers("change", epo_trigger),
                    originalevent=false,
                    found=false;
                for (var i = eventschange.length - 1; i >= 0; i--) {
                    if (found==true){
                        break;
                    }
                    if (eventschange[i].events){
                        for (var j = eventschange[i].events.length - 1; j >= 0; j--) {
                            if (eventschange[i].events[j].selector && eventschange[i].events[j].selector=="input, select"){
                                originalevent = eventschange[i].events[j].handler;
                                found=true;
                                break;
                            }
                        }
                    }
                }

                // find if product price is valid for the bookable product
                _document.ajaxSuccess(function(event, xhr, settings) {
                    if (!settings['data']){
                        return;
                    }
                    if (xhr && xhr.responseText){

                        var data = settings.data.tmparseParams();

                        if(data['action'] && data.action=="wc_bookings_get_blocks"){
                            // pricing not valid yet since no product price is calculated
                            this_epo_totals_container.data('price', 0);
                            this_totals_container.find('.cpf-product-price').val(0);
                            main_cart.trigger({
                                "type":"tm-epo-update",
                                "product_false":true
                            });
                        }

                    }

                });

                // alter bookings change handler
                if (originalevent!==false && found){
                    var bookingform = epo_trigger.closest('.wc-bookings-booking-form');
                        
                    bookingform
                    .off('change', 'input, select', originalevent)
                    .on('change.tcbookings', 'input, select', function(event){
                        var $this           = $(this),
                            $form           = $(this).closest('form'),
                            required_fields = $form.find('input.required_for_calculation'),
                            filled          = true;
                            
                        $.each( required_fields, function( index, field ) {
                            var value = $(field).val();
                            if ( ! value ) {
                                filled = false;
                            }
                        });
                        if ( ! filled ) {
                            //console.log('not filled');
                            return;
                        }

                        if (tm_apply_validation(form)){
                            if (!form.tc_validate().form()){
                                event.stopImmediatePropagation();
                                //console.log('not validated');
                                return;                                
                            }
                        }
                        
                        event.stopImmediatePropagation();
                            
                        var data = {
                            action: 'tc_epo_bookings_calculate_costs',
                            form:   $form.serialize(),
                            post_id: product_id,
                        };
                        $.post(tm_epo_js.ajax_url, data, function(response) {
                            if (response && response.result=="SUCCESS" && ("product_price" in response)){
                                this_epo_totals_container.data('price', response.product_price);
                                this_totals_container.find('.cpf-product-price').val(response.product_price);
                                this_epo_totals_container.data('bookings_form_init',1);
                                main_cart.trigger({
                                    "type":"tm-epo-update",
                                });
                                $this.trigger('tc_bookings_change');
                            }else if (response && response.result=="ERROR" ){
                                this_epo_totals_container.data('bookings_form_init',0);
                                
                                $form.find('.wc-bookings-booking-cost').show().html( response.html );                            

                            }
                        },'json');

                    });
                    bookingform.on('change tc_bookings_change', 'input, select', originalevent);
                }

            }

            if (!main_epo_inside_form){

                if (epo_trigger.length>0){


                    // trigger main bookings cost change when plugin fields change
                    this_epo_container.find('.tm-epo-field').not('.tm-epo-counter')
                    .off('change.tcbookings')
                    .on('change.tcbookings',  function(pass) {
                        
                        epo_trigger.trigger('change');
                        return;

                    });

                    // inject options data to bookings ajax
                    $.ajaxPrefilter(function( options, originalOptions, jqXHR ) {
                        if (options.type.toLowerCase()!=="post") {
                            return;
                        }
                        if(originalOptions.data && originalOptions.data["action"] && originalOptions.data["action"]=="wc_bookings_calculate_costs" && originalOptions.data["form"]){
                            var epos=$(epo_selector+'.tm-cart-main.tm-product-id-'+product_id+'[data-epo-id="'+epo_id+'"]');
                            if(epos.length==1){
                                var form = originalOptions.data["form"].tmparseParams();
                                form = $.extend(
                                        form, 
                                        epos.tm_aserializeObject()
                                    );
                                originalOptions.data["form"] = $.param(form, false );
                                options.data = $.param( 
                                    $.extend(
                                        originalOptions.data, 
                                        {}
                                    ), false );
                            }  
                        }                        
                    });
                   
                }
            }
        }

        function tm_check_main_cart(){
            if (!main_cart){
                 main_cart = get_main_cart(main_product,"form",product_id);
            }
            
            var form                        = get_main_form(),
                main_epo_inside_form_check  = form.find(epo_selector+".tm-cart-main").length,
                main_totals_inside_form_check  = form.find(".tm-totals-form-main").length;;
            
            if (main_epo_inside_form_check>0){
                main_epo_inside_form=true;
            }
            if (main_totals_inside_form_check>0){
                main_totals_inside_form=true;
            }
            if (!main_totals_inside_form){
                form_submit_events[form_submit_events.length] = {
                    "trigger":function(){return true;},
                    "on_true":function(){
                        // hidden fields see totals.php
                        var epos_hidden = $('.tc-totals-form.tm-totals-form-main.tm-product-id-'+product_id+'[data-epo-id="'+epo_id+'"]').tm_clone(),
                            formepo     = $('<div class="tm-hidden tm-formepo-normal"></div>');

                        form.find('.tm-formepo-normal').remove();
                        formepo.append(epos_hidden);
                        form.append(formepo);
                        return true;
                    },
                    "on_false":function(){
                        setTimeout(function() {
                            $('.tm-formepo').remove();
                        }, 100 );
                    }
                }
            }
            if (!main_epo_inside_form){

                form_submit_events[form_submit_events.length] = {
                    "trigger":function(){return true;},
                    "on_true":function(){
                        // visible fields
                        var epos        = $(epo_selector+'.tm-cart-main.tm-product-id-'+product_id+'[data-epo-id="'+epo_id+'"]').tm_clone(),
                        // hidden fields see totals.php
                            epos_hidden = $('.tc-totals-form.tm-totals-form-main.tm-product-id-'+product_id+'[data-epo-id="'+epo_id+'"]').tm_clone(),
                            formepo     = $('<div class="tm-hidden tm-formepo"></div>');

                        form.find('.tm-formepo').remove();
                        formepo.append(epos);
                        formepo.append(epos_hidden);
                        form.append(formepo);
                        return true;
                    },
                    "on_false":function(){
                        setTimeout(function() {
                            $('.tm-formepo').remove();
                        }, 100 );
                    }
                };
            }
        }
        
        function tm_show_hide_add_to_cart_button(){
            //Hide cart button check
            if (has_epo && tm_epo_js.tm_epo_hide_add_cart_button=="yes"){
                var button_selector = '.single_add_to_cart_button',
                    button          = main_product.find(button_selector);
                if(one_option_is_selected){
                    button.removeClass('tc-hide-add-to-cart-button');
                }else{
                    button.addClass('tc-hide-add-to-cart-button');
                }
            }

        }

        function tm_limit_c_selection($this,prevent){
            var allowed = parseInt($this.attr('data-limit')),
                checked = false,
                val;
            if (allowed>0){
                checked = 0;
                $this.closest(".tm-extra-product-options-checkbox").find("input.tm-epo-field[type='checkbox']:checked").each(function(){
                    var t=$(this),
                        q=t.closest('li.tmcp-field-wrap').find('input.tm-qty');
                    if (q.length>0){
                        val = parseInt(q.val());
                        if (val<=0){
                            val = 1;
                        }
                        checked = checked + val;
                    }else{
                        checked = checked + 1;
                    }

                });
                if (checked>allowed){
                    if(prevent){
                        $this.prop("checked", "").trigger("change");
                    }
                    return false;
                }
            }
            return true;
        }

        function tm_exact_c_selection($this,prevent){
            var allowed = parseInt($this.attr('data-limit')),
                checked = false,
                val;
            allowed=parseInt($this.attr('data-exactlimit'));
            if (allowed>0){
                checked = 0;
                $this.closest(".tm-extra-product-options-checkbox").find("input.tm-epo-field[type='checkbox']:checked").each(function(){
                    var t=$(this),
                        q=t.closest('li.tmcp-field-wrap').find('input.tm-qty');
                    if (q.length>0){
                        val = parseInt(q.val());
                        if (val<=0){
                            val = 1;
                        }
                        checked = checked + val;
                    }else{
                        checked = checked + 1;
                    }

                });             
                if (checked>allowed){
                    if(prevent){
                        $this.prop("checked", "").trigger("change");
                    }
                    return false;
                }
            }
            return true;
        }

        function tm_exactlimit_cont(exactlimit_cont){
            var checkall        = true,
                first_error_obj = false;
            exactlimit_cont.each(function(){
                 var exactlimit=$(this).find("[type='checkbox'][data-exactlimit]");
                 if (exactlimit.length && field_is_active(exactlimit)){
                    var eln     = parseInt(exactlimit.attr('data-exactlimit')),
                        checked = 0;
                    $(this).find("input.tm-epo-field[type='checkbox']:checked").each(function(){
                        var t=$(this),
                            val,
                            q=t.closest('li.tmcp-field-wrap').find('input.tm-qty');
                        if (q.length>0){
                            val = parseInt(q.val());
                            if (val<=0){
                                val = 1;
                            }
                            checked = checked + val;
                        }else{
                            checked = checked + 1;
                        }
                    });
                    var ew=$(this).closest('.cpf_hide_element'),
                        em=ew.find('div.tm-error-min');
                    if (eln!==checked){
                        checkall=false;
                        first_error_obj=$(this);
                        var message = tm_epo_js.tm_epo_global_validator_messages.epoexact.replace( "{0}", eln );
                        if(em.length){
                            em.remove();
                        }
                        $(this).after('<div class="tm-error-min tm-error">'+message+'</div>');
                    }else{
                        em.remove();
                    }
                 }                 
            });
            if(first_error_obj){
                global_error_obj=first_error_obj;
            }
            return checkall;
        }

        function tm_check_exactlimit_cont(exactlimit_cont){        
            form_submit_events[form_submit_events.length] = {
                "trigger" : function(){
                    var check=tm_exactlimit_cont(exactlimit_cont);
                    return check;
                },
                "on_true" : function(){return true;},
                "on_false" : function(){goto_error_item($(exactlimit_cont).find(".tm-epo-field").first());return true;}
            };
            
        }

        function tm_minimumlimit_cont(minimumlimit_cont){
            var checkall=true,
                first_error_obj=false;
            minimumlimit_cont.each(function(){
                 var minimumlimit=$(this).find("[type='checkbox'][data-minimumlimit]");
                 if (minimumlimit.length && field_is_active(minimumlimit)){
                    var eln=parseInt(minimumlimit.attr('data-minimumlimit'));
                    var checked = 0;
                    $(this).find("input.tm-epo-field[type='checkbox']:checked").each(function(){
                        var t=$(this),
                            val,
                            q=t.closest('li.tmcp-field-wrap').find('input.tm-qty');
                        if (q.length>0){
                            val = parseInt(q.val());
                            if (val<=0){
                                val = 1;
                            }
                            checked = checked + val;
                        }else{
                            checked = checked + 1;
                        }
                    });
                    var ew=$(this).closest('.cpf_hide_element'),
                        em=ew.find('div.tm-error-min');
                    if (eln>checked){
                        checkall=false;
                        first_error_obj=$(this);
                        var message = tm_epo_js.tm_epo_global_validator_messages.epomin.replace( "{0}", eln );
                        if(em.length){
                            em.remove();
                        }
                        $(this).after('<div class="tm-error-min tm-error">'+message+'</div>');
                    }else{
                        em.remove();
                    }
                 }                 
            });
            if(first_error_obj){
                global_error_obj=first_error_obj;
            }
            
            return checkall;
        }

        function tm_check_minimumlimit_cont(minimumlimit_cont){        

            form_submit_events[form_submit_events.length] = {
                "trigger" : function(){
                    var check=tm_minimumlimit_cont(minimumlimit_cont);
                    return check;
                },
                "on_true" : function(){return true;},
                "on_false" : function(){goto_error_item();return true;}
            };
            
        }

        function goto_error_item(item){
            var el=global_error_obj || item;
            if(el){
                var elsection=el.closest('.cpf-section'),
                    elsectionlink=elsection.find('.tm-section-link'),
                    elcpf_hide_element=el.closest('.cpf_hide_element');
                
                if(elsection.find('.tm-toggle').length){
                    elsection.find('.tm-toggle').trigger('openwrap.tmtoggle');
                }

                if (elsection.is('.section_popup')){
                    $(window).tc_scrollTo(elsectionlink,300);
                    elsectionlink.trigger('click.tmsectionpoplink');
                }else if(elsection.is('.tm-owl-slider-section')){
                    if (elcpf_hide_element.length>0){
                        $(window).tc_scrollTo(elcpf_hide_element,300);
                    }
                    var pos=el.closest('.owl-item').index();
                    elsection.find('.owl-carousel').trigger('to.owl.carousel', [pos, 1,true]);
                    setTimeout(function() {
                        elsection.find('.owl-carousel').trigger('refresh.owl.carousel');
                    }, 100 );
                }
                else{
                    if (elcpf_hide_element.length>0){
                        $(window).tc_scrollTo(elcpf_hide_element,300);
                    }                    
                }
                if(!item){
                    global_error_obj = false;    
                }                
            }
        }

        function tm_set_subscription_period(){
            this_epo_totals_container.each(function(){
                var cart_id=$(this).attr('data-cart-id'),
                    $cart=main_product.find('.tm-extra-product-options.tm-cart-'+cart_id),
                    subscription_period=$(this).data('subscription-period'),
                    variations_subscription_period=$(this).data('variations-subscription-period'),
                    base=$cart.find('.tmcp-field').closest('.tmcp-field-wrap'),
                    is_subscription=$(this).data('is-subscription');

                if (is_subscription){
                    base.find('.tmperiod').remove();
                    
                    var is_hidden=base.find('.amount').is(".hidden");
                    if (is_hidden){
                        is_hidden=" hidden";
                    }else{
                        is_hidden="";
                    }


                    var variation_id_selector='input[name^="variation_id"]',
                        $_cart=$(this).data('tm_for_cart');

                    if ($_cart){
                        if ( $_cart.find( 'input.variation_id' ).length > 0 ){
                            variation_id_selector='input.variation_id';
                        }
                        var current_variation=$_cart.find(variation_id_selector).val();
                        if (!current_variation) {
                            current_variation = 0;
                        }
                        if(variations_subscription_period[current_variation]){
                            subscription_period=variations_subscription_period[current_variation];    
                        }
                    }

                    base.find('.amount').after('<span class="tmperiod'+is_hidden+'"> / '+subscription_period+'</span>');
                    
                    $(this).find('.tmperiod').remove();
                    $(this).find('.amount.options').after('<span class="tmperiod"> / '+subscription_period+'</span>');
                    $(this).find('.amount.final').after('<span class="tmperiod"> / '+subscription_period+'</span>');
                }
            });

        }

        function get_composite_container_id(bto){
            var container_id = bto.attr('data-container-id');
            if (!container_id){
                var $composite_form=$(bto).closest('.composite_form'),
                    container_id=$composite_form.find( '.composite_data' ).data( 'container_id' );
            }
            return container_id;
        }

        function get_composite_price_data(container_id){
            var price_data = main_product.find( '.bto_form_' + container_id + ',#composite_form_' + container_id + ',#composite_data_' + container_id ).data( 'price_data' );
            return price_data;
        }

        function get_review_selector(item_id){
            return ' .review .price_' + item_id +', .summary_element_'+ item_id +' .summary_element_price';
        }

        function get_composite_item_id(item){
            return  item.attr('data-item-id') || item.attr('data-item_id');
        }

        function tm_apply_dpd(totals,price,apply){
            if (typeof(price)=="object"){
                price=price[0];
                if (isNaN(parseFloat(price))){
                    price=0;
                }
            }
            if(apply!=1){
                return price;
            }
            var rules=totals.data('product-price-rules'),
                $cart=totals.data('tm_for_cart');

            if (!rules || !$cart){
                return price;
            }else{
                var variation_id_selector='input[name^="variation_id"]';
                if ( $cart.find( 'input.variation_id' ).length > 0 ){
                    variation_id_selector='input.variation_id';
                }
                var qty_element = $cart.find('input.qty').last(),
                    qty = parseFloat(qty_element.val()),
                    current_variation=$cart.find(variation_id_selector).val();

                if (!current_variation) {
                    current_variation = 0;
                }
                if (isNaN(qty)){
                    if (totals.attr("data-is-sold-individually") || qty_element.length==0){
                        qty=1;
                    }
                }
                if ((rules[current_variation] && current_variation!=0) || rules[0]) {
                    if(!rules[current_variation]){
                        current_variation=0;
                    }
                    $(rules[current_variation]).each(function(id,rule){
                        var min=parseFloat(rule['min']),
                            max=parseFloat(rule['max']),
                            type=rule['type'],
                            value=parseFloat(rule['value']);
                        if (price==undefined){
                            price=0;
                        }

                        if (min <= qty && qty <= max){
                            switch (type){
                                case "percentage":
                                    price= price*(1-value/100);
                                    return false;
                                break;
                                case "price":
                                    price= price-value;
                                    return false;
                                break;
                                case "fixed":
                                    price= value;
                                    return false;
                                break;
                            }
                            
                        }
                    });    
                }
                
            }

            return price;

        }

        function tm_get_dpd(totals,apply){
            if(apply!=1){
                return false;
            }
            var price=[false,false],
                rules=totals.data('product-price-rules'),
                $cart=totals.data('tm_for_cart');

            if (!rules || !$cart){
                return false;
            }else{
                var variation_id_selector='input[name^="variation_id"]';
                if ( $cart.find( 'input.variation_id' ).length > 0 ){
                    variation_id_selector='input.variation_id';
                }
                var qty_element = $cart.find('input.qty').last(),
                    qty = parseFloat(qty_element.val()),
                    current_variation=$cart.find(variation_id_selector).val();

                if (!current_variation) {
                    current_variation = 0;
                }
                if (isNaN(qty)){
                    if (totals.attr("data-is-sold-individually") || qty_element.length==0){
                        qty=1;
                    }
                }
                if ((rules[current_variation] && current_variation!=0) || rules[0]) {
                    if(!rules[current_variation]){
                        current_variation=0;
                    }
                    $(rules[current_variation]).each(function(id,rule){
                        var min=parseFloat(rule['min']),
                            max=parseFloat(rule['max']),
                            type=rule['type'],
                            value=parseFloat(rule['value']);

                        if (min <= qty && qty <= max){
                            switch (type){
                                case "percentage":
                                    price= [value,type];
                                    return false;
                                break;
                                case "price":
                                    price= [value,type];
                                    return false;
                                break;
                                case "fixed":
                                    price= [value,type];
                                    return false;
                                break;
                            }
                            
                        }
                    });    
                }
                
            }

            return price;

        }

        function tm_calculate_product_price(totals){
            var rules=totals.data('product-price-rules'),
                price=parseFloat(totals.data('price')),
                $cart=totals.data('tm_for_cart');

            if (!rules || !$cart){
                return price;
            }else{
                var variation_id_selector='input[name^="variation_id"]';
                if ( $cart.find( 'input.variation_id' ).length > 0 ){
                    variation_id_selector='input.variation_id';
                }
                var qty_element = $cart.find('input.qty').last(),
                    qty = parseFloat(qty_element.val()),
                    current_variation=$cart.find(variation_id_selector).val();

                if (!current_variation) {
                    current_variation = 0;
                }
                if(!rules[current_variation]){
                    current_variation = 0;
                }
                if (isNaN(qty)){
                    if (totals.attr("data-is-sold-individually") || qty_element.length==0){
                        qty=1;
                    }
                }
                if ((rules[current_variation] && current_variation!=0) || rules[0]) {
                    if(!rules[current_variation]){
                        current_variation=0;
                    }
                    $(rules[current_variation]).each(function(id,rule){
                        var min=parseFloat(rule['min']),
                            max=parseFloat(rule['max']),
                            type=rule['type'],
                            value=parseFloat(rule['value']);

                        if (min <= qty && qty <= max){
                            switch (type){
                                case "percentage":
                                    price= price*(1-value/100);
                                    price = Math.ceil(price * Math.pow(10, tm_epo_js.currency_format_num_decimals) - 0.5) * Math.pow(10, -( parseInt(tm_epo_js.currency_format_num_decimals) ));
                                    if(price < 0){
                                        price=0;
                                    }
                                    return false;
                                break;
                                case "price":
                                    price= price-value;
                                    price = Math.ceil(price * Math.pow(10, tm_epo_js.currency_format_num_decimals) - 0.5) * Math.pow(10, -( parseInt(tm_epo_js.currency_format_num_decimals) ));
                                    if(price < 0){
                                        price=0;
                                    }
                                    return false;
                                break;
                                case "fixed":
                                    price= value;
                                    price = Math.ceil(price * Math.pow(10, tm_epo_js.currency_format_num_decimals) - 0.5) * Math.pow(10, -( parseInt(tm_epo_js.currency_format_num_decimals) ));
                                    if(price < 0){
                                        price=0;
                                    }
                                    return false;
                                break;
                            }
                            
                        }
                    });    
                }
                
            }
            return price;
        }

        /**
         * Set field price rules
         */
        function tm_element_epo_rules(obj,args){
            var element=$(obj),
                setter = element,
                bto,
                cart,
                current_variation,
                is_bto,
                bundleid,
                $totals,
                apply_dpd,
                per_product_pricing=true,
                is_range_field=element.is(".tmcp-range");
            if (!args){                
                bto = element.closest(composite_selector);
                cart=element.closest('.cart');
                var variation_id_selector='input[name^="variation_id"]';
                if ( cart.find( 'input.variation_id' ).length > 0 ){
                    variation_id_selector='input.variation_id';
                }
                current_variation=cart.find(variation_id_selector).val();
                is_bto=(bto.length>0);
                bundleid=cart.attr( 'data-product_id' );
                if (!bundleid){
                    bundleid=cart.closest('.component_content').attr( 'data-product_id' );
                    if (!bundleid){
                        bundleid=0;
                    }
                }
                // get current woocommerce variation
                if (!current_variation) {
                    current_variation = 0;
                }
                if (!is_bto){
                    $totals = this_epo_totals_container;
                }else{
                    $totals = main_product.find('.tm-epo-totals.tm-cart-'+bundleid);
                }
                apply_dpd=$totals.data('fields-price-rules');
            }else{
                bto=args["bto"];
                cart=args["cart"];
                current_variation=args["current_variation"];
                is_bto=args["is_bto"];
                bundleid=args["bundleid"];
                $totals=args["totals"];
                apply_dpd=args["apply_dpd"];
            }
            if (element.is('select')) {
                setter = element.find('option:selected');
            }
            var rules = setter.data('rules'),
                rulestype = setter.data('rulestype'),
                original_rules = setter.data('original-rules'),
                _rules, 
                _rulestype,
                _original_rules,
                pricetype, 
                price,
                original_price,
                formatted_price,
                original_formatted_price,
                product_price,
                cpf_bto_price = cart.find('.cpf-bto-price');

            if (original_rules==undefined){
                original_rules=rules;
            }
            // Composite Products                    
            if (is_bto){                  
                if (cpf_bto_price.length>0){
                    if (cpf_bto_price.data('per_product_pricing')){
                        product_price = cpf_bto_price.val();
                    }else{
                        product_price = 0;
                        per_product_pricing=false;
                    }
                    cpf_bto_price.val(product_price);                        
                }
            }else{
                if ($totals.length){
                    product_price = tm_calculate_product_price($totals);
                }
            }
            if(per_product_pricing==false){
                return;
            }
            pricetype='';
            if (typeof rules === "object") {

                if (current_variation in rules) {
                    price = rules[current_variation];
                    original_price = original_rules[current_variation];
                } else {
                    _rules = element.closest('.tmcp-ul-wrap').data('rules');
                    _original_rules = element.closest('.tmcp-ul-wrap').data('original-rules');

                    if (typeof _rules === "object") {
                        if (current_variation in _rules) {
                            price = _rules[current_variation];
                        } else {
                            price = rules[0];
                        }
                    } else {
                        price = rules[0];
                    }

                    if (typeof _original_rules === "object") {
                        if (current_variation in _original_rules) {
                            original_price = _original_rules[current_variation];
                        } else {
                            original_price = original_rules[0];
                        }
                    } else {
                        original_price = original_rules[0];
                    }
                }

                if (typeof rulestype === "object") {
                    if (current_variation in rulestype) {
                        pricetype = rulestype[current_variation];
                    }else{
                        _rulestype = element.closest('.tmcp-ul-wrap').data('rulestype');
                        if (typeof _rulestype === "object") {
                            if (current_variation in _rulestype) {
                                pricetype = _rulestype[current_variation];
                            }else{
                                pricetype = rulestype[0];
                            }
                        }else{
                            pricetype = rulestype[0];
                        }
                    }
                }else{
                    rulestype = element.closest('.tmcp-ul-wrap').data('rulestype');
                    if (typeof rulestype === "object") {
                        if (current_variation in rulestype) {
                            pricetype = rulestype[current_variation];
                        } else {
                            pricetype = rulestype[0];
                        }
                    }
                }
                if(typeof pricetype=="object"){
                    pricetype=pricetype[0];
                }
                
                if (element.is('select')) {
                    element.find('option').each(function(){
                        var $t=$(this);
                        $t.removeData('tm-price-for-late');
                        $t.removeData('islate');
                        $t.removeClass('tm-epo-late-field');
                    });
                }else{
                    setter.removeData('tm-price-for-late');
                    setter.removeData('islate');
                    setter.removeClass('tm-epo-late-field');
                }

                switch(pricetype){
                    case '':
                    case 'fee':
                        price=tm_apply_dpd($totals,price,apply_dpd);
                        original_price=tm_apply_dpd($totals,original_price,apply_dpd);
                    break;
                    case 'percent':
                        price=(price/100)*product_price;
                        original_price=(original_price/100)*product_price;
                    break;
                    case 'percentcurrenttotal':
                        late_fields_prices.push({"setter":setter,"price":price,"bundleid":bundleid});                    
                        setter.data('tm-price-for-late',price).data('islate', 1).addClass('tm-epo-late-field');
                        price=0;
                        original_price=0;
                    break;
                    case 'char':
                        price=tm_apply_dpd($totals,price,apply_dpd)*setter.val().length;
                        original_price=tm_apply_dpd($totals,original_price,apply_dpd)*setter.val().length;
                    break;
                    case 'charpercent':
                        price=(price/100)*product_price*setter.val().length;
                        original_price=(original_price/100)*product_price*setter.val().length;
                    break;
                    case 'charnospaces':
                        price=tm_apply_dpd($totals,price,apply_dpd) * setter.val().replace(/\s/g, "").length;
                        original_price=tm_apply_dpd($totals,original_price,apply_dpd) * setter.val().replace(/\s/g, "").length;
                    break;
                    case 'charnofirst':
                        var textlength=setter.val().length-1;
                        if (textlength<0){
                            textlength=0;
                        }
                        price=tm_apply_dpd($totals,price,apply_dpd)*textlength;
                        original_price=tm_apply_dpd($totals,original_price,apply_dpd)*textlength;
                    break;

                    case 'charnon':
                        var freechars=parseInt(setter.attr('data-freechars'));                            
                        if (isNaN(freechars)){
                            freechars=0;
                        }
                        var textlength=setter.val().length-freechars;
                        if (textlength<0){
                            textlength=0;
                        }
                        price=tm_apply_dpd($totals,price,apply_dpd)*textlength;
                        original_price=tm_apply_dpd($totals,original_price,apply_dpd)*textlength;
                    break;
                    case 'charpercentnon':
                        var freechars=parseInt(setter.attr('data-freechars'));                            
                        if (isNaN(freechars)){
                            freechars=0;
                        }
                        var textlength=setter.val().length-freechars;
                        if (textlength<0){
                            textlength=0;
                        }
                        price=(price/100)*product_price*textlength;
                        original_price=(original_price/100)*product_price*textlength;
                    break;
                    case 'charnonnospaces':
                        var freechars=parseInt(setter.attr('data-freechars'));                            
                        if (isNaN(freechars)){
                            freechars=0;
                        }
                        var textlength=setter.val().replace(/\s/g, "").length-freechars;
                        if (textlength<0){
                            textlength=0;
                        }
                        price=tm_apply_dpd($totals,price,apply_dpd)*textlength;
                        original_price=tm_apply_dpd($totals,original_price,apply_dpd)*textlength;
                    break;
                    case 'charpercentnonnospaces':
                        var freechars=parseInt(setter.attr('data-freechars'));                            
                        if (isNaN(freechars)){
                            freechars=0;
                        }
                        var textlength=setter.val().replace(/\s/g, "").length-freechars;
                        if (textlength<0){
                            textlength=0;
                        }
                        price=(price/100)*product_price*textlength;
                        original_price=(original_price/100)*product_price*textlength;
                    break;


                    case 'charpercentnofirst':
                        var textlength=setter.val().length-1;
                        if (textlength<0){
                            textlength=0;
                        }
                        price=(price/100)*product_price*textlength;
                        original_price=(original_price/100)*product_price*textlength;
                    break;
                    case 'step':
                    case 'stepfee':
                        if(is_range_field){
                            price=tm_apply_dpd($totals,price,apply_dpd)*setter.val();
                            original_price=tm_apply_dpd($totals,original_price,apply_dpd)*setter.val();
                        }else{
                            price=tm_apply_dpd($totals,price,apply_dpd)*setter.val().tmtoFloat();
                            original_price=tm_apply_dpd($totals,original_price,apply_dpd)*setter.val().tmtoFloat();
                        }                        
                    break;
                    case 'currentstep':
                    case 'currentstepfee':
                        if(is_range_field){
                            price=tm_apply_dpd($totals,setter.val(),apply_dpd);
                            original_price=tm_apply_dpd($totals,setter.val(),apply_dpd);
                        }else{
                            price=tm_apply_dpd($totals,setter.val().tmtoFloat(),apply_dpd);
                            original_price=tm_apply_dpd($totals,setter.val().tmtoFloat(),apply_dpd);
                        }
                    break;
                    case 'intervalstep':
                        if(is_range_field){
                            var min_value=parseFloat($('.tm-range-picker[data-field-id="'+setter.attr("id")+'"]').attr("data-min"));
                            price=tm_apply_dpd($totals,price,apply_dpd)*(setter.val()-min_value);
                            original_price=tm_apply_dpd($totals,original_price,apply_dpd)*(setter.val()-min_value);
                        }
                    break;
                    case 'row':
                        price=tm_apply_dpd($totals,price,apply_dpd)*((setter.val().match(new RegExp("\n", "g")) || []).length+1);
                        original_price=tm_apply_dpd($totals,original_price,apply_dpd)*((setter.val().match(new RegExp("\n", "g")) || []).length+1);
                    break;
                }
                if(element.data('tm-quantity')){
                    price=price*parseFloat(element.data('tm-quantity'));
                    original_price=original_price*parseFloat(element.data('tm-quantity'));
                }
                formatted_price = tm_set_price(price, $totals);
                original_formatted_price = tm_set_price(original_price, $totals);
                setter.data('price', tm_set_tax_price(price,$totals));
                setter.data('original_price', tm_set_tax_price(original_price,$totals));
                tm_update_price(setter.closest('.tmcp-field-wrap').find('.tc-price'),price,formatted_price,original_price,original_formatted_price);

            } else {
                var _tmcpulwrap = element.closest('.tmcp-ul-wrap');
                rules = _tmcpulwrap.data('rules');
                original_rules = _tmcpulwrap.data('original-rules');

                if (typeof rules === "object") {
                    if (current_variation in rules) {
                        price = rules[current_variation];
                    } else {
                        price = rules[0];
                    }
                    if (typeof original_rules === "object") {
                        if (current_variation in original_rules) {
                            original_price = original_rules[current_variation];
                        } else {
                            original_price = original_rules[0];
                        }
                    }else{
                        original_price = price;
                    }

                    if (typeof rulestype === "object") {
                        if (current_variation in rulestype) {
                            pricetype = rulestype[current_variation];
                        }else{
                            _rulestype = _tmcpulwrap.data('rulestype');
                            if (typeof _rulestype === "object") {
                                if (current_variation in _rulestype) {
                                    pricetype = _rulestype[current_variation];
                                }else{
                                    pricetype = rulestype[0];
                                }
                            }else{
                                pricetype = rulestype[0];
                            }
                        }
                    }else{
                        rulestype = _tmcpulwrap.data('rulestype');
                        if (typeof rulestype === "object") {
                            if (current_variation in rulestype) {
                                pricetype = rulestype[current_variation];
                            } else {
                                pricetype = rulestype[0];
                            }
                        }
                    }
                    if(typeof pricetype=="object"){
                        pricetype=pricetype[0];
                    }
                    
                    if (element.is('select')) {
                        element.find('option').each(function(){
                            var $t=$(this);
                            $t.removeData('tm-price-for-late');
                            $t.removeData('islate');
                            $t.removeClass('tm-epo-late-field');
                        });
                    }else{
                        setter.removeData('tm-price-for-late');
                        setter.removeData('islate');
                        setter.removeClass('tm-epo-late-field');
                    }

                    switch(pricetype){
                        case '':
                        case 'fee':
                            price=tm_apply_dpd($totals,price,apply_dpd);
                            original_price=tm_apply_dpd($totals,original_price,apply_dpd);
                        break;
                        case 'percent':
                            price=(price/100)*product_price;
                            original_price=(original_price/100)*product_price;
                        break;
                        case 'percentcurrenttotal':
                            late_fields_prices.push({"setter":setter,"price":price,"bundleid":bundleid});                    
                            setter.data('tm-price-for-late',price).data('islate', 1).addClass('tm-epo-late-field');
                            price=0;
                            original_price=0;
                        break;
                        case 'char':
                            price=tm_apply_dpd($totals,price,apply_dpd)*setter.val().length;
                            original_price=tm_apply_dpd($totals,original_price,apply_dpd)*setter.val().length;
                        break;
                        case 'charpercent':
                            price=(price/100)*product_price*setter.val().length;
                            original_price=(original_price/100)*product_price*setter.val().length;
                        break;
                        case 'step':
                        case 'stepfee':
                            if(is_range_field){
                                price=tm_apply_dpd($totals,price,apply_dpd)*setter.val();
                                original_price=tm_apply_dpd($totals,original_price,apply_dpd)*setter.val();
                            }else{
                                price=tm_apply_dpd($totals,price,apply_dpd)*setter.val().tmtoFloat();
                                original_price=tm_apply_dpd($totals,original_price,apply_dpd)*setter.val().tmtoFloat();
                            }                            
                        break;
                        case 'currentstep':
                        case 'currentstepfee':
                            if(is_range_field){
                                price=tm_apply_dpd($totals,setter.val(),apply_dpd);
                                original_price=tm_apply_dpd($totals,setter.val(),apply_dpd);
                            }else{
                                price=tm_apply_dpd($totals,setter.val().tmtoFloat(),apply_dpd);
                                original_price=tm_apply_dpd($totals,setter.val().tmtoFloat(),apply_dpd);
                            }                            
                        break;
                        case 'intervalstep':
                            if(is_range_field){
                                var min_value=parseFloat($('.tm-range-picker[data-field-id="'+setter.attr("id")+'"]').attr("data-min"));
                                price=tm_apply_dpd($totals,price,apply_dpd)*(setter.val()-min_value);
                                original_price=tm_apply_dpd($totals,original_price,apply_dpd)*(setter.val()-min_value);
                            }
                        break;

                    }
                    if(element.data('tm-quantity')){
                        price=price*parseFloat(element.data('tm-quantity'));
                        original_price=original_price*parseFloat(element.data('tm-quantity'));
                    }

                    formatted_price = tm_set_price(price, $totals);
                    original_formatted_price = tm_set_price(original_price, $totals);
                    setter.data('price', tm_set_tax_price(price,$totals));
                    setter.data('original_price', tm_set_tax_price(original_price,$totals));
                    tm_update_price(setter.closest('.tmcp-field-wrap').find('.tc-price'),price,formatted_price,original_price,original_formatted_price);
                }
            }
        }   

        function tm_epo_rules($thecart) {
            late_fields_prices=[];
            var all_carts;
            if (!$thecart){
               all_carts = main_product.find('.cart');
            }else{
               all_carts = $thecart; 
            }
            if (!all_carts.length>0){
                return;
            }
            all_carts.each(function(cart_index,cart){
                cart=$(cart); 
                var variation_id_selector='input[name^="variation_id"]';
                if ( cart.find( 'input.variation_id' ).length > 0 ){
                    variation_id_selector='input.variation_id';
                }
                var per_product_pricing=true,
                    bto = $(this).closest(composite_selector),
                    current_variation=cart.find(variation_id_selector).val(),
                    is_bto=false,
                    bundleid=cart.attr( 'data-product_id' );
                if (!bundleid){
                    bundleid=cart.closest('.component_content').attr( 'data-product_id' );
                    if (!bundleid){
                        bundleid=0;
                    }
                }

                if (bto.length>0){
                    is_bto=true;
                    var container_id = get_composite_container_id(bto);
                    var price_data = get_composite_price_data(container_id);
                    if(price_data){
                        per_product_pricing = is_per_product_pricing(price_data[ 'per_product_pricing' ]);    
                    }                    
                }
                // get current woocommerce variation
                if (!current_variation) {
                    current_variation = 0;
                }
                var $cart;
                if (!is_bto){
                    $cart=this_epo_container;
                    var $totals = this_epo_totals_container;
                }else{
                    $cart=main_product.find('.tm-extra-product-options.tm-cart-'+bundleid);
                    var $totals = main_product.find('.tm-epo-totals.tm-cart-'+bundleid);
                }
                // WooCommerce Dynamic Pricing & Discounts 
                var apply_dpd=$totals.data('fields-price-rules');
                // set initial prices for all fields
                if (!$cart.data('tm_rules_init_done')){
                    if ($totals.data('force-quantity')){
                        cart.find('input.qty').val($totals.data('force-quantity'));
                    }
                    $cart.find('.tm-quantity .tm-qty').each(function(){
                        var $this=$(this),field = $this.closest('.tmcp-field-wrap').find('.tm-epo-field');
                        field.data('tm-quantity',$this.val());
                    });//tmaddquantity
                    $cart.find('.tmcp-attributes, .tmcp-elements').each(function(index, element) {
                        var element = $(element),
                            rules = element.data('rules'),
                            original_rules = element.data('original-rules');
                        // if rule doesn't exit then init an empty rule
                        if (typeof rules !== "object") {
                            rules = {
                                0: "0"
                            };
                        }
                        if (typeof original_rules !== "object") {
                            original_rules = {
                                0: "0"
                            };
                        }
                        if (typeof rules === "object") {
                            // we skip price validation test so that every field has at least a price of 0
                            var price = tm_apply_dpd($totals,rules[current_variation],apply_dpd),
                                formatted_price = tm_set_price(price, $totals),
                                original_price = tm_apply_dpd($totals,original_rules[current_variation],apply_dpd),
                                original_formatted_price = tm_set_price(original_price, $totals);

                            element.find('.tmcp-field').each(function(i, e) {
                                var f=$(e);
                                if (per_product_pricing){
                                    f.data('price', tm_set_tax_price(price,$totals));
                                    f.data('original_price', tm_set_tax_price(original_price,$totals));
                                    tm_update_price(f.closest('.tmcp-field-wrap').find('.tc-price'),price,formatted_price,original_price,original_formatted_price);
                                }else{
                                    f.data('price', 0);
                                    f.data('original_price', 0);
                                    f.closest('.tmcp-field-wrap').find('.amount').empty();
                                }
                            });
                        }
                    });
                    $cart.data('tm_rules_init_done',1);
                }
                // skip specific field rules if per_product_pricing is false
                if (!per_product_pricing){
                    return true;
                }
                var args={
                    "bto":bto,
                    "cart":cart,
                    "current_variation":current_variation,
                    "is_bto":is_bto,
                    "bundleid":bundleid,
                    "totals":$totals,
                    "apply_dpd":apply_dpd
                };
                //  apply specific field rules
                $cart.find('.tmcp-field,.tmcp-sub-fee-field,.tmcp-fee-field').each(function(index, element) {
                    tm_element_epo_rules(element,args);
                });

            });
            
        }

        function tm_get_native_prices_block(obj){
            return obj.find('.single_variation .price,.bundle_price .price,.bto_item_wrap .price,.component_wrap .price,.composite_wrap .price');
        }

        /**
         * Set event handlers
         */
        function tm_epo_init($cart_container,$composite_cart) {

            // if $cart_container & $composite_cart is defined we are on the composite product

            var container_id,
                item_id = "main",
                $epo_holder,
                $totals_holder_container,
                $totals_holder,
                current_cart;

            if (!$cart_container){

                if (!main_cart || main_cart.length==0){
                    if (this_epo_container.is('.tc-shortcode')){
                        main_cart = main_product;
                    }else{
                        main_cart = get_main_cart(main_product,"form",product_id);
                    }                    
                }
                $cart_container             = main_cart.parent();
                $epo_holder                 = this_epo_container;
                $totals_holder_container    = this_totals_container;
                $totals_holder              = this_epo_totals_container;
            
            }else{
                // Composite bundle id
                container_id                = get_composite_container_id($cart_container);
                item_id                     = get_composite_item_id($cart_container);
                if(!item_id){
                    item_id                 = $cart_container.attr('data-item_id');
                }
                $epo_holder                 = main_product.find('.tm-extra-product-options.tm-cart-'+item_id);
                $totals_holder_container    = main_product.find('.tm-totals-form-'+item_id);
                $totals_holder              = main_product.find('.tm-epo-totals.tm-cart-'+item_id);
            
            }

            current_cart = $composite_cart || main_cart;
            $totals_holder.data('tm_for_cart',current_cart);
            
            var this_product_type = $totals_holder.data('type'),
                $variation_form = $cart_container.find('.variations_form');

            $variation_form.data('tc_product_id',product_id);

            // Element quantity selector
            $epo_holder
            .off( 'focus.cpf', '.tm-quantity .tm-qty')
            .on( 'focus.cpf', '.tm-quantity .tm-qty', function() {
                var $this=$(this),
                    field = $this.closest('.tmcp-field-wrap').find('.tm-epo-field'),
                    currentVal  = parseFloat( $this.val() ),
                    max         = parseFloat( $this.attr( 'max' ) ),
                    min         = parseFloat( $this.attr( 'min' ) ),
                    step        = $this.attr( 'step' ),
                    check1=tm_limit_c_selection(field,false),
                    check2=tm_exact_c_selection(field,false),
                    check3=true;
                
                // Format values
                if ( ! currentVal || currentVal === '' || currentVal === 'NaN' ) currentVal = 0;
                if ( max === '' || max === 'NaN' ) max = '';
                if ( min === '' || min === 'NaN' ) min = 0;
                if ( step === 'any' || step === '' || step === undefined || parseFloat( step ) === 'NaN' ) step = 1;
                
                if (currentVal<min || currentVal>max){
                    check3=false;
                }

                if(check1 && check2 && check3){
                    $this.data('tm-prev-value',currentVal);
                }else{
                    $this.data('tm-prev-value',min);
                }

            })
            .off( 'change.cpf', '.tm-quantity .tm-qty')
            .on( 'change.cpf', '.tm-quantity .tm-qty', function() {
                var $this=$(this),
                    field = $this.closest('.tmcp-field-wrap').find('.tm-epo-field'),
                    currentVal  = parseFloat( $this.val() ),
                    max         = parseFloat( $this.attr( 'max' ) ),
                    min         = parseFloat( $this.attr( 'min' ) ),
                    step        = $this.attr( 'step' ),
                    check1=tm_limit_c_selection(field,false),
                    check2=tm_exact_c_selection(field,false),
                    check3=true;
                
                // Format values
                if ( ! currentVal || currentVal === '' || currentVal === 'NaN' ) currentVal = 0;
                if ( max === '' || max === 'NaN' ) max = '';
                if ( min === '' || min === 'NaN' ) min = 0;
                if ( step === 'any' || step === '' || step === undefined || parseFloat( step ) === 'NaN' ) step = 1;

                if (currentVal<min || currentVal>max){
                    check3=false;
                }

                if(check1 && check2 && check3){
                    field.data('tm-quantity',$this.val()).trigger('change.cpf');
                }else{
                    if ($this.data('tm-prev-value')){
                        $this.val($this.data('tm-prev-value'));
                    }else{
                        $this.val(min);
                    }
                }                
            })
            .on( 'tmaddquantity', '.tm-quantity .tm-qty')
            .on( 'tmaddquantity', '.tm-quantity .tm-qty', function() {
                var $this=$(this),
                    field = $this.closest('.tmcp-field-wrap').find('.tm-epo-field');
                field.data('tm-quantity',$this.val());
            });

            // Custom variation events
            $epo_holder.find('.tm-epo-reset-variation')
            .off('click.cpfv')
            .on('click.cpfv', function(e) {
                var t=$(this),
                    id=t.attr('data-tm-for-variation'),
                    v="",
                    section=t.closest('.cpf-type-variations'),
                    inputs=t.closest('.cpf_hide_element').find('.tm-epo-variation-element');

                inputs.removeAttr("checked").prop("checked",false);
                $variation_form.find('#'+id).val(v).change();
                $variation_form.find('#'+id).trigger('focusin');

                main_product.find('.cpf-type-variations').not(section).each(function(i,el){
                    $variation_form.find('#'+$(el).find('.tm-epo-variation-element').first().attr('data-tm-for-variation')).trigger('focusin');
                });
                $(this).blur();
                $variation_form.trigger( 'woocommerce_update_variation_values_tmlogic' );
            });
            $epo_holder.find('input.tm-epo-variation-element,input.tm-epo-variation-element + label')
            .off('mouseup.cpfv')
            .on('mouseup.cpfv', function(e) {
                var t=$(this);
                if (t.is("label")){
                    t=t.prev("input");
                }
                if(t.attr("disabled")){
                    $variation_form.find('.reset_variations').trigger('click');
                }
                var id=t.attr('data-tm-for-variation');
                $variation_form.find('#'+id).trigger('focusin');
            });
            $epo_holder.find('.tm-epo-variation-element')
            .off('change.cpfv tm_epo_variation_element_change')
            .on('change.cpfv tm_epo_variation_element_change', function(e) {
                var t=$(this),
                    id=t.attr('data-tm-for-variation'),
                    v=t.val(),section=t.closest('.cpf-type-variations'),
                    native_select=$variation_form.find('#'+id);
                
                if (e && e.type && e.type=='tm_epo_variation_element_change'){

                }else{
                    var exists = false;
                    native_select.each(function(){
                        if (this.value == v) {
                            exists = true;
                            return false;
                        }
                    });
                    if (!exists){
                        native_select.trigger('focusin');
                    }
                    native_select.val(v).change();
                }
                                
                if(!v){
                    native_select.trigger('focusin');
                }

                main_product.find('.cpf-type-variations').not(section).each(function(i,el){
                    $variation_form.find('#'+$(el).find('.tm-epo-variation-element').first().attr('data-tm-for-variation')).trigger('focusin');
                });

                $(this).blur();
                $variation_form.trigger( 'woocommerce_update_variation_values_tmlogic' );
            })
            .off('focusin.cpfv')
            .on('focusin.cpfv', function() {
                if (!$(this).is('select')){
                    return;
                }
                var t=$(this),
                    id=t.attr('data-tm-for-variation'),
                    v=t.val();

                $variation_form.find('#'+id).trigger('focusin');
                
                $variation_form
                    .trigger( 'woocommerce_update_variation_values_tmlogic' );
            });
            // update price amount for select elements
            $epo_holder.find('select.tm-epo-field')
            .off('tm-select-change-html')
            .on('tm-select-change-html', function() {
                if ($composite_cart && main_cart && main_cart.data('per_product_pricing')!=undefined && !main_cart.data('per_product_pricing')){
                    return;
                }
                var field=$(this), 
                    formatted_price = tm_set_price(field.find('option:selected').data('price'), $totals_holder),
                    original_formatted_price = tm_set_price(field.find('option:selected').data('original_price'), $totals_holder);;
                
                tm_update_price(field.closest('.tmcp-field-wrap').find('.tc-price') ,field.find('option:selected').data('price') ,formatted_price ,field.find('option:selected').data('original_price') ,original_formatted_price);
                var e_tip=field.closest('.tmcp-field-wrap').find('.tc-tooltip');
                if(e_tip.length>0){
                    e_tip.attr('data-tm-tooltip-html',field.find('option:selected').attr('data-tm-tooltip-html')).trigger('tc-tooltip-html-changed');    
                }                
            })
            .off('tm-select-change')
            .on('tm-select-change', function() {
                if ($composite_cart && main_cart && main_cart.data('per_product_pricing')!=undefined && !main_cart.data('per_product_pricing')){
                    return;
                }
                $(this).trigger('tm-select-change-html');
                 
                current_cart.trigger({
                    "type":"tm-epo-update",
                    "norules":1,
                    "element":$(this)
                });
            });
            $epo_holder.find('select.tm-epo-field').trigger('tm-select-change-html');

            $epo_holder.find('input.tm-epo-field[maxlength],textarea.tm-epo-field[maxlength]').each(function(){
                var $this = $(this),
                    html = $.fn.tm_template(template_engine.tc_chars_remanining,{'maxlength':$this.attr('maxlength'),'characters_remaining':tm_epo_js.i18n_characters_remaining});
                $this.after($(html));
            });
            $epo_holder.find('input.tm-epo-field[maxlength],textarea.tm-epo-field[maxlength]')
            .off('change.tc_maxlen keyup.tc_maxlen')
            .on('change.tc_maxlen keyup.tc_maxlen',  function(pass) {
                 var $this=$(this);
                 $this.closest('.tmcp-field-wrap').find('.tc-chars-remanining').html( parseInt($this.attr('maxlength'))-parseInt($this.val().length) );
            });
            $epo_holder.find('.tm-epo-field')
            .off('tm_trigger_product_image')
            .on('tm_trigger_product_image',  function(pass) {
                var $this=$(this),
                    field=$(this);

                if (field.is('.tm-product-image:checkbox, .tm-product-image:radio, select.tm-product-image')){
                    var uic=field.closest('.tmcp-field-wrap').find('label img');
                    var variation_element_section=field.closest('.cpf-section'),
                        is_variation_element=variation_element_section.is('.tm-epo-variation-section');
                    if(field.is('select.tm-product-image')){
                        $this=field.children('option:selected');
                    }
                    if ($(uic).length>0 || ($this.attr('data-image')!=undefined && $this.attr('data-image')!='') || ($this.attr('data-imagep')!=undefined && $this.attr('data-imagep')!='')){
                        
                        if (field.is(':checked') 
                            || (field.is('select.tm-product-image') && field.val()!='' && (field.find("option:selected").attr("data-rules")!='' || field.is('.tm-epo-variation-element')) ) ){
                            
                            var src=$(uic).first().attr('data-original');

                            if (!src&& !is_variation_element){
                                src=$(uic).first().attr('src');
                            }
                            if (!src && !is_variation_element){
                                src=$this.attr('data-image');
                            }
                            if($this.attr('data-imagep')){
                                src=$this.attr('data-imagep');
                            }
                            if (src){
                                main_product.trigger('tm_change_product_image',{
                                    "src":src, 
                                    "element":field,
                                    "main_product":main_product,
                                    "epo_holder":$epo_holder
                                });                                
                            }else{
                                main_product.trigger('tm_change_product_image',{
                                    "src":false, 
                                    "element":field,
                                    "main_product":main_product,
                                    "epo_holder":$epo_holder
                                });
                            }

                        }else{
                            main_product.trigger('tm_restore_product_image',{
                                "element":field,
                                "main_product":main_product,
                                "epo_holder":$epo_holder
                            }); 
                        }
                    }else{
                         main_product.trigger('tm_restore_product_image',{
                            "element":field,
                            "main_product":main_product,
                            "epo_holder":$epo_holder
                        }); 
                    }
                }else{
                     main_product.trigger('tm_attempt_product_image',{
                        "element":field,
                        "main_product":main_product,
                        "epo_holder":$epo_holder
                    });
                }
            });

            $epo_holder.find('.tm-quantity')
            .off('showhide.cpfcustom')
            .on('showhide.cpfcustom',  function(event) {
                var quantity_selector=$(this),
                    field=quantity_selector.closest('.tmcp-field-wrap').find('.tm-epo-field'),
                    show=false;
                if (!field.is('.tm-epo-variation-element')){
                    if (field.is('select')){
                        if (field.val()!==''){
                            show=true;
                        }
                    }else if (field.is(':checkbox')) {                      
                        if (field.is(':checked')) {
                            show=true;
                        }
                    }else if (field.is(':radio')) {
                        if (field.is(':checked')) {
                            show=true;
                            if (tm_epo_js.tm_epo_show_only_active_quantities=='yes'){
                                var radios = field.closest('.cpf_hide_element').find(".tm-epo-field.tmcp-radio");
                                radios.each(function(){
                                    $(this).closest('.tmcp-field-wrap').find('.tm-quantity').hide();
                                });
                            }
                        }
                    }else{
                        if (field.val()) {
                            show=true;
                        }
                    }
                    if(show){
                        if (tm_epo_js.tm_epo_show_only_active_quantities=='yes'){
                            quantity_selector.show();
                        }
                        quantity_selector.find('.tm-qty').removeClass('ignore');
                    }else{
                        if (tm_epo_js.tm_epo_show_only_active_quantities=='yes'){
                            quantity_selector.hide();
                        }
                        quantity_selector.find('.tm-qty').addClass('ignore');
                    }
                }
            });

            $epo_holder.find('.tm-quantity').trigger('showhide.cpfcustom');
            $epo_holder.find('.tm-epo-field')
            .off('change.cpfcustom')
            .on('change.cpfcustom',  function(event) {
                var field=$(this),
                    quantity_selector=field.closest('.tmcp-field-wrap').find('.tm-quantity'),
                    show=false;
                quantity_selector.trigger('showhide.cpfcustom');
            });

            $epo_holder.find('.tm-epo-field')
            .off('change.cpf')
            .on('change.cpf',  function(pass) {
                var field=$(this);
                if (!field.is('.tm-epo-variation-element')){
                    if(field.is(".tmcp-range")){
                        field.trigger('change.cpflogic');
                    }
                    if (field.is('select')){
                        field.trigger('tm-select-change');
                    }else{
                        if(field.is(".tmcp-radio")){
                            //maybe do this on form submit?
                            field.closest('.cpf-type-radio').find('.tm-quantity .tm-qty').each(function(){
                                if (!$(this).closest('li.tmcp-field-wrap').find('.tmcp-radio').is(":checked")){
                                    $(this).attr("disabled","disabled");
                                }else{
                                    $(this).removeAttr("disabled");
                                }
                            });
                        }
                        current_cart.trigger({
                            "type":"tm-epo-update",
                            "norules":1,
                            "element":field
                        });
                    }                    
                }
                field.trigger('tm_trigger_product_image');
                main_product.trigger('tm_attempt_product_image',{
                    "element":field,
                    "main_product":main_product,
                    "epo_holder":$epo_holder
                });
            });
            
            $epo_holder.find('.tm-epo-reset-radio')
            .off('click.cpf')
            .on('click.cpf',  function() {
                var t=$(this),
                    c=t.closest('.cpf_hide_element'),
                    r=c.find('.tm-epo-field.tmcp-radio:checked');
                if (r.length){
                    r.removeAttr("checked").prop("checked",false);
                    r.trigger('change.cpf');
                    r.trigger("change.cpflogic");
                }
            });

            $epo_holder.find('.tm-epo-field.tmcp-textarea,.tm-epo-field.tmcp-textfield')
            .off('keyup.cpf')
            .on('keyup.cpf',  function() {
                $(this).trigger('change.cpf');
            });

            $epo_holder.find('.tm-epo-field.tmcp-upload')
            .off('change.cpfv')
            .on('change.cpfv',  function() {
                var $this=$(this),
                    label=$this.closest('label'),
                    li=$this.closest('.tmcp-field-wrap'),
                    name=li.find('.tm-filename');

                if (!name.length>0){
                    name=$('<span class="tm-filename"></span>')
                    label.after(name);                    
                }
                name.html($this.val());
            });

            $cart_container.find('input.qty').last()
            .off('change.cpf')
            .on('change.cpf',  function() {
                current_cart.trigger('tm-epo-check-dpd');

                $(this).data('tm-prev-value', $(this).val());

                current_cart.trigger({
                    "type":"tm-epo-update",
                    "norules":2
                });
            }).data('tm-prev-value', 1);

            // measurement price calculator compatibility
            $cart_container.find('.total_price')
            .off('wc-measurement-price-calculator-total-price-change.cpf')
            .on('wc-measurement-price-calculator-total-price-change.cpf', function(e,d,v) {
                $totals_holder_container.find('.cpf-product-price').val(v);
                $totals_holder.data('price',v);
                current_cart.trigger({
                    "type":"tm-epo-update",
                    //"norules":1
                });
            });
            $cart_container.find('.product_price')
            .off('wc-measurement-price-calculator-product-price-change.cpf dwc-measurement-price-calculator-update.cpf')
            .on('wc-measurement-price-calculator-product-price-change.cpf dwc-measurement-price-calculator-update.cpf', function(e,d,v) {
                $totals_holder_container.find('.cpf-product-price').val(v);
                $totals_holder.data('price',v);
                current_cart.trigger({
                    "type":"tm-epo-update",
                    //"norules":1
                });
            });

            // Name your price compatibility
            current_cart
            .off( 'woocommerce-nyp-update.cpf')
            .on( 'woocommerce-nyp-update.cpf', function() {
                var $nyp = current_cart.find( '.nyp' ),
                    new_product_price = $nyp.data( 'price');

                if ($nyp.length>0){
                    $totals_holder_container.find('.cpf-product-price').val(new_product_price);
                    $totals_holder.data('price',new_product_price);    
                    current_cart.trigger({
                        "type":"tm-epo-update",
                        //"norules":1
                    });
                }                    
            });
            current_cart.trigger( 'woocommerce-nyp-update.cpf');

            // Fancy product designer
            $('#fancy-product-designer-'+$totals_holder.parent().attr('data-product-id'))
            .off('priceChange.cpf')
            .on('priceChange.cpf', function(evt, sp, tp) {
                var v=$cart_container.find('input[name="fpd_product_price"]').val();
                
                if ($totals_holder.data('fpdprice')===undefined){
                    $totals_holder.data('fpdprice',parseFloat(v));
                }else{
                    $totals_holder.data('fpdprice',parseFloat(v));
                }
                
                if ($totals_holder.data('tcprice')===undefined){
                    $totals_holder.data('tcprice',parseFloat($totals_holder.data('price')));
                }else{
                    $totals_holder.data('price',parseFloat($totals_holder.data('tcprice')));
                }

                v =parseFloat($totals_holder.data('price')) + parseFloat(v);
                
                $totals_holder.parent().find('.cpf-product-price').val(v);
                
                $totals_holder.data('price',v);
                
                current_cart.trigger({
                    "type":"tm-epo-update",
                    "norules":1
                });
            });

            /* DPD update displayed values when rules change */
            current_cart
            .off("tm-epo-check-dpd")
            .on('tm-epo-check-dpd', function(pass) {
                var $totals=$totals_holder,
                    apply_dpd=$totals.data('fields-price-rules');

                if(apply_dpd!=1){
                    return;
                }
                var rules=$totals.data('product-price-rules'),
                    $cart=$totals.data('tm_for_cart');

                if (!rules || !$cart){
                    return;
                }else{
                    var variation_id_selector='input[name^="variation_id"]';
                    if ( $cart.find( 'input.variation_id' ).length > 0 ){
                        variation_id_selector='input.variation_id';
                    }
                    var qty_element = $cart.find('input.qty').last(),
                        qty = parseFloat(qty_element.val()),
                        qty_prev = parseFloat(qty_element.data('tm-prev-value')),
                        current_variation=$cart.find(variation_id_selector).val();

                    if (!current_variation) {
                        current_variation = 0;
                    }
                    if (isNaN(qty)){
                        if ($totals.attr("data-is-sold-individually") || qty_element.length==0){
                            qty=1;
                        }
                    }

                    if ((rules[current_variation] && current_variation!=0) || rules[0]) {
                        if(!rules[current_variation]){
                            current_variation=0;
                        }
                        $(rules[current_variation]).each(function(id,rule){
                            var min=parseFloat(rule['min']),
                                max=parseFloat(rule['max']),
                                type=rule['type'],
                                value=parseFloat(rule['value']);

                            if (min <= qty && qty <= max){
                                if (min <= qty_prev && qty_prev <= max){
                                    
                                }else{
                                    tm_epo_rules($cart);
                                }
                            }
                        });    
                    }
                    
                }
            });

            // global custom update event
            current_cart
            .off("tm-epo-update")
            .on('tm-epo-update', function(pass) {
                
                var $cart=$(this),
                    check_for_bto_internal_show,
                    bundleid=$cart.attr( 'data-product_id' );

                if (!bundleid){
                    bundleid=$cart.closest('.component_content').attr( 'data-product_id' );
                    if (!bundleid){
                        bundleid=0;
                    }
                }

                if ($composite_cart){
                    $totals_holder.addClass("cpf-bto-totals");
                }
                if(!pass.norules){
                    tm_epo_rules($cart);
                }else{
                    if(pass.norules==1){
                        tm_element_epo_rules(pass.element);
                    }
                    late_fields_prices=[];
                    $epo_holder.find('.tm-epo-late-field').each(function(){
                        var setter=$(this), 
                            price=setter.data('tm-price-for-late');
                        setter.data('price',0);
                        late_fields_prices.push({"setter":setter,"price":price,"bundleid":bundleid});
                    });
                }
                var variation_id_selector='input[name^="variation_id"]';
                if ( $cart.find( 'input.variation_id' ).length > 0 ){
                    variation_id_selector='input.variation_id';
                }
                var product_price       = 0,
                    v_product_price     = 0,
                    product_price_bto   = false,
                    total               = 0,
                    product_type        = $totals_holder.data('type'),
                    show_total          = false,
                    qty_element         = $cart.find('input.qty').last(),
                    qty                 = parseFloat(qty_element.val()),
                    cpf_bto_price       = current_cart.find('.cpf-bto-price'),
                    per_product_pricing = true,
                    is_bto              = false,
                    current_variation   = $cart.find(variation_id_selector).val(),
                    price_override      = $totals_holder.attr('data-price-override'),
                    tm_floating_box_data= [];

                if (isNaN(qty)){
                    if ($totals_holder.attr("data-is-sold-individually") || qty_element.length==0){
                        qty=1;
                    }
                }
                
                if ($totals_holder.length){
                    product_price = tm_calculate_product_price($totals_holder);
                }else{
                    if (cpf_bto_price.length>0){
                        product_price = cpf_bto_price.val();
                    }
                }
                v_product_price=product_price;

                // Composite Products
                if ($composite_cart && $cart.find('.cpf-bto-price').length>0){
                    is_bto=true;
                    product_price=parseFloat($cart.find('.cpf-bto-price').val());
                    per_product_pricing=$cart.find('.cpf-bto-price').data('per_product_pricing');

                }else if (!$composite_cart && main_product.find('.cpf-bto-price').length>0){
                    
                    check_for_bto_internal_show=1;                   
                    
                    product_price_bto=[];
                    
                    main_product.find('.cpf-bto-price').each(function(ind,el){                    
                        if (!isNaN( parseFloat($(this).val()))){
                            var _qty=$(this).closest('.cart').find('input.qty');  
                            if (_qty.length>0){
                                _qty=parseFloat(_qty.val());
                            }else{
                                _qty=1;
                            }
                            var isi=$(this).parent().find('.cpf-bto-totals').attr("data-is-sold-individually");
                            var optionsprice=$(this).parent().find('.cpf-bto-optionsprice').val();
                            if (!isNaN( parseFloat(optionsprice))){
                                optionsprice=parseFloat(optionsprice);
                            }else{
                                optionsprice=0;
                            }
                            product_price=parseFloat(product_price)+parseFloat($(this).val()*_qty);
                            product_price_bto.push([$(this).val(),optionsprice,_qty,isi]);
                        }
                    });
                    
                    main_product.find('.cpf-bto-optionsprice').each(function(ind,el){
                        if (!isNaN( parseFloat($(this).val()))){
                            product_price=parseFloat(product_price)+parseFloat($(this).val());
                        }
                    });
                    
                }
                if ($composite_cart || (main_epo_inside_form && tm_epo_js.tm_epo_totals_box_placement=="woocommerce_before_add_to_cart_button")){
                    if ( (product_type == 'variable' || product_type == 'variable-subscription')  && !$totals_holder.data("moved_inside")) {
                        $cart.find('.variations_button').before($totals_holder);
                        $totals_holder.data("moved_inside",1);
                    }
                }
                /* move total box of main cart if is composite */
                if (main_epo_inside_form && tm_epo_js.tm_epo_totals_box_placement=="woocommerce_before_add_to_cart_button"){
                    if ((product_type == 'bto' || product_type == 'composite') && !$totals_holder.data("moved_inside")) {
                        $cart.find('.bundle_price,.composite_price').after($totals_holder);
                        $totals_holder.data("moved_inside",1);
                    }
                }
                $epo_holder.find('.tmcp-field').each(function() {
                    var field=$(this),
                        field_div=field.closest('.cpf_hide_element'),
                        field_label_show=field_div.attr("data-fblabelshow"),
                        field_value_show=field_div.attr("data-fbvalueshow"),
                        field_title=(field_label_show=='')?field_div.find('.tm-epo-field-label').html():'',
                        _value='',
                        option_quantity=field_div.find('.tm-qty').val();
                    if (option_quantity==undefined){
                        option_quantity="";
                    }
                    if (field.is(':checkbox, :radio, :input')) {
                        if ( field_is_active( field ) ){ 
                            var option_price = 0;
                            if (field.is('.tmcp-checkbox, .tmcp-radio')) {
                                if (field.is(':checked')) {
                                    option_price = field.data('price');
                                    show_total = true;
                                    field.data('isset',1);
                                    var liw=field.closest('li.tmcp-field-wrap'),
                                        cri=liw.find('.checkbox_image,.radio_image');
                                    _value="";
                                    if (cri.length){
                                        _value=cri.html();
                                    }else{
                                        var tl=field.closest('li.tmcp-field-wrap').find('.tm-label');
                                        if (tl.length){
                                            _value=tl.html();
                                        }                                        
                                    }                                        
                                    if (field.is('.use_images')){
                                        if (cri.length){
                                            _value=_value+cri.clone().addClass('tc-img-floating')[0].outerHTML;
                                        }else{
                                            _value=_value+liw.find('label').html();
                                        }
                                    }
                                    tm_floating_box_data.push({title:field_title,value:_value,price:option_price,quantity:option_quantity,label_show:field_label_show,value_show:field_value_show});
                                }else{
                                    field.data('isset',0);
                                }
                            } else if (field.is('.tmcp-select')) {
                                option_price = field.find('option:selected').data('price');
                                 
                                var options = field.children('option:selected');                                    
                                if (options.val()==="" && options.attr('data-rulestype')===""){
                                    //not selected
                                }else{
                                    show_total = true;
                                }
                                
                                field.find('option').data('isset',0);
                                field.find('option:selected').data('isset',1);

                                if (!(field.find('option:selected').val()==="" && field.find('option:selected').attr('data-rulestype')==="")){
                                    
                                    _value=field.find('option:selected').text();
                                    
                                    tm_floating_box_data.push({title:field_title,value:_value,price:option_price,quantity:option_quantity,label_show:field_label_show,value_show:field_value_show});
                                }
                            } else {
                                if (field.val()) {
                                    if (field.is(".tmcp-range") && field.val()=="0"){
                                        field.data('isset',0);
                                    }else{
                                        option_price = field.data('price');
                                        show_total = true;
                                        field.data('isset',1);
                                        
                                        _value=field.val();
                                        if (field.is(".tmcp-range")){
                                            var forrangepicker = $(".tm-range-picker[data-field-id='"+field.attr("id")+"']"),
                                                $decimals = forrangepicker.attr('data-step').split(".");
                                            if ($decimals.length == 1){
                                                $decimals = 0;
                                            }else{
                                                $decimals = $decimals[1].length;
                                            }
                                            _value = accounting.formatNumber(_value, {
                                                decimal: local_decimal_separator,
                                                thousand: local_thousand_separator,
                                                precision: $decimals
                                            });
                                        }
                                        

                                        tm_floating_box_data.push({title:field_title,value:_value,price:option_price,quantity:option_quantity,label_show:field_label_show,value_show:field_value_show});
                                    }
                                }else{
                                    field.data('isset',0);
                                }
                            }
                            if (!option_price) {
                                option_price = 0;
                            }

                            total = parseFloat(total) + parseFloat(option_price);
                        }
                    }
                });
                
                if (price_override=="1" && parseFloat(total)>0){
                    product_price=v_product_price=0;
                }
                
                $totals_holder.data('tm-floating-box-data',tm_floating_box_data);
                
                var subscription_options_total=0;
                var cart_fee_options_total=0;
                $epo_holder.find('.tmcp-sub-fee-field,.tmcp-fee-field').each(function() {
                    var field=$(this);
                    if (field.is(':checkbox, :radio, :input')) {
                        if ( field_is_active( field ) ){ 
                            var option_price = 0;
                            if (field.is('.tmcp-checkbox, .tmcp-radio')) {
                                if (field.is(':checked')) {
                                    option_price = field.data('price');
                                    show_total = true;
                                    field.data('isset',1);
                                }else{
                                    field.data('isset',0);
                                }
                            } else if (field.is('.tmcp-select')) {
                                option_price = field.find('option:selected').data('price');
                                var options = field.children('option:selected');                                    
                                if (options.val()==="" && options.attr('data-rulestype')===""){
                                    //not selected
                                }else{
                                    show_total = true;
                                }
                                field.find('option').data('isset',0);
                                field.find('option:selected').data('isset',1);
                            } else {
                                if (field.val()) {
                                    option_price = field.data('price');
                                    show_total = true;
                                    field.data('isset',1);
                                }else{
                                    field.data('isset',0);
                                }
                            }
                            if (!option_price) {
                                option_price = 0;
                            }

                            if (field.is('.tmcp-sub-fee-field')){
                                subscription_options_total = parseFloat(subscription_options_total) + parseFloat(option_price);
                            }
                            if (field.is('.tmcp-fee-field')){
                                cart_fee_options_total = parseFloat(cart_fee_options_total) + parseFloat(option_price);
                            }
                        }
                    }
                });

                one_option_is_selected=show_total;
                tm_show_hide_add_to_cart_button();

                if(cart_fee_options_total>0){                    
                    show_total=true;
                }

                if ($totals_holder.attr('data-type')=="bto" || $totals_holder.attr('data-type')=="composite"){
                    var bto_show=this_epo_totals_container.data('btois');
                    if (bto_show==='show'){
                        show_total=true;
                    }
                }
                
                if (check_for_bto_internal_show){
                    show_total=true;
                }
                
                if ($composite_cart && !per_product_pricing){
                    show_total=false;
                }

                if(tm_epo_js.tm_epo_final_total_box=='pxq' 
                    || tm_epo_js.tm_epo_final_total_box=='hide' 
                    || tm_epo_js.tm_epo_final_total_box=='normal'
                    || tm_epo_js.tm_epo_final_total_box=='final'
                    || tm_epo_js.tm_epo_final_total_box=='hideoptionsifzero'
                    ){
                    show_total=true;
                }

                if (qty > 1){
                    show_total=true;
                }
                if ( (product_type == 'variable' || product_type == 'variable-subscription') && !current_variation){
                    show_total=false;
                }

                // Original price + options price type requires this here.
                var _total=total;
                product_price=tm_set_tax_price(product_price,$totals_holder);
                var late_total_price= add_late_fields_prices(parseFloat(product_price) + parseFloat(_total),bundleid,$totals_holder); 

                //if(tm_epo_js.tm_epo_final_total_box=='disable'){
                //     show_total=false;
                //}
                
                var tc_totals_ob = {};

                if (show_total && qty > 0) {
                    /* hide native prices */
                    var hide_native_price = $.tc_apply_filters("hide_native_price",true);
                    if (hide_native_price===true){
                        tm_get_native_prices_block($cart).hide();    
                    }else{
                        tm_get_native_prices_block($cart).show();
                    }
                    
                    //var _total=total;

                    total = parseFloat(total * qty);

                    var formatted_options_total,// = tm_set_price(total),
                        formatted_final_total,
                        extra_fee=0;
                    
                    if (tm_epo_js.extra_fee){
                        extra_fee=parseFloat(tm_epo_js.extra_fee);
                        if (isNaN(extra_fee)){
                            extra_fee=0;
                        }
                    }
                    //product_price=tm_set_tax_price(product_price,$totals_holder);

                    var product_total_price = parseFloat(product_price * qty);

                    // fix for products that are sold individually
                    if(product_price_bto){
                        product_price=tm_set_tax_price(v_product_price,$totals_holder);
                        product_total_price= product_total_price = parseFloat(product_price * qty);
                        for (var i = 0; i < product_price_bto.length; i++) {
                            var pp=product_price_bto[i],line=0;
                            if(pp[3]){
                                line=tm_set_tax_price(parseFloat(pp[0])+parseFloat(pp[1]),$totals_holder);
                                product_price=product_price+line;
                                product_total_price=product_total_price+parseFloat(line);
                            }else{
                                line=+tm_set_tax_price((parseFloat(pp[0])*parseFloat(pp[2]))+parseFloat(pp[1]),$totals_holder);
                                product_price=product_price+line;
                                product_total_price=product_total_price+parseFloat(line * qty);
                            }
                        };
                    }
                                        
                    _total = _total + late_total_price;
                    total = parseFloat(_total * qty);

                    // Woothemes Bookings
                    if ($totals_holder.data('tc_is_bookings')!==undefined){
                        var bookingform = $totals_holder.data('bookings_form'),
                            options_multiplier = 0,
                            found = false;
                        if (bookingform.length){
                           
                            if (tm_epo_js.wc_booking_person_qty_multiplier){
                                bookingform.find('[id^="wc_bookings_field_persons"]').each(function(){                             
                                    options_multiplier = options_multiplier + parseFloat($(this).val());
                                    found = true;
                                });
                            }
                            if (tm_epo_js.wc_booking_block_qty_multiplier && bookingform.find('#wc_bookings_field_duration').length){
                                options_multiplier = options_multiplier + parseFloat(bookingform.find('#wc_bookings_field_duration').val());
                                found = true;
                            }
                        }
                        if (found){
                            total = total * options_multiplier;
                        }
                    }

                    total = $.tc_apply_filters("tc_adjust_total",total,$totals_holder);
                    cart_fee_options_total = $.tc_apply_filters("tc_adjust_totals_fee",cart_fee_options_total,$totals_holder);

                    var total_plus_fee=parseFloat(total)+parseFloat(cart_fee_options_total);
                    formatted_options_total = tm_set_price(total_plus_fee,$totals_holder,true,true);
                    
                    product_total_price = parseFloat(product_total_price + total_plus_fee + extra_fee);
                    product_total_price = $.tc_apply_filters("tc_adjust_product_total_price",product_total_price,total_plus_fee,extra_fee,total,cart_fee_options_total,$totals_holder);

                    formatted_final_total = tm_set_price(product_total_price,$totals_holder,true,true);
                    
                    var html,
                        show_options_total=false,
                        formatted_extra_fee='',
                        show_extra_fee=false,
                        show_final_total=false,
                        formatted_subscription_fee_total='',
                        show_sign_up_fee=false;
                    
                    if (
                        tm_epo_js.tm_epo_final_total_box!='pxq' 
                        && tm_epo_js.tm_epo_final_total_box!='final' 
                        && tm_epo_js.tm_epo_final_total_box!='hide' 
                        && (!(total_plus_fee==0 && tm_epo_js.tm_epo_final_total_box=='hideoptionsifzero')) ){
                        show_options_total=true;                    
                    }
                    if (extra_fee) {
                        show_extra_fee=true;
                        formatted_extra_fee=tm_set_price(extra_fee,$totals_holder,true,true);
                    }
                    if (formatted_final_total) {
                        show_final_total=true;
                    }
                    if ($totals_holder.data('is-subscription') ) {
                        var subscription_total=parseFloat($totals_holder.data('subscription-sign-up-fee'))+parseFloat(subscription_options_total);
                        if(subscription_total){
                            show_sign_up_fee=true;
                            formatted_subscription_fee_total=tm_set_price(subscription_total,$totals_holder,true,true);
                        }
                    }

                    // Woothemes Bookings
                    if(pass.product_false===true || $totals_holder.data('bookings_form_init')!==undefined && $totals_holder.data('bookings_form_init')===0){
                        //formatted_options_total = "";
                        formatted_final_total = "-";
                    }

                    tc_totals_ob = {
                            'options_total'                     : tm_epo_js.i18n_options_total,
                            'formatted_options_total'           : formatted_options_total,
                            'show_options_total'                : show_options_total,
                            'extra_fee'                         : tm_epo_js.i18n_extra_fee,
                            'formatted_extra_fee'               : formatted_extra_fee,
                            'show_extra_fee'                    : show_extra_fee,
                            'final_total'                       : tm_epo_js.i18n_final_total,
                            'formatted_final_total'             : formatted_final_total,
                            'show_final_total'                  : show_final_total,
                            'sign_up_fee'                       : tm_epo_js.i18n_sign_up_fee,
                            'formatted_subscription_fee_total'  : formatted_subscription_fee_total,
                            'show_sign_up_fee'                  : show_sign_up_fee
                        };
                    html = $.fn.tm_template( template_engine.tc_final_totals, tc_totals_ob );
                    $totals_holder.data('tm-html',html);
                    
                    if(tm_epo_js.tm_epo_final_total_box!='disable'){

                        if ((tm_epo_js.tm_epo_final_total_box=='hideifoptionsiszero' && total_plus_fee==0) || tm_epo_js.tm_epo_final_total_box=='hide'){
                            html='';
                            $totals_holder.html(html).hide();
                            this_totals_container.hide();
                            if (formatted_final_total){
                                var update_native_html=tm_get_native_prices_block($cart);
                                update_native_html.html('<span class="amount">'+formatted_final_total+'</span>').show();
                                if (tm_epo_js.tm_epo_final_total_box=='hide'){
                                    if(total_plus_fee>0){
                                        $(native_product_price_selector).html('<span class="amount">'+formatted_final_total+'</span>');
                                    }else{
                                        $(native_product_price_selector).html(native_product_price_selector_initial_html);
                                    }
                                }
                            }
                        }else{

                            $totals_holder.html(html).show();
                            this_totals_container.show();

                            var apply_dpd=$totals_holder.data('fields-price-rules'),
                                dpd_prefix=$totals_holder.data('tm-epo-dpd-prefix'),
                                dpd_suffix=$totals_holder.data('tm-epo-dpd-suffix');
                            if (apply_dpd==1){
                                var dpd_discount=tm_get_dpd($totals_holder,apply_dpd),
                                    dpd_string='';
                                if (dpd_discount[0] && dpd_discount[1] && (dpd_prefix || dpd_suffix)){
                                    var dpd_discount_type=dpd_discount[1],
                                        dpd_discount_string='';
                                    switch(dpd_discount_type){
                                        case 'percentage':
                                            dpd_discount_string = dpd_discount[0] +'%';
                                        break;
                                        case 'price':
                                            dpd_discount_string = tm_set_price(dpd_discount[0]*qty,$totals_holder,false,false);
                                        break;
                                        case 'fixed':
                                            dpd_discount_string = tm_set_price(dpd_discount[0],$totals_holder,false,false);
                                        break;
                                    }
                                    dpd_string = dpd_prefix + ' ' + dpd_discount_string + ' ' + dpd_suffix;
                                }
                                if(dpd_string){
                                    $totals_holder.find('.tm-final-totals .amount.final').after('<span class="tm_dpd_label">'+dpd_string+'</span>');
                                }
                            }
                        }
                    }

                    if ($composite_cart){
                        if (per_product_pricing){
                            $cart.find('.cpf-bto-optionsprice').val(parseFloat(total));
                        }
                        main_cart.trigger({
                            "type":"tm-epo-update",
                            "norules":1
                        });
                    }else{
                        this_epo_totals_container.data('is_active',true);
                        tm_set_subscription_period();
                    }
                } else {
                    /* show native prices */
                    tm_get_native_prices_block($cart)
                    .show()
                    .each(function(){
                        if (!$(this).data('tm-original-html')){
                            $(this).data('tm-original-html',$(this).html());
                        }else{
                            $(this).html($(this).data('tm-original-html'));
                        }

                    });
                    if (v_product_price==0 && tm_epo_js.tm_epo_remove_free_price_label=="yes"){
                        tm_get_native_prices_block($cart).hide();    
                    }                    


                    $totals_holder.empty().hide();
                    $totals_holder.data('tm-html','');
                    if ($composite_cart){
                        if (per_product_pricing){
                            $cart.find('.cpf-bto-optionsprice').val(parseFloat(total*qty));    
                        }                        
                        main_cart.trigger({
                            "type":"tm-epo-update",
                            "norules":1
                        });
                    }
                    tm_set_subscription_period();
                }
                if (container_id){
                    main_product.find( '.bto_form_' + container_id + ',#composite_form_' + container_id + ',#composite_data_' + container_id ).trigger('cpf_bto_review');
                }
                main_cart.trigger("tm-epo-after-update");
                _window.trigger("tc-epo-after-update",{"epo":tc_totals_ob});
            });

            $cart_container.find('.variations_form')
            .off('found_variation.tmepo tm_fix_stock', '.single_variation_wrap')
            .on('found_variation.tmepo tm_fix_stock', '.single_variation_wrap', function(event, variation) {
                tm_fix_stock_tmepo($(this),$cart_container);
            });
            if(this_product_type=='variable' || this_product_type=='variable-subscription'){
                // update prices when a variation is found
                $cart_container.find('.variations_form')
                .off('found_variation.tmepo')
                .on('found_variation.tmepo', function(event, variation) {
                    found_variation_tmepo(event, variation);
                    tm_fix_stock_tmepo($(this),$cart_container);
                })
                .off('hide_variation.tmepo')
                .on('hide_variation.tmepo', function(event, variation) {
                    current_cart.trigger({
                        "type":"tm-epo-update",
                        "norules":2
                    }); 
                });

                _window.on('tm-do-epo-update',function(){
                    current_cart.trigger({
                        "type":"tm-epo-update",
                        //"norules":1
                    }); 
                });
            }
            function found_variation_tmepo(event, variation) {
                var variation_form = $(this), //$(event.target);                   
                    variations      = $totals_holder.data('variations'),
                    variations_subscription_sign_up_fee = $totals_holder.data('variations-subscription-sign-up-fee'),
                    variations_subscription_period = $totals_holder.data('variations-subscription-period'),
                    product_price;
                
                if (variations && variation && variation.variation_id && variations_subscription_sign_up_fee[variation.variation_id]){
                    $totals_holder.data('subscription-sign-up-fee', variations_subscription_sign_up_fee[variation.variation_id]);
                }
                if (variations && variation && variation.variation_id && variations_subscription_period[variation.variation_id]){
                    $totals_holder.data('subscription-period', variations_subscription_period[variation.variation_id]);
                }
                // Currency convertes that don't allow multi currency checkout will fail the following if statement
                /*if (variation.display_price!=undefined){
                    product_price=variation.display_price;
                    $totals_holder.data('price', product_price);
                }
                else */
                if (variations && variation && variation.variation_id && variations[variation.variation_id]!==undefined){
                    product_price=variations[variation.variation_id];
                    $totals_holder.data('price', product_price);
                }
                else if (variation && $(variation.price_html).find('.amount:last').length) {
                    product_price = $(variation.price_html).find('.amount:last').text();
                    product_price = product_price.replace(tm_epo_js.currency_format_thousand_sep, '');
                    product_price = product_price.replace(tm_epo_js.currency_format_decimal_sep, '.');
                    product_price = product_price.replace(/[^0-9\.]/g, '');
                    product_price = parseFloat(product_price);
                    $totals_holder.data('price', product_price);
                }
                $totals_holder_container.find('.cpf-product-price').val(product_price);
                
                variation_form.data('tm_variations_did_init',true);

                if(!variation_form.data('tm-styled-variations')){
                    current_cart.trigger({
                        "type":"tm-epo-update",
                        //"norules":1
                    });                    
                }
                
            };
            $cart_container.find('.variations select')
            .off('blur.cpf')
            .on('blur.cpf',function() {
                main_product.find('.variations_form').data('tm_variations_did_init',true);
            });

            tm_custom_variations($cart_container,item_id,main_product,$epo_holder);
            main_product.find('.tm-variation-ul-color .tmhexcolorimage-li-nowh,ul.use_images_containter .tmhexcolorimage-li-nowh').each(function(i,el){
                var el=$(el),w=el.width()*0.9,im=el.find(".tmhexcolorimage");
                im.css({"min-width":w+"px","min-height":w+"px"});
            });

            _window.trigger('tm-epo-init-events',{"epo":{
                'epo_id': epo_id,
                'form':get_main_form(),
                'current_cart':current_cart,
                'cart_container':$cart_container,
                'epo_holder':$epo_holder,
                'totals_holder_container':$totals_holder_container,
                'totals_holder':$totals_holder,
                'main_cart':main_cart,
                'main_epo_inside_form':main_epo_inside_form,
                'product_id_selector':product_id_selector,
                'epo_id_selector':epo_id_selector,
                'product_id':product_id,
                'this_epo_container':this_epo_container,
                'this_totals_container':this_totals_container,
                'this_epo_totals_container':this_epo_totals_container,
            }});

            // show extra options
            _window.trigger('epo_options_before_visible');
            setTimeout(function() {
                $epo_holder.css('opacity',0).addClass('tc-show').animate({
                    opacity: 1
                }, 500, "easeOutExpo", function() {
                    _window.trigger('epo_options_visible');
                    _window.trigger("tmlazy");
                });  
            }, 500);

        }

        function bto_support(){
            if(main_product.data('tm-composite-setup')){
                return;
            }
            var $totals = this_epo_totals_container;

            main_product.data('tm-composite-setup',1);
            // support to listen to after post success event for purchasable prodcuts (2.4)
            $(composite_selector).find( '.cart' ).append('<input type="hidden" class="tm-post-support addon">');
            main_product.find('.tm-post-support.addon').on('change', function(event) {
                $(this).closest(composite_selector).trigger('wc-composite-item-updated.cpf');
            });

            $(composite_selector)
            .off('found_variation.cpf')
            .on('found_variation.cpf', function(event, variation) {
                var item            = $(this),
                    container_id    = get_composite_container_id(item),
                    price_data      = get_composite_price_data(container_id),
                    product_price,
                    item_id         = get_composite_item_id(item);

                if(!price_data){
                    return;
                }
                main_product.find(".bto_form,#composite_form_"+ container_id + ',#composite_data_' + container_id).find( get_review_selector(item_id) ).removeData('cpf_review_price');
                main_product.find(".bto_form,#composite_form_"+ container_id + ',#composite_data_' + container_id).find( get_review_selector(item_id) ).find('.amount').empty();

                if ( is_per_product_pricing(price_data[ 'per_product_pricing' ]) == true ) {                   
                    product_price = parseFloat(variation.price);
                }
                item.find('.cpf-bto-price').data('per_product_pricing',is_per_product_pricing(price_data[ 'per_product_pricing' ]) );
                item.find('.cpf-bto-price').val(product_price);
                main_cart.data('per_product_pricing',is_per_product_pricing(price_data[ 'per_product_pricing' ]) );

                item.find('.cart').trigger({
                        "type":"tm-epo-update",
                        "norules":1
                    });
                $totals.data('btois','none');

            })
            .off('wc-composite-component-loaded.cpf')
            .on('wc-composite-component-loaded.cpf', function() {
                $(this).trigger('wc-composite-item-updated.cpf');
            })
            .off( 'wc-composite-item-updated.cpf')
            .on( 'wc-composite-item-updated.cpf', function() {
                tm_lazyload();
                main_product.find(".tm-collapse").tmtoggle();
                var item = $(this),item_tm_extra_product_options=item.find(".tm-extra-product-options");
                tm_set_datepicker(item);
                tm_set_range_pickers(item);
                tm_css_styles(item);
                tm_set_color_pickers();
                /**
                 * Start Condition Logic
                 */
                cpf_section_logic(item_tm_extra_product_options);
                cpf_element_logic(item_tm_extra_product_options);
                run_cpfdependson(item_tm_extra_product_options);

                var container_id    = get_composite_container_id(item),
                    price_data      = get_composite_price_data(container_id),
                    product_price,
                    item_id         = get_composite_item_id(item);

                main_product.find(".bto_form,#composite_form_"+ container_id + ',#composite_data_' + container_id).find( get_review_selector(item_id) ).removeData('cpf_review_price');
                main_product.find(".bto_form,#composite_form_"+ container_id + ',#composite_data_' + container_id).find( get_review_selector(item_id) ).find('.amount').empty();

                if (!price_data){
                    return;
                }
                if ( is_per_product_pricing(price_data[ 'per_product_pricing' ]) == true ) {                   
                    product_price = parseFloat(item.find( '.bto_item_data,.component_data' ).data( 'price' ));
                }
                item.find('.cpf-bto-price').data('per_product_pricing',is_per_product_pricing(price_data[ 'per_product_pricing' ]) );
                item.find('.cpf-bto-price').val(product_price);
                main_cart.data('per_product_pricing',is_per_product_pricing(price_data[ 'per_product_pricing' ]) );

                tm_epo_init(item,item.find('.cart'));

                item.find('.cart').trigger({
                    "type":"tm-epo-update",
                    //"norules":"init"
                });
                main_cart.trigger({
                    "type":"tm-epo-update",
                    "norules":1
                });
                tm_fix_stock_tmepo(item.find('.cart'),item);
            })
            .off( 'change.cpfbto', '.bto_item_options select,.component_options_select')
            .on( 'change.cpfbto', '.bto_item_options select,.component_options_select', function( event ) {
                var item            = $(this),
                    container_id    = get_composite_container_id(item),
                    item_id         = get_composite_item_id(item);

                main_product.find(".bto_form,#composite_form_"+ container_id + ',#composite_data_' + container_id).find( get_review_selector(item_id) ).removeData('cpf_review_price');
                main_product.find(".bto_form,#composite_form_"+ container_id + ',#composite_data_' + container_id).find( get_review_selector(item_id) ).find('.amount').empty();
                if (item.val() === ''){                                                         
                    $totals.data('passed',false);
                    $totals.data('btois','none');
                }else{
                    main_cart.trigger({
                        "type":"tm-epo-update",
                        "norules":1
                    });
                }
            } )
            .off( 'woocommerce_variation_select_change.cpf')
            .on( 'woocommerce_variation_select_change.cpf', function( event ) {
                var item            = $(this),
                    container_id    = get_composite_container_id(item),
                    item_id         = get_composite_item_id(item);

                main_product.find(".bto_form,#composite_form_"+ container_id + ',#composite_data_' + container_id).find( get_review_selector(item_id) ).removeData('cpf_review_price');
                main_product.find(".bto_form,#composite_form_"+ container_id + ',#composite_data_' + container_id).find( get_review_selector(item_id) ).find('.amount').empty();
                if (item.find( '.variations .attribute-options select' ).val()===''){                                                     
                    $totals.data('passed',false);
                    $totals.data('btois','none');
                }
            });

            main_product.find('.bundle_wrap')
            .off('show_bundle.cpf,wc-composite-show-add-to-cart.cpf')
            .on('show_bundle.cpf,wc-composite-show-add-to-cart.cpf',function(){
                var id=$(this).closest('.cart').attr('data-container-id');
                check_bto(id);                
            });

            main_product.find('.composite_data .composite_wrap')
            .off('wc-composite-show-add-to-cart.cpf')
            .on('wc-composite-show-add-to-cart.cpf',function(){
                var $composite_form=$(this).closest('.composite_form'),
                    id=$composite_form.find( '.composite_data' ).data( 'container_id' );
                check_bto(id);
                main_product.find( '#composite_data_' + id ).trigger('cpf_bto_review');
            });

            main_product.find( '.bto_form,.composite_form'  )
            .off( 'woocommerce-product-addons-update.cpf cpf_bto_review')
            .on( 'woocommerce-product-addons-update.cpf cpf_bto_review', function() {
                var bto_form=$(this);
                $(this).parent().find( composite_selector ).each( function(){
                    var item        = $(this),
                        item_id     = get_composite_item_id(item),
                        html        = bto_form.find( get_review_selector(item_id) ),
                        value,
                        options     = item.find(".cpf-bto-optionsprice").val();

                    if(!html.length){
                        return;
                    }
                    if (html.data('cpf_review_price')){
                        value = accounting.unformat(html.data('cpf_review_price'),local_decimal_separator);
                    }else if(html.find('.amount').length){
                        value = accounting.unformat(html.find('.amount').html(),tm_epo_js.currency_format_decimal_sep);
                        html.data('cpf_review_price',value);
                    }

                    if (options){
                        var total = parseFloat(value)+parseFloat(options);
                        html.find('.amount').html(tm_set_price(total,false));
                    }
                });                        

            } );

            $(composite_selector).trigger('wc-composite-component-loaded.cpf');

        }

        function check_bto(id){
            var show=true;
            var $totals = this_epo_totals_container;
            main_product.find( '.bto_form_' + id + ',#composite_form_' + id + ',#composite_data_' + id ).parent().find( composite_selector ).each( function(){
                var item        = $(this),
                    item_id         = get_composite_item_id(item),
                    form_data       = main_product.find( '.bto_form_' + id + ' .bundle_wrap .bundle_button .form_data_' + item_id + ',#composite_form_' + id + ' .bundle_wrap .bundle_button .form_data_' + item_id + ',#composite_data_' + id + ' .composite_wrap .composite_button .form_data_' + item_id),
                    product_input   = form_data.find( 'input.product_input' ).val(),
                    quantity_input  = form_data.find( 'input.quantity_input' ).val(),
                    variation_input = form_data.find( 'input.variation_input' ).val(),
                    product_type    = item.find( '.bto_item_data,.component_data' ).data( 'product_type' );
                
                if ( product_type == undefined || product_type == '' || product_input === '' ){
                    show = false;
                }
                else if ( product_type != 'none' && quantity_input == '' ){
                    show = false;
                }
                else if ( product_type == 'variable' && variation_input == undefined ) {
                    show = false;
                }
            });
            
            if (show){
                $totals.data('btois','show');
            }else{
                $totals.data('btois','none');
            }
            main_cart.trigger({
                "type":"tm-epo-update",
                "norules":1
            });
        }

        function tm_lazyload(){
            if (tm_epo_js.tm_epo_no_lazy_load=="yes"){
                return;
            }
            if (tm_lazyload_container){
                $(tm_lazyload_container).find("img.tmlazy").lazyLoadXT();
            }else{
                _window.lazyLoadXT();
            }
        }

        function tm_css_styles(obj){
            if (!obj){
                obj=this_epo_container;
            }
            obj.find('.tm-owl-slider-section').each(function(){
                var dv=$(this),
                    dvv=dv.css('display');
                
                dv.find('.tm-slide').first().before('<div class="tm-owl-slider"></div>');
                dv.find('.tm-slide').appendTo(dv.find('.tm-owl-slider'));
                
                dv=dv.find('.tm-owl-slider');
                dvv=dv.css('display');
                
                dv
                .show()
                .addClass('owl-carousel')
                .tmowlCarousel({
                        
                    dots:false,
                    nav:true,
                    items:1,
                    autoHeight:true,
                    mouseDrag:false,
                    touchDrag:true,
                    //navigation:true,
                    navText:[tm_epo_js.i18n_prev_text,tm_epo_js.i18n_next_text],
                    navClass: [ 'owl-prev button', 'owl-next button' ],
                    navElement:'a',
                    loop:false,
                    navRewind:false
                        
                });
                
                dv.css('display',dvv);              
              
            });            

        }

        function tm_set_color_pickers(obj){
            if (!obj){
                obj=this_epo_container.find('.tm-color-picker');
            }
            if ($(obj).length){
                $(obj).spectrum({});
                $(obj).spectrum("enable");
            }            
        }

        function has_active_changes_product_image(field){
            var uic=field.closest('.tmcp-field-wrap').find('label img');
            if(field.is('select.tm-product-image')){
                field=field.children('option:selected');
            }
            var src=$(uic).first().attr('data-original');
            if (!src){
                src=$(uic).first().attr('src');
            }
            if (!src){
                src=field.attr('data-image');
            }
            if(field.attr('data-imagep')){
                src=field.attr('data-imagep');
            }
            if(src){
                return true;
            }
            return false;
        }

        function tm_product_image(){
            var product_element=main_product.closest('#product-'+product_id);
            if (!product_element.length>0){
                product_element=main_product.closest('.post-'+product_id);
            }
            var img=product_element.find(".images img").not('.thumbnails img');
            
            if ($(img).length>1){
                img=$(img).first();
            }
            var is_yith_wcmg=false,
                yith_wcmg = $('.images'),
                yith_wcmg_zoom = $('.yith_magnifier_zoom'),
                yith_wcmg_image = $('.yith_magnifier_zoom img'),
                yith_wcmg_default_zoom = yith_wcmg.find('.yith_magnifier_zoom').first().attr('href'),
                _yith_wcmg_default_zoom = yith_wcmg_default_zoom,
                yith_wcmg_default_image = yith_wcmg.find('.yith_magnifier_zoom img').first().attr('src'),
                _yith_wcmg_default_image = yith_wcmg_default_image,
                img_width=img.width(),
                img_height=img.height();
            if (window.yith_magnifier_options && yith_wcmg.data('yith_magnifier')){
                is_yith_wcmg = true;
            }
            var is_iosSlider=false,is_iosSlider_element=$('.iosSlider.product-gallery-slider,.iosSlider.product-slider');
            if(is_iosSlider_element.length && is_iosSlider_element.iosSlider){
                is_iosSlider = true;
            }
            var is_flexslider=false,is_flexslider_element=product_element.find('.images .fusion-flexslider');
            if(is_flexslider_element.length && is_flexslider_element.flexslider){
                is_flexslider = true;
            }
            var is_easyzoom=false,is_easyzoom_element=product_element.find('.images .easyzoom');
            if(is_easyzoom_element.length && is_easyzoom_element.filter('.images .easyzoom.first').data('easyZoom')){
                is_easyzoom_element=is_easyzoom_element.filter('.images .easyzoom.first').data('easyZoom');
                is_easyzoom = true;
            }
            if ($(img).length>0){
                img.data('tm-current-image',false);
                var a=img.closest("a"),
                    a_href_original=a.attr('href'),
                    a_title_original=a.attr('title'),
                    img_title_original=img.attr('title'),
                    img_alt_original=img.attr('alt'),
                    img_src_original=img.attr('src');

                main_product
                .off('tm_change_product_image')
                .on('tm_change_product_image',function(evt,e){
                    var variation_element_section=e.element.closest('.cpf-section'),
                        is_variation_element=variation_element_section.is('.tm-epo-variation-section'),
                        $this_epo_container=(is_variation_element)?variation_element_section:e.epo_holder,
                        tm_last_visible_image_element=$this_epo_container.find('.tm-product-image:checked,select.tm-product-image');
                        
                    var last_activate_field=[];
                    tm_last_visible_image_element.each(function(){
                        var t=$(this);
                        if(field_is_active(t) 
                            && has_active_changes_product_image(t)
                            && (tm_check_field_match({
                        "element":t.closest(".cpf_hide_element"),
                                "operator":"isnotempty",
                                "value":""
                        }))){
                            last_activate_field.push(t);
                        }
                    });
                    var tm_last_visible_image_element_id='';
                    if (last_activate_field.length){
                        tm_last_visible_image_element=last_activate_field[last_activate_field.length-1];
                        tm_last_visible_image_element_id=tm_last_visible_image_element.attr('id');
                    }

                    var tm_current_image_element_id=img.data('tm-current-image');
                    var can_show_image=true;

                    var new_title=e.element.closest(".tmcp-field-wrap").find('.tc-label').html();

                    if (tm_last_visible_image_element.attr('id')!=e.element.attr('id')){
                        can_show_image=false;
                    }
                    var tm_current_image_element_id=e.element.attr('id'),
                        $main_product=e.main_product,
                        $current_product_element=$main_product.closest('#product-'+product_id);
                    if (!$current_product_element.length>0){
                        $current_product_element=$main_product.closest('.post-'+product_id);
                    }    
                    var current_cloned_image=$current_product_element.find('#'+tm_current_image_element_id+'_tmimage'),
                        preload_width=img_width,
                        preload_height=img_height;
                    
                    if(current_cloned_image.length==0){
                        current_cloned_image=img;
                    }

                    var preloader=$("<div class='tm-preloader-img'></div>");
                    preloader.css({'width':preload_width,'height':preload_height});

                    var preload_img_onerror = function() {
                        preloader.remove();
                        a.attr('href',a_href_original);
                        a.attr('title',a_title_original);
                        img.attr('title',img_title_original);
                        img.attr('alt',img_alt_original);
                        img.data('tm-current-image',false);
                        $current_product_element.find('.tm-clone-product-image').hide();
                        img.show();
                    };                    
                    if(e.src==current_cloned_image.attr("src") && current_cloned_image.is(":visible")){
                        return;
                    }

                    if (e.src == false){
                        preload_img_onerror();
                        return;
                    }
                    var clone_image=img.tm_clone(),
                        preload_img = new Image();


                    if(can_show_image){
                        img.before(preloader);
                    }

                    yith_wcmg_default_zoom = yith_wcmg.find('.yith_magnifier_zoom').first().attr('href');
                    yith_wcmg_default_image = yith_wcmg.find('.yith_magnifier_zoom img').first().attr('src');

                    preload_img.onerror = function() { preload_img_onerror(); };

                    preload_img.onload = function() {
                        if ('naturalHeight' in this) {
                            if (this.naturalHeight + this.naturalWidth === 0) {
                                this.onerror();
                                return;
                            }
                        } else if (this.width + this.height == 0) {
                            this.onerror();
                            return;
                        }
                        $current_product_element.find('#'+tm_current_image_element_id+'_tmimage').remove();
                        $current_product_element.find('.tm-clone-product-image').hide();
                        clone_image.prop('src',preload_img.src).hide();
                        img.hide().after(clone_image);

                        clone_image.css('opacity',0).show();
                        if(is_iosSlider){
                            setTimeout(function() {
                                is_iosSlider_element.iosSlider('update');
                            }, 150);
                        }
                        if(is_flexslider){
                           _window.trigger('resize');
                        }
                        if(is_easyzoom){
                            is_easyzoom_element.swap(null,preload_img.src);
                        }

                        preloader.animate({
                            opacity: 0
                        }, 750, "easeOutExpo", function() {
                            preloader.remove();
                        });
                        clone_image.animate({
                            opacity: 1
                        }, 1500, "easeOutExpo", function() {
                            
                        });

                        if (is_yith_wcmg){
                            clone_image
                            .attr('srcset',preload_img.src)
                            .attr('src-orig',preload_img.src);

                            if (yith_wcmg.data('yith_magnifier')) {
                                yith_wcmg.yith_magnifier('destroy');
                            }
                            var _elements= {
                                            elements: {
                                                zoom: $('.yith_magnifier_zoom'),
                                                zoomImage: clone_image,
                                                gallery: $('.yith_magnifier_gallery li a')
                                            }
                            };

                            yith_wcmg.yith_magnifier($.extend(true, {}, yith_magnifier_options, _elements));
                        }

                    };

                    clone_image
                    .attr('id',tm_current_image_element_id+'_tmimage')
                    .addClass('tm-clone-product-image').hide();

                    if (clone_image.attr('src-orig')){
                        clone_image.attr('src-orig',e.src)
                    }
                    if (clone_image.attr('srcset')){
                        clone_image.attr('srcset',e.src)
                    }
                    if (clone_image.attr('data-o_src')){
                        clone_image.attr('data-o_src',e.src)
                    }

                    if(can_show_image){
                        preload_img.src = e.src;
                        a.attr('href',e.src);
                        a.attr('title',new_title);
                        img.attr('title',new_title);
                        img.attr('alt',new_title);                        

                        img.data('tm-current-image',tm_current_image_element_id);
                    }else{
                        clone_image.prop('src',e.src).hide();
                        img.after(clone_image);
                    }
                });

                main_product
                .off('tm_restore_product_image')
                .on('tm_restore_product_image',function(evt,e){
                    var tm_current_image_element_id=e.element.attr('id'),
                        $main_product=e.main_product,
                        $current_product_element=$main_product.closest('#product-'+product_id);
                    if(!$current_product_element.length>0){
                        $current_product_element=$main_product.closest('.post-'+product_id);
                    }
                    var variation_element_section=e.element.closest('.cpf-section'),
                        is_variation_element=variation_element_section.is('.tm-epo-variation-section'),
                        $this_epo_container=(is_variation_element)?variation_element_section:e.epo_holder;

                    var current_element,
                        current_image_replacement,
                        found=false,
                        is_it_visible = $current_product_element.find('#'+tm_current_image_element_id+'_tmimage').is(':visible');

                    $current_product_element.find('#'+tm_current_image_element_id+'_tmimage').remove();
                    if($current_product_element.find('.tm-clone-product-image').length==0){
                        img.show();
                        a.attr('href',a_href_original);
                        a.attr('title',a_title_original);
                        img.attr('title',img_title_original);
                        img.attr('alt',img_alt_original);
                        img.data('tm-current-image',false);                        
                    }else{
                        if(!is_it_visible){
                            return;
                        }
                        var len=$current_product_element.find('.tm-clone-product-image').length,                            
                            el_to_check,                            
                            tm_current_image_element_id=img.data('tm-current-image');
                        for (var i = len - 1; i >= 0; i--) {
                            current_image_replacement = $current_product_element.find('.tm-clone-product-image').eq(i);
                            current_element = current_image_replacement.attr('id').replace('_tmimage','');
                            el_to_check = $this_epo_container.find('[id="'+current_element+'"]');
                            
                            if (el_to_check.is(":checked") && el_to_check.closest(".cpf_hide_element").is(":visible")){
                                $current_product_element.find('.tm-clone-product-image').eq(i).show();
                                a.attr('href',$current_product_element.find('.tm-clone-product-image').eq(i).prop('src'));
                                img.data('tm-current-image',current_element);
                                found=true;
                                break;
                            }else{
                                $current_product_element.find('.tm-clone-product-image').eq(i).hide();
                            }
                        };
                        if(!found){
                            img.show();
                            a.attr('href',a_href_original);
                            a.attr('title',a_title_original);
                            img.attr('title',img_title_original);
                            img.attr('alt',img_alt_original);
                            img.data('tm-current-image',false);
                        }else{
                            $current_product_element.find('#'+tm_current_image_element_id+'_tmimage').remove();
                        }                        
                    }
                    if (is_yith_wcmg){
                        if (!$current_product_element.find('.tm-clone-product-image').filter(':visible').length){
                            yith_wcmg_zoom.attr('href', _yith_wcmg_default_zoom);
                        }else{
                            yith_wcmg_zoom.attr('href', yith_wcmg_default_zoom);
                        }
                        
                        if (yith_wcmg.data('yith_magnifier')) {
                            yith_wcmg.yith_magnifier('destroy');
                        }

                        yith_wcmg.yith_magnifier(yith_magnifier_options);
                    }
                    if(is_iosSlider){
                        setTimeout(function() {
                            is_iosSlider_element.iosSlider('update');
                        }, 150);
                    }
                    if(is_flexslider){
                        _window.trigger('resize');
                    }
                    if(is_easyzoom){
                        if (found) {
                            is_easyzoom_element.swap(null,current_image_replacement.attr('src'));
                        }else{
                            is_easyzoom_element.swap(null,img_src_original);
                        }                        
                    }

                });

                main_product
                .off('tm_attempt_product_image')
                .on('tm_attempt_product_image',function(evt,e){
                    var $main_product=e.main_product,
                        $current_product_element=$main_product.closest('#product-'+product_id);
                    if(!$current_product_element.length>0){
                        $current_product_element=$main_product.closest('.post-'+product_id);
                    }

                    var variation_element_section=(e.element)?e.element.closest('.cpf-section'):$($main_product.find('.tm-epo-variation-section'),e.epo_holder),
                        is_variation_element=variation_element_section.is('.tm-epo-variation-section'),
                        $this_epo_container=(is_variation_element)?variation_element_section:e.epo_holder,
                        tm_last_visible_image_element=$this_epo_container.find('.tm-product-image:checked,select.tm-product-image');

                    $this_epo_container = $main_product.find('.tm-epo-variation-section').add(e.epo_holder);
                    tm_last_visible_image_element=$this_epo_container.find('.tm-product-image:checked,select.tm-product-image');

                    var last_activate_field=[];
                    tm_last_visible_image_element.each(function(){
                        var t=$(this);
                        if(field_is_active(t) 
                            && has_active_changes_product_image(t)
                            && (tm_check_field_match({
                        "element":t.closest(".cpf_hide_element"),
                                "operator":"isnotempty",
                                "value":""
                        }))){
                            last_activate_field.push(t);
                        }
                    });
                    var tm_last_visible_image_element_id='';
                    if (last_activate_field.length){
                        tm_last_visible_image_element=last_activate_field[last_activate_field.length-1];
                        tm_last_visible_image_element_id=tm_last_visible_image_element.attr('id');
                    }

                    var current_image_replacement,
                        current_element,
                        found=false,
                        tm_current_image_element_id=img.data('tm-current-image');
                    
                    if (last_activate_field.length && tm_last_visible_image_element.length && (!tm_current_image_element_id || tm_last_visible_image_element_id!=tm_current_image_element_id)){
                        tm_last_visible_image_element.last().trigger('tm_trigger_product_image');
                        return;
                    }else{
                        
                        if (!tm_current_image_element_id || $this_epo_container.find('[id="'+tm_current_image_element_id+'"]').closest(".cpf_hide_element").is(":visible")){
                            return;
                        }else{
                            $current_product_element.find('#'+tm_current_image_element_id+'_tmimage').remove();
                            var len=$current_product_element.find('.tm-clone-product-image').length;
                            if(len==0){
                                img.show();
                                a.attr('href',a_href_original);
                                a.attr('title',a_title_original);
                                img.attr('title',img_title_original);
                                img.attr('alt',img_alt_original);
                                img.data('tm-current-image',false);
                            }else{                               
                                
                                for (var i = len - 1; i >= 0; i--) {
                                    current_image_replacement = $current_product_element.find('.tm-clone-product-image').eq(i);
                                    current_element=current_image_replacement.attr('id').replace('_tmimage','');
                                    var el_to_check = $this_epo_container.find('[id="'+current_element+'"]');
                            
                                    if (el_to_check.is(":checked") &&  el_to_check.closest(".cpf_hide_element").is(":visible")){
                                        $current_product_element.find('.tm-clone-product-image').eq(i).show();
                                        a.attr('href',$current_product_element.find('.tm-clone-product-image').eq(i).prop('src'));
                                        img.data('tm-current-image',current_element);
                                        found=true;
                                        break;
                                    }else{
                                        $current_product_element.find('.tm-clone-product-image').eq(i).hide();
                                    }
                                };
                                if(!found){
                                    img.show();
                                    a.attr('href',a_href_original);
                                    a.attr('title',a_title_original);
                                    img.attr('title',img_title_original);
                                    img.attr('alt',img_alt_original);
                                    img.data('tm-current-image',false);
                                }
                            }
                        }

                    }
                    if (is_yith_wcmg){
                        if (!$current_product_element.find('.tm-clone-product-image').filter(':visible').length){
                            yith_wcmg_zoom.attr('href', _yith_wcmg_default_zoom);
                        }else{
                            yith_wcmg_zoom.attr('href', yith_wcmg_default_zoom);
                        }
                        if (yith_wcmg.data('yith_magnifier')) {
                            yith_wcmg.yith_magnifier('destroy');
                        }

                        yith_wcmg.yith_magnifier(yith_magnifier_options);
                    }
                    if(is_iosSlider){
                        setTimeout(function() {
                            is_iosSlider_element.iosSlider('update');
                        }, 150);
                    }
                    if(is_flexslider){
                        _window.trigger('resize');
                    }
                    if(is_easyzoom){
                        if (found) {
                            is_easyzoom_element.swap(null,current_image_replacement.attr('src'));
                        }else{
                            is_easyzoom_element.swap(null,img_src_original);
                        }
                    }
                });

                var last_activate_field=[];
                $(this_epo_container,main_product.find('.tm-epo-variation-section')).find('.tm-product-image:checked,select.tm-product-image').each(function(){
                    var t=$(this);
                    if(field_is_active(t)){
                        last_activate_field.push(t);
                    }
                });
                if (last_activate_field.length){
                    last_activate_field[last_activate_field.length-1].trigger('tm_trigger_product_image');
                }
                              
            }

            _window.trigger('tm_product_image_loaded')
        }

        function tm_custom_variations(form,item_id,$main_product,$epo_holder){

            var variation_id_selector='input[name^="variation_id"]';
            if ( form.find( 'input.variation_id' ).length > 0 ){
                variation_id_selector='input.variation_id';
            }
            if ($epo_holder.find('.tm-epo-variation-element').length || $epo_holder.data('tm-epo-variation-element') ){
                $epo_holder.data('tm-epo-variation-element',$epo_holder.find('.tm-epo-variation-element'));
                var tm_epo_variation_section = $epo_holder.find(".tm-epo-variation-section");

                if (item_id && item_id!="main"){// on composite
                    var li_variations=tm_epo_variation_section.closest("li.tm-extra-product-options-field");
                    form.find('.variations').hide().after(tm_epo_variation_section.addClass("tm-extra-product-options nopadding"));
                    if (li_variations.is(":empty")){
                        li_variations.hide();
                    }
                    form
                    .off('wc_variation_form.tmlogic')
                    .on('wc_variation_form.tmlogic', function() {
                        form.find(".variations_form").on("click.tmlogic",".reset_variations",function(e){
                            form.find('.tm-epo-variation-element').closest("li").show();
                            form.find('select.tm-epo-variation-element').val("").children('option').removeAttr("disabled").show();
                            form.find('.tm-epo-variation-element')
                                .removeAttr("disabled").removeClass("tm-disabled")
                                .removeAttr("checked").prop("checked",false);
                            _window.trigger("tmlazy");
                            $main_product.find('.tm-epo-variation-element').trigger('tm_trigger_product_image');
                        });
                        // Disable option fields that are unavaiable for current set of attributes 
                        form
                        .off("woocommerce_update_variation_values_tmlogic")
                        .on("woocommerce_update_variation_values_tmlogic",function(e,variations){
                            tm_custom_variations_update(form);
                        });
                        for (var i = 0; i < late_variation_event.length; i++) {
                            var form_event = late_variation_event[i],
                                type = typeof(form_event);
                            if(type=="object"){
                                var name = typeof(form_event.name)=="string" || false,
                                    selector = typeof(form_event.selector)=="string" || false,
                                    func = typeof(form_event.func)=="function" || false;
                                if(name && func){
                                    if(selector=='input[name="variation_id"]'){
                                        selector=variation_id_selector;
                                    }
                                    if (form_event.selector){
                                        form
                                        .data('tm-styled-variations',1)
                                        .off(form_event.name,form_event.selector)
                                        .on(form_event.name,form_event.selector,form_event.func);
                                    }else{
                                        form
                                        .data('tm-styled-variations',1)
                                        .off(form_event.name)
                                        .on(form_event.name,form_event.func);
                                    }
                                    
                                }
                            }
                        };
                        late_variation_event = [];
                        form.find('.tm-epo-variation-element').last().trigger('tm_epo_variation_element_change');
                    });
                }else{
                    form = form.find(".variations_form");
                    form.find('.variations').hide();
                    var li_variations=tm_epo_variation_section.closest("li.tm-extra-product-options-field");
                    
                    form.find('.variations').hide().after(tm_epo_variation_section.addClass("tm-extra-product-options nopadding"));
                    if (li_variations.is(":empty")){
                        li_variations.hide();
                    }
                    form
                    .off("click.tmlogic",".reset_variations")
                    .on("click.tmlogic",".reset_variations",function(e){
                        form.find('.tm-epo-variation-element').closest("li").show();
                        form.find('select.tm-epo-variation-element').val("").children('option').removeAttr("disabled").show();
                        form.find('.tm-epo-variation-element')
                            .removeAttr("disabled").removeClass("tm-disabled")
                            .removeAttr("checked").prop("checked",false);
                        _window.trigger("tmlazy");
                        $main_product.find('.tm-epo-variation-element').trigger('tm_trigger_product_image');
                    });
                    // Disable option fields that are unavaiable for current set of attributes 
                    form
                    .off("woocommerce_update_variation_values_tmlogic")
                    .on("woocommerce_update_variation_values_tmlogic",function(e,variations){
                        tm_custom_variations_update(form);
                    });
                    for (var i = 0; i < late_variation_event.length; i++) {
                        var form_event = late_variation_event[i],
                            type = typeof(form_event);
                        if(type=="object"){
                            var name = typeof(form_event.name)=="string" || false,
                                selector = typeof(form_event.selector)=="string" || false,
                                func = typeof(form_event.func)=="function" || false;
                            if(name && func){
                                if(selector=='input[name="variation_id"]'){
                                    selector=variation_id_selector;
                                }
                                if (form_event.selector){
                                    form
                                    .data('tm-styled-variations',1)
                                    .off(form_event.name,form_event.selector)
                                    .on(form_event.name,form_event.selector,form_event.func);
                                }else{                                    
                                    form
                                    .data('tm-styled-variations',1)
                                    .off(form_event.name)
                                    .on(form_event.name,form_event.func);
                                }
                                
                            }
                        }
                    };
                    late_variation_event = [];                
                    form.find('.tm-epo-variation-element').last().trigger('tm_epo_variation_element_change');
                }

                // global event for custom variations
                form_submit_events[form_submit_events.length] = {
                    "trigger":function(){return true;},
                    "on_true":function(){
                        $main_product.find('.tm-epo-variation-element').attr("disabled","disabled");
                        return true;
                    },
                    "on_false":function(){
                        $main_product.find('.tm-epo-variation-element').removeAttr("disabled");
                    }
                };
                var uls=$main_product.find('.tm-variation-ul-color').find("li");
                uls.each(function(i,el){
                    var el=$(el),w=el.width()*0.9,im=el.find(".tmhexcolorimage");
                    im.css({"min-width":w+"px","min-height":w+"px"});
                });                
            }
        }

        function tm_theme_specific_actions(){
            var totals=this_epo_totals_container,
                theme_name=totals.attr('data-theme-name');

            if (theme_name){
                theme_name = theme_name.toLowerCase(); 
                var all_epo_selects = this_epo_container.find('select');
                switch (theme_name){
                    case 'flatsome':
                    case 'flatsome-child':
                    case 'flatsome child':
                        all_epo_selects.wrap('<div class="custom select-wrapper"/>');
                        break;

                    case 'avada':
                    case 'avada-child':
                    case 'avada child':
                        all_epo_selects.wrap('<div class="avada-select-parent tm-select-parent"></div>');
                        $('<div class="select-arrow">&#xe61f;</div>').appendTo(this_epo_container.find('.tm-select-parent'));
                        if (window.calc_select_arrow_dimensions){
                            calc_select_arrow_dimensions();
                        }
                        break;

                    case 'bazar':
                    case 'bazar-child':
                    case 'bazar child':
                        all_epo_selects.wrap('<div class="tm-select-wrapper select-wrapper"/>');
                        break;

                    case 'blaszok':
                    case 'blaszok-child':
                    case 'blaszok child':
                        var blaszok_selects = function(){setTimeout(function() {
                            var $epo_select = $('.tm-extra-product-options select').not('.hasCustomSelect').filter(":visible");
                            $epo_select.each(function() {
                                if (!$(this).is('.mpcthSelect')){
                                    $(this).width($(this).outerWidth());
                                    $(this).customSelect({customClass: 'mpcthSelect'});                                    
                                }
                            });
                            
                        }, 100 );
                        };
                        _window.on("cpflogicrun",function(){blaszok_selects()});
                        _window.on('epo_options_visible',function(){blaszok_selects()});
                        
                        break;
                    case 'enfold':
                    case 'enfold-child':
                        _window.load(function(){
                            $(".quantity .minus,.quantity .plus").on("click",function(){
                                $(this).closest(".quantity").find(".input-text").trigger("change");
                            });
                        });
                        break;
                }
                _window.trigger('tm-theme-specific-actions',{"epo":{
                    'theme_name':theme_name,
                    'all_epo_selects':all_epo_selects,
                }});
            }        
            
        }

        function add_late_fields_prices(product_price,bid,_cart){
            var total=0;
            $.each(late_fields_prices,function(i,field){
                var price=field["price"],
                    original_price=field["original_price"],
                    setter=field["setter"],
                    id,
                    hidden,
                    bundleid=field["bundleid"],
                    real_setter=setter;

                if (setter.is("option")){
                    real_setter=setter.closest("select");                    
                }
                id=real_setter.attr("name");
                hidden=this_epo_container.find('#'+id+'_hidden');
                
                if (bundleid==bid){
                    
                    price=(price/100)*product_price;
                    original_price=(original_price/100)*product_price;
                    if(real_setter.data('tm-quantity')){
                        price=price*parseFloat(real_setter.data('tm-quantity'));
                        original_price=original_price*parseFloat(real_setter.data('tm-quantity'));
                    }
                    if (setter.data('isset')==1 && field_is_active(setter)){
                        total=total+price;
                    }
                    var formatted_price = tm_set_price(price,_cart,true),
                        original_formatted_price = tm_set_price(original_price,_cart,true);
                    setter.data('price', tm_set_tax_price(price,_cart));
                    setter.data('pricew', tm_set_tax_price(price,_cart));
                    setter.data('original_price', tm_set_tax_price(original_price,_cart));
                    setter.data('original_pricew', tm_set_tax_price(original_price,_cart));
                    
                    tm_update_price(setter.closest('.tmcp-field-wrap').find('.tc-price'),price,formatted_price,original_price,original_formatted_price);
                    
                    if (hidden.length==0){
                        real_setter.before('<input type="hidden" id="'+id+'_hidden" name="'+id+'_hidden" value="'+tm_set_price_without_tax(price,_cart)+'" />');                        
                    }
                    if (setter.is(".tm-epo-field.tmcp-radio")){
                        if(setter.is(":checked")){
                            hidden.val(tm_set_price_without_tax(price,_cart));
                        }
                    }else{
                        hidden.val(tm_set_price_without_tax(price,_cart));
                    }
                }else{
                    if (setter.data('pricew')!==undefined){
                        var formatted_price = tm_set_price(setter.data('pricew'),_cart,true),
                            original_formatted_price="";

                        if (setter.data('original_pricew')!==undefined){
                            original_formatted_price = tm_set_price(setter.data('original_pricew'),_cart,true);
                        }

                        tm_update_price(setter.closest('.tmcp-field-wrap').find('.tc-price'),setter.data('pricew'),formatted_price,setter.data('original_pricew'),original_formatted_price);                        
                    }
                    
                }
            });
            late_fields_prices=[];

            return total;
        }

        function tm_set_checkboxes_rules(){
            // Limit checkbox selection
            this_epo_container.on('change.cpflimit', 'input.tm-epo-field.tmcp-checkbox', function () {            
                var $this=$(this);
                tm_limit_c_selection($this,true);
                tm_exact_c_selection($this,true);            
            });

            // Exact value checkbox check (Todo:check for isvisible)
            var exactlimit_cont=this_epo_container.find('.tm-exactlimit');
            if (exactlimit_cont.length){
                tm_check_exactlimit_cont(exactlimit_cont);
            }

            // Minimum number checkbox check (Todo:check for isvisible)
            var minimumlimit_cont=this_epo_container.find('.tm-minimumlimit');
            if (minimumlimit_cont.length){
                tm_check_minimumlimit_cont(minimumlimit_cont);
            }
            
        }

        function manual_init(container){
            container = $(container);
            // Start Condition Logic            
            cpf_section_logic(container);
            cpf_element_logic(container);
            run_cpfdependson(container);
                
            // Init field price rules
            tm_epo_rules();
            late_fields_prices=[];

            tm_epo_init();
            tm_product_image();
            bto_support();
            main_cart.trigger('tm-epo-check-dpd');

            main_cart.trigger({
                "type":"tm-epo-update"
            });
            
        }

       _window.on('tc_manual_init', function(evt,container){
            manual_init(container);
        });

        _window.trigger('tm-epo-init-start');

        /**
         * Holds the main cart when using Composite Products
         */
        var main_cart               = false,        
            main_epo_inside_form    = false,
            main_totals_inside_form = false,
            form_submit_events      = [],
            global_error_obj        = false,
            has_epo                 = typeof(product_id)!=='undefined',
            composite_selector      = '.bto_item,.component',
            one_option_is_selected  = false,
            not_has_epo             = false;

        if(!has_epo){
            if (main_product.is(".product")){
                not_has_epo=true;
                has_epo = body.find(epo_selector).length;
            }            
        }
        
        // return if product has no extra options and the totals box is not enabled for all product
        if(!has_epo && tm_epo_js.tm_epo_enable_final_total_box_all=="no" && !main_product.is(".tm-no-options-composite")){
            _window.trigger('tm-epo-init-end-no-options');
            return;
        }

        // set the main_product variable again for products that have no extra options
        if(not_has_epo){
            _window.trigger('tm-epo-init-no-options');
            if ( main_product.is(".product") && !(main_product.is(".tm-no-options-pxq") || main_product.is(".tm-no-options-composite")) ){
                main_product=body;
            }
        }

        if (!product_id){
            var add_to_cart_field=main_product.find(add_to_cart_selector).last();
            if (add_to_cart_field.length>0){
                product_id=add_to_cart_field.val();
            }else{
                add_to_cart_field=$(".tc-totals-form.tm-totals-form-main");
                product_id=add_to_cart_field.attr("data-product-id");
            }
            if (!product_id){
                product_id="";
            }            
        }

        if (!epo_id){
            
            epo_id = parseInt(main_product.find('input.tm-epo-counter').last().val());
            
            if(isNaN(epo_id)){
                epo_id="";
            }

        }

        var product_id_selector         = '.tm-product-id-'+product_id,
            epo_id_selector             = '[data-epo-id="'+epo_id+'"]',
            this_epo_container          = $('.tc-extra-product-options'+product_id_selector+epo_id_selector),
            this_totals_container       = $('.tc-totals-form'+product_id_selector+epo_id_selector),
            this_epo_totals_container   = $('.tc-epo-totals'+product_id_selector+epo_id_selector);

        tm_check_main_cart();

        tm_set_checkboxes_rules();
        tm_set_datepicker();
        tm_set_range_pickers();
        tm_set_url_fields();
        tm_set_subscription_period();
        $.tm_tooltip(this_epo_container.find('.tm-tooltip'));

        this_epo_container.find(".tm-collapse").tmtoggle();
        this_epo_container.find(".tm-section-link").tmsectionpoplink();

        /**
         * Holds the active precentage of total current price type fields
         */
        var late_fields_prices=[],
            variations_form=main_product.find('.variations_form');

        if (variations_form.length > 0) {
            var run_wc_variation_form_cpf=function(){
                variations_form.on('wc_variation_form.cpf', function() {
                    if (variations_form.data('tc_loaded')){
                        return;
                    }

                    // Start Condition Logic
                    cpf_section_logic(this_epo_container);
                    cpf_element_logic(this_epo_container);
                    run_cpfdependson(this_epo_container);
                    
                    late_fields_prices=[];
        
                    tm_epo_init();
                    tm_product_image();

                    setTimeout(function() {
                        main_cart.trigger({
                            "type":"tm-epo-update",
                            //"norules":1
                        });
                    }, 10 );

                    variations_form.data('tc_loaded',true);
                    
                });
                
                if (variations_form_is_loaded){
                    variations_form.trigger('wc_variation_form.cpf');
                }
            };
            var detect_variation_swatches=false;
            if ($('.variation_form_section .variations-table').length){
                detect_variation_swatches=true;
            }
            if (detect_variation_swatches){
                var detect_variation_swatches_interval = function(){
                    var $id=requestAnimationFrame(detect_variation_swatches_interval);
                    var bound=variations_form.data('bound');
                    if (bound){
                        cancelAnimationFrame($id);
                        run_wc_variation_form_cpf();
                        variations_form.trigger('wc_variation_form.cpf');
                    }
                };
                detect_variation_swatches_interval();
            }else{
                run_wc_variation_form_cpf();
            }
        } else {
            setTimeout(function() {
            
                // Start Condition Logic            
                cpf_section_logic(this_epo_container);
                cpf_element_logic(this_epo_container);
                run_cpfdependson(this_epo_container);
                
                // Init field price rules
                tm_epo_rules();
                late_fields_prices=[];

                tm_epo_init();
                tm_product_image();
                bto_support();
                main_cart.trigger('tm-epo-check-dpd');

                main_cart.trigger({
                    "type":"tm-epo-update",
                    //"norules":"init"
                });
            
            }, 20 );
        }

        tm_lazyload();
        
        tm_css_styles();
        tm_set_color_pickers();

        tm_floating_totals();
        tm_form_submit_event();

        tm_show_hide_add_to_cart_button();

        tm_theme_specific_actions();

        tc_compatibility();

        _window.trigger('tm-epo-init-end',{"epo":{
            'main_cart':main_cart,
            'main_epo_inside_form':main_epo_inside_form,
            'product_id_selector':product_id_selector,
            'epo_id_selector':epo_id_selector,
            'product_id':product_id,
            'this_epo_container':this_epo_container,
            'this_totals_container':this_totals_container,
            'this_epo_totals_container':this_epo_totals_container,
        }});
    }

    function init_epo_plugin(evt){
        if (tm_epo_js.tm_epo_no_lazy_load=="no"){
             
            $.extend($.lazyLoadXT, {
              autoInit:  false,
              selector: 'img.tmlazy',
              srcAttr: 'data-original',
              visibleOnly:false,
              updateEvent:$.lazyLoadXT.updateEvent + ' tmlazy'
            });
            
        }
        /* 
         * tm-no-options-pxq = product has not options but the "Enable Final total box for all products" is on
         * tm-no-options-composite = product is a composite product with no options but at least one of its bundles have options
         */
        var epo_container=$('.tm-no-options-pxq, .tm-no-options-composite');
        if (epo_container.length>0){
            
            // Special cases
            // -------------
            // Price x Quantity display (.tm-no-options-pxq) & composite without option but a component has extra options (.tm-no-options-composite)

            epo_container.each(function(loop_index,product_wrap){

                var j_product_wrap=$(product_wrap);

                tm_init_epo(j_product_wrap,false);

            });

        }
        try{
            
            // new main way of calling tm_init_epo
            // -----------------------------------
            // Normal product pages

            var epo_options_container=$(epo_selector).not('.tm-no-options-pxq, .tm-no-options-composite');

            if (epo_options_container.length>0){

                epo_options_container.each(function(loop_index,product_wrap){

                    var $this           = $(this),
                        product_id      = $this.attr("data-product-id"),
                        epo_id          = $this.attr("data-epo-id"),
                        quickview_floating = false,
                        j_product_wrap  = $(add_to_cart_selector+'[value="'+product_id+'"]').closest('form,.cart').first().parent();

                    if (!j_product_wrap.length>0){
                        j_product_wrap  = $(tc_add_to_cart_selector+'[value="'+product_id+'"]').closest('form,.cart').first().parent();
                        
                        if (!j_product_wrap.length>0){
                            j_product_wrap  = $this.closest('form,.cart').first().parent(".tm-has-options");

                            if (!j_product_wrap.length>0){
                                if ($this.is('.tc-shortcode')){
                                    j_product_wrap=$this.parent();
                                }
                            }
                            if (j_product_wrap.length>0){
                                // in shop (variation logic will not work here)
                                quickview_floating = true;
                                $this.closest('form,.cart').first().append($('<input name="add-to-cart" value="'+product_id+'" type="hidden" />'));
                                $this.closest('form,.cart').first().append($('<input type="hidden" value="" class="variation_id" name="variation_id">'));
                            }
                        }
                    }
                    
                    if (j_product_wrap.length>0){

                        tm_init_epo(j_product_wrap,quickview_floating,product_id,epo_id);

                    }

                });

            }

        }catch(error){

        }

    }

    //document.addEventListener('DOMContentLoaded', function() {

    _document.ready(function() {
        
        _window.on('tc_init_epo_plugin', function(evt){
            init_epo_plugin(evt);
        });

        $.ajaxPrefilter(function( options, originalOptions, jqXHR ) {
            
            if (tm_epo_js.tm_epo_enable_in_shop=="yes"){
                var found=false,
                    hashes = options.url.split("?");
                if (hashes && hashes.length>=1){
                    hashes=hashes[1];
                    if (hashes){
                        var hash = hashes.split('&');
                        for (var i = 0; i < hash.length; i++) {
                            var params=hash[i].split("=");
                            if (params.length>=1){
                                if (params[0] && params[1] && params[0]=="wc-ajax" && params[1]=="add_to_cart"){
                                    found=true;
                                }
                            }
                        }
                        if (found){
                            options.originalsuccess=options.success;
                            options.success=function(response){
                                if ( response && response.error && response.product_url ) {
                                    var $thisbutton=$( '.ajax_add_to_cart[data-product_id="'+originalOptions.data["product_id"]+'"]' ) ;
                                    $thisbutton.removeClass( 'added' );
                                    $thisbutton.removeClass( 'loading' );
                                }else{
                                    options.originalsuccess.call(null, response);
                                }
                            };
                        }
                    }
                }
            }
            
            if (options.type.toLowerCase()!=="post") {
                return;
            }
            if(originalOptions.data && originalOptions.data["product_id"] && originalOptions.data["quantity"]){
                var epos=$('.tc-extra-product-options.tm-product-id-'+originalOptions.data["product_id"]);
                if(epos.length==1){
                    options.data = $.param( 
                        $.extend(
                            originalOptions.data, 
                            epos.tm_aserializeObject(),
                            {
                                tcaddtocart:originalOptions.data["product_id"],
                                cpf_product_price:$(".tc-totals-form.tm-product-id-"+originalOptions.data["product_id"]).find(".cpf-product-price").val()
                            }
                        ), false );
                }                
            };
            
        });
        _document.ajaxSuccess(function(event, xhr, settings) {

            //fix for menu cart pop up
             $(".tm-cart-link").tmpoplink();

            // quickview plugins
            var qv_container = {
                    'quick_view_plugin'             : $('.woocommerce.quick-view').not('body'),
                    'theme_flatsome'                : $('.product-lightbox'),
                    'theme_kleo'                    : $('#productModal'),
                    'yith_quick_view_plugin'        : $('#yith-quick-view-modal,.yith-quick-view.yith-modal'),
                    'venedor_quick_view_plugin'     : $('.quickview-wrap'),
                    'rubbez_quick_view'             : $('#quickview-content'),
                    'jckqv_quick_view'              : $('#jckqv'),
                    'themify_quick_view'            : $('#product_single_wrapper'),
                    'porto_quick_view'              : $('.quickview-wrap'),
                    'woocommerce_product_layouts'   : $('.dhvc-woo-product-quickview'),
                    'nm_getproduct'                 : $('#popup'),
                };

            for (var key in qv_container) {
                if (qv_container.hasOwnProperty(key)) {
                    if (qv_container[key].length){
                        
                        if(key=="yith_quick_view_plugin" && !qv_container[key].find(".product").length>0){
                            continue;
                        }
                        if(key=='jckqv_quick_view'){
                            variations_form_is_loaded = true;
                        }
                        tm_lazyload_container=qv_container[key];

                        var product_id  = tm_lazyload_container.find(epo_selector).attr("data-product-id"),
                            epo_id      = tm_lazyload_container.find(epo_selector).attr("data-epo-id");
                        
                        tm_init_epo(tm_lazyload_container,true,product_id,epo_id);
                        _window.trigger("tmlazy");
                        _window.trigger('tm_epo_loaded_quickview');
                        if ($.jMaskGlobals){
                            tm_lazyload_container.find($.jMaskGlobals.maskElements).each(function(){
                                var t=$(this);
                                if (t.attr('data-mask')){
                                    t.mask(t.attr('data-mask'));
                                }
                            });
                        }
                    }
                }
            }

            return;
        });

        // bulk variations forms plugin
        $('#wholesale_form').on('submit',function(){
            var _product_id = $('form.cart').find(add_to_cart_selector).val(),
                // visible fields
                epos=$(epo_selector+'.tm-cart-main[data-product-id="'+_product_id+'"]').tm_clone(),
                // hidden fields see totals.php
                epos_hidden=$('.tm-totals-form-main[data-product-id="'+_product_id+'"]').tm_clone(),
                formepo=$('<div class="tm-hidden tm-formepo"></div>');
            
            formepo.append(epos);
            formepo.append(epos_hidden);
            $(this).append(formepo);
            return true;
        });
            
        //_document.tcready(function() {
            init_epo_plugin();
    
            $(".tm-cart-link").tmpoplink();
            body.on('updated_checkout' ,function(){
                $(".tm-cart-link").tmpoplink();
            });

            _window.trigger("tmlazy");

            _window.trigger('tm_epo_loaded');
        //});

        
    });
})(jQuery);


(function($) {
    $(document).ready(function(){
        $(document).on('click','.jckqv-qty-spinner',function(){
            $(this).closest('.quantity').find('input.qty').trigger('change');
        });
    });
})(jQuery);