<?php

class Tesla_Team {

    protected $post_type = 'team';

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

    public function tesla_team_show($atts) {
        $atts = shortcode_atts(array(
                'ids' => '',
                'headline' => ''
                        ), $atts);
        $ids = array_filter(array_map('trim', explode(',', $atts['ids'])), 'is_numeric');
        if (empty($ids)) {
            $args = array('posts_per_page' => 999, 'orderby' => 'date', 'order' => 'DESC');
        } else {
            $args = array(
                    'post__in' => $ids
            );
        }
        $_posts = $this->wp_query($args);
        $data = array('_posts' => &$_posts, 'headline' => &$atts['headline']);
        $output = Tesla_View::render('template_parts/tesla_shortcode-team_show.php', $data);
        wp_reset_postdata();
        return $output;
    }

    protected function __init_shortcodes() {
        add_shortcode('tesla_team_show', array($this, 'tesla_team_show'));
    }

    protected function __init_post_type() {
        $labels = array(
                'name' => __('Team', 'hudson'),
                'singular_name' => __('Team', 'hudson'),
                'add_new' => __('Add New', 'hudson'),
                'add_new_item' => __('Add New Team Member', 'hudson'),
                'edit_item' => __('Edit Team Member', 'hudson'),
                'new_item' => __('New Team Member', 'hudson'),
                'all_items' => __('All Team Members', 'hudson'),
                'view_item' => __('View Team Member', 'hudson'),
                'search_items' => __('Search Team Members', 'hudson'),
                'not_found' => __('No team members found', 'hudson'),
                'not_found_in_trash' => __('No team members found in Trash', 'hudson'),
                'parent_item_colon' => '',
                'menu_name' => __('Team', 'hudson')
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
                'menu_icon' => TEMPLATEURI . '/images/team.png',
                'has_archive' => false,
                'hierarchical' => false,
                'menu_position' => null,
                'supports' => array('title', 'editor', 'thumbnail', 'excerpt')
        );

        register_post_type($this->post_type, $args);
    }

}

$tesla_class_registry['Tesla_Team'] = new Tesla_Team;