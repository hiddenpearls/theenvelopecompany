<?php

class TeslaWidgetSubscribe extends WP_Widget {

    private $defaults;
    private $state = 'normal';

    /**
     * Register widget with WordPress.
     */
    public function __construct() {
        parent::__construct(
                'tesla-subscribe', // Base ID
                'Tesla Subscribe Widget', // Name
                array('description' => __('Tesla Subscribe Widget', 'hudson'),) // Args
        );
        $this->defaults = array(
            'title' => __('New title', 'hudson'),
            'placeholder-email' => __('Name', 'hudson'),
            'placeholder-name' => __('Email', 'hudson'),
            'submit-text' => __('Subscribe', 'hudson')
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget($args, $instance) {
        extract($args);

        $title = apply_filters('widget_title', $instance['title']);

        echo $before_widget;
        if (!empty($title))
            echo $before_title . $title . $after_title;
        ?>  
        <form id="tt_subscribe_form" class="subscribe_form" method="POST">
            <div id="tt_subscribe_form_result" class="alert hide"></div>
            <input name="tesla_subscription_name" class="newsletter_line" type="text" placeholder="<?php echo $this->get_value('placeholder-name', $instance, $this->defaults) ?>" />
            <input data-tt-subscription-required data-tt-subscription-type="email" name="tesla_subscription_email" class="newsletter_line" type="text" placeholder="<?php echo $this->get_value('placeholder-email', $instance, $this->defaults) ?>" />
            <input type="submit" class="newsletter_send" value="<?php echo $this->get_value('submit-text', $instance, $this->defaults) ?>">
        </form>
        <?php
        echo $after_widget;
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form($instance) {
        ?>
        <p>
            <label for="<?php echo $this->get_field_name('title'); ?>"><?php _e('Title:', 'hudson'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($this->get_value('title', $instance)); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_name('placeholder-name'); ?>"><?php _e('Placeholder for name input:', 'hudson'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('placeholder-name'); ?>" name="<?php echo $this->get_field_name('placeholder-name'); ?>" type="text" value="<?php echo esc_attr($this->get_value('placeholder-name', $instance)); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_name('placeholder-email'); ?>"><?php _e('Placeholder for email input:', 'hudson'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('placeholder-email'); ?>" name="<?php echo $this->get_field_name('placeholder-email'); ?>" type="text" value="<?php echo esc_attr($this->get_value('placeholder-email', $instance)); ?>" />
        </p>   
        <p>
            <label for="<?php echo $this->get_field_name('submit-text'); ?>"><?php _e('Submit button text:', 'hudson'); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id('submit-text'); ?>" name="<?php echo $this->get_field_name('submit-text'); ?>" type="text" value="<?php echo esc_attr($this->get_value('submit-text', $instance)); ?>" />
        </p>
        <?php
    }

    protected function get_value($key, &$instance, &$defaults = array()) {
        if (isset($instance[$key]))
            return $instance[$key];
        if (empty($defaults))
            return NULL;
        return $defaults[$key];
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title']) ) ? strip_tags($new_instance['title']) : '';
        $instance['placeholder-name'] = (!empty($new_instance['placeholder-name']) ? strip_tags($new_instance['placeholder-name']) : '');
        $instance['placeholder-email'] = (!empty($new_instance['placeholder-email']) ? strip_tags($new_instance['placeholder-email']) : '');
        $instance['submit-text'] = (!empty($new_instance['submit-text']) ? strip_tags($new_instance['submit-text']) : '');

        return $instance;
    }

}

function register_tesla_widget_subscribe() {
    register_widget('TeslaWidgetSubscribe');
}

add_action('widgets_init', 'register_tesla_widget_subscribe');