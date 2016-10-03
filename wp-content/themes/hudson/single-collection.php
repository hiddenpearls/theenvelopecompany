<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 */
wp_enqueue_script('magnicifc_popup-js', get_template_directory_uri() . '/scripts/magnific_popup/js/magnific_popup.js', array('jquery'), '1.0', TRUE);
wp_enqueue_style('magnicifc_popup-css', get_template_directory_uri() . '/scripts/magnific_popup/css/magnific-popup.css');
get_header();
$gallery = get_post_gallery(0, FALSE);
$gallery_ids = explode(',', $gallery['ids']);
?>

<!-- =====================================
    START CONTENT -->
<div class="content">
    <div class="container">
        <div class="path">
            <a href="#"><?php _e('Home', 'hudson'); ?></a> / <a href="<?php echo get_post_type_archive_link('collection'); ?>"><?php _e('collections', 'hudson'); ?></a> / <?php the_title(); ?>
        </div>
        <?php get_template_part('template_parts/cart', 'floating'); ?>

        <div class="rubric_b"><?php _e('collections', 'hudson'); ?></div>

        <!-- COLLECTIONS -->
        <div class="collection">

            <div class="collection_slider">
                <div class="product_images collection_box">
                    <a href="#" class="zoom">
                        <span></span>
                        <img src="data:image/gif;base64,R0lGODlhAQABAIAAAP///wAAACH5BAEAAAAALAAAAAABAAEAAAICRAEAOw==" class="large_preview" />
                    </a>
                    <p>
                        <a href="#"><?php the_title(); ?></a>
                    <p>
                </div>  
                <div class="separation"></div>
                <div class="collection_box_mini">
                    <div class="collection_left"></div>
                    <div class="collection_right"></div>                
                    <?php
                    foreach ($gallery_ids as $_gallery_img_id) {
                        $thumb = wp_get_attachment_image_src($_gallery_img_id, 'thumbnail');
                        $fullsrc = wp_get_attachment_image_src($_gallery_img_id, 'large');
                        ?>
                        <div class="collection_selection" data-fullsrc="<?php echo esc_attr($fullsrc[0]); ?>"><img src="<?php echo $thumb[0]; ?>" /></div>
                        <?php
                    }
                    ?>
                </div>
            </div>

        </div>
        <!-- COLLECTIONS -->



        <div class="clear"></div>

    </div>

</div>

<?php get_footer(); ?>