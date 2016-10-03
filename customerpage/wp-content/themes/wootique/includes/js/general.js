// JavaScript Document

jQuery(function(){ 

	jQuery('.view a').click(function(){

		if(jQuery(this).hasClass('gallery')){
		
			jQuery(this).parent().parent().find('.block').removeClass('block-fullwidth');
			jQuery('.view a').removeClass('active');
			jQuery(this).addClass('active');
		
		} else {
					
			jQuery(this).parent().parent().find('.block').addClass('block-fullwidth');
			jQuery('.view a').removeClass('active');
			jQuery(this).addClass('active');
		
		}
		
		return false;
		
	});
	
	// Uniform
	jQuery("select.orderby, .variations select, input[type=radio]").uniform();
	
	// Carousel
	if (jQuery().jcarousel) {
		jQuery('#featured-products.fp-slider ul.featured-products').jcarousel({
			scroll: 4
		});
		jQuery('.jcarousel-prev, .jcarousel-next').appendTo('#featured-products.fp-slider');
	}
	
});