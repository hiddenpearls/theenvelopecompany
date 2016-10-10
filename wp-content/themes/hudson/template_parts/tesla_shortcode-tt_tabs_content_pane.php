<div class="tab-pane <?php if (!empty($atts['class'])) echo esc_attr($atts['class']); ?>" id="<?php echo esc_attr($atts['id']); ?>">
    <p>
        <?php print $content; ?>
    </p>
</div>