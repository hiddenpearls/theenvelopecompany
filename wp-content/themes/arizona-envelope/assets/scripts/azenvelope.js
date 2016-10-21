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
	  	console.log(scroll);
		if (scroll >= 1) sticky.addClass('fixed-header');
		else sticky.removeClass('fixed-header');
	}
});