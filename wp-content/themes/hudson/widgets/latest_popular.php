<?php
/*
  Plugin Name: Latest/Popular Posts
  Plugin URI: http://teslathemes.com/
  Description: Show latest and popular posts tabs
  Author: TeslaThemes
  Version: 1
  Author URI: http://teslathemes.com/
 */

class LatestPopularWidget extends WP_Widget{
    
    public $posts_number = 2;
    
    function __construct()
    {
        $widget_ops = array('classname' => 'LatestPopularWidget', 'description' => __('Displays Posts tabs.','hudson'));
        parent::__construct('LatestPopularWidget', '['.THEME_PRETTY_NAME.'] ' . __('Latest and Popular Posts','hudson'), $widget_ops);
    }
    
    function form($instance)
    {
        $instance = wp_parse_args((array) $instance, array('nr_posts' => $this->posts_number));
        $nr_posts = $instance['nr_posts'];
        ?>
        <p><label for="<?php echo esc_attr($this->get_field_id('nr_posts')); ?>"><?php _e('Nr of posts to show:','hudson') ?> <input class="widefat" id="<?php echo esc_attr($this->get_field_id('nr_posts')); ?>" name="<?php echo esc_attr($this->get_field_name('nr_posts')); ?>" type="title" value="<?php echo esc_attr($nr_posts); ?>" /></label></p>
        <?php
    }
    
    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['nr_posts'] = $new_instance['nr_posts'];
        return $instance;
    }
    
    function widget($args, $instance)
    {
        extract($args, EXTR_SKIP);
        $nr_posts = empty($instance['nr_posts']) ? $this->posts_number : $instance['nr_posts'];
        print $before_widget;
        require_once (tt_wf_get_widgets_directory() . '/views/latest_posts_widget.php');
        print $after_widget;
    }
    
}
add_action('widgets_init', create_function('', 'return register_widget("LatestPopularWidget");'));