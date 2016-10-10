<?php if (!empty($headline)) { ?><div class="rubric_b"><?php print $headline; ?></div><?php } ?>

<div class="about_members">
    <div class="row">

        <?php if ($_posts->have_posts()) { ?>
            <!--  =====  START SLIDER  =====  -->

            <?php
            while ($_posts->have_posts()) {
                $_posts->the_post();
                $job_title = get_post_meta(get_the_ID(), THEME_NAME . '_job_title', TRUE);
                $facebook_url = get_post_meta(get_the_ID(), THEME_NAME . '_facebook_url', TRUE);
                $twitter_url = get_post_meta(get_the_ID(), THEME_NAME . '_twitter_url', TRUE);
                ?>
                <div class="span4">
                    <div class="member_image"><?php the_post_thumbnail('large') ?></div>
                    <h1><?php print $job_title; ?></h1>
                    <h2><?php the_title(); ?></h2>
                    <?php the_content(); ?>
                    <div class="about_social">
                        <ul>
                            <li><a href="<?php echo esc_attr( $facebook_url ); ?>"><img src="<?php echo esc_attr( TEMPLATEURI ) ?>/images/social/facebook.png" alt="facebook" /></a></li>
                            <li><a href="<?php echo esc_attr( $twitter_url ); ?>"><img src="<?php echo esc_attr( TEMPLATEURI ) ?>/images/social/twitter.png" alt="twitter" /></a></li>  
                        </ul>
                    </div>
                </div>
                <?php
            } // end of the loop.  
            ?>               
            <!--  =====  END SLIDER  =====  -->
            <?php
        }
        ?>

    </div>                
</div>