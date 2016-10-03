<?php

class login_wid extends WP_Widget {
	
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
		add_action( 'wp_head', array( $this, 'custom_styles_afo' ) );
		parent::__construct(
	 		'login_wid',
			'Login Widget AFO',
			array( 'description' => __( 'This is a simple login form in the widget.', 'login-sidebar-widget' ), )
		);
	 }

	public function widget( $args, $instance ) {
		extract( $args );
		
		$wid_title = apply_filters( 'widget_title', $instance['wid_title'] );
		
		echo $args['before_widget'];
		if ( ! empty( $wid_title ) )
			echo $args['before_title'] . $wid_title . $args['after_title'];
			$this->loginForm( $args['widget_id'] );
		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['wid_title'] = strip_tags( $new_instance['wid_title'] );
		return $instance;
	}


	public function form( $instance ) {
		$wid_title = '';
		if(!empty($instance[ 'wid_title' ])){
			$wid_title = esc_html($instance[ 'wid_title' ]);
		}
		?>
		<p><label for="<?php echo $this->get_field_id('wid_title'); ?>"><?php _e('Title','login-sidebar-widget'); ?> </label>
		<input class="widefat" id="<?php echo $this->get_field_id('wid_title'); ?>" name="<?php echo $this->get_field_name('wid_title'); ?>" type="text" value="<?php echo $wid_title; ?>" />
		</p>
		<?php 
	}
	
	public function add_remember_me(){
		$login_afo_rem = get_option('login_afo_rem');
		if($login_afo_rem == 'Yes'){
			echo '<label for="remember"> '.__('Remember Me','login-sidebar-widget').'</label>  <input type="checkbox" name="remember" value="Yes" />';
		}
	}
	
	public function add_extra_links(){
		$login_afo_forgot_pass_link = get_option('login_afo_forgot_pass_link');
		$login_afo_register_link = get_option('login_afo_register_link');
		if($login_afo_forgot_pass_link){
			echo '<a href="'. esc_url( get_permalink($login_afo_forgot_pass_link) ).'">'.__('Lost Password?','login-sidebar-widget').'</a>';
		}
		
		if( $login_afo_forgot_pass_link and $login_afo_register_link ){
			echo ' | ';
		}
		
		if($login_afo_register_link){
			echo '<a href="'. esc_url( get_permalink($login_afo_register_link) ) .'">'.__('Register','login-sidebar-widget').'</a>';
		}
	}
	
	public function curPageURL() {
	 $pageURL = 'http';
	 if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
	 $pageURL .= "://";
	 if (isset($_SERVER["SERVER_PORT"]) and $_SERVER["SERVER_PORT"] != "80") {
	  $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
	 } else {
	  $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
	 }
	 return $pageURL;
	}


	public function loginForm( $wid_id = '' ){
		if(!session_id()){
			@session_start();
		}
		global $post;
		$redirect_page = get_option('redirect_page');
		$redirect_page_url = get_option('redirect_page_url');
		$logout_redirect_page = get_option('logout_redirect_page');
		$link_in_username = get_option('link_in_username');
		$default_login_form_hooks = get_option('default_login_form_hooks');
		
		if($redirect_page_url){
			$redirect = $redirect_page_url;
		} else {
			if($redirect_page){
				$redirect = get_permalink($redirect_page);
			} else {
				$redirect = $this->curPageURL();
			}
		}
		
		if($logout_redirect_page){
			$logout_redirect_page = get_permalink($logout_redirect_page);
		} else {
			$logout_redirect_page = $this->curPageURL();
		}
		$this->load_script();
		if(!is_user_logged_in()){
		?>
		<div id="log_forms" class="log_forms <?php echo $wid_id;?>">
        <?php $this->error_message();?>
		<form name="login" id="login" method="post" action="">
		<input type="hidden" name="option" value="afo_user_login" />
		<input type="hidden" name="redirect" value="<?php echo esc_url( $redirect ); ?>" />
		<div class="log-form-group">
			<label for="username"><?php _e('Username','login-sidebar-widget');?> </label>
			<input type="text" name="user_username" required="required"/>
		</div>
		<div class="log-form-group">
			<label for="password"><?php _e('Password','login-sidebar-widget');?> </label>
			<input type="password" name="user_password" required="required"/>
		</div>
        <?php do_action('login_afo_form');?>
        <?php $default_login_form_hooks == 'Yes'?do_action('login_form'):'';?>
		<div class="log-form-group">
			<?php $this->add_remember_me();?>
		</div>
		<div class="log-form-group"><input name="login" type="submit" value="<?php _e('Login','login-sidebar-widget');?>" /></div>
		<div class="log-form-group extra-links">
			<?php $this->add_extra_links();?>
		</div>
		</form>
		</div>
		<?php 
		} else {
		$current_user = wp_get_current_user();
     	
		if($link_in_username){
			$link_with_username = '<a href="'. esc_url( get_permalink($link_in_username) ) .'">'.__('Howdy','login-sidebar-widget').', '.$current_user->display_name.'</a>';
		} else {
			$link_with_username = __('Howdy','login-sidebar-widget').', '.$current_user->display_name;
		}
		?>
        <div class="logged-in"><?php echo $link_with_username;?> | <a href="<?php echo wp_logout_url( $logout_redirect_page ); ?>" title="<?php _e('Logout','login-sidebar-widget');?>"><?php _e('Logout','login-sidebar-widget');?></a></div>
		<?php 
		}
	}
	
	public function error_message(){
		if(isset($_SESSION['msg']) and $_SESSION['msg']){
			echo '<div class="'.$_SESSION['msg_class'].'">'.$_SESSION['msg'].$this->message_close_button().'</div>';
			unset($_SESSION['msg']);
			unset($_SESSION['msg_class']);
		}
	}
	
	public function message_close_button(){
		$cb = '<a href="javascript:void(0);" onclick="closeMessage();" class="close_button_afo">x</a>';
		return $cb;
	}
	
	public function register_plugin_styles() {
		wp_enqueue_style( 'style_login_widget', plugins_url( 'login-sidebar-widget/style_login_widget.css' ) );
	}
	
	public function custom_styles_afo(){
		echo '<style>';
		echo stripslashes(get_option('custom_style_afo'));
		echo '</style>';
	}
	
	public function load_script(){?>
		<script type="text/javascript">
			function closeMessage(){jQuery('.error_wid_login').hide();}
		</script>
	<?php }
	
} 

