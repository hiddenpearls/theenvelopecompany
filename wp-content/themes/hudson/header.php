<?php
extract(_gall());
global $woocommerce;
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
        <title><?php wp_title('|', true, 'right'); ?></title>
        <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
        <?php if ($favicon) { ?>
            <link rel="shortcut icon" href="<?php echo ($favicon); ?>" />
        <?php } ?>
        <?php // Loads HTML5 JavaScript file to add support for HTML5 elements in older IE versions.  ?>

        <!--[if lt IE 9]>
                    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
            <![endif]-->
        <?php if (is_singular()) wp_enqueue_script('comment-reply'); ?>
        <?php wp_head(); ?>
    </head>
    <body <?php body_class(); ?>>        
        <?php if (tesla_has_woocommerce()) get_template_part('template_parts/quickview', 'popup'); ?>
        <!-- =====================================
        START HEADER -->
        <div class="header">
            <div class="top_line">
                <?php if (tesla_has_woocommerce()) { ?>
                    <div class="container">
                        <?php if (!is_user_logged_in()) { ?>
                            <ul>
                                <li class="open_login"><?php _e('Sign in', 'hudson'); ?></li>
                                <li>/</li>
                                <li class="open_register"><?php _e('Register', 'hudson'); ?></li>                    
                            </ul>
                            <?php get_template_part('template_parts/account', 'login_register'); ?>
                        <?php } else { ?>
                            <ul>
                                <li class="my_account"><a href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')); ?>"><?php _e('My Account', 'hudson'); ?></a></li>
                                <li>/</li>
                                <li class=""><a href="<?php echo wp_logout_url(site_url('/')); ?>"><?php _e('Log Out', 'hudson'); ?></a></li>            
                            </ul>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>


            <div class="container">
                <div class="row">
                    <div class="span2">
                        <div class="logo">
                        <?php 
                            $logo_text_color = _go('logo_text_color');
                            $logo_text_font = _go('logo_text_font');
                            $logo_text_size = _go('logo_text_size');
                         ?>     
                            <a href="<?php echo home_url(); ?>" style="<?php echo($logo_text_color) ? "color:$logo_text_color;" : ''; echo ($logo_text_font) ? "font-family:$logo_text_font;":'';  echo ($logo_text_size) ? "font-size:".$logo_text_size."px;" : '' ?>" >
                                <?php
                                if (_go('logo_text')):
                                    echo _go('logo_text');
                                elseif (_go('logo_image')):?>
                                    <img src="<?php echo _go('logo_image'); ?>" alt="<?php echo THEME_PRETTY_NAME ?> Logo">
                                    <?php
                                else: ?>
                                    <img src="<?php echo get_template_directory_uri() ?>/images/logo.png" alt="<?php echo THEME_PRETTY_NAME ?> Logo">
                                <?php
                                endif;
                                ?>
                            </a>
                        </div>
                    </div>
                    <div class="span10">
                        <div class="cart_search">
                            <?php if (tesla_has_woocommerce()) { ?>
                                <ul class="cart">                                 
                                    <li class="cart_bg">
                                        <?php _e('My cart', 'hudson'); ?> ( <span class="dynamic_cart_contents_count"><?php print $woocommerce->cart->get_cart_contents_count(); ?></span> ) <span class="dynamic_cart_total"><?php print $woocommerce->cart->get_cart_subtotal(); ?></span>
                                        <?php get_template_part('template_parts/cart', 'mini_custom'); ?>
                                    </li> 
                                </ul>   
                            <?php } else { ?>
                                <div class="social_icons">  
                                    <?php if (!_go('social_platforms_facebook') && !_go('social_platforms_twitter') && !_go('social_platforms_pinterest')) { ?>
                                        <ul class="socials">
                                            <li>
                                                <a href="http://facebook.com"><img src="<?php echo get_template_directory_uri() ?>/images/social/facebook.png" alt="facebook"></a>
                                            </li>        
                                            <li>
                                                <a href="http://pinterest.com"><img src="<?php echo get_template_directory_uri() ?>/images/social/pinterest.png" alt="pinterest"></a>
                                            </li>        
                                            <li>
                                                <a href="http://twitter.com"><img src="<?php echo get_template_directory_uri() ?>/images/social/twitter.png" alt="twitter"></a>
                                            </li>        
                                        </ul>
                                    <?php } else echo do_shortcode('[tesla_social_icons services="facebook, pinterest, twitter, rss"]'); ?>
                                </div>
                            <?php } ?>

                            <form action="<?php echo site_url('/'); ?>" class="search_form">
                                <span><input type="submit" class="search_submit" value="" /></span>
                                <input name="s" type="text" class="search" placeholder="Search">
                            </form>

                        </div>

                        <div class="top_menu">
                            <div class="menu-responsive"><?php _e('Menu','hudson')?></div>
                            <?php
                            if (has_nav_menu('primary'))
                                wp_nav_menu(array('theme_location' => 'primary', 'menu_class' => '', 'container_class' => 'menu'));
                            else
                                wp_page_menu(array('theme_location' => 'primary', 'menu_class' => 'menu'));
                            ?>
                        </div>

                        <div class="clear"></div>
                    </div>
                </div>

            </div>
            <div class="clear"></div>
        </div>
        <div class="menu_line">
            <div class="container">
                <div class="category-responsive"><?php _e('Categories','hudson')?></div>
                <?php
                if (has_nav_menu('categories_menu'))
                    wp_nav_menu(array('theme_location' => 'categories_menu', 'menu_class' => '', 'container_class' => 'category_menu' . (!tesla_has_woocommerce() ? ' category_menu_wp' : ''), 'depth' => 3));
                else
                    wp_page_menu(array('theme_location' => 'categories_menu', 'menu_class' => 'category_menu' . (!tesla_has_woocommerce() ? ' category_menu_wp' : '')));
                ?>                
            </div>
        </div>
        <div class="content">            