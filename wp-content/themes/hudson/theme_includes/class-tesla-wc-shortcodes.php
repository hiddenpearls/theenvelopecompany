<?php

class Tesla_WC_Shortcodes {

    public function __construct() {
        if (tesla_has_woocommerce()) {
            add_shortcode('hudson_recent_products', array($this, 'recent_products'));
        }
    }

    /**
     * Recent Products shortcode
     *
     * @access public
     * @param array $atts
     * @return string
     */
    public function recent_products($atts) {

        global $woocommerce_loop, $woocommerce, $tesla_trans_var;

        extract(shortcode_atts(array(
                    'per_page' => '12',
                    'columns' => '4',
                    'orderby' => 'date',
                    'order' => 'desc',
                    'headline' => __('// Latest products','hudson')
                        ), $atts));

        $meta_query = $woocommerce->query->get_meta_query();

        $args = array(
            'post_type' => 'product',
            'post_status' => 'publish',
            'ignore_sticky_posts' => 1,
            'posts_per_page' => $per_page,
            'orderby' => $orderby,
            'order' => $order,
            'meta_query' => $meta_query
        );

        ob_start();

        $products = new WP_Query($args);

        $woocommerce_loop['columns'] = $columns;

        if ($products->have_posts()) :
            ?>


            <?php while ($products->have_posts()) : $products->the_post(); ?>

                <?php get_template_part('template_parts/slider_recent_products', 'loop'); ?>

            <?php endwhile; // end of the loop.  ?>


            <?php

        endif;

        wp_reset_postdata();
        $tesla_trans_var['looped_products'] = ob_get_clean();
        $tesla_trans_var['headline'] = $headline;
        ob_start();
        get_template_part('template_parts/slider_recent_products', 'container');
        return ob_get_clean();
    }

}

$tesla_class_registry['Tesla_WC_Shortcodes'] = new Tesla_WC_Shortcodes;
