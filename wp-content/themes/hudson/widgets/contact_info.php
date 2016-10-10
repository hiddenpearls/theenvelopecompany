<?php

/*
  Plugin Name: Contact Info
  Plugin URI: http://teslathemes.com/
  Description: Show product/business contact info
  Author: TeslaThemes
  Version: 1
  Author URI: http://teslathemes.com/
 */

class ContactInfoWidget extends WP_Widget{
    
    function __construct()
    {
        $widget_ops = array('classname' => 'ContactInfoWidget', 'description' => __('Displays Contact Info.','hudson'));
        parent::__construct('ContactInfoWidget', '['.THEME_PRETTY_NAME.'] ' . __('Contact Info','hudson'), $widget_ops);
    }
    
    function form($instance)
    {
        $instance = wp_parse_args((array) $instance, array('widget_contact_heading' => ''));
        $widget_contact_heading = $instance['widget_contact_heading'];
        ?>
        <p>
            <div><?php _e('Insert Contact Info Heading','hudson') ?></div>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('widget_contact_heading')); ?>" name="<?php echo esc_attr($this->get_field_name('widget_contact_heading')); ?>" value="<?php echo esc_attr($widget_contact_heading); ?>" />
        </p>
        <?php
    }
    
    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['widget_contact_heading'] = $new_instance['widget_contact_heading'];
        return $instance;
    }
    
    function widget($args, $instance)
    {
        extract($args, EXTR_SKIP);
        $widget_contact_heading = empty($instance['widget_contact_heading']) ? '' : $instance['widget_contact_heading'];
        print $before_widget;
        require_once (tt_wf_get_widgets_directory() . '/views/contact_info_view.php');
        print $after_widget;
    }
    
}
add_action('widgets_init', create_function('', 'return register_widget("ContactInfoWidget");'));