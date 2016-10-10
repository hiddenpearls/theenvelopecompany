<ul class="nav nav-tabs  <?php if (!empty($atts['class'])) echo esc_attr($atts['class']); ?>" <?php if ($atts['id']) { ?>id="<?php echo esc_attr($atts['id']); ?>"<?php } ?>>
    <?php print $content; ?>
</ul>