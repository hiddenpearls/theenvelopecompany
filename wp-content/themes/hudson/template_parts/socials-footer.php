<ul class="socials">
    <?php
    foreach ($services as $platform):
        if (_go('social_platforms_' . $platform)):
            ?>
            <li>
                <a href="<?php echo _go('social_platforms_' . $platform) ?>"><img src="<?php echo TT_THEME_URI ?>/images/social/<?php echo esc_attr($platform) ?>.png" alt="<?php echo esc_attr($platform) ?>" /></a>
            </li>        
            <?php
        endif;
    endforeach;
    ?>
</ul>