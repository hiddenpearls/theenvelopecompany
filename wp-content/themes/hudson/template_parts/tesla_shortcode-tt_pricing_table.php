<div class="p_table <?php if (!empty($atts['type'])) echo 'p_table_' . esc_attr($atts['type']); ?> <?php if (!empty($atts['class'])) echo esc_attr($atts['class']); ?>">
    <div class="p_table_head">
        <span><?php echo $atts['heading']; ?></span>
    </div>
    <div class="p_table_body">
        <div class="p_table_price"><span><?php echo $atts['currency']; ?></span><?php echo $atts['price']; ?></div>
        <?php echo $content ?>
        <div class="p_table_buy">
            <a href="<?php echo $atts['url']; ?>"><?php echo $atts['buy_text']; ?></a>
        </div>
        <div class="clear"></div>
    </div>
</div> 
