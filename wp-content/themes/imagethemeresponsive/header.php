<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">

<head> 

  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> 

  <title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>          

  <?php wp_head(); ?>

  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

  <link rel="shortcut icon" href="<?php bloginfo('stylesheet_directory'); ?>/favicon.ico" type="image/x-icon" />

  <!--[if lt IE 9]>
  <script src="http://css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>

  <![endif]-->              

  <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" title="no title" charset="utf-8"/>

  <script src="<?php bloginfo('stylesheet_directory'); ?>/js/jquery-latest.js" type="text/javascript"></script>

  <script src="<?php bloginfo('stylesheet_directory'); ?>/js/scripts.js" type="text/javascript"></script>

  <?php $shortname = "minimalist"; ?>

  <?php if(get_option($shortname.'_custom_background_color','') != "") { ?>

  <style type="text/css">

    body { background-color: <?php echo get_option($shortname.'_custom_background_color',''); ?>; }

  </style>

  <?php } ?>    

  <script type="text/javascript">

   $(document).ready(function() {        

      <?php if(is_numeric(get_option($shortname.'_slideshow_timeout',''))) { ?>

      start_custom_slider(<?php echo get_option($shortname.'_slideshow_timeout',''); ?>);

      <?php } else { ?>

      start_custom_slider('5000');

      <?php } ?>      

   });

  </script>

</head>

<body>
<div class="page-wrapper">
	<div class="page-top"></div>
  	<div class="page-top-content-noback">
	    <div class="page-top-container">
        <div class="head_social_cont">

            <ul>

              <?php if(get_option($shortname.'_twitter_link','') != "") { ?>

              <li><a href="<?php echo get_option($shortname.'_twitter_link',''); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/twitter-icon.png" /></a></li>

              <?php } ?>

              <?php if(get_option($shortname.'_facebook_link','') != "") { ?>

              <li><a href="<?php echo get_option($shortname.'_facebook_link',''); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/facebook-icon.png" /></a></li>

              <?php } ?>

              <?php if(get_option($shortname.'_google_plus_link','') != "") { ?>

              <li><a href="<?php echo get_option($shortname.'_google_plus_link',''); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/google-plus-icon.png" /></a></li>

              <?php } ?>

              <?php if(get_option($shortname.'_dribbble_link','') != "") { ?>

              <li><a href="<?php echo get_option($shortname.'_dribbble_link',''); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/dribbble-icon.png" /></a></li>

              <?php } ?>

              <?php if(get_option($shortname.'_pinterest_link','') != "") { ?>

              <li><a href="<?php echo get_option($shortname.'_pinterest_link',''); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/pinterest-icon.png" /></a></li>

              <?php } ?>


              <li>Call us toll free: <b>(800) 540-6883</b></li>

            </ul>
            

            <div class="clear"></div>


        </div><!--//head_social_cont-->


        <div class="clear"></div>


        <?php if(get_option($shortname.'_custom_logo_url','') != "") { ?>

          <div align="center"><a href="<?php bloginfo('url'); ?>"><img src="<?php echo stripslashes(stripslashes(get_option($shortname.'_custom_logo_url',''))); ?>" class="logo" /></a></div>

        <?php } else { ?>

          <div align="center"><a href="<?php bloginfo('url'); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/logo.png" class="logo" /></a></div>

        <?php } ?>            

          <div align="center"><a href="http://azenvelope.com/online-quote/"><img src="http://azenvelope.com/wp-content/themes/imagethemeresponsive/images/quotebtn.png" class="quotebtn" /></a></div>
        

        <div class="head_menu_cont">

        

            <div class="page_menu">

                <?php wp_nav_menu('menu=header_menu&menu_class=header_menu&container=false&menu_id=menu'); ?>

                <div class="clear"></div>

            </div><!--//page_menu-->

            

            <div class="cat_menu">
              <?php wp_nav_menu('menu=category_menu&container=false&menu_class=cat_list&fallback_cb=false'); ?>
              <div class="clear"></div>

            </div><!--//cat_menu-->

        

        </div><!--//head_menu_cont-->


        <div class="clear"></div>
  </div><!-- //page-top-container -->
  </div>

    

        <div class="head_social_cont">

            <ul>

              <?php if(get_option($shortname.'_twitter_link','') != "") { ?>

              <li><a href="<?php echo get_option($shortname.'_twitter_link',''); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/twitter-icon.png" /></a></li>

              <?php } ?>

              <?php if(get_option($shortname.'_facebook_link','') != "") { ?>

              <li><a href="<?php echo get_option($shortname.'_facebook_link',''); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/facebook-icon.png" /></a></li>

              <?php } ?>

              <?php if(get_option($shortname.'_google_plus_link','') != "") { ?>

              <li><a href="<?php echo get_option($shortname.'_google_plus_link',''); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/google-plus-icon.png" /></a></li>

              <?php } ?>

              <?php if(get_option($shortname.'_dribbble_link','') != "") { ?>

              <li><a href="<?php echo get_option($shortname.'_dribbble_link',''); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/dribbble-icon.png" /></a></li>

              <?php } ?>

              <?php if(get_option($shortname.'_pinterest_link','') != "") { ?>

              <li><a href="<?php echo get_option($shortname.'_pinterest_link',''); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/pinterest-icon.png" /></a></li>

              <?php } ?>

            </ul>

            <div class="clear"></div>

        </div><!--//head_social_cont-->

        

        <div class="clear"></div>
       
    </div><!--//header-->