<?php get_header(); ?>

    

    <div id="slideshow_cont">

    

        <div id="slideshow">

        

            <?php

  

            $slider_arr = array();

            $x = 0;

            $args = array(

                         //'category_name' => 'blog',

                         'post_type' => 'post',

                         'meta_key' => 'ex_show_in_slideshow',

                         'meta_value' => 'Yes',

                         'posts_per_page' => 10

                         );

            query_posts($args);

            while (have_posts()) : the_post(); ?>                

            



            <div class="slide_cont <?php if($x == 0) { ?>active<?php } ?>">

                <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('featured-slideshow'); ?></a>

                

                <div class="slide_desc">

                    <h3><?php the_title(); ?></h3>

                    <p><?php echo ds_get_excerpt('48'); ?></p>

                </div><!--//slide_desc-->

            </div><!--//slide_cont-->



            

            <?php array_push($slider_arr,get_the_ID()); ?>

            <?php $x++; ?>

            <?php endwhile; ?>

  

            <?php wp_reset_query(); ?>                        

            

            <img src="<?php bloginfo('stylesheet_directory'); ?>/images/slide-prev.png" class="slide_prev" />

            <img src="<?php bloginfo('stylesheet_directory'); ?>/images/slide-next.png" class="slide_next" />            

        

        </div><!--//slideshow-->

    

    </div><!--//slideshow_cont-->

    

    <?php

    $category_ID = get_category_id('blog');

    if (ereg('iPhone',$_SERVER['HTTP_USER_AGENT'])) {

    $args = array(

                 'post_type' => 'post',

                 'posts_per_page' => -1,

                 'post__not_in' => $slider_arr,

                 'cat' => '-' . $category_ID

                 );      

    } else {

    $args = array(

                 'post_type' => 'post',

                 'posts_per_page' => 6,

                 'post__not_in' => $slider_arr,

                 'cat' => '-' . $category_ID

                 );  

    }



    query_posts($args);

    $x = 0;



    while (have_posts()) : the_post(); ?>                

    

    <div class="home_big_box <?php if($x == 1 || $x == 3 || $x == 5) { ?>home_big_box_last<?php } ?>">

        

        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('home-big-image'); ?></a>

           

        </a><!--//hover_content-->

    </div><!--//home_big_box-->        

    

    <?php if($x == 1 || $x == 3 || $x == 5) { ?>

        <div class="clear"></div>

    <?php } ?>



    <?php $x++; ?>

    <?php endwhile; ?>

    <?php wp_reset_query(); ?>                

        

    <div class="clear"></div>

    

    <?php

    $args = array(

                 'category_name' => 'blog',

                 'post_type' => 'post',

                 'posts_per_page' => 3,

                 'paged' => ( get_query_var('paged') ? get_query_var('paged') : 1)

                 );

    query_posts($args);

    $x = 0;

    while (have_posts()) : the_post(); ?> 



    <div class="home_small_box <?php if($x == 2) { ?>home_small_box_last<?php } ?>">

        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('home-small-image'); ?></a>

        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>

       

    </div><!--//home_small_box-->

    

    <?php $x++; ?>

    <?php endwhile; ?>                                                    

    <?php wp_reset_query(); ?>                

    

    <div class="clear"></div>

    

<?php get_footer(); ?>                        