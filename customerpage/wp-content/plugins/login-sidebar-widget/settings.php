<?php
class login_settings {

	public $default_style = '.log_forms{ width:98%; padding:5px; border:1px solid #CCC; margin:2px; } .log_forms input[type=text], input[type=password] { margin: 10px 0 20px; width: 99%; padding: 7px 0 7px 4px; border: 1px solid #E3E3E3; } .log_forms input[type=submit] { margin: 10px 0 20px; width: 100%; padding: 7px; border: 1px solid #7ac9b7; } .log_forms input[type=text]:focus, input[type=password]:focus { border-color: #4697e4; }';
	
	public function __construct() {
		$this->load_settings();
	}
	
	public function login_widget_afo_save_settings(){
		
		if(isset($_POST['option']) and $_POST['option'] == "login_widget_afo_save_settings"){
			
			if ( ! isset( $_POST['login_widget_afo_field'] )  || ! wp_verify_nonce( $_POST['login_widget_afo_field'], 'login_widget_afo_action' ) ) {
			   wp_die( 'Sorry, your nonce did not verify.' );
			   exit;
			} 
			$lmc = new login_message_class;
			if(!empty($_POST['redirect_page'])){
				update_option( 'redirect_page',  sanitize_text_field($_POST['redirect_page']) );
			} else {
				delete_option( 'redirect_page' );
			}
			
			if(!empty($_POST['redirect_page_url'])){
				update_option( 'redirect_page_url',  sanitize_text_field($_POST['redirect_page_url']) );
			} else {
				delete_option( 'redirect_page_url' );
			}
			
			if(!empty($_POST['logout_redirect_page'])){
				update_option( 'logout_redirect_page',  sanitize_text_field($_POST['logout_redirect_page']) );
			} else {
				delete_option( 'logout_redirect_page' );
			}
			
			if(!empty($_POST['link_in_username'])){
				update_option( 'link_in_username',  sanitize_text_field($_POST['link_in_username']) );
			} else {
				delete_option( 'link_in_username' );
			}
			
			if(!empty($_POST['login_afo_rem'])){
				update_option( 'login_afo_rem',  sanitize_text_field($_POST['login_afo_rem']) );
			} else {
				delete_option( 'login_afo_rem' );
			}
			
			if(!empty($_POST['login_afo_forgot_pass_link'])){
				update_option( 'login_afo_forgot_pass_link',  sanitize_text_field($_POST['login_afo_forgot_pass_link']) );
			} else {
				delete_option( 'login_afo_forgot_pass_link' );
			}
			
			if(!empty($_POST['login_afo_register_link'])){
				update_option( 'login_afo_register_link',  sanitize_text_field($_POST['login_afo_register_link']) );
			} else {
				delete_option( 'login_afo_register_link' );
			}
			
			if(!empty($_POST['login_afo_fp_from_email'])){
				update_option( 'login_afo_fp_from_email',  sanitize_text_field($_POST['login_afo_fp_from_email']) );
			} else {
				delete_option( 'login_afo_fp_from_email' );
			}
			
			if(!empty($_POST['lafo_invalid_username'])){
				update_option( 'lafo_invalid_username',  sanitize_text_field($_POST['lafo_invalid_username']) );
			} else {
				delete_option( 'lafo_invalid_username' );
			}
			
			if(!empty($_POST['lafo_invalid_password'])){
				update_option( 'lafo_invalid_password',  sanitize_text_field($_POST['lafo_invalid_password']) );
			} else {
				delete_option( 'lafo_invalid_password' );
			}
			
			if(!empty($_POST['captcha_on_admin_login'])){
				update_option( 'captcha_on_admin_login',  sanitize_text_field($_POST['captcha_on_admin_login']) );
			} else {
				delete_option( 'captcha_on_admin_login' );
			}
			
			if(!empty($_POST['captcha_on_user_login'])){
				update_option( 'captcha_on_user_login',  sanitize_text_field($_POST['captcha_on_user_login']) );
			} else {
				delete_option( 'captcha_on_user_login' );
			}
			
			if(!empty($_POST['default_login_form_hooks'])){
				update_option( 'default_login_form_hooks',  sanitize_text_field($_POST['default_login_form_hooks']) );
			} else {
				delete_option( 'default_login_form_hooks' );
			}
			
			if(isset( $_POST['load_default_style'] ) and sanitize_text_field($_POST['load_default_style']) == "Yes"){
				update_option( 'custom_style_afo', sanitize_text_field($this->default_style) );
			} else {
				update_option( 'custom_style_afo',  sanitize_text_field($_POST['custom_style_afo']) );
			}
			
			if(!empty($_POST['login_sidebar_widget_from_email'])){
				update_option( 'login_sidebar_widget_from_email', sanitize_text_field($_POST['login_sidebar_widget_from_email']) );
			} else {
				delete_option( 'login_sidebar_widget_from_email' );
			}
			
			if(!empty($_POST['forgot_password_link_mail_subject'])){
				update_option( 'forgot_password_link_mail_subject', sanitize_text_field($_POST['forgot_password_link_mail_subject']) );
			} else {
				delete_option( 'forgot_password_link_mail_subject' );
			}
			
			if(!empty($_POST['forgot_password_link_mail_body'])){
				update_option( 'forgot_password_link_mail_body', esc_html($_POST['forgot_password_link_mail_body']) );
			} else {
				delete_option( 'forgot_password_link_mail_body' );
			}
			
			if(!empty($_POST['new_password_mail_subject'])){
				update_option( 'new_password_mail_subject',sanitize_text_field($_POST['new_password_mail_subject']) );
			} else {
				delete_option( 'new_password_mail_subject' );
			}
			
			if(!empty($_POST['new_password_mail_body'])){
				update_option( 'new_password_mail_body', esc_html($_POST['new_password_mail_body']) );
			} else {
				delete_option( 'new_password_mail_body' );
			}
			
			$lmc->add_message('Settings updated successfully.','success');
		}
	}
	
