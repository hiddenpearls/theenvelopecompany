<?php global $post, $product; ?>
<div class="span3">
    <div class="item">
        <div class="item_image">
            <div class="item_view" data-product-id="<?php the_ID(); ?>"><?php _e('quick view', 'hudson'); ?> +</div>
            <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
        </div>
        <h1><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
        <div class="tt-short-description">
            <a href="<?php the_permalink(); ?>"><?php echo apply_filters('woocommerce_short_description', $post->post_excerpt) ?></a>
        </div>
        <div class="tt-grid-item-footer">
            <div class="woocommerce"><?php woocommerce_template_loop_add_to_cart(); ?></div>
            <div class="item_price"><?php print $product->get_price_html(); ?></div>
        </div>
        <div class="clear"></div>
    </div>
</div>