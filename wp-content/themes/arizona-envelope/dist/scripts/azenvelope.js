jQuery(document).ready(function(e){function t(){var t=e(".site-header"),i=e(window).scrollTop();i>=80?t.addClass("fixed-header"):t.removeClass("fixed-header")}function i(){var t=e("#yahm-quantity option:selected").is(":selected");t=e(this).val(),e('#tm-epo-field-1 option[value="'+t+'"]').prop("selected",!0);var i,a,r=e("#yahm-quantity option:selected").index();r++,r<2?(i=1,a=1):r>1?(i=r,a=0):(i=2,a=0),e("input#quantityGo").val(r),e("input#quantityGo").change();var n="#yahm-tm-price-tmcp_radio_"+a,o=e(n).text();e("#yahm-unit-price").text(o);var c="#yahm-tm-label-tmcp_radio_"+a,s=e(c).text();e("#yahm-unit-label").text(s)}function a(e){var t=new RegExp("[?&]"+e+"=([^&#]*)").exec(window.location.href);return null==t?null:t[1]||0}!function(){"use strict";function e(e){e.addEventListener("click",function(e){e.preventDefault(),this.classList.contains("is-active")===!0?this.classList.remove("is-active"):this.classList.add("is-active")})}for(var t=document.querySelectorAll(".c-hamburger"),i=t.length-1;i>=0;i--){var a=t[i];e(a)}}(),t(),e(window).scroll(function(){t()});var r=function(t){var i;t?(t.preventDefault(),i=e(this).attr("href")):i=location.hash;var a,r=e(i).offset().top,n=e(i).height(),o=e(window).height();a=n<o?r-(o/2-n/2):r,e("html,body").animate({scrollTop:a},2e3,function(){location.hash=i})};e("a[href^=#]").bind("click",r),location.hash&&setTimeout(function(){r()},0),e('#tm-epo-field-1 option[value="1,000_1"]').prop("selected",!0),e("#yahm-quantity").on("change",i),e("#tm-epo-field-1").on("change",i),e("#gform_10").on("submit",function(){e(".contact-form > p").hide()}),window.location.href.indexOf("news")>-1&&e("#menu-site-navigation li a").each(function(){e(this).attr("href").indexOf("about-us")>-1&&e(this).parent(".menu-item").addClass("current-page-ancestor")}),(window.location.href.indexOf("product-category")>-1||window.location.href.indexOf("products")>-1)&&e("#menu-site-navigation li a").each(function(){e(this).attr("href").indexOf("product-category")>-1&&e(this).parent(".menu-item").addClass("current-page-ancestor")}),window.location.href.indexOf("products")>-1&&e("#menu-shop-navigation li a").each(function(){if(e(this).attr("href").indexOf("product-category")>-1){var t=e(this),i=e(this).attr("href").split("product-category/").pop();i=i.replace("/","");var a=window.location.href;a.indexOf(i)>=0&&t.parent(".menu-item").addClass("current-page-ancestor")}}),window.location.href.indexOf("my-account")>-1&&e("#menu-top-navigation li a").each(function(){e(this).attr("href").indexOf("my-account")>-1&&e(this).parent(".menu-item").addClass("current-page-ancestor")}),window.location.href.indexOf("resources")>-1&&e("#menu-site-navigation li a").each(function(){e(this).attr("href").indexOf("resources")>-1&&e(this).parent(".menu-item").addClass("current-page-ancestor")}),window.location.href.indexOf("company")>-1&&e("#menu-site-navigation li a").each(function(){e(this).attr("href").indexOf("company")>-1&&e(this).parent(".menu-item").addClass("current-page-ancestor")}),window.location.href.indexOf("view-order")>-1&&e(".woocommerce-MyAccount-navigation .orange-navigation-bar li a").each(function(){console.log(e(this)),e(this).attr("href").indexOf("orders")>-1&&e(this).parent(".woocommerce-MyAccount-navigation-link").addClass("current-page-ancestor")}),e(".tc-label.tm-epo-style").each(function(t){e(this).attr("tabindex",0)}),e("#yahm-quantity").attr("tabindex",0);var n=e("input[name='tc_cart_edit_key']");if(n.length>0){var o=a("last_qty");e("#yahm-quantity option").each(function(){var t=e(this).attr("value").split("_"),i=t[0].replace(",","");i==o&&e("#yahm-quantity").val(e(this).attr("value"))})}e(".tm-cart-edit-options").length>0&&e(".cart_item").each(function(){var t=e(this).find(".product-quantity span").text(),i=e(this).find(".product-edit a"),a=i.attr("href")+"&last_qty="+t;i.attr("href",a)}),e(".tmcp-upload").after('<div class="filename"><span class="file">No file chosen</span></div>').after('<button type="button" id="browse-button" class="button">Choose file</button>'),e(".tmcp-upload").css("display","none");var c=e("#browse-button")[0],s=e(".tmcp-upload")[0];null!=c&&(c.addEventListener("click",function(e){s.click()}),e(s).change(function(){var t=e(s).val();e(".file").html(t.substr(t.lastIndexOf("\\")+1))})),e(".gform_wrapper").length&&gform.addFilter("gform_datepicker_options_pre_init",function(e,t,i){return 12==t&&51==i&&(e.minDate=0),e}),e("li.gf_readonly")&&e("li.gf_readonly input").attr("readonly","readonly"),e("#checkout-form").length&&(e("#stripe-card-number").attr("name","stripe_card_number"),e("#stripe-card-expiry").attr("name","stripe_card_expiry"),e("#stripe-card-cvc").attr("name","stripe_card_cvc"),e.validator.messages.required="",e("#checkout-form").validate({rules:{billing_first_name:{required:!0},billing_last_name:{required:!0},billing_email:{required:!0,email:!0},billing_phone:{required:!0},billing_address_1:{required:!0},shipping_first_name:{required:!0},shipping_last_name:{required:!0},shipping_address_1:{required:!0},po_reference_number:{required:!0},billing_city:{required:!0},billing_postcode:{required:!0},stripe_card_number:{required:!0},stripe_card_expiry:{required:!0},stripe_card_cvc:{required:!0}},errorClass:"woocommerce-invalid-required-field woocommerce-invalid",validClass:"woocommerce-validated",highlight:function(t,i,a){e(t).parents("p.form-row").addClass(i).removeClass(a)},unhighlight:function(t,i,a){e(t).parents("p.form-row").removeClass(i).addClass(a)}}),e(document).ready(function(){e("#billing_city_field label").append(' <abbr class="required" title="required" aria-required="true">*</abbr>'),e("#billing_state_field label").append(' <abbr class="required" title="required" aria-required="true">*</abbr>'),e("#billing_postcode_field label").append(' <abbr class="required" title="required" aria-required="true">*</abbr>')})),e(".gform_confirmation_wrapper").length&&e(".online-quote-description").css("display","none")});
//# sourceMappingURL=azenvelope.js.map
