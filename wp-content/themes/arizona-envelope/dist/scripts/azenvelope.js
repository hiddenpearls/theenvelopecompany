jQuery(document).ready(function(t){function e(){var e=t(".site-header"),a=t(window).scrollTop();a>=80?e.addClass("fixed-header"):e.removeClass("fixed-header")}function a(){var e=t("#yahm-quantity option:selected").is(":selected");e=t(this).val(),t('#tm-epo-field-1 option[value="'+e+'"]').prop("selected",!0);var a,i,n=t("#yahm-quantity option:selected").index();n++,n<2?(a=1,i=1):n>1?(a=n,i=0):(a=2,i=0),t("input#quantityGo").val(n),t("input#quantityGo").change();var o="#yahm-tm-price-tmcp_radio_"+i,c=t(o).text();t("#yahm-unit-price").text(c);var r="#yahm-tm-label-tmcp_radio_"+i,s=t(r).text();t("#yahm-unit-label").text(s)}!function(){"use strict";function t(t){t.addEventListener("click",function(t){t.preventDefault(),this.classList.contains("is-active")===!0?this.classList.remove("is-active"):this.classList.add("is-active")})}for(var e=document.querySelectorAll(".c-hamburger"),a=e.length-1;a>=0;a--){var i=e[a];t(i)}}(),e(),t(window).scroll(function(){e()});var i=function(e){var a;e?(e.preventDefault(),a=t(this).attr("href")):a=location.hash;var i,n=t(a).offset().top,o=t(a).height(),c=t(window).height();i=o<c?n-(c/2-o/2):n,t("html,body").animate({scrollTop:i},2e3,function(){location.hash=a})};t("a[href^=#]").bind("click",i),location.hash&&setTimeout(function(){i()},0),t('#tm-epo-field-1 option[value="1,000_1"]').prop("selected",!0),t("#yahm-quantity").on("change",a),t("#tm-epo-field-1").on("change",a),t("#gform_10").on("submit",function(){t(".contact-form > p").hide()}),window.location.href.indexOf("news")>-1&&t("#menu-site-navigation li a").each(function(){t(this).attr("href").indexOf("about-us")>-1&&t(this).parent(".menu-item").addClass("current-page-ancestor")}),(window.location.href.indexOf("product-category")>-1||window.location.href.indexOf("products")>-1)&&t("#menu-site-navigation li a").each(function(){t(this).attr("href").indexOf("product-category")>-1&&t(this).parent(".menu-item").addClass("current-page-ancestor")}),window.location.href.indexOf("products")>-1&&t("#menu-shop-navigation li a").each(function(){if(t(this).attr("href").indexOf("product-category")>-1){var e=t(this),a=t(this).attr("href").split("product-category/").pop();a=a.replace("/","");var i=window.location.href;i.indexOf(a)>=0&&(console.log(e.parent()),e.parent(".menu-item").addClass("current-page-ancestor"))}}),window.location.href.indexOf("my-account")>-1&&t("#menu-top-navigation li a").each(function(){t(this).attr("href").indexOf("my-account")>-1&&t(this).parent(".menu-item").addClass("current-page-ancestor")}),t(".tc-label.tm-epo-style").each(function(e){t(this).attr("tabindex",0)}),t("#yahm-quantity").attr("tabindex",0)});
//# sourceMappingURL=azenvelope.js.map
