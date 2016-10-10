<div class="p_table <?php if (!empty($atts['type'])) echo 'p_table_' . esc_attr($atts['type']); ?> <?php if (!empty($atts['class'])) echo esc_attr($atts['class']); ?>">
    <div class="p_table_head">
        <span><?php print $atts['heading']; ?></span>
    </div>
    <div class="p_table_body">
        <div class="p_table_price"><span><?php print $atts['currency']; ?></span><?php print $atts['price']; ?></div>
        <?php print $content ?>
        <div class="p_table_buy">
            <a href="<?php echo esc_attr($atts['url']); ?>"><?php print $atts['buy_text']; ?></a>
        </div>
        <div class="clear"></div>
    </div>
</div> 
