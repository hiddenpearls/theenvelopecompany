jQuery(document).ready(function( $ ) {

	//create animation for the mobile hamburger menu icon
	(function() {

	  "use strict";

	  function toggleHandler(toggle) {
	    toggle.addEventListener( "click", function(e) {
	      e.preventDefault();
	      if(this.classList.contains("is-active") === true) { 
	      	this.classList.remove("is-active");
	      	$('body').removeClass("noscroll");
	      } else {
	      	this.classList.add("is-active");
	      	
	      	$('body').addClass("noscroll");

	      }
	    });
	  }

	  var toggles = document.querySelectorAll(".c-hamburger");

	  for (var i = toggles.length - 1; i >= 0; i--) {
	    var toggle = toggles[i];
	    toggleHandler(toggle);
	  }

	  

	})();

  	scrollPosition();
	$(window).scroll(function() {
	    scrollPosition();
	});
	function scrollPosition(){
		var sticky = $('.site-header'),
	  	scroll = $(window).scrollTop();
	  	//console.log(scroll);
		if (scroll >= 1) sticky.addClass('fixed-header');
		else sticky.removeClass('fixed-header');
	}

	var $ =jQuery.noConflict();	

	// alert('test 1');
	 
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
		
		var k = '#yahm-tm-price-tmcp_radio_'+j; // alert(k);
		console.log(k);
		var kk = $(k).text(); // alert(kk); 
		console.log(kk);
		$('#yahm-unit-price').text(kk);	
		
		var h = '#yahm-tm-label-tmcp_radio_'+j;
		var hh = $(h).text();
		$('#yahm-unit-label').text(hh);

	};			
	$("#yahm-quantity")
	$(document).ready(function() {
		$('#tm-epo-field-1 option[value="1,000_1"]' ).prop('selected',true);
		$("#yahm-quantity").on("change", optChange);
		$("#tm-epo-field-1").on("change", optChange);	
	});

	$(window).ready(function() {
		$('#tm-epo-field-1 option[value="1,000_1"]' ).prop('selected',true);
		$("#yahm-quantity").on("change", optChange);
		$("#tm-epo-field-1").on("change", optChange);
	});



});