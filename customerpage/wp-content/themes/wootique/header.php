<?php
/**
 * Header Template
 *
 * Here we setup all logic and HTML that is required for the header section of all screens.
 *
 */
 global $woo_options, $woocommerce;
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html <?php language_attributes(); ?> class="no-js ie6"> <![endif]-->
<!--[if IE 7 ]>    <html <?php language_attributes(); ?> class="no-js ie7"> <![endif]-->
<!--[if IE 8 ]>    <html <?php language_attributes(); ?> class="no-js ie8"> <![endif]-->
<!--[if IE 9 ]>    <html <?php language_attributes(); ?> class="no-js ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html <?php language_attributes(); ?> class="no-js"> <!--<![endif]-->
<head profile="http://gmpg.org/xfn/11">

<title><?php woo_title(); ?></title>
<?php woo_meta(); ?>

<!-- CSS  -->
	
<!-- The main stylesheet -->
<link rel="stylesheet" href="<?php echo get_stylesheet_directory_uri(); ?>/style.css">

<!-- /CSS -->

<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php $GLOBALS['feedurl'] = get_option('woo_feed_url'); if ( !empty($feedurl) ) { echo $feedurl; } else { echo get_bloginfo_rss('rss2_url'); } ?>" />

<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
      
<?php wp_head(); ?>
<?php woo_head(); ?>

</head>

<body <?php body_class(get_option('woo_site_layout')); ?>>
<?php woo_top(); ?>

<div id="wrapper">

	<?php if ( function_exists( 'has_nav_menu') && has_nav_menu( 'top-menu' ) ) { ?>

	<div id="top">
		<div class="col-full">
			<?php wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'top-nav', 'menu_class' => 'nav fl', 'theme_location' => 'top-menu' ) ); ?>
		</div>
	</div><!-- /#top -->

    <?php } ?>
    
    <div class="header">
					
			<div id="logo">
	
			<?php 
				if ($woo_options['woo_texttitle'] != 'true' ) : 
				$logo = $woo_options['woo_logo']; 
				if ( is_ssl() ) { $logo = preg_replace("/^http:/", "https:", $woo_options['woo_logo']); }
			?>
				<h1>
					<a href="<?php echo home_url( '/' ); ?>" title="<?php bloginfo( 'description' ); ?>">
						<img src="<?php if ($logo) echo $logo; else { echo get_template_directory_uri(); ?>/images/logo.png<?php } ?>" alt="<?php bloginfo( 'name' ); ?>" />
					</a>
				</h1>
	        <?php endif; ?>
	
	        <?php if( is_singular() && !is_front_page() ) : ?>
				<span class="site-title"><a href="<?php echo home_url( '/' ); ?>"><?php bloginfo( 'name' ); ?></a></span>
	        <?php else : ?>
				<h1 class="site-title"><a href="<?php echo home_url( '/' ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
	        <?php endif; ?>
				<span class="site-description"><?php bloginfo( 'description' ); ?></span>
	
			</div><!-- /#logo -->
			
			<?php woo_nav_before(); ?>
			
			<div id="navigation" class="col-full">
				<?php
				if ( function_exists( 'has_nav_menu') && has_nav_menu( 'primary-menu') ) {
					wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'main-nav', 'menu_class' => 'nav fl', 'theme_location' => 'primary-menu' ) );
				} else {
				?>
		        <ul id="main-nav" class="nav fl">
					<?php
		        	if ( isset($woo_options[ 'woo_custom_nav_menu' ]) AND $woo_options[ 'woo_custom_nav_menu' ] == 'true' ) {
		        		if ( function_exists( 'woo_custom_navigation_output') )
							woo_custom_navigation_output();
					} else { ?>
			            <?php if ( is_page() ) $highlight = "page_item"; else $highlight = "page_item current_page_item"; ?>
			            <li class="<?php echo $highlight; ?>"><a href="<?php echo home_url( '/' ); ?>"><?php _e( 'Home', 'woothemes' ) ?></a></li>
			            <?php
			    			wp_list_pages( 'sort_column=menu_order&depth=6&title_li=&exclude=' );
					}
					?>
		        </ul><!-- /#nav -->
		        <?php } ?>
		        
		        <?php woo_nav_after(); ?>
		        
			</div><!-- /#navigation -->
		
		</div>

	
	<div id="container" class="col-full">
	
	<?php woo_content_before(); ?>
