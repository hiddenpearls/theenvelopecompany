
  $(document).ready(function() {        
  
        my_counter = 0;
        curr_div = 0;  
  
      $('#slideshow .slide_cont').each(function() {
          $(this).addClass('div_' + my_counter);
          my_counter++;
      });  


      $(".home_big_box").hover(
        function () {
          $(this).find('.hover_content').css('display','block');
        },
        function () {
          $(this).find('.hover_content').css('display','none');
        }
      );      
  
  });
  
  
        function home_switch_div() {
  
            if(curr_div >= my_counter)
                curr_div = 0;
            else if(curr_div < 0)
                curr_div = (my_counter-1);
            
            //$('.slide_' + curr_image).show('slide', {direction: 'right'}, 1000);
            $('.div_' + curr_div).fadeIn('slow', function() { 
            
            });
            
        }
        
        function hide_curr_div() {
               //$('.div_' + curr_div).hide();        
               $('.div_' + curr_div).fadeOut('slow', function() {
                   
               });        
        }        
        
        function next_slide_div() {

            hide_curr_div();
            curr_div++;        
            
            home_switch_div();
        }        
        
        function prev_slide_div() {

            hide_curr_div()
            curr_div--;        
            
            home_switch_div();
        }                        
  
  
        function start_custom_slider(slider_interval) {
        
            $('.slide_prev').click(function() {
                prev_slide_div();
                clearInterval(intervalID);
                intervalID = setInterval(next_slide_div, slider_interval);
            });
            
            $('.slide_next').click(function() {
                
                next_slide_div();
                clearInterval(intervalID);
                intervalID = setInterval(next_slide_div, slider_interval);
            });                        
            
            
            intervalID = setInterval(next_slide_div, slider_interval);                
        
        }  