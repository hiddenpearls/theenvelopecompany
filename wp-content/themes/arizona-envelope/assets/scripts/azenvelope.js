jQuery(document).ready(function ($) {

    //create animation for the mobile hamburger menu icon
    (function () {

        "use strict";

        function toggleHandler(toggle) {
            toggle.addEventListener("click", function (e) {
                e.preventDefault();
                if (this.classList.contains("is-active") === true) {
                    this.classList.remove("is-active");
                    //$('body').removeClass("noscroll");
                } else {
                    this.classList.add("is-active");

                    //$('body').addClass("noscroll");

                }
            });
        }

        var toggles = document.querySelectorAll(".c-hamburger");

        for (var i = toggles.length - 1; i >= 0; i--) {
            var toggle = toggles[i];
            toggleHandler(toggle);
        }

    })();

    function scrollPosition() {
        var sticky = $('.site-header');
        var scroll = $(window).scrollTop();

        if (scroll >= 20) {
            sticky.addClass('fixed-header');
        }
        else {
            sticky.removeClass('fixed-header');
        }
    }


    scrollPosition();

    $(window).scroll(function () {
        scrollPosition();
    });

    //$('html, body').hide();

    // ----------- 
    // Called when a user clicks on an achor 
    var jump = function (e) {
        var target;
        if (e) {
            e.preventDefault();
            target = $(this).attr("href");
        } else {
            target = location.hash;
        }
        var elOffset = $(target).offset().top;
        var elHeight = $(target).height();
        var windowHeight = $(window).height();
        var offset;

        if (elHeight < windowHeight) {
            offset = elOffset - ((windowHeight / 2) - (elHeight / 2));
        }
        else {
            offset = elOffset;
        }

        $('html,body').animate({
            scrollTop: offset
        }, 2000, function () {
            location.hash = target;
        });

    };

    // ------------------

    $('a[href^=#]').bind("click", jump);
    //--------
    // When the page loads, check if there is a # in the URL. 
    if (location.hash) {
        setTimeout(function () {
            //$('html, body').scrollTop(0).show();
            jump();
        }, 0);
    } else {
        //$('html, body').show();
    }

    function optChange() {
        var x = $("#yahm-quantity option:selected").is(':selected');
        x = ($(this).val());
        $('#tm-epo-field-1 option[value="' + x + '"]').prop('selected', true);
        var n = $("#yahm-quantity option:selected").index();
        var v; var j; n++;
        if (n < 2) { v = 1; j = 1; }
        else if (n > 1) { v = n; j = 0; }
        else { v = 2; j = 0; }
        $("input#quantityGo").val(n);
        $("input#quantityGo").change();
        var k = '#yahm-tm-price-tmcp_radio_' + j;
        //alert(k);
        var kk = $(k).text(); // 
        //alert(kk); 
        $('#yahm-unit-price').text(kk);
        var h = '#yahm-tm-label-tmcp_radio_' + j;
        var hh = $(h).text();
        $('#yahm-unit-label').text(hh);
    }
    $('#tm-epo-field-1 option[value="1,000_1"]').prop('selected', true);
    $("#yahm-quantity").on("change", optChange);
    $("#tm-epo-field-1").on("change", optChange);


    $('#gform_10').on("submit", function () {
        $('.contact-form > p').hide();
    });

    //set active nav state when viewing single posts
	/*if (window.location.href.indexOf('news') > -1) {
    	$('#menu-site-navigation li a').each(function(){
    		//console.log($(this));
			if($(this).attr('href').indexOf('about-us') > -1){
				$(this).parent('.menu-item').addClass('current-page-ancestor');
				
			}
		});
	}
	//set active nav state when on product pages
	if (window.location.href.indexOf('product-category') > -1 || window.location.href.indexOf('products') > -1 ) {
    	$('#menu-site-navigation li a').each(function(){
    		//console.log($(this));
			if($(this).attr('href').indexOf('product-category') > -1){
				$(this).parent('.menu-item').addClass('current-page-ancestor');
				
			}
		});
		
	}
	//set active on products category nav when on single product page.
	if ( window.location.href.indexOf('shop') > -1 ){
		$('#menu-shop-navigation li a').each(function(){
			if($(this).attr('href').indexOf('product-category') > -1){
				//console.log($(this).attr('href'));
				var menuItem = $(this);
				var selectedCategory = $(this).attr('href').split("product-category/").pop();
				selectedCategory = selectedCategory.replace("/", "");
				//console.log(selectedCategory);
				var selectedProduct = window.location.href;
				//console.log(selectedProduct);
				if( selectedProduct.indexOf(selectedCategory) >= 0){
					//console.log(menuItem.parent());
					menuItem.parent('.menu-item').addClass("current-page-ancestor");
					//$(this).parent('menu-item').addClass("current-page-ancestor");
				}
			}
		});
	}
	//my account
	if (window.location.href.indexOf('my-account') > -1) {
    	$('#menu-top-navigation li a').each(function(){
    		//console.log($(this));
			if($(this).attr('href').indexOf('my-account') > -1){
				$(this).parent('.menu-item').addClass('current-page-ancestor');
				
			}
		});
	}
	//resources
	if (window.location.href.indexOf('resources') > -1) {
    	$('#menu-site-navigation li a').each(function(){
    		//console.log($(this));
			if($(this).attr('href').indexOf('resources') > -1){
				$(this).parent('.menu-item').addClass('current-page-ancestor');
			}
		});
	}
	//company
	if (window.location.href.indexOf('company') > -1) {
    	$('#menu-site-navigation li a').each(function(){
    		//console.log($(this));
			if($(this).attr('href').indexOf('company') > -1){
				$(this).parent('.menu-item').addClass('current-page-ancestor');
			}
		});
	}
	//set active nav state when viewing single orders
	if (window.location.href.indexOf('view-order') > -1) {
    	$('.woocommerce-MyAccount-navigation .orange-navigation-bar li a').each(function(){
    		//console.log($(this));
    		console.log($(this));
    		if($(this).attr('href').indexOf('orders') > -1){
				$(this).parent('.woocommerce-MyAccount-navigation-link').addClass("current-page-ancestor");
			}
		});
	}*/

    $(".tc-label.tm-epo-style").each(function (i) {
        $(this).attr("tabindex", 0);
    });
    $("#yahm-quantity").attr("tabindex", 0);
    $('input, textarea').attr("tabindex", 0);

    $(document).on("keypress", ":input:not(textarea):not([type=submit]):not(select)", function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
        }
    });


    //run this function only if in single product page
    var wq = $("input[name='tc_cart_edit_key']");
    if (wq.length > 0) {
        var lastQty = urlParam('last_qty');
        //console.log( lastQty );
        $("#yahm-quantity option").each(function () {
            var optionValue = $(this).attr("value").split("_");
            //console.log(optionValue);
            var rawValue = optionValue[0].replace(",", "");
            if (rawValue == lastQty) {
                //console.log('I got here');
                $('#yahm-quantity').val($(this).attr("value"));
            }
        })
    }

    if ($(".tm-cart-edit-options").length > 0) {
        $(".cart_item").each(function () {
            var lastQty = $(this).find('.product-quantity span').text();
            //console.log(lastQty);
            var editLink = $(this).find('.product-edit a');
            //console.log(editLink);
            var newUrl = editLink.attr('href') + '&last_qty=' + lastQty;
            editLink.attr('href', newUrl);
            //console.log(editLink.attr("href"));
        })
    }
    function urlParam(name) {
        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
        if (results == null) {
            return null;
        }
        else {
            return results[1] || 0;
        }
    }


    $('.tmcp-upload').after('<div class="filename"><span class="file">No file chosen</span></div>').after('<button type="button" id="browse-button" class="button">Choose file</button>');
    $('.tmcp-upload').css('display', 'none');

    var browse_button = $('#browse-button')[0];
    var file_input = $('.tmcp-upload')[0];

    // Handler to trigger click on file input
    if (browse_button != null) {

        browse_button.addEventListener('click', function (e) {
            file_input.click();
        });

        $(file_input).change(function () {
            var fname = $(file_input).val();
            $('.file').html(fname.substr(fname.lastIndexOf('\\') + 1));
        });
    }

    //$(".woocommerce-checkout p.form-row").addClass("validate-required"); 
	/** Filter the datepicker of Gravity Forms - Only allow future dates.
 * Dcumentation: https://www.gravityhelp.com/documentation/article/gform_datepicker_options_pre_init/
 **/

    if ($('.gform_wrapper').length) { // check if a Gravity Form is present on the page
        gform.addFilter('gform_datepicker_options_pre_init', function (optionsObj, formId, fieldId) {
            if (formId == 12 && fieldId == 51) {
                optionsObj.minDate = 0;
            }

            return optionsObj;
        });
    }

    /* Make GF inputs readonly if a gf_readonly class is present */

    if ($("li.gf_readonly")) {
        $("li.gf_readonly input").attr("readonly", "readonly");
    }


    /* Checkout form validation */

    function add_classes(class_element) {
        $(class_element).removeClass('woocommerce-validated');
        $(class_element).addClass('woocommerce-invalid-required-field');
        $(class_element).addClass('woocommerce-invalid');
    }

    function deleteclasses(class_element) {
        $(class_element).removeClass('woocommerce-invalid-required-field');
        $(class_element).removeClass('woocommerce-invalid');
        $(class_element).addClass('woocommerce-validated');
    }

    if ($('#checkout-form').length) {
        $('#stripe-card-number').attr('name', 'stripe_card_number');
        $('#stripe-card-expiry').attr('name', 'stripe_card_expiry');
        $('#stripe-card-cvc').attr('name', 'stripe_card_cvc');

        $('#place_order').on('click', function () {
            if ($('#billing_state').val() == '') {
                add_classes('#billing_state_field');
            } else {
                deleteclasses('#billing_state_field');
            }

            if ($('#ship-to-different-address-checkbox').is(":checked")) {
                if ($('#shipping_state').val() == '') {
                    add_classes('#shipping_state_field');
                } else {
                    deleteclasses('#shipping_state_field');
                }
            }

            if (!$('#ship-to-different-address-checkbox').is(":checked")) {
                $('#checkout-form p').removeClass('woocommerce-invalid-required-field');
                $('#checkout-form p').removeClass('woocommerce-invalid');
                $('#checkout-form p').removeClass('woocommerce-validated');
            }
        });

        $.validator.messages.required = '';
        $('#checkout-form').validate({
            rules: {
                billing_first_name: {
                    required: true,
                },
                billing_last_name: {
                    required: true,
                },
                billing_email: {
                    required: true,
                    email: true
                },
                billing_phone: {
                    required: true,
                },
                billing_address_1: {
                    required: true,
                },
                shipping_first_name: {
                    required: function () {
                        if ($('#ship-to-different-address-checkbox').is(":checked")) {
                            return true;
                        }
                        else {
                            return false;
                        }
                    }
                },
                shipping_last_name: {
                    required: function () {
                        if ($('#ship-to-different-address-checkbox').is(":checked")) {
                            return true;
                        }
                        else {
                            return false;
                        }
                    }
                },
                shipping_address_1: {
                    required: function () {
                        if ($('#ship-to-different-address-checkbox').is(":checked")) {
                            return true;
                        }
                        else {
                            return false;
                        }
                    }
                },
                shipping_city: {
                    required: function () {
                        if ($('#ship-to-different-address-checkbox').is(":checked")) {
                            return true;
                        }
                        else {
                            return false;
                        }
                    }
                },
                shipping_postcode: {
                    required: function () {
                        if ($('#ship-to-different-address-checkbox').is(":checked")) {
                            return true;
                        }
                        else {
                            return false;
                        }
                    }
                },
                po_reference_number: {
                    required: true,
                },
                billing_city: {
                    required: true,
                },
                billing_postcode: {
                    required: true,
                },
                stripe_card_number: {
                    required: true,
                },
                stripe_card_expiry: {
                    required: true,
                },
                stripe_card_cvc: {
                    required: true,
                },
            },
            errorClass: 'woocommerce-invalid-required-field woocommerce-invalid',
            validClass: 'woocommerce-validated',
            highlight: function (element, errorClass, validClass) {
                $(element).parents('p.form-row').addClass(errorClass).removeClass(validClass);
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents('p.form-row').removeClass(errorClass).addClass(validClass);
            }
        });
        
            $('#billing_city_field label').append(' <abbr class="required" title="required" aria-required="true">*</abbr>');
            $('#billing_state_field label').append(' <abbr class="required" title="required" aria-required="true">*</abbr>');
            $('#billing_postcode_field label').append(' <abbr class="required" title="required" aria-required="true">*</abbr>');
            $('#shipping_state_field label').append(' <abbr class="required" title="required" aria-required="true">*</abbr>');
            $('#shipping_city_field  label').append(' <abbr class="required" title="required" aria-required="true">*</abbr>');
            $('#shipping_postcode_field  label').append(' <abbr class="required" title="required" aria-required="true">*</abbr>');
        
    }

    //
    if ($('.gform_confirmation_wrapper').length) {
        $('.online-quote-description').css('display', 'none');
    }

    //Disable front/back color checkbox if counter part is enabled.
    $('.printing_options input:checkbox').click(function () {
        var $inputs = $('.printing_options input:checkbox');
        if ($(this).prop('checked')) {  // <-- check if clicked box is currently checked
            var id = $($inputs.not(this)).prop("id");
            $("#" + id).prop("checked", false); // <-- uncheck all but checked checkbox
        }
    });


    // Checkout
    if ($('#checkout-form').length) {
        // Shipping form fields to be used to reassign the original values
        var billingForm = $('.woocommerce-billing-fields');
        var shippingForm = $('.shipping_address.form');

        
        var initialShippingName = shippingForm.find('#shipping_first_name').val();
        var initialShippingLast = shippingForm.find('#shipping_last_name').val();
        var initialShippingCompany = shippingForm.find('#shipping_company').val();
        var initialShippingEmail = shippingForm.find('#shipping_email').val();
        var initialShippingPhone = shippingForm.find('#shipping_phone').val();
        var initialShippingAddress1 = shippingForm.find('#shipping_address_1').val();
        var initialShippingAddress2 = shippingForm.find('#shipping_address_2').val();
        var initialShippingCity = shippingForm.find('#shipping_city').val();
        var initialShippingState = shippingForm.find('#shipping_state').val();
        var initialShippingZip = shippingForm.find('#shipping_postcode').val();
        console.log(initialShippingName);

        var shippingName = shippingForm.find('#shipping_first_name');
        var shippingLast = shippingForm.find('#shipping_last_name');
        var shippingCompany = shippingForm.find('#shipping_company');
        var shippingEmail = shippingForm.find('#shipping_email');
        var shippingPhone = shippingForm.find('#shipping_phone');
        var shippingAddress1 = shippingForm.find('#shipping_address_1');
        var shippingAddress2 = shippingForm.find('#shipping_address_2');
        var shippingCity = shippingForm.find('#shipping_city');
        var shippingState = shippingForm.find('#shipping_state');
        var shippingZip = shippingForm.find('#shipping_postcode');

        
        // Store form field values on load
        function getBillingFormValues(){
            // Billing form values
            
            var billingName = billingForm.find('#billing_first_name').val();
            var billingLast = billingForm.find('#billing_last_name').val();
            var billingCompany = billingForm.find('#billing_company').val();
            var billingEmail = billingForm.find('#billing_email').val();
            var billingPhone = billingForm.find('#billing_phone').val();
            var billingAddress1 = billingForm.find('#billing_address_1').val();
            var billingAddress2 = billingForm.find('#billing_address_2').val();
            var billingCity = billingForm.find('#billing_city').val();
            var billingState = billingForm.find('#billing_state').val();
            var billingZip = billingForm.find('#billing_postcode').val();

            // auto populate fields here with the values from the billing form and disable fields
            shippingName.attr("value", billingName).prop("disabled", true);
            shippingLast.attr("value", billingLast).prop("disabled", true);
            shippingCompany.attr("value", billingCompany).prop("disabled", true);
            shippingEmail.attr("value", billingEmail).prop("disabled", true);
            shippingPhone.attr("value", billingPhone).prop("disabled", true);
            shippingAddress1.attr("value", billingAddress1).prop("disabled", true);
            shippingAddress2.attr("value", billingAddress2).prop("disabled", true);
            shippingCity.attr("value", billingCity).prop("disabled", true);
            shippingState.attr("value", billingState).prop("disabled", true);
            shippingZip.attr("value", billingZip).prop("disabled", true);
        }
        
        function setInitialShippingValues() {
            // Initial Shipping form values stored to re-populate the field values with them in case user checks both options before completing order
            
            // $( 'div.shipping_address.form' ).slideDown(); taken care of by WooCommerce default
                    // Re populate form fields with original values
                    shippingName.attr("value", initialShippingName).prop("disabled", false);
                    shippingLast.attr("value", initialShippingLast).prop("disabled", false);
                    shippingCompany.attr("value", initialShippingCompany).prop("disabled", false);
                    shippingEmail.attr("value", initialShippingEmail).prop("disabled", false);
                    shippingPhone.attr("value", initialShippingPhone).prop("disabled", false);
                    shippingAddress1.attr("value", initialShippingAddress1).prop("disabled", false);
                    shippingAddress2.attr("value", initialShippingAddress2).prop("disabled", false);
                    shippingCity.attr("value", initialShippingCity).prop("disabled", false);
                    shippingState.attr("value", initialShippingState).prop("disabled", false);
                    shippingZip.attr("value", initialShippingZip).prop("disabled", false);
        }

        

        // Update "ship to same address" form when billing form is changed
        $('#checkout-form .woocommerce-billing-fields :input').change(function (e) {
            // Get DOM element that trigered the event
            var element = e.target;
            console.log(e.target);

            var changedInput = e.target.id;
            // Get the ID of the input field that changed
            changedInput = $("#"+changedInput).attr("id");
            // Get new value
            var input = $("#"+changedInput).val();
            console.log(input);
            changedInput = changedInput.toString();
            console.log(changedInput);
            changedInput = changedInput.replace("billing", "shipping");
            // $("#"+changedInput);
            
            // changedInput = $(changedInput).text().replace("billing", "shipping");
            console.log(changedInput);
            if( $(".same-address.shipping_address").length >= 1 ){
                var targetInput = $(".same-address.shipping_address").find("#"+changedInput);
                targetInput.attr("value", input);
            }
            
            //targetInput.hide();
        });

       

        // When a checkbox is clicked on
        $('.input-checkbox.checkout-ship').click(function () {

            // Store form div container in a var for cache
            var form = $('div.shipping_address.form');

            // Store checkboxes in a variable for cache and ease of use
            var $inputs = $('.input-checkbox.checkout-ship');

            // validate if any of the fields are checked before continue'ing, if not checked only hide all forms
            if ($(this).prop('checked')) {  // <-- check if clicked box is currently checked

                // Uncheck all other checkboxes
                var id = $($inputs.not(this)).prop("id");
                $("#" + id).prop("checked", false); // <-- uncheck all but checked checkbox

                // Same as billing address option
                // move the form in the DOM to be below the checked field
                // hide the form for the unchecked option, show the checked one.
                if (id === 'ship-to-different-address-checkbox') {
                    form.hide();
                    form.insertBefore(".ship-to-different-address");
                    form.addClass("same-address");
                    getBillingFormValues();
                    form.slideDown();
                    

                } else if (id === 'ship-to-same-address-checkbox') {
                    form.hide();
                    form.insertAfter(".ship-to-different-address");
                    setInitialShippingValues();
                    form.removeClass("same-address");
                    
                    
                }
            } else {
                // form.removeClass("same-address");
                form.hide();
            }
        });
    }
});