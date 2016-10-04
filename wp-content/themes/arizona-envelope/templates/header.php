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
                <img src="<?php the_field('site_logo', 'option'); ?>" alt="<?php echo get_bloginfo(); ?> logo">
            </a>
        </div>
        <div class="collapse navbar-collapse main-site-nav clearfix" id="main-site-nav">
            <div class="nav nav-utilities">
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

<?php if(is_page('die-lines') || is_page('resources') || is_page('helpful-information') | is_page('equipment')){?>
    <div class="category-shop-header">
        <div class="container">
            <ul class="shop-navigation-bar">
                <?php 
                $menu = wp_get_nav_menu_items('resources-menu');
                foreach ($menu as $key => $menu_item) {
                    echo '<li>';    
                    echo  '<a class="shop-nav-btn" href="'. $menu_item->url .'">'.$menu_item->title.'</a>';
                    echo '</li>'; 
                }?> 
            </ul>
        </div>
    </div>
<?php }?>

<?php if(is_page('contact-us')){?>
    <div class="category-shop-header">
        <div class="container">
            <ul class="shop-navigation-bar">
                <?php

                      $taxonomy     = 'product_cat';
                      $orderby      = 'name';  
                      $show_count   = 0;      // 1 for yes, 0 for no
                      $pad_counts   = 0;      // 1 for yes, 0 for no
                      $hierarchical = 0;      // 1 for yes, 0 for no  
                      $title        = '';  
                      $empty        = 0;

                      $args = array(
                             'taxonomy'     => $taxonomy,
                             'orderby'      => $orderby,
                             'show_count'   => $show_count,
                             'pad_counts'   => $pad_counts,
                             'hierarchical' => $hierarchical,
                             'title_li'     => $title,
                             'hide_empty'   => $empty
                      );
                     $all_categories = get_categories( $args );
                     foreach ($all_categories as $cat) {
                        $counter = 1;
                        if($cat->category_parent == 0) {
                            $category_id = $cat->term_id;   
                            echo '<li>';    
                              echo  '<a class="shop-nav-btn" href="'. get_term_link($cat->slug, 'product_cat') .'">'.$cat->name.'</a>';
                              echo '</li>';
                        } 
                        $counter++;      
                    }
                    ?>
            </ul>
        </div>
    </div>
<?php }?>