	public function  login_widget_afo_options () {
	global $wpdb;
	$lmc = new login_message_class;
	
	$redirect_page = get_option('redirect_page');
	$redirect_page_url = get_option('redirect_page_url');
	$logout_redirect_page = get_option('logout_redirect_page');
	$link_in_username = get_option('link_in_username');
	$login_afo_rem = get_option('login_afo_rem');
	$login_afo_forgot_pass_link = get_option('login_afo_forgot_pass_link');
	$login_afo_register_link = get_option('login_afo_register_link');
	$login_afo_fp_from_email = get_option('login_afo_fp_from_email');
	
	$lafo_invalid_username = get_option('lafo_invalid_username');
	$lafo_invalid_password = get_option('lafo_invalid_password');
	
	$captcha_on_admin_login = get_option('captcha_on_admin_login');
	$captcha_on_user_login = get_option('captcha_on_user_login');
	
	$default_login_form_hooks = get_option('default_login_form_hooks');
	
	$custom_style_afo = stripslashes(get_option('custom_style_afo'));
	
	$login_sidebar_widget_from_email = get_option('login_sidebar_widget_from_email');
	$forgot_password_link_mail_subject = get_option('forgot_password_link_mail_subject');
	$forgot_password_link_mail_body = get_option('forgot_password_link_mail_body');
	$new_password_mail_subject = get_option('new_password_mail_subject');
	$new_password_mail_body = get_option('new_password_mail_body');
	
	$lmc->show_message();
	
	self :: fb_login_pro_add();
	self :: social_login_so_setup_add();
	self :: help_support();
	self :: wp_register_profile_add();
	?>
	<form name="f" method="post" action="">
	<?php wp_nonce_field('login_widget_afo_action','login_widget_afo_field'); ?>
	<input type="hidden" name="option" value="login_widget_afo_save_settings" />
	<table width="98%" style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px; margin:2px;">
	  <tr>
		<td width="45%"><h1><?php _e('Login Widget AFO Settings','login-sidebar-widget');?></h1></td>
		<td width="55%">&nbsp;</td>
	  </tr>
	  <tr>
		<td><strong><?php _e('Login Redirect Page','login-sidebar-widget');?>:</strong></td>
		<td><?php
				$args = array(
				'depth'            => 0,
				'selected'         => $redirect_page,
				'echo'             => 1,
				'show_option_none' => '-',
				'id' 			   => 'redirect_page',
				'name'             => 'redirect_page'
				);
				wp_dropdown_pages( $args ); 
			?> <?php _e('Or','login-sidebar-widget');?> <input type="text" name="redirect_page_url" value="<?php echo esc_url( $redirect_page_url );?>" placeholder="URL" /></td>
	  </tr>
	  
	   <tr>
		<td><strong><?php _e('Logout Redirect Page','login-sidebar-widget');?>:</strong></td>
		 <td><?php
				$args1 = array(
				'depth'            => 0,
				'selected'         => $logout_redirect_page,
				'echo'             => 1,
				'show_option_none' => '-',
				'id' 			   => 'logout_redirect_page',
				'name'             => 'logout_redirect_page'
				);
				wp_dropdown_pages( $args1 ); 
			?></td>
	  </tr>
	   
	  <tr>
		<td><strong><?php _e('Link in Username','login-sidebar-widget');?></strong></td>
		<td><?php
				$args2 = array(
				'depth'            => 0,
				'selected'         => $link_in_username,
				'echo'             => 1,
				'show_option_none' => '-',
				'id' 			   => 'link_in_username',
				'name'             => 'link_in_username'
				);
				wp_dropdown_pages( $args2 ); 
			?></td>
	  </tr>
	  <tr>
		<td><strong><?php _e('Add Remember Me','login-sidebar-widget');?></strong></td>
		<td><input type="checkbox" name="login_afo_rem" value="Yes" <?php echo $login_afo_rem == 'Yes'?'checked="checked"':'';?> /></td>
	  </tr>
	  <tr>
		<td><strong><?php _e('Forgot Password Link','login-sidebar-widget');?></strong></td>
		<td>
			<?php
				$args3 = array(
				'depth'            => 0,
				'selected'         => $login_afo_forgot_pass_link,
				'echo'             => 1,
				'show_option_none' => '-',
				'id' 			   => 'login_afo_forgot_pass_link',
				'name'             => 'login_afo_forgot_pass_link'
				);
				wp_dropdown_pages( $args3 ); 
			?>
			<i><?php _e('Leave blank to not include the link','login-sidebar-widget');?></i>
			</td>
	  </tr>
	  <tr>
		<td><strong><?php _e('Register Link','login-sidebar-widget');?></strong></td>
		<td>
			<?php
				$args4 = array(
				'depth'            => 0,
				'selected'         => $login_afo_register_link,
				'echo'             => 1,
				'show_option_none' => '-',
				'id' 			   => 'login_afo_register_link',
				'name'             => 'login_afo_register_link'
				);
				wp_dropdown_pages( $args4 ); 
			?>
			<i><?php _e('Leave blank to not include the link','login-sidebar-widget');?></i>
			</td>
	  </tr>
	  <tr>
		<td><strong><?php _e('Forgot Password From Email','login-sidebar-widget');?></strong></td>
		<td><input type="text" name="login_afo_fp_from_email" value="<?php echo $login_afo_fp_from_email;?>" placeholder="no-reply@example.com" />
			<i><?php _e('This will make sure that the mail does not go to spam folder.','login-sidebar-widget');?></i>
			</td>
	  </tr>
      <tr>
        <td width="45%"><h1><?php _e('Security','login-sidebar-widget');?></h1></td>
        <td width="55%">&nbsp;</td>
      </tr>
      <tr>
		<td><strong><?php _e('Captcha on Admin Login','login-sidebar-widget');?></strong></td>
		<td><input type="checkbox" name="captcha_on_admin_login" value="Yes" <?php echo $captcha_on_admin_login == 'Yes'?'checked="checked"':'';?> /><i><?php _e('Check to enable captcha on admin login form','login-sidebar-widget');?></i></td>
	  </tr>
       <tr>
		<td><strong><?php _e('Captcha on User Login','login-sidebar-widget');?></strong></td>
		<td><input type="checkbox" name="captcha_on_user_login" value="Yes" <?php echo $captcha_on_user_login == 'Yes'?'checked="checked"':'';?> /><i><?php _e('Check to enable captcha on user login form','login-sidebar-widget');?></i></td>
	  </tr>
      <tr>
		<td colspan="2">&nbsp;</td>
	  </tr>
      <tr>
		<td colspan="2">
        <div style="border:1px solid #AEAE00; width:94%; background-color:#FFF; margin:0px auto; padding:10px;">
        Click <a href="admin.php?page=login_log_afo">here</a> to check the user <strong>Login Log</strong>. Use <strong><a href="http://www.aviplugins.com/fb-login-widget-pro/" target="_blank">PRO</a></strong> version that has added security with <strong>Blocking IP</strong> after 5 wrong login attempts. <strong>Blocked IPs</strong> can be <strong>Whitelisted</strong> from admin panel or the <strong>Block</strong> gets automatically removed after <strong>1 Day</strong>.
        </div>
        </td>
	  </tr>
      <tr>
        <td width="45%"><h1><?php _e('Compatibility','login-sidebar-widget');?></h1></td>
        <td width="55%">&nbsp;</td>
      </tr>
      <tr>
		<td valign="top"><strong><?php _e('Enable default WordPress login form hooks','login-sidebar-widget');?></strong></td>
		<td><input type="checkbox" name="default_login_form_hooks" value="Yes" <?php echo $default_login_form_hooks == 'Yes'?'checked="checked"':'';?> /><i>Check to <strong>Enable</strong> default WordPress login form hooks. This will make the login form compatible with other plugins. For example <strong>Enable</strong> this if you want to use CAPTCHA on login, from another plugin. <strong>Disable</strong> this so that no other plugins can interfere with your login form.</i></td>
	  </tr>
      
      <tr>
        <td width="45%"><h1><?php _e('Error Message','login-sidebar-widget');?></h1></td>
        <td width="55%">&nbsp;</td>
      </tr>
      <tr>
		<td valign="top"><strong><?php _e('Invalid Username Message','login-sidebar-widget');?></strong></td>
		<td><input type="text" name="lafo_invalid_username" value="<?php echo $lafo_invalid_username;?>" placeholder="<?php _e('Error: Invalid Username','login-sidebar-widget');?>" size="35"><i><?php _e('Error message for wrong Username','login-sidebar-widget');?></i></td>
	  </tr>
      <tr>
		<td valign="top"><strong><?php _e('Invalid Password Message','login-sidebar-widget');?></strong></td>
		<td><input type="text" name="lafo_invalid_password" value="<?php echo $lafo_invalid_password;?>" placeholder="<?php _e('Error: Invalid Username & Password','login-sidebar-widget');?>" size="35"><i><?php _e('Error message for wrong Password','login-sidebar-widget');?></i></td>
	  </tr>
	   <tr>
			<td width="45%"><h1><?php _e('Styling','login-sidebar-widget');?></h1></td>
			<td width="55%">&nbsp;</td>
		  </tr>
	   <tr>
			<td valign="top"><input type="checkbox" name="load_default_style" value="Yes" /><strong> <?php _e('Load Default Styles','login-sidebar-widget');?></strong><br />
			<?php _e('Check this and hit the save button to go back to default styling.','login-sidebar-widget');?>
			</td>
			<td><textarea name="custom_style_afo" style="width:80%; height:200px;"><?php echo $custom_style_afo;?></textarea></td>
		  </tr>
          <tr>
			<td width="45%"><h1><?php _e('Email Settings','login-sidebar-widget');?></h1></td>
			<td width="55%">&nbsp;</td>
		  </tr>
           <tr>
				<td valign="top"><strong><?php _e('From Email','login-sidebar-widget');?></strong></td>
				<td><input type="text" name="login_sidebar_widget_from_email" value="<?php echo $login_sidebar_widget_from_email;?>" placeholder="no-reply@example.com" />
                <i><?php _e('This will be the from email address in the emails. This will make sure that the emails do not go to a spam folder.','login-sidebar-widget');?></i>
				</td>
			  </tr>
           <tr>
				<td width="30%"><strong><?php _e('Reset Password Link Mail Subject','login-sidebar-widget');?></strong></td>
				<td><input type="text" name="forgot_password_link_mail_subject" value="<?php echo $forgot_password_link_mail_subject;?>" />
				</td>
			  </tr>
	 		 <tr>
				<td valign="top"><strong><?php _e('Reset Password Link Mail Body','login-sidebar-widget');?></strong>
				<p><i><?php _e('This mail will fire when a user request for a new password.','login-sidebar-widget');?></i></p>
				</td>
				<td><textarea name="forgot_password_link_mail_body" style="height:200px; width:100%;"><?php echo $forgot_password_link_mail_body;?></textarea>
				<p>Shortcodes: #site_url#, #user_name#, #resetlink#</p>
				</td>
			  </tr>
	 	 	 <tr>
				<td><strong><?php _e('New Password Mail Subject','login-sidebar-widget');?></strong></td>
				<td><input type="text" name="new_password_mail_subject" value="<?php echo $new_password_mail_subject;?>" /></td>
			  </tr>
			 <tr>
				<td valign="top"><strong><?php _e('New Password Mail Subject Body','login-sidebar-widget');?></strong>
				<p><i><?php _e('This mail will fire when a user clicks on the password reset link provided in the above mail.','login-sidebar-widget');?></i></p>
				</td>
				<td><textarea name="new_password_mail_body" style="height:200px; width:100%;"><?php echo $new_password_mail_body;?></textarea>
				<p>Shortcodes: #site_url#, #user_name#, #user_password#</p>
				</td>
			  </tr>
		  
	  <tr>
		<td>&nbsp;</td>
		<td><input type="submit" name="submit" value="<?php _e('Save','login-sidebar-widget');?>" class="button button-primary button-large" /></td>
	  </tr>
	  <tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	  </tr>
      <tr>
        <td width="45%"><h1><?php _e('Shortcode','login-sidebar-widget');?></h1></td>
        <td width="55%">&nbsp;</td>
      </tr>
	  <tr>
		<td colspan="2">Use <span style="color:#000066;">[login_widget]</span> shortcode to display login form in post or page.<br />
		 Example: <span style="color:#000066;">[login_widget title="Login Here"]</span></td>
	  </tr>
	  <tr>
		<td colspan="2">Use <span style="color:#000066;">[forgot_password]</span> shortcode to display forgot password form in post or page.<br />
		 Example: <span style="color:#000066;">[forgot_password title="Forgot Password?"]</span></td>
	  </tr>
      <tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	  </tr>
	</table>
	</form>
	<?php 
	self :: fb_comment_addon_add();
	if ( !is_plugin_active( 'fb-comments-afo-addon/fb_comment.php' ) ) { self :: donate(); }
	}
	
	
	public function fb_comment_plugin_addon_options(){
	global $wpdb;
	$lmc = new login_message_class;
	
	$fb_comment_addon = new afo_fb_comment_settings;
	$fb_comments_color_scheme = get_option('fb_comments_color_scheme');
	$fb_comments_width = get_option('fb_comments_width');
	$fb_comments_no = get_option('fb_comments_no');
	$fb_comments_language = get_option('fb_comments_language');
	$lmc->show_message();
	?>
	<form name="f" method="post" action="">
	<input type="hidden" name="option" value="save_afo_fb_comment_settings" />
	<table width="100%" border="0" style="background-color:#FFFFFF; margin-top:20px; width:98%; padding:5px; border:1px solid #999999; ">
	  <tr>
		<td colspan="2"><h1><?php _e('Social Comments Settings','login-sidebar-widget');?></h1></td>
	  </tr>
	  <?php do_action('fb_comments_settings_top','login-sidebar-widget');?>
	   <tr>
		<td><h3><?php _e('Facebook Comments','login-sidebar-widget');?></h3></td>
		<td></td>
	  </tr>
	   <tr>
		<td><strong><?php _e('Language','login-sidebar-widget');?></strong></td>
		<td><select name="fb_comments_language">
			<option value=""> -- </option>
			<?php echo $fb_comment_addon->language_selected($fb_comments_language);?>
		</select>
		</td>
	  </tr>
	 <tr>
		<td><strong><?php _e('Color Scheme','login-sidebar-widget');?></strong></td>
		<td><select name="fb_comments_color_scheme">
			<?php echo $fb_comment_addon->get_color_scheme_selected($fb_comments_color_scheme);?>
		</select>
		</td>
	  </tr>
	   <tr>
		<td><strong><?php _e('Width','login-sidebar-widget');?></strong></td>
		<td><input type="text" name="fb_comments_width" value="<?php echo $fb_comments_width;?>"/> <?php _e('In Percent (%)','login-sidebar-widget');?></td>
	  </tr>
	   <tr>
		<td><strong><?php _e('No of Comments','login-sidebar-widget');?></strong></td>
		<td><input type="text" name="fb_comments_no" value="<?php echo $fb_comments_no;?>"/> <?php _e('Default is 10','login-sidebar-widget');?></td>
	  </tr>
	  <?php do_action('fb_comments_settings_bottom','login-sidebar-widget');?>
	  <tr>
		<td>&nbsp;</td>
		<td><input type="submit" name="submit" value="<?php _e('Save','login-sidebar-widget');?>" class="button button-primary button-large" /></td>
	  </tr>
	  <tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	  </tr>
	  <tr>
		<td colspan="2">Use <span style="color:#000066;">[social_comments]</span> shortcode to display Facebook / Disqus Comments in post or page.<br />
		 Example: <span style="color:#000066;">[social_comments title="Comments"]</span>
		 <br /> <br />
		 Or else<br /> <br />
		 You can use this function <span style="color:#000066;">social_comments()</span> in your template to display the Facebook Comments. <br />
		 Example: <span style="color:#000066;">&lt;?php social_comments("Comments");?&gt;</span>
		 </td>
	  </tr>
	</table>
	</form>
	<?php 
	}

