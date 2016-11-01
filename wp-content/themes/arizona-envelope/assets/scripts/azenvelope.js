jQuery(document).ready(function( $ ) {

	//create animation for the mobile hamburger menu icon
	(function() {

	  "use strict";

	  function toggleHandler(toggle) {
	    toggle.addEventListener( "click", function(e) {
	      e.preventDefault();
	      if(this.classList.contains("is-active") === true) { 
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

	function scrollPosition(){
		var sticky = $('.site-header');
	  	var scroll = $(window).scrollTop();

		if (scroll >= 120){
			sticky.addClass('fixed-header');
			sticky.next().addClass("fixed-header-content");
		} 
		else{
			sticky.removeClass('fixed-header');	
			sticky.next().removeClass("fixed-header-content");
		} 
	}
	scrollPosition();
	$(window).scroll(function() {
	    scrollPosition();
	});
	var jump=function(e){
		var target;
	   	if (e){
	       e.preventDefault();
	       target = $(this).attr("href");
	   	}else{
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
	   },2000,function(){
	       location.hash = target;
	   });

	};
	$('html, body').hide();
	$(document).ready(function(){
	    $('a[href^=#]').bind("click", jump);

	    if (location.hash){
	        setTimeout(function(){
	            $('html, body').scrollTop(0).show();
	            jump();
	        }, 0);
	    }else{
	        $('html, body').show();
	    }
	});

	function optChange(){	
		var x = $("#yahm-quantity option:selected").is(':selected');
			x =($(this).val()); 
			$('#tm-epo-field-1 option[value="'+x+'"]' ).prop('selected',true);	
		var n = $("#yahm-quantity option:selected").index(); 
		var v; var j; n++;  
			if ( n < 2 ) { v = 1; j = 1;  } 
			else if ( n > 1 ) {	v = n;  j = 0; } 
			else{ v = 2;  j = 0;  }
			$("input#quantityGo").val(n);
			$("input#quantityGo").change();
		var k = '#yahm-tm-price-tmcp_radio_'+j; 
		//alert(k);
		var kk = $(k).text(); // 
		//alert(kk); 
		$('#yahm-unit-price').text(kk);	
		var h = '#yahm-tm-label-tmcp_radio_'+j;
		var hh = $(h).text();
		$('#yahm-unit-label').text(hh);
	}
	$('#tm-epo-field-1 option[value="1,000_1"]' ).prop('selected',true);
	$("#yahm-quantity").on("change", optChange);
	$("#tm-epo-field-1").on("change", optChange);	


	$('#gform_10').on("submit", function(){
		$('.contact-form > p').hide();
	});

	//set active nav state when viewing single posts
	if (window.location.href.indexOf('news') > -1) {
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
	if ( window.location.href.indexOf('products') > -1 ){
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
					console.log(menuItem.parent());
					menuItem.parent('.menu-item').addClass("current-page-ancestor");
					//$(this).parent('menu-item').addClass("current-page-ancestor");
				}
			}
		});
	}
	if (window.location.href.indexOf('my-account') > -1) {
    	$('#menu-top-navigation li a').each(function(){
    		//console.log($(this));
			if($(this).attr('href').indexOf('my-account') > -1){
				$(this).parent('.menu-item').addClass('current-page-ancestor');
				
			}
		});
	}
});