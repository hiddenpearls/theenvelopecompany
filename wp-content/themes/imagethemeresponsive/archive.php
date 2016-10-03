<?php get_header(); ?>



    <?php

    global $wp_query;

    $args = array_merge( $wp_query->query, array( 'posts_per_page' => 10 ) );

    query_posts( $args );

    $x = 0;

    while (have_posts()) : the_post(); ?>                

    

    <div class="home_big_box <?php if($x == 1 || $x == 3 || $x == 5 || $x == 7 || $x == 9) { ?>home_big_box_last<?php } ?>">

        <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('home-big-image'); ?></a>

      

            

        </a><!--//hover_content-->

    </div><!--//home_big_box-->     



    <?php if($x == 1 || $x == 3 || $x == 5 || $x == 7 || $x == 9) { ?>

        <div class="clear"></div>

    <?php } ?>



    <?php $x++; ?>

    <?php endwhile; ?>

        

    <div class="clear"></div>    

    

    <div class="archive_nav">

        <div class="left"><?php previous_posts_link('&lt; &lt; Previous') ?></div>

        <div class="right"><?php next_posts_link('Next &gt; &gt;') ?></div>    

        <div class="clear"></div>

    </div><!--//archive_nav-->

    

    <?php wp_reset_query(); ?>                    

    

    <div class="clear"></div>

    

<?php get_footer(); ?>                        