	public function login_widget_afo_text_domain(){
		load_plugin_textdomain('login-sidebar-widget', FALSE, basename( dirname( __FILE__ ) ) .'/languages');
	}
		
	public function login_widget_afo_menu () {
		add_menu_page( 'Login Widget', 'Login Widget Settings', 'activate_plugins', 'login_widget_afo', array( $this,'login_widget_afo_options' ));
	}
	
	public function load_login_admin_style(){
		wp_register_style( 'style_login_admin', plugin_dir_url( __FILE__ ) . '/style_login_admin.css' );
		wp_enqueue_style( 'style_login_admin' );
	}
	
	public function load_settings(){
		add_action( 'admin_menu' , array( $this, 'login_widget_afo_menu' ) );
		add_action( 'admin_init', array( $this, 'login_widget_afo_save_settings' ) );
		add_action( 'plugins_loaded',  array( $this, 'login_widget_afo_text_domain' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'load_login_admin_style' ) );
	}
	
	private static function wp_register_profile_add(){ ?>
	<table width="98%" border="0" style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px; margin:2px;">
  <tr>
    <td><p><strong>Login Widget With Shortcode</strong> recommends you to download and activate <a href="https://wordpress.org/plugins/wp-register-profile-with-shortcode/" target="_blank">WP Register Profile With Shortcode</a> from <a href="https://wordpress.org/" target="_blank">wordpress.org</a>, so that users can register in your site.</p></td>
  </tr>
</table>
	<?php }
	
