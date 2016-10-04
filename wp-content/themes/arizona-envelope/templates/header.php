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
                <div class="phone-icon phone-block">
                    <?php 
                        $phone = get_field('toll_free_phone', 'options'); 
                        $cleaned_phone = $phone;
                        $cleaned_phone = str_replace(array('(', ')', '-', ' '), "", $cleaned_phone);
                    ?>
                    <a href="tel:+1<?php echo $cleaned_phone; ?>" class="d-block"><?php echo $phone; ?></a>
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