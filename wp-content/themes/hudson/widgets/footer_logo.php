<?php

/*
  Plugin Name: Footer Logo
  Plugin URI: http://teslathemes.com/
  Description: Show your company/product logo in footer
  Author: TeslaThemes
  Version: 1
  Author URI: http://teslathemes.com/
 */

class FooterLogoWidget extends WP_Widget{
    
    function __construct()
    {
        $widget_ops = array('classname' => 'FooterLogoWidget', 'description' => __('Displays Footer Logo.','hudson'));
        parent::__construct('FooterLogoWidget', '['.THEME_PRETTY_NAME.'] ' . __('Footer Logo','hudson'), $widget_ops);
    }
    
    function form($instance)
    {
        $instance = wp_parse_args((array) $instance, array('footer_logo_img' => ''));
        $footer_logo_img = $instance['footer_logo_img'];
        $src = (wp_get_attachment_url($footer_logo_img)) ? wp_get_attachment_url($footer_logo_img) : '';
        ?>
        <p>
            <div id="xxxx">
                <div><?php _e('Select Footer Logo:','hudson') ?></div>
                <img id="wf_footer_logo" data-src="holder.js/80x80/auto" alt="Footer Logo" src="<?php echo esc_attr($src) ?>" style="cursor: pointer;"/>
                <input class="widefat" type="hidden" id="<?php echo esc_attr($this->get_field_id('footer_logo_img')); ?>" name="<?php echo esc_attr($this->get_field_name('footer_logo_img')); ?>" value="<?php echo esc_attr($footer_logo_img); ?>" />
            </div>
        </p>
        <?php
    }
    
    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['footer_logo_img'] = $new_instance['footer_logo_img'];
        return $instance;
    }
    
    function widget($args, $instance)
    {
        extract($args, EXTR_SKIP);
        $footer_logo_img = empty($instance['footer_logo_img']) ? '' : $instance['footer_logo_img'];
        print $before_widget;
        require_once (tt_wf_get_widgets_directory() . '/views/footer_logo.php');
        print $after_widget;
    }
}
add_action('widgets_init', create_function('', 'return register_widget("FooterLogoWidget");'));
?>
