<?php if ($_posts->have_posts()) { ?>
    <!--  =====  START SLIDER  =====  -->
    <?php if (!empty($headline)) { ?>
        <div class="rubric_b"><?php print $headline; ?></div>
    <?php } ?>
    <div class="h_services">
        <div class="row">
            <?php
            while ($_posts->have_posts()) {
                $_posts->the_post();
                $url = get_post_meta(get_the_ID(), THEME_NAME . '_offer_url', TRUE);
                ?>               
                <div class="span3">
                    <div class="services_box">
                        <?php
                        if ($url) {
                            ?>
                            <a href="<?php echo esc_attr($url); ?>">
                                <?php
                            } the_post_thumbnail('large');
                            if ($url) {
                                ?></a><?php
                }
                            ?>
                        <h3><?php the_title(); ?></h3>
                    </div>
                    <?php the_content(); ?>
                </div>

                <?php
            } // end of the loop.  
            ?>               
        </div>
    </div>
    <!--  =====  END SLIDER  =====  -->
    <?php
}
?>