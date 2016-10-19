jQuery(document).ready(function( $ ) {

	//create animation for the mobile hamburger menu icon
	(function() {

	  "use strict";

	  function toggleHandler(toggle) {
	    toggle.addEventListener( "click", function(e) {
	      e.preventDefault();
	      if(this.classList.contains("is-active") === true) { 
	      	this.classList.remove("is-active");
	      } else {
	      	this.classList.add("is-active");
	      }
	    });
	  }

	  var toggles = document.querySelectorAll(".c-hamburger");

	  for (var i = toggles.length - 1; i >= 0; i--) {
	    var toggle = toggles[i];
	    toggleHandler(toggle);
	  }

	  

	})();
	$('[data-toggle=offcanvas]').click(function() {
	    $('.row-offcanvas').toggleClass('active');
	    $('.showhide').toggle();
  	});
  	scrollPosition();
	$(window).scroll(function() {
	    scrollPosition();
	});
	function scrollPosition(){
		var height = $(window).scrollTop();
	    if(height  < 300) {
	        $('.site-header').removeClass("fixed-header");
	    }
	    if(height  > 300) {
	        $('.site-header').addClass("fixed-header");
	    }
	}
});