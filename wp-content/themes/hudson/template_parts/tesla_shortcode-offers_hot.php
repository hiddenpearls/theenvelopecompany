<?php if ($_posts->have_posts()) { ?>
    <!--  =====  START SLIDER  =====  -->
        <div class="new-slider" data-tesla-plugin="slider" data-tesla-item=".slide" data-tesla-next=".slide-right" data-tesla-prev=".slide-left" data-tesla-container=".slide-wrapper">
            <ul class="new-slider-arrows">
                <li class="slide-left"></li>
                <li class="slide-right"></li>
            </ul>

            <ul class="slide-wrapper">
                <?php
                $i = 0;
                while ($_posts->have_posts()) {
                    $_posts->the_post();
                    $url = get_post_meta(get_the_ID(), THEME_NAME . '_offer_url', TRUE);
                    ?>
                    <li class="slide <?php if (++$i == 1) { ?>rs_mainslider_items_active<?php } ?>">
                        <?php
                        if ($url) {
                            ?>
                            <a href="<?php echo esc_attr($url); ?>">
                                <?php
                            } the_post_thumbnail('full', array('class' => 'rs_mainslider_items_image'));
                            if ($url) {
                                ?></a><?php
                }
                            ?>
                    </li>
                    <?php
                } // end of the loop.  
                ?>
            </ul>
            <div class="container">
                <ul class="counting" data-tesla-plugin="bullets">
                <?php
                $i = 0;
                while ($_posts->have_posts()) {
                    $_posts->the_post();
                    $url = get_post_meta(get_the_ID(), THEME_NAME . '_offer_url', TRUE);
                    ?>
                    <li class="<?php if (++$i == 1) { ?>active<?php } ?>"></li>
                    <?php
                } // end of the loop.  
                ?>
                </ul>
            </div>
        </div>
    <!--  =====  END SLIDER  =====  -->
    <?php
}
?>


