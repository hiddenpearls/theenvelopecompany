<?php
/*
  Plugin Name: Sidebar Ads
  Plugin URI: http://teslathemes.com/
  Description: Show sidebar ads
  Author: TeslaThemes
  Version: 1
  Author URI: http://teslathemes.com/
 */

class SidebarAdsWidget extends WP_Widget {

    public $def_val = '';
    
    function __construct() {
        $widget_ops = array('classname' => 'SidebarAdsWidget', 'description' => 'Displays sidebar ads.');
        parent::__construct('SidebarAdsWidget', '[' . THEME_PRETTY_NAME . '] Sidebar Ads', $widget_ops);
    }

    function form($instance) {
        $instance = wp_parse_args(
                (array) $instance,
                array(
                    'ad_slot_1' => $this->def_val,
                    'ad_slot_2' => $this->def_val,
                    'ad_slot_3' => $this->def_val,
                    'ad_slot_4' => $this->def_val,
                )
        );
        $ad_slot_1 = $instance['ad_slot_1'];
        $ad_slot_2 = $instance['ad_slot_2'];
        $ad_slot_3 = $instance['ad_slot_3'];
        $ad_slot_4 = $instance['ad_slot_4'];
        ?>
        <p><label for="<?php echo $this->get_field_id('ad_slot_1'); ?>">Ad Slot #1: <textarea class="widefat" style="max-width: 100%" id="<?php echo $this->get_field_id('ad_slot_1'); ?>" name="<?php echo $this->get_field_name('ad_slot_1'); ?>" type="title"><?php echo esc_attr($ad_slot_1); ?></textarea></label></p>
        <p><label for="<?php echo $this->get_field_id('ad_slot_2'); ?>">Ad Slot #2: <textarea class="widefat" style="max-width: 100%" id="<?php echo $this->get_field_id('ad_slot_2'); ?>" name="<?php echo $this->get_field_name('ad_slot_2'); ?>" type="title"><?php echo esc_attr($ad_slot_2); ?></textarea></label></p>
        <p><label for="<?php echo $this->get_field_id('ad_slot_3'); ?>">Ad Slot #3: <textarea class="widefat" style="max-width: 100%" id="<?php echo $this->get_field_id('ad_slot_3'); ?>" name="<?php echo $this->get_field_name('ad_slot_3'); ?>" type="title"><?php echo esc_attr($ad_slot_3); ?></textarea></label></p>
        <p><label for="<?php echo $this->get_field_id('ad_slot_4'); ?>">Ad Slot #4: <textarea class="widefat" style="max-width: 100%" id="<?php echo $this->get_field_id('ad_slot_4'); ?>" name="<?php echo $this->get_field_name('ad_slot_4'); ?>" type="title"><?php echo esc_attr($ad_slot_4); ?></textarea></label></p>
        <?php
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['ad_slot_1'] = $new_instance['ad_slot_1'];
        $instance['ad_slot_2'] = $new_instance['ad_slot_2'];
        $instance['ad_slot_3'] = $new_instance['ad_slot_3'];
        $instance['ad_slot_4'] = $new_instance['ad_slot_4'];
        return $instance;
    }

    function widget($args, $instance) {
        extract($args, EXTR_SKIP);
        $ad_slot_1 = empty($instance['ad_slot_1']) ? $this->def_val : $instance['ad_slot_1'];
        $ad_slot_2 = empty($instance['ad_slot_2']) ? $this->def_val : $instance['ad_slot_2'];
        $ad_slot_3 = empty($instance['ad_slot_3']) ? $this->def_val : $instance['ad_slot_3'];
        $ad_slot_4 = empty($instance['ad_slot_4']) ? $this->def_val : $instance['ad_slot_4'];
        echo $before_widget;
        require_once (tt_wf_get_widgets_directory() . '/views/sidebar_ads_widget.php');
        echo $after_widget;
    }

}

add_action('widgets_init', create_function('', 'return register_widget("SidebarAdsWidget");'));
