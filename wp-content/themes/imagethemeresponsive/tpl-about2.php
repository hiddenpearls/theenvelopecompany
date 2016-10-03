<?php

/*

  Template Name: Page2
*/

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml">

<head> 

  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/> 

  <title><?php wp_title('&laquo;', true, 'right'); ?> <?php bloginfo('name'); ?></title>          

  <?php wp_head(); ?>

  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />

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

<style type="text/css">
body {margin:0; padding:0;}
.page-wrapper {width:1040px; margin:0 auto;}
.page-top {
	background-color: #ff3715;
	height: 4px;
	width: 980px;
	margin:0 auto;
}
.page-top-content {
	background-image: url(/wp-content/themes/imagethemeresponsive/images/page-top-content.jpg);
	background-repeat: no-repeat;
	height: 120px;
	width: 1040px;
}
.page-top-logo {
	width: 275px;
	margin-right: 20px;
	float: left;
}
.page-top-menu {
	float: left;
	width: 600px;
}
.page-top-right {
	float: left;
	width: 125px;
	padding-right: 20px;
}
.page-content-sub {
	background-image: url(/wp-content/themes/imagethemeresponsive/images/body-page-center.jpg);
	background-repeat: repeat-y;
	width: 1040px;
	margin-bottom: 20px;
}
.page-bottom-box {
	float: left;
	height: 180px;
	width: 322px;
	background-image: url(/wp-content/themes/imagethemeresponsive/images/bottom-box.jpg);
	margin-right:5px;

}
.page-bottom {
	height: 180px;
	width: 1040px;
	margin-right: 20px;
	margin-left: 30px;
}
.page-footer-top {
	background-color: #ff3715;
	height: 4px;
	width: 980px;
	margin:0 auto;
	margin-top: 20px;
}
.page-footer-text {
	width: 980px;
	padding-top: 20px;
	margin: 0 auto;
	;
}
.page-top-container {
	width: 980px;
	padding-right: 30px;
	padding-left: 30px;
}
.quote-button {
	-moz-box-shadow:0px 1px 0px 0px #f29c93;
	-webkit-box-shadow:0px 1px 0px 0px #f29c93;
	box-shadow:0px 1px 0px 0px #f29c93;
	-moz-border-radius:5px;
	-webkit-border-radius:5px;
	border-radius:5px;
	border:4px solid #ff3715;
	display:inline-block;
	color:#ff3715;
	font-family:arial;
	font-size:16px;
	font-weight:bold;
	padding:6px 24px;
	text-decoration:none;
	text-shadow:1px 1px 0px #b23e35;
	background:url("/wp-content/themes/imagethemeresponsive/images/btn-background.jpg") no-repeat scroll 0 0 transparent;
}
.quote-button:hover {
	background-color:#e6e6e6;
}
.quote-button:active {
	position:relative;
	top:1px;
}
#box1:hover{ background-image:url(/wp-content/themes/imagethemeresponsive/images/bottom-box-over.jpg); }
#box2:hover{ background-image:url(/wp-content/themes/imagethemeresponsive/images/bottom-box-over.jpg); }
#box3:hover{ background-image:url(/wp-content/themes/imagethemeresponsive/images/bottom-box-over.jpg); }

.page-content-text-sub {
	padding-top:60px;
	padding-left:100px;
	padding-right:100px;
	background-image: url(/wp-content/themes/imagethemeresponsive/images/body-page-top.jpg);
	background-position: top center;
	background-repeat: no-repeat;
	min-height: 400px;
}

</style>
</head>

<body>
<div class="page-wrapper">
	<div class="page-top"></div>
  	<div class="page-top-content">
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

            </ul>
            

            <div class="clear"></div>


        </div><!--//head_social_cont-->

        

        <div class="clear"></div>

        

        <?php if(get_option($shortname.'_custom_logo_url','') != "") { ?>

          <div align="center"><a href="<?php bloginfo('url'); ?>"><img src="<?php echo stripslashes(stripslashes(get_option($shortname.'_custom_logo_url',''))); ?>" class="logo" /></a></div>

        <?php } else { ?>

          <div align="center"><a href="<?php bloginfo('url'); ?>"><img src="<?php bloginfo('stylesheet_directory'); ?>/images/logo.png" class="logo" /></a></div>

        <?php } ?>            

        

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

            <input type="button" class="quote-button" value="Get a Quote" onclick="/quote/" style="float:right; margin-top:-15px; background-image: url(images/btn-background.jpg);" />
        

        <div class="clear"></div>
  </div><!-- //page-top-container -->
  </div>
    <div class="page-content-sub">
        <div class="page-content-text-sub">
            <?php if (have_posts()) : while (have_posts()) : the_post(); ?>          
        

                <h1 class="single_title"><?php the_title(); ?></h1>
                <div class="single_content">
        

                <?php the_content(); ?>
        
                </div><!--//single_content-->

                <br /><br />
        

                <?php //comments_template(); ?>
        

            <?php endwhile; else: ?>
        

                <h3>Sorry, no posts matched your criteria.</h3>
        

            <?php endif; ?>    

        

            <div class="clear"></div>
      </div>
      <div style="background-image:url(/wp-content/themes/imagethemeresponsive/images/body-page-bottom.jpg); background-repeat:
      no-repeat; background-position: bottom center; height:62px;"></div>
    </div>
<div style="clear:both;"></div>
	<div class="page-bottom">
    	<div class="page-bottom-box" id="box1">
            	<?php if ( is_active_sidebar( 'sidebar-4' ) ) : ?>
					<?php dynamic_sidebar( 'sidebar-4' ); ?>
				<?php endif; ?>
        </div>

    	<div class="page-bottom-box" id="box2">
            	<?php if ( is_active_sidebar( 'sidebar-5' ) ) : ?>
					<?php dynamic_sidebar( 'sidebar-5' ); ?>
				<?php endif; ?>
        </div>

    	<div class="page-bottom-box" id="box3">
            	<?php if ( is_active_sidebar( 'sidebar-6' ) ) : ?>
					<?php dynamic_sidebar( 'sidebar-6' ); ?>
				<?php endif; ?>
        </div>
        <div style="clear:both"></div>
    </div>        
	<div class="page-footer">
   	  <div class="page-footer-text">
            	<?php if ( is_active_sidebar( 'sidebar-7' ) ) : ?>
					<?php dynamic_sidebar( 'sidebar-7' ); ?>
				<?php endif; ?>
      </div>
	  <div class="page-footer-top"></div>
      <div class="page-footer-copyright">
            	<?php if ( is_active_sidebar( 'sidebar-8' ) ) : ?>
					<?php dynamic_sidebar( 'sidebar-8' ); ?>
				<?php endif; ?>
      </div>
    </div>    


</div><!-- page wrapper -->
<?php wp_footer(); ?>
</body>
</html>
