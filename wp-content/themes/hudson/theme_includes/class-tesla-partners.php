<?php

class Tesla_Partners {

    protected $post_type = 'partner';

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

    public function tesla_list_partners($atts) {
        $atts = shortcode_atts(array(
                'orderby' => 'date',
                'order' => 'DESC'
                        ), $atts);
        $posts = $this->wp_query($atts);
        ob_start();
        if ($posts->have_posts()) {
            while ($posts->have_posts()) {
                $posts->the_post();
                get_template_part('template_parts/list_partners', 'loop');
            } // end of the loop.  
        }
        wp_reset_postdata();
        global $tesla_trans_var;
        $tesla_trans_var['looped_partners'] = ob_get_clean();
        ob_start();
        get_template_part('template_parts/list_partners', 'container');
        return ob_get_clean();
    }

    protected function __init_shortcodes() {
        add_shortcode('tesla_list_partners', array($this, 'tesla_list_partners'));
    }

    protected function __init_post_type() {
        $labels = array(
                'name' => __('Partners', 'hudson'),
                'singular_name' => __('Partner', 'hudson'),
                'add_new' => __('Add New', 'hudson'),
                'add_new_item' => __('Add New Partner', 'hudson'),
                'edit_item' => __('Edit Partner', 'hudson'),
                'new_item' => __('New Partner', 'hudson'),
                'all_items' => __('All Partners', 'hudson'),
                'view_item' => __('View Partner', 'hudson'),
                'search_items' => __('Search Partners', 'hudson'),
                'not_found' => __('No partners found', 'hudson'),
                'not_found_in_trash' => __('No partners found in Trash', 'hudson'),
                'parent_item_colon' => '',
                'menu_name' => __('Partners', 'hudson')
        );

        $args = array(
                'labels' => $labels,
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'query_var' => true,
                'rewrite' => array('slug' => $this->post_type),
                'capability_type' => 'post',
                'menu_icon' => TEMPLATEURI . '/images/handshake.png',
                'has_archive' => true,
                'hierarchical' => false,
                'menu_position' => null,
                'supports' => array('title', 'editor', 'author', 'thumbnail', 'excerpt')
        );

        register_post_type($this->post_type, $args);
    }

}

$tesla_class_registry['Tesla_Partners'] = new Tesla_Partners;