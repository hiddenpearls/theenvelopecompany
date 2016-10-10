<?php
/*
  Widget: Instagram Feed
 */
if(!class_exists('Tesla_instagram')){
	class Tesla_instagram extends WP_Widget {

		function __construct() {
			parent::__construct(
				'tesla_instagram',
				'['.THEME_PRETTY_NAME.'] Instagram',
				array(
					'description' => _x('Instagram feed.', 'dashboard-widgets','TeslaFramework'),
					'classname' => 'tesla-instagram-widget',
				)
			);
		}

		function widget($args, $instance) {
			wp_enqueue_style( 'tt-fw-widgets' , TT_FW . '/static/css/widgets.css' );
			extract($args, EXTR_SKIP);
			$title = apply_filters('widget_title', empty($instance['title']) ? '' : $instance['title'], $instance, $this->id_base);
			print $before_widget;
			if (!empty($title))
				print $before_title . $title . $after_title;

			//call helper function
			echo tt_instagram_generate_output($instance['username'], $instance['cache_h'] , $instance['nr_images'] );

			print $after_widget;
		}

		function form($instance) {
			$instance = wp_parse_args((array) $instance, array(
				'title'		=> _x('Instagram','dashboard-widgets','TeslaFramework'),
				'nr_images'	=> 3,
				'username'	=> '',
				'cache_h'	=> 1
				));
			?>

			<p><label><?php _ex('Title:','dashboard-widgets','TeslaFramework') ?></label>
				<input type="text" name="<?php echo esc_attr($this->get_field_name( 'title' ))?>" id="<?php echo esc_attr($this->get_field_id( 'title' ))?>" value="<?php echo esc_attr($instance['title'])?>" class="widefat" />
			</p>
			<p><label><?php _ex('Image nr:','dashboard-widgets','TeslaFramework') ?></label>
				<input type="number" min="0" size="2" name="<?php echo esc_attr($this->get_field_name( 'nr_images' ))?>" id="<?php echo esc_attr($this->get_field_id( 'nr_images' ))?>" value="<?php echo esc_attr($instance['nr_images'])?>" class="widefat" />
			</p>
			<p><label><?php _ex('Username:','dashboard-widgets','TeslaFramework') ?></label>
				<input type="text" name="<?php echo esc_attr($this->get_field_name( 'username' ))?>" id="<?php echo esc_attr($this->get_field_id( 'username' ))?>" value="<?php echo esc_attr($instance['username'])?>" class="widefat" />
			</p>
			<p><label><?php _ex('Cache Hours:','dashboard-widgets','TeslaFramework') ?></label>
				<input type="number" min="0" name="<?php echo esc_attr($this->get_field_name( 'cache_h' ))?>" id="<?php echo esc_attr($this->get_field_id( 'cache_h' ))?>" value="<?php echo esc_attr($instance['cache_h'])?>" class="widefat" />
			</p>
			<?php
		}

		function update($new_instance, $old_instance) {
			$instance = array();
			$instance['title']		= ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
			$instance['nr_images']	= ( ! empty( $new_instance['nr_images'] ) ) ? strip_tags( (int)$new_instance['nr_images'] ) : 0;
			$instance['username']	= ( ! empty( $new_instance['username'] ) ) ? strip_tags( $new_instance['username'] ) : '';
			$instance['cache_h']	= ( ! empty( $new_instance['cache_h'] ) ) ? strip_tags( (float)$new_instance['cache_h'] ) : 0;
			return $instance;
		}
		
	}

	add_action('widgets_init', create_function('', 'return register_widget("Tesla_instagram");'));
}