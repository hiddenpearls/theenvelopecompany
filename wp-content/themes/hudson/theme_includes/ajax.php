<?php

add_action('wp_ajax_nopriv_get_product_qw', 'hudson_get_product_qw');
add_action('wp_ajax_get_product_qw', 'hudson_get_product_qw');

function hudson_get_product_qw() {
    global $product;
    $product = get_product($_POST['product_id']);
    $response = array('status' => 1, 'product' => array(
            'image' => get_the_post_thumbnail($product->id),
            'url' => get_permalink($product->id),
            'title' => get_the_title($product->id),
            'rating_html' => $product->get_rating_html(),
            'short_description' => apply_filters('woocommerce_short_description', $product->post->post_excerpt),
            'excerpt' => (strlen($product->post->post_content) > 150) ? substr($product->post->post_content, 0, 147) . '...' : $product->post->post_content,
            'add_to_cart_url' => $product->add_to_cart_url(),
            'price' => $product->get_price_html(),
            'attributes' => Tesla_View::render('template_parts/quickview-attributes.php', array('product' => $product))
        )
    );
    if ($response['product']['rating_html'])
        $response['product']['rating_html'].=' (' . $product->get_rating_count() . ')';
    die(json_encode($response));
}