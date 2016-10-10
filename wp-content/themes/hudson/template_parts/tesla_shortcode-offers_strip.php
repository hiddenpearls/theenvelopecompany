<?php
if ($_posts->have_posts()) {
    while ($_posts->have_posts()) {
        $_posts->the_post();
        $url = get_post_meta(get_the_ID(), THEME_NAME . '_offer_url', TRUE);
        $has_post_thumbnail = has_post_thumbnail();
        ?>
        <div class="info_line <?php if ($has_post_thumbnail) { ?>strip_no_padding<?php } ?>">
            <?php
            if ($has_post_thumbnail) {
                if ($url) {
                    ?>
                    <a href="<?php echo esc_attr($url); ?>">
                        <?php
                    } the_post_thumbnail('large');
                    if ($url) {
                        ?></a><?php
            }
        } else {
            if ($url) {
                        ?>
                    <a href="<?php echo esc_attr($url); ?>">
                        <?php
                    }
                    the_content();
                    if ($url) {
                        ?></a><?php
            }
        }
                ?>
        </div>
        <?php
    } // end of the loop.  
}
?>