function login_validate(){
	$lla = new login_log_adds;
	
	if( isset($_POST['option']) and $_POST['option'] == "afo_user_login"){
		if(!session_id()){
			session_start();
		}
		global $post;
		
		if($_POST['user_username'] != "" and $_POST['user_password'] != ""){
			$creds = array();
			$creds['user_login'] = sanitize_text_field($_POST['user_username']);
			$creds['user_password'] = sanitize_text_field($_POST['user_password']);
			if(sanitize_text_field($_POST['remember']) == 'Yes'){
				$remember = true;
			} else {
				$remember = false;
			}
			$creds['remember'] = $remember;
			$user = wp_signon( $creds, true );
			if(isset($user->ID) and $user->ID != ''){
				wp_set_auth_cookie($user->ID, $remember);
				$lla->log_add($_SERVER['REMOTE_ADDR'], 'Login success', date("Y-m-d H:i:s"), 'success');
				wp_redirect( $_POST['redirect'] );
				exit;
			} else{
				$_SESSION['msg_class'] = 'error_wid_login';
				$_SESSION['msg'] = __(get_login_error_message_text($user),'login-sidebar-widget');
				do_action('afo_login_log_front', $user);
			}
		} else {
			$_SESSION['msg_class'] = 'error_wid_login';
			$_SESSION['msg'] = __('Username or password is empty!','login-sidebar-widget');
			$lla->log_add($_SERVER['REMOTE_ADDR'], 'Username or password is empty', date("Y-m-d H:i:s"), 'failed');
		}
	}
}

add_action( 'widgets_init', create_function( '', 'register_widget( "login_wid" );' ) );

add_action( 'init', 'login_validate' );