	private static function help_support(){ ?>
	<table width="98%" border="0" style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px; margin:2px;">
	  <tr>
		<td align="right"><a href="http://www.aviplugins.com/support.php" target="_blank">Help and Support</a> <a href="http://www.aviplugins.com/rss/news.xml" target="_blank"><img src="<?php echo  plugin_dir_url( __FILE__ ) . '/images/rss.png';?>" style="vertical-align: middle;" alt="RSS"></a></td>
	  </tr>
	</table>
	<?php
	}
	
	private static function fb_comment_addon_add(){ 
		if ( !is_plugin_active( 'fb-comments-afo-addon/fb_comment.php' ) ) {
	?>
		<table width="98%" border="0" style="background-color:#FFFFD2; border:1px solid #E6DB55; padding:0px 0px 0px 10px; margin:2px;">
	  <tr>
		<td><p>There is a <strong>Facebook Comments Addon</strong> for this plugin. The plugin replace the default <strong>WordPress</strong> Comments module and enable <strong>Facebook</strong> Comments Module. You can get it <a href="http://www.aviplugins.com/fb-comments-afo-addon/" target="_blank">here</a> in <strong>USD 1.00</strong> </p></td>
	  </tr>
	</table>
	<?php 
		}
	}
	
	private static function fb_login_pro_add(){ ?>
	<table width="98%" border="0" style="background-color:#FFFFD2; border:1px solid #E6DB55; padding:0px 0px 0px 10px; margin:2px;">
  <tr>
    <td><p>The <strong>PRO</strong> version supports login with <strong>Facebook</strong>, <strong>Google</strong>,  <strong>Twitter</strong> and <strong>LinkedIn</strong> accounts. Addons are available for logging in with <strong><a href="http://www.aviplugins.com/microsoft-login-addon/" target="_blank">Microsoft</a></strong> and <strong><a href="http://www.aviplugins.com/yahoo-login-addon/" target="_blank">Yahoo</a></strong> accounts. You can get it <a href="http://www.aviplugins.com/fb-login-widget-pro/" target="_blank">here</a> in <strong>USD 3.00</strong> </p></td>
  </tr>
</table>
	<?php }
	
