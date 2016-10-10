<li class="<?php if (!empty($atts['class'])) echo esc_attr($atts['class']); ?>">
    <a href="#<?php echo esc_attr($atts['link_id']); ?>" data-toggle="tab">
        <?php print $content; ?>
    </a>
</li>
