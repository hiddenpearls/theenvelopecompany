<?php
/**
 * Single Product Thumbnails
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.6.3
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post, $product, $woocommerce;?>

<div class="collection_box_mini">
    <div class="collection_left"></div>
    <div class="collection_right"></div>                
    <?php
    $gallery_img_ids = array();
    if (has_post_thumbnail())
        $gallery_img_ids[] = get_post_thumbnail_id($product->post->ID);
    $gallery_img_ids = array_merge($gallery_img_ids, $product->get_gallery_attachment_ids());
    foreach ($gallery_img_ids as $_gallery_img_id) {
        $thumb = wp_get_attachment_image_src($_gallery_img_id, apply_filters( 'single_product_small_thumbnail_size', 'shop_thumbnail' ));
        $fullsrc = wp_get_attachment_image_src($_gallery_img_id, 'large');
        ?>
        <div class="collection_selection" data-fullsrc="<?php echo esc_attr($fullsrc[0]); ?>"><img src="<?php echo esc_attr($thumb[0]); ?>" /></div>
        <?php
    }
    ?>
</div>