<?php
if(!class_exists('Tesla_twitter_widget')){
    class Tesla_twitter_widget extends WP_Widget {

        function __construct() {
            parent::__construct(
                    'tesla_twitter',
                    '['.THEME_PRETTY_NAME.'] Twitter',
                    array(
                        'description'   => _x('Twitter feed.','dashboard-widgets','TeslaFramework'),
                        'classname'     => 'tesla-twitter-widget',
                    )
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
        function widget($args, $instance) {
            extract($args);
            $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
            $user = empty($instance['user']) ? 'teslathemes' : $instance['user'];
            if (empty($instance['number']) || !$number = absint($instance['number']))
                $number = 3;

            print $before_widget;
            if (!empty($title))
                print $before_title . $title . $after_title;

            echo tt_twitter_generate_output($user, $number, '', '', false,false, $instance);

            print $after_widget;
        }

        /**
         * Back-end widget form.
         *
         * @see WP_Widget::form()
         *
         * @param array $instance Previously saved values from database.
         */
        function form($instance) {
            $instance = wp_parse_args((array) $instance, 
                array(
                    'title'             => _x('Twitter','dashboard-widgets','TeslaFramework'),
                    'user'              => '',
                    'number'            => 3,
                    'consumerkey'       => '',
                    'consumersecret'    => '',
                    'accesstoken'       => '',
                    'accesstokensecret' => ''
                    )
            );
            $title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
            $number = isset($instance['number']) ? absint($instance['number']) : 3;
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('title')); ?>">
                    <?php _ex('Title:','dashboard-widgets','TeslaFramework'); ?>
                    <input id="<?php echo esc_attr($this->get_field_id('title')); ?>" class="widefat" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
                </label> 
            </p>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('user')); ?>">
                    <?php _ex('Twitter user:','dashboard-widgets','TeslaFramework'); ?>
                    <input id="<?php echo esc_attr($this->get_field_id('user')); ?>" class="widefat" name="<?php echo esc_attr($this->get_field_name('user')); ?>" type="text" value="<?php echo esc_attr($instance['user']); ?>" />
                </label> 
            </p>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('number')); ?>">
                    <?php _ex('Number of tweets to show:','dashboard-widgets','TeslaFramework'); ?>
                    <input id="<?php echo esc_attr($this->get_field_id('number')); ?>" class="widefat" name="<?php echo esc_attr($this->get_field_name('number')); ?>" type="number" value="<?php echo esc_attr($number); ?>" size="3" />
                </label>                
            </p>
            <p><?php _ex('Visit','dashboard-widgets','TeslaFramework') ?> <a href='https://dev.twitter.com/apps/new' target='_blank'><?php _ex('Twitter Apps','dashboard-widgets','TeslaFramework') ?></a> , <?php _ex("create your App; press 'Create Access token' at the bottom; insert the following from the 'Keys and Access Tokens' tab.",'dashboard-widgets','TeslaFramework') ?></p>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('consumerkey')); ?>">
                    <?php _ex('Consumer Key:','dashboard-widgets','TeslaFramework'); ?>
                    <input id="<?php echo esc_attr($this->get_field_id('consumerkey')); ?>" class="widefat" name="<?php echo esc_attr($this->get_field_name('consumerkey')); ?>" type="text" value="<?php echo esc_attr($instance['consumerkey']); ?>" />
                </label> 
            </p>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('consumersecret')); ?>">
                    <?php _ex('Consumer Secret:','dashboard-widgets','TeslaFramework'); ?>
                    <input id="<?php echo esc_attr($this->get_field_id('consumersecret')); ?>" class="widefat" name="<?php echo esc_attr($this->get_field_name('consumersecret')); ?>" type="text" value="<?php echo esc_attr($instance['consumersecret']); ?>" />
                </label> 
            </p>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('accesstoken')); ?>">
                    <?php _ex('Access Token:','dashboard-widgets','TeslaFramework'); ?>
                    <input id="<?php echo esc_attr($this->get_field_id('accesstoken')); ?>" class="widefat" name="<?php echo esc_attr($this->get_field_name('accesstoken')); ?>" type="text" value="<?php echo esc_attr($instance['accesstoken']); ?>" />
                </label> 
            </p>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id('accesstokensecret')); ?>">
                    <?php _ex('Access Token Secret:','dashboard-widgets','TeslaFramework'); ?>
                    <input id="<?php echo esc_attr($this->get_field_id('accesstokensecret')); ?>" class="widefat" name="<?php echo esc_attr($this->get_field_name('accesstokensecret')); ?>" type="text" value="<?php echo esc_attr($instance['accesstokensecret']); ?>" />
                </label> 
            </p>
            <?php
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
        function update($new_instance, $old_instance) {
            $instance = array();
            $instance['title']              = strip_tags($new_instance['title']);
            $instance['user']               = strip_tags($new_instance['user']);
            $instance['number']             = (int)strip_tags($new_instance['number']);
            $instance['consumerkey']        = strip_tags( $new_instance['consumerkey'] );
            $instance['consumersecret']     = strip_tags( $new_instance['consumersecret'] );
            $instance['accesstoken']        = strip_tags( $new_instance['accesstoken'] );
            $instance['accesstokensecret']  = strip_tags( $new_instance['accesstokensecret'] );

            return $instance;
        }
    }

    add_action('widgets_init', create_function('', 'return register_widget("Tesla_twitter_widget");'));
}