jQuery(document).ready(function($){
  //===================Clearing options START============================================================
  var timer;
  function clear_result(){
    clearTimeout(timer);
    timer = setTimeout(function(){
      $('progress').animate({'value':'0'},1000);
      $('#result').fadeOut('slow',function(){$(this).html('').show().css('color','black');});
    },3000);
  }

  $('#clear').on('click',function(){
    var btn = $(this);
    var action_long = btn.attr('data-action');
    var action = btn.attr('id');
    var old_text = btn.text();
    btn.text( action_long );
    $('progress').stop().animate({'value':'50'},500,function(){
      $.post(ajaxurl, {
        option_action:action,
        action:'options_actions'
      }, function(response) {
          if (response === '1'){
            $('progress').stop().animate({'value':'100'},1000,function(){$('#result').html('Options Cleared.').hide().fadeIn('slow',function(){
                clear_result();
                btn.text( old_text );
              });
            });
          }else{
            $('#result').html("Options failed to be cleared or already clear.").hide().fadeIn().css('color','red');
          }
      });
    });
  });
  //===================Clearing options END============================================================
  //===================Exporting options START============================================================
  $('#export').on('click',function(){
    var btn = $(this);
    var action_long = btn.attr('data-action');
    var action = btn.attr('id');
    var old_text = btn.text();
    btn.text( action_long );
    $('progress').stop().animate({'value':'50'},500,function(){
      
      $('progress').stop().animate({'value':'100'},1000,function(){
        $('#result').html('Options Exported.').hide().fadeIn('slow',function(){
          window.location.href = download_url;
          btn.text( old_text );
          clear_result();
        });
      });
      
    });
  });
  //===================Exporting options END============================================================
  //===================Importing options START============================================================
  $('#import').on('click',function(){
    var btn = $(this);
    var action_long = btn.attr('data-action');
    var action = btn.attr('id');
    btn.fadeOut();
    $('#controls button.btn').animate({'opacity':'0.5'}).attr('disabled','disabled');
    $('#upload_form').slideDown('slow');
    $('#cancel').on('click',function(event){
      event.preventDefault();
      $('#upload_form').slideUp('slow',function(){
        btn.fadeIn('fast');
      });
      $('#controls button.btn').animate({'opacity':'1'}).removeAttr('disabled');
    });

    var options = {
        target:        '',      // target element(s) to be updated with server response 
        beforeSubmit:  showRequest,     // pre-submit callback 
        success:       showResponse,    // post-submit callback 
        url:    ajaxurl                 // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php     
      };

    // bind form using 'ajaxForm' 
    $('#upload_form').ajaxForm(options);

    function showRequest(formData, jqForm, options) {
      //do extra stuff before submit like disable the submit button
      $('progress').stop().animate({'value':'50'},500);
    }

    function showResponse(responseText, statusText, xhr, $form)  {
      if (responseText === '1'){
        $('progress').stop().animate({'value':'100'},1000,function(){$('#result').html('Options Imported.').hide().fadeIn('slow',function(){
            clear_result();
          });
        });
      }else{
        $('#result').html(responseText).css('color','red');
        clear_result();
      }
    }
  });
  //===================Importing options END============================================================
  //===================Importing Demo options START============================================================
  $('#reset').on('click',function(){
    var btn = $(this);
    var action_long = btn.attr('data-action');
    var action = btn.attr('id');
    var old_text = btn.text();
    btn.text( action_long );
    $('progress').stop().animate({'value':'50'},500,function(){
      $.post(ajaxurl, {
        option_action:action,
        action:'options_actions'
      }, function(response) {
        if(response === '1'){
          $('progress').stop().animate({'value':'100'},500,function(){$('#result').html('Demo Options Imported.').hide().fadeIn('slow',function(){
              clear_result();
              btn.text( old_text );
            });
          });
        }else{
          $('#result').html(response).css('color','red');
        }
      });
    });
  });
  //===================Importing Demo options END============================================================
  //===================Importing XML START============================================================
  function import_ajax(step,total_steps){
    var last_response_len = false;
    console.log(step)
    $.ajax({
      url: ajaxurl,
      method: 'POST',
      data: {
        action:'import_xml',
        step: step,
        total_steps: total_steps
      },
      xhr: function(){
        var xhr = new window.XMLHttpRequest();
        //Download progress
        xhr.addEventListener("progress", function(e){
          var this_response, response = e.currentTarget.response;
            if(last_response_len === false){
              this_response = response;
              last_response_len = response.length;
            }
            else{
              this_response = response.substring(last_response_len);
              last_response_len = response.length;
            }
            console.log(this_response);
            $('#result').append(this_response)
        }, false);
        return xhr;
      },
    })
    .fail(function(jqXHR, textStatus, errorThrown){
      console.log(jqXHR, textStatus, errorThrown);
      $('#result').append(textStatus + ' ' + errorThrown + ' <br> Even if this error appears please allow a few more minutes for the backend request to finish downloading media (watch the media until no images are added).');
    })
    .always(function(response) {
      step ++;
      if(step <= total_steps)
        import_ajax(step,total_steps);
      else
        $('#result_content .spinner').removeClass('is-active');
    });
  }

  $('#import_xml_button').on('click',function(){
        var step          = 1,
        total_steps       = $(this).data('multiple') ? $(this).data('multiple') : 1;
    $(this).addClass('.button_loading');
    $('#import_xml_button,#tt_import_alert').fadeOut(function(){
      $("#tt_import_alert").html('Importing Demo Content.<br>\
                          <b>Note : importing should not take long on regular servers. If it fails to import (still loading) even after 20-30 min please use the default WordPress Importer (Tools->Import->Wordpress) and upload the <code>/theme_config/import.xml</code> file.</b>')
      .fadeIn();
    });
    $('#result_content .spinner').addClass('is-active');
    import_ajax(step,total_steps);
  });
}); //>>>>>>>>>>>>>>>>>>>END DONCUMENT READY<<<<<<<<<<<<<<<<<<<<<<<<<<<<<