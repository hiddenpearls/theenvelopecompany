<div class="alert_<?php echo $atts['type']; ?>  <?php if (!empty($atts['class'])) echo esc_attr($atts['class']); ?>">
    <?php if ($atts['has_close'] == 'true') { ?><a class="close" data-dismiss="alert" href="#">&times;</a><?php } ?>
    <?php echo $content; ?>
</div>