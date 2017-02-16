<?php 
/*
* Analytics Settings
*/
if(get_field('enabled', 'option')){
   the_field('gtm_body', 'option'); 
}
if( is_front_page() ){
    $site_header = "transparent-header";
} else {
    $site_header = "solid-header";
}
?>
<header class="site-header <?php echo $site_header; ?>">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed c-hamburger c-hamburger--htx" data-toggle="collapse" data-target="#main-site-nav" aria-expanded="false">
                <span>
                    <p class="sr-only">Toggle navigation</p>
                </span>
            </button>
            <a class="brand" href="<?= esc_url(home_url('/')); ?>">
                <img class="regular-logo" src="<?php the_field('site_logo', 'option'); ?>" alt="<?php echo get_bloginfo(); ?> logo">
                <img class="retina-logo" src="<?php the_field('retina_site_logo', 'option'); ?>" alt="<?php echo get_bloginfo(); ?> logo">
            </a>
        </div>
        <div class="collapse navbar-collapse main-site-nav clearfix" id="main-site-nav">
            <div class="nav nav-utilities">
                <div class="phone-icon phone-block">
                    <?php 
                        $phone = get_field('toll_free_phone', 'options'); 
                        $cleaned_phone = $phone;
                        $cleaned_phone = str_replace(array('(', ')', '-', ' '), "", $cleaned_phone);
                    ?>
                    <a href="tel:<?php echo $cleaned_phone; ?>">
                        <?php echo $phone; ?>
                    </a>
                </div>
                <?php
                    if (has_nav_menu('top_navigation')) :
                        wp_nav_menu(['theme_location' => 'top_navigation', 'menu_class' => 'nav top-nav']);
                    endif;
                ?>
                <div class="responsive-search">
                    <?php 
                        get_search_form();
                    ?>
                </div>
            </div>
            <nav class="nav-primary clearfix">
                <?php
                    if (has_nav_menu('primary_navigation')) :
                        wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav site-navigation']);
                    endif;
                ?>
                <?php 
                    get_search_form();
                ?>
            </nav>
        </div><!-- /.navbar-collapse -->
    </div>
</header>

<?php if( is_front_page() ) : ?>
<?php  
  //Get sale banner if enabled
  if( get_field('sale_banner_enabled') ) {
    //get selected color/image background type
    $background_type = get_field('image_or_color_background');
    if( $background_type === 'color'){
      $background = get_field('sale_banner_background_color');
      $section_background = "background-color: ".$background.";";
    } else {
      $background = get_field('sale_banner_background_image');
      $section_background = "background-image: url('".$background['url']."');";
      $section_background_title = $background['alt'];
    }

?>
    <section class="panel-wbgd sale-banner-section <?php echo $background_type; ?>" style="<?php echo $section_background ?>" title="<?php echo $sale_banner_title; ?>">
        <div class="container">
            <div class="row">
                <div class="col-md-12 clearfix">
                    <?php if( get_field('sale_banner_title') ) : ?>
                        <h2 class="heading-text"><?php the_field('sale_banner_title'); ?></h2>
                    <?php endif; ?>
                    <?php if( get_field('sale_banner_text') ) : ?>
                        <p><?php the_field('sale_banner_text'); ?></p>
                    <?php endif; ?>
                  <?php if( have_rows('sale_banner_calls_to_action') ) : ?>
                    <?php while( have_rows('sale_banner_calls_to_action') ) : the_row(); ?>
                      <a href="<?php the_sub_field('button_url'); ?>" class="white-btn btn big" title="<?php the_sub_field('button_label'); ?>">
                        <?php the_sub_field('button_label'); ?>
                      </a>
                    <?php endwhile; ?>
                  <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
<?php
  } else {
    //Banner disabled. Do nothing
  }
?>

<?php endif; ?>

<!-- sidebar nav just for reference, remove when done -->
<?php 
    if( is_front_page() ){
        if( have_rows('hero_banner') ):
?>
<div class="hero-panel">
    <?php while( have_rows('hero_banner') ) : the_row(); ?>
        <?php $image = get_sub_field('background_image'); 
            if ( !empty($image) ){
        ?>
            <img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>">
        <?php
            }
        ?>
        
        <div class="container">
            <div class="slider-caption">
                <h1><?php the_sub_field('hero_title'); ?></h1>
                <p><?php the_sub_field('hero_subtitle'); ?></p>
                <?php 
                if( get_sub_field('first_hero_button_label') ):
                ?>
                <a class="btn orange-btn big" href="<?php the_sub_field('first_hero_button_url'); ?>"><?php the_sub_field('first_hero_button_label'); ?></a>
                <?php
                endif;
                 ?>
                 <?php 
                if( get_sub_field('second_hero_button_label') ):
                ?>
                <a class="btn white-btn big" href="<?php the_sub_field('second_hero_button_url'); ?>"><?php the_sub_field('second_hero_button_label'); ?></a>
                <?php
                endif;
                 ?>

            </div>
        </div>
    <?php endwhile; ?>
</div>

<?php
        endif;
    }

 ?>

<?php //print resources menu for the resources template pages
if(is_page('layouts-die-lines') || is_page('samples') || is_page('helpful-information') | is_page('equipment')){?>
    <div class="category-shop-header">
        <div class="container menu-shop-navigation-container">
            <ul class="shop-navigation-bar">
                <?php 
                wp_nav_menu( array(
                    'menu' => 'resources-menu'
                ) );
                ?> 
            </ul>
        </div>
    </div>
<?php }?>
<?php //print about menu for the about template pages
if(is_page('about-us') || is_page('privacy-policy') || is_page('terms-conditions') || is_home() || is_singular("post") ){?>
    <div class="category-shop-header">
        <div class="container menu-shop-navigation-container">
            <ul class="shop-navigation-bar">
                <?php 
                wp_nav_menu( array(
                    'menu' => 'about-us-menu'
                ) );
                ?> 
            </ul>
        </div>
    </div>
<?php }?>
<?php 
    if( is_user_logged_in() ){
        $status = "in";
    } else {
        $status = "out";
        if( is_page('my-account') ){
            $status = "account-out";
        }
    }

 ?>
<?php //print shop menu for the shop template pages
if(is_cart()||is_product()||$status==="account-out"||is_page("checkout")||is_page('online-quote')){ ?>
<div class="category-shop-header">
    <div class="container">
        <div>
            <?php
                if (has_nav_menu('shop_navigation')) :
                    wp_nav_menu(['theme_location' => 'shop_navigation', 'menu_class' => 'nav shop-navigation-bar']);
                endif;
                ?>
        </div>
    </div>
</div>
<?php } ?>