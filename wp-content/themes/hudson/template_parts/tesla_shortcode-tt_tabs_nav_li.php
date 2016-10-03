<li class="<?php if (!empty($atts['class'])) echo esc_attr($atts['class']); ?>">
    <a href="#<?php echo $atts['link_id']; ?>" data-toggle="tab">
        <?php echo $content; ?>
    </a>
</li>
