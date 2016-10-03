<?php

class Tesla_Offers {

    protected $post_type = 'offer';

    public function __construct() {
        $this->__init_post_type();
        $this->__init_shortcodes();
    }

    public function wp_query(array $args = array()) {
        $overwrite = array(
                'post_type' => $this->post_type
        );
        $defaults = array(
                'post_status' => 'publish',
                'ignore_sticky_posts' => 1,
                'posts_per_page' => 999,
                'orderby' => 'date',
                'order' => 'DESC'
        );
        $args = array_merge($defaults, $args, $overwrite);

        return new WP_Query($args);
    }

    public function tesla_offers_strip($atts) {
        $atts = shortcode_atts(array(
                'offer_ids' => ''
                        ), $atts);
        $ids = array_filter(array_map('trim', explode(',', $atts['offer_ids'])), 'is_numeric');
        if (empty($ids)) {
            $args = array('posts_per_page' => 1, 'orderby' => 'date', 'order' => 'DESC');
        } else {
            $args = array(
                    'post__in' => $ids
            );
        }
        $args = array_merge($args, array('meta_query' => array(array('key' => THEME_NAME . '_offer_type', 'value' => 'offer_strip', 'compare' => '='))));
        $_posts = $this->wp_query($args);
        $data = array('_posts' => &$_posts);
        $output = Tesla_View::render('template_parts/tesla_shortcode-offers_strip.php', $data);
        wp_reset_postdata();
        return $output;
    }

    public function tesla_offers_hot($atts) {
        $atts = shortcode_atts(array(
                'offer_ids' => ''
                        ), $atts);
        $ids = array_filter(array_map('trim', explode(',', $atts['offer_ids'])), 'is_numeric');
        if (empty($ids)) {
            $args = array('posts_per_page' => -1, 'orderby' => 'date', 'order' => 'DESC');
        } else {
            $args = array(
                    'post__in' => $ids
            );
        }
        $args = array_merge($args, array('meta_query' => array(array('key' => THEME_NAME . '_offer_type', 'value' => 'offer_hot', 'compare' => '='))));
        $_posts = $this->wp_query($args);
        $data = array('_posts' => &$_posts);
        $output = Tesla_View::render('template_parts/tesla_shortcode-offers_hot.php', $data);
        wp_reset_postdata();
        return $output;
    }

    public function tesla_offers_service($atts) {
        $atts = shortcode_atts(array(
                'offer_ids' => '',
                'headline' => ''
                        ), $atts);
        $ids = array_filter(array_map('trim', explode(',', $atts['offer_ids'])), 'is_numeric');
        if (empty($ids)) {
            $args = array('posts_per_page' => 4, 'orderby' => 'date', 'order' => 'DESC');
        } else {
            $args = array(
                    'post__in' => $ids
            );
        }
        $args = array_merge($args, array('meta_query' => array(array('key' => THEME_NAME . '_offer_type', 'value' => 'offer_service', 'compare' => '='))));
        $_posts = $this->wp_query($args);
        $data = array('_posts' => &$_posts, 'headline' => &$atts['headline']);
        $output = Tesla_View::render('template_parts/tesla_shortcode-offers_service.php', $data);
        wp_reset_postdata();
        return $output;
    }

    public function tesla_offers_about_services($atts) {
        $atts = shortcode_atts(array(
                'offer_ids' => '',
                'headline' => ''
                        ), $atts);
        $ids = array_filter(array_map('trim', explode(',', $atts['offer_ids'])), 'is_numeric');
        if (empty($ids)) {
            $args = array('posts_per_page' => 4, 'orderby' => 'date', 'order' => 'DESC');
        } else {
            $args = array(
                    'post__in' => $ids
            );
        }
        $args = array_merge($args, array('meta_query' => array(array('key' => THEME_NAME . '_offer_type', 'value' => 'offer_about_service', 'compare' => '='))));
        $_posts = $this->wp_query($args);
        $data = array('_posts' => &$_posts, 'headline' => &$atts['headline']);
        $output = Tesla_View::render('template_parts/tesla_shortcode-offers_about_services.php', $data);
        wp_reset_postdata();
        return $output;
    }

    public function tesla_offers_generic($atts) {
        $atts = shortcode_atts(array(
                'offer_ids' => '',
                'headline' => ''
                        ), $atts);
        $ids = array_filter(array_map('trim', explode(',', $atts['offer_ids'])), 'is_numeric');
        if (empty($ids)) {
            $args = array('posts_per_page' => 4, 'orderby' => 'date', 'order' => 'DESC');
        } else {
            $args = array(
                    'post__in' => $ids
            );
        }
        $args = array_merge($args, array('meta_query' => array(array('key' => THEME_NAME . '_offer_type', 'value' => 'offer_generic', 'compare' => '='))));
        $_posts = $this->wp_query($args);
        $data = array('_posts' => &$_posts, 'headline' => &$atts['headline']);
        $output = Tesla_View::render('template_parts/tesla_shortcode-offers_generic.php', $data);
        wp_reset_postdata();
        return $output;
    }

    protected function __init_shortcodes() {
        add_shortcode('tesla_offers_strip', array($this, 'tesla_offers_strip'));
        add_shortcode('tesla_offers_hot', array($this, 'tesla_offers_hot'));
        add_shortcode('tesla_offers_service', array($this, 'tesla_offers_service'));
        add_shortcode('tesla_offers_about_services', array($this, 'tesla_offers_about_services'));
        add_shortcode('tesla_offers_generic', array($this, 'tesla_offers_generic'));
    }

    protected function __init_post_type() {
        $labels = array(
                'name' => __('Offers', 'hudson'),
                'singular_name' => __('Offer', 'hudson'),
                'add_new' => __('Add New', 'hudson'),
                'add_new_item' => __('Add New Offer', 'hudson'),
                'edit_item' => __('Edit Offer', 'hudson'),
                'new_item' => __('New Offer', 'hudson'),
                'all_items' => __('All Offers', 'hudson'),
                'view_item' => __('View Offer', 'hudson'),
                'search_items' => __('Search Offers', 'hudson'),
                'not_found' => __('No offers found', 'hudson'),
                'not_found_in_trash' => __('No offers found in Trash', 'hudson'),
                'parent_item_colon' => '',
                'menu_name' => __('Offers', 'hudson')
        );

        $args = array(
                'labels' => $labels,
                'public' => false,
                'publicly_queryable' => false,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => array('slug' => $this->post_type),
                'capability_type' => 'post',
                'menu_icon' => TEMPLATEURI . '/images/special-offer.png',
                'has_archive' => false,
                'hierarchical' => false,
                'exclude_from_search' => true,
                'menu_position' => null,
                'supports' => array('title', 'editor', 'thumbnail', 'excerpt')
        );

        register_post_type($this->post_type, $args);
    }

}

$tesla_class_registry['Tesla_Offers'] = new Tesla_Offers;