	private static function social_login_so_setup_add(){ ?>
	<table width="98%" border="0" style="background-color:#FFFFD2; border:1px solid #E6DB55; padding:0px 0px 0px 10px; margin:2px;">
  <tr>
    <td><p>Check out the <strong>Social Login No Setup</strong> plugin that supports login with <strong>Facebook</strong>, <strong>Google</strong>,  <strong>Twitter</strong>, <strong>LinkedIn</strong> and <strong>Microsoft</strong> accounts. It requires no Setups, no Maintanance, no need to create any APPs, APIs, Client Ids, Client Secrets or anything ( Everythins is maintained by aviplugins.com ). Just Install and users will start logging in with their social networking accounts right away. <a href="http://www.aviplugins.com/social-login-no-setup/" target="_blank">More Details</a>.</p></td>
  </tr>
</table>
	<?php }
	
	private static function donate(){	?>
	<table width="98%" border="0" style="background-color:#FFF; border:1px solid #ccc; margin:2px;">
	 <tr>
	 <td align="right"><a href="http://www.aviplugins.com/donate/" target="_blank">Donate</a> <img src="<?php echo  plugin_dir_url( __FILE__ ) . '/images/paypal.png';?>" style="vertical-align: middle;" alt="PayPal"></td>
	  </tr>
	</table>
	<?php
	}
	
}