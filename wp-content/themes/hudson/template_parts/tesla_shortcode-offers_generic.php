<?php if ($_posts->have_posts()) { ?>
    <!--  =====  START SLIDER  =====  -->
    <?php if (!empty($headline)) { ?>
        <h4 class="headline"><?php print $headline; ?></h4>
    <?php } ?>
    <div class="offers">
        <div class="row">
            <?php
            while ($_posts->have_posts()) {
                $_posts->the_post();
                $url = get_post_meta(get_the_ID(), THEME_NAME . '_offer_url', TRUE);
                ?>
                <div class="span4">
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