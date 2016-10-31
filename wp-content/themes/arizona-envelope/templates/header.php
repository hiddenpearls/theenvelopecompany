<?php 
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
                    <span class="d-block"><?php echo $phone; ?></span>
                </div>
                <?php
                    if (has_nav_menu('top_navigation')) :
                        wp_nav_menu(['theme_location' => 'top_navigation', 'menu_class' => 'nav top-nav']);
                    endif;
                ?>

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

<!-- sidebar nav just for reference, remove when done -->
<?php 
    if( is_front_page() ){
        if( have_rows('hero_banner') ):
?>
<div class="hero-panel">
        <?php while( have_rows('hero_banner') ) : the_row(); ?>
            <img src="<?php the_sub_field('background_image'); ?>" alt="">
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
if(is_page('layouts-die-lines') || is_page('resources') || is_page('helpful-information') | is_page('equipment')){?>
    <div class="category-shop-header">
        <div class="container menu-shop-navigation-container">
            <ul class="shop-navigation-bar">
                <?php 
                wp_nav_menu( array(
                    'menu' => 'resources-menu'
                ) );
                /*$menu = wp_get_nav_menu_items('resources-menu');
                foreach ($menu as $key => $menu_item) {
                    echo '<li class="menu-item">';    
                    echo  '<a class="shop-nav-btn" href="'. $menu_item->url .'">'.$menu_item->title.'</a>';
                    echo '</li>'; 
                }*/?> 
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
                /*$menu = wp_get_nav_menu_items('resources-menu');
                foreach ($menu as $key => $menu_item) {
                    echo '<li class="menu-item">';    
                    echo  '<a class="shop-nav-btn" href="'. $menu_item->url .'">'.$menu_item->title.'</a>';
                    echo '</li>'; 
                }*/?> 
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
if(is_cart()||is_product()||$status==="account-out"||is_page("checkout")){ ?>
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
<?php //if(is_product()||$status==="account-out" ){ ?>
<!--<div class="cart-shop-header">
    <div class="container">-->
      <?php 
        /*global $woocommerce;
        $cart_url = $woocommerce->cart->get_cart_url(); */
      ?>
      <!--<a class="cart-btn" href="<?php echo $cart_url; ?>"><i class="fa fa-shopping-cart"></i>My Cart</a>
      <a class="cart-contents" href="<?php //echo wc_get_cart_url(); ?>" title="<?php //_e( 'View your shopping cart' ); ?>"><?php //echo sprintf ( _n( 'item: (%d)', 'items: (%d)', WC()->cart->get_cart_contents_count() ), WC()->cart->get_cart_contents_count() ); ?>  <?php //echo WC()->cart->get_cart_total(); ?></a>-->
      <?php 
      /*global $woocommerce;

      if ( sizeof( $woocommerce->cart->cart_contents) > 0 ) :
        echo '<a class="btn white-btn" href="' . $woocommerce->cart->get_checkout_url() . '" title="' . __( 'Checkout' ) . '">' . __( 'Check Out' ) . '</a>';
      endif;
        */
      ?>
    <!--</div>
  </div>-->
<?php //} ?>