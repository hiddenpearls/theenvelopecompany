<?php
if (!tesla_has_woocommerce()) {
    get_template_part('index');
    return;
}

/**
 * Template Name: Default Front Page
 *
 * Description: This is the default front page for your Hudson theme. You can choose another page to show as the front page from Settings>Reading>Front page displays>Front Page
 *
 * @package WordPress
 * @subpackage Hudson
 * @since Hudson 1.0
 */
get_header();
?>
<div class="container">

    <?php get_template_part('template_parts/cart', 'floating'); ?>

    <?php echo do_shortcode('[tesla_offers_strip]'); ?>

    <?php echo do_shortcode('[tesla_offers_hot]'); ?>

    <?php echo do_shortcode('[hudson_recent_products]'); ?>
</div>

<div class="separation"></div>

<div class="container">

    <?php echo do_shortcode('[tesla_offers_service headline="'.__('// Our Services','hudson').'"]'); ?>

    <div class="separation"></div>

    <?php echo do_shortcode('[tesla_offers_generic]'); ?>

    <?php echo do_shortcode('[tesla_list_partners]'); ?>

</div>
<div class="clear"></div>
<?php get_footer(); ?>