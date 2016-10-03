<?php

/*
  Plugin Name: Subscribe Form
  Plugin URI: http://teslathemes.com/
  Description: Show subscribe form
  Author: TeslaThemes
  Version: 1
  Author URI: http://teslathemes.com/
 */

class SubscribeFormWidget extends WP_Widget{
    
    function __construct()
    {
        $widget_ops = array('classname' => 'SubscribeFormWidget', 'description' => 'Displays Subscription Form.');
        parent::__construct('SubscribeFormWidget', '['.THEME_PRETTY_NAME.'] Subscription Form', $widget_ops);
    }
    
    function form($instance)
    {
        $instance = wp_parse_args((array) $instance, array(
            'widget_subscribe_heading' => '',
            'widget_email_placeholder' => '',));
        $widget_subscribe_heading = $instance['widget_subscribe_heading'];
        $widget_email_placeholder = $instance['widget_email_placeholder'];
        ?>
        <div>
            <p>
            <span>Enter Subscription Heading</span>
            <input class="widefat" id="<?php echo $this->get_field_id('widget_subscribe_heading'); ?>" 
                   name="<?php echo $this->get_field_name('widget_subscribe_heading'); ?>" 
                   value="<?php echo esc_attr($widget_subscribe_heading); ?>" />
            </p>
            <p>
            <span>Enter Email Placeholder</span>
            <input class="widefat" id="<?php echo $this->get_field_id('widget_email_placeholder'); ?>" 
                   name="<?php echo $this->get_field_name('widget_email_placeholder'); ?>" 
                   value="<?php echo esc_attr($widget_email_placeholder); ?>" />
            </p>
        </div>
        <?php
    }
    
    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['widget_subscribe_heading'] = $new_instance['widget_subscribe_heading'];
        $instance['widget_email_placeholder'] = $new_instance['widget_email_placeholder'];
        return $instance;
    }
    
    function widget($args, $instance)
    {
        extract($args, EXTR_SKIP);
        extract($instance, EXTR_SKIP);
        echo $before_widget;
        require_once (tt_wf_get_widgets_directory() . '/views/subscribe_view.php');
        echo $after_widget;
    }
}
add_action('widgets_init', create_function('', 'return register_widget("SubscribeFormWidget");'));

?>