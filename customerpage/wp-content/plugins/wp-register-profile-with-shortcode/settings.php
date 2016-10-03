<?php
class register_settings {
	
	public function __construct() {
		$this->load_settings();
	}
	
	public function register_widget_afo_save_settings(){
		if(isset($_POST['option']) and sanitize_text_field($_POST['option']) == "register_widget_afo_save_settings"){
			if ( ! isset( $_POST['register_widget_afo_save_action_field'] ) || ! wp_verify_nonce( $_POST['register_widget_afo_save_action_field'], 'register_widget_afo_save_action' ) ) {
			   wp_die( 'Sorry, your nonce did not verify.');
			} 
			
			if( !empty($_POST['thank_you_page_after_registration_url']) ){
				update_option( 'thank_you_page_after_registration_url', sanitize_text_field($_POST['thank_you_page_after_registration_url']) );
			} else {
				delete_option( 'thank_you_page_after_registration_url' );
			}
			if( !empty($_POST['username_in_registration']) ){
				update_option( 'username_in_registration', sanitize_text_field($_POST['username_in_registration']) );
			} else {
				delete_option( 'username_in_registration' );
			}
			if( !empty($_POST['password_in_registration']) ){
				update_option( 'password_in_registration', sanitize_text_field($_POST['password_in_registration']) );
			} else {
				delete_option( 'password_in_registration' );
			}
			
			if( !empty($_POST['firstname_in_registration']) ){
				update_option( 'firstname_in_registration', sanitize_text_field($_POST['firstname_in_registration']) );
			} else {
				delete_option( 'firstname_in_registration' );
			}
			if( !empty($_POST['firstname_in_profile']) ){
				update_option( 'firstname_in_profile', sanitize_text_field($_POST['firstname_in_profile']) );
			} else {
				delete_option( 'firstname_in_profile' );
			}
			if( !empty($_POST['is_firstname_required']) ){
				update_option( 'is_firstname_required', sanitize_text_field($_POST['is_firstname_required']) );
			} else {
				delete_option( 'is_firstname_required' );
			}
			
			if( !empty($_POST['lastname_in_registration']) ){
				update_option( 'lastname_in_registration', sanitize_text_field($_POST['lastname_in_registration']) );
			} else {
				delete_option( 'lastname_in_registration' );
			}
			if( !empty($_POST['lastname_in_profile']) ){
				update_option( 'lastname_in_profile', sanitize_text_field($_POST['lastname_in_profile']) );
			} else {
				delete_option( 'lastname_in_profile' );
			}
			if( !empty($_POST['is_lastname_required']) ){
				update_option( 'is_lastname_required', sanitize_text_field($_POST['is_lastname_required']) );
			} else {
				delete_option( 'is_lastname_required' );
			}
			
			if( !empty($_POST['displayname_in_registration']) ){
				update_option( 'displayname_in_registration', sanitize_text_field($_POST['displayname_in_registration']) );
			} else {
				delete_option( 'displayname_in_registration' );
			}
			if( !empty($_POST['displayname_in_profile']) ){
				update_option( 'displayname_in_profile', sanitize_text_field($_POST['displayname_in_profile']) );
			} else {
				delete_option( 'displayname_in_profile' );
			}
			if( !empty($_POST['is_displayname_required']) ){
				update_option( 'is_displayname_required', sanitize_text_field($_POST['is_displayname_required']) );
			} else {
				delete_option( 'is_displayname_required' );
			}
			if( !empty($_POST['userdescription_in_registration']) ){
				update_option( 'userdescription_in_registration', sanitize_text_field($_POST['userdescription_in_registration']) );
			} else {
				delete_option( 'userdescription_in_registration' );
			}
			if( !empty($_POST['userdescription_in_profile']) ){
				update_option( 'userdescription_in_profile', sanitize_text_field($_POST['userdescription_in_profile']) );
			} else {
				delete_option( 'userdescription_in_profile' );
			}
			if( !empty($_POST['is_userdescription_required']) ){
				update_option( 'is_userdescription_required', sanitize_text_field($_POST['is_userdescription_required']) );
			} else {
				delete_option( 'is_userdescription_required' );
			}
			
			if( !empty($_POST['userurl_in_registration']) ){
				update_option( 'userurl_in_registration', sanitize_text_field($_POST['userurl_in_registration']) );
			} else {
				delete_option( 'userurl_in_registration' );
			}
			if( !empty($_POST['userurl_in_profile']) ){
				update_option( 'userurl_in_profile', sanitize_text_field($_POST['userurl_in_profile']) );
			} else {
				delete_option( 'userurl_in_profile' );
			}
			if( !empty($_POST['is_userurl_required']) ){
				update_option( 'is_userurl_required', sanitize_text_field($_POST['is_userurl_required']) );
			} else {
				delete_option( 'is_userurl_required' );
			}
			
			if( !empty($_POST['wprw_admin_email']) ){
				update_option( 'wprw_admin_email', sanitize_text_field($_POST['wprw_admin_email']) );
			} else {
				delete_option( 'wprw_admin_email' );
			}
			if( !empty($_POST['wprw_from_email']) ){
				update_option( 'wprw_from_email', sanitize_text_field($_POST['wprw_from_email']) );
			} else {
				delete_option( 'wprw_from_email' );
			}
			if( !empty($_POST['new_user_register_mail_subject']) ){
				update_option( 'new_user_register_mail_subject', sanitize_text_field($_POST['new_user_register_mail_subject']) );
			} else {
				delete_option( 'new_user_register_mail_subject' );
			}
			if( !empty($_POST['new_user_register_mail_body']) ){
				update_option( 'new_user_register_mail_body', esc_html($_POST['new_user_register_mail_body']) );
			} else {
				delete_option( 'new_user_register_mail_body' );
			}
			
			if( !empty($_POST['captcha_in_registration']) ){
				update_option( 'captcha_in_registration', sanitize_text_field($_POST['captcha_in_registration']) );
			} else {
				delete_option( 'captcha_in_registration' );
			}
			if( !empty($_POST['force_login_after_registration']) ){
				update_option( 'force_login_after_registration', sanitize_text_field($_POST['force_login_after_registration']) );
			} else {
				delete_option( 'force_login_after_registration' );
			}
			if( !empty($_POST['default_registration_form_hooks']) ){
				update_option( 'default_registration_form_hooks', sanitize_text_field($_POST['default_registration_form_hooks']) );
			} else {
				delete_option( 'default_registration_form_hooks' );
			}
			if( !empty($_POST['enable_cfws_newsletter_subscription']) ){
				update_option( 'enable_cfws_newsletter_subscription', sanitize_text_field($_POST['enable_cfws_newsletter_subscription']) );
			} else {
				delete_option( 'enable_cfws_newsletter_subscription' );
			}
			
			$_SESSION['msg'] = 'Plugin data updated successfully.';
			$_SESSION['msg_class'] = 'success_msg_rp';
		}
		
		
	}
	
	private function error_message(){
		if(isset($_SESSION['msg']) and $_SESSION['msg']){
			echo '<div class="'.$_SESSION['msg_class'].'">'.$_SESSION['msg'].'</div>';
			unset($_SESSION['msg']);
			unset($_SESSION['msg_class']);
		}
	}
	
	public function  register_widget_afo_options () {
	global $wpdb;
	
	$thank_you_page_after_registration_url = get_option('thank_you_page_after_registration_url');
	
	$username_in_registration = get_option( 'username_in_registration' );
	
	$password_in_registration = get_option( 'password_in_registration' );
	
	$firstname_in_registration = get_option( 'firstname_in_registration' );
	$firstname_in_profile = get_option( 'firstname_in_profile' );
	$is_firstname_required = get_option( 'is_firstname_required' );
	
	$lastname_in_registration = get_option( 'lastname_in_registration' );
	$lastname_in_profile = get_option( 'lastname_in_profile' );
	$is_lastname_required = get_option( 'is_lastname_required' );
	
	$displayname_in_registration = get_option( 'displayname_in_registration' );
	$displayname_in_profile = get_option( 'displayname_in_profile' );
	$is_displayname_required = get_option( 'is_displayname_required' );
	
	$userdescription_in_registration = get_option( 'userdescription_in_registration' );
	$userdescription_in_profile = get_option( 'userdescription_in_profile' );
	$is_userdescription_required = get_option( 'is_userdescription_required' );
	
	$userurl_in_registration = get_option( 'userurl_in_registration' );
	$userurl_in_profile = get_option( 'userurl_in_profile' );
	$is_userurl_required = get_option( 'is_userurl_required' );
	
	$wprw_admin_email = get_option( 'wprw_admin_email' );
	$wprw_from_email = get_option( 'wprw_from_email' );
	$new_user_register_mail_subject = get_option('new_user_register_mail_subject');
	$new_user_register_mail_body = get_option('new_user_register_mail_body');
	
	$captcha_in_registration = get_option( 'captcha_in_registration' );
	$force_login_after_registration = get_option( 'force_login_after_registration' );
	
	$default_registration_form_hooks = get_option( 'default_registration_form_hooks' );
	
	$enable_cfws_newsletter_subscription = get_option( 'enable_cfws_newsletter_subscription' );
	
	$this->help_support();
	$this->login_sidebar_widget_add();
	$this->error_message();
	?>
	<form name="f" method="post" action="">
	<input type="hidden" name="option" value="register_widget_afo_save_settings" />
    <?php wp_nonce_field( 'register_widget_afo_save_action', 'register_widget_afo_save_action_field' ); ?>
	<table style="width:98%; margin:2px 0px;" border="0">
      <tr>
		<td colspan="2">
       	 <table width="100%" border="0" style="background-color:#FFFFFF; padding:10px; border:1px dotted #999999;">
         	    <tr>
		<td colspan="2"><h1>WP Register Profile With Shortcode Settings</h1></td>
	  </tr>
	 			<tr>
		<td valign="top"><strong>Thank You Page</strong></td>
		<td><?php
				$args = array(
				'depth'            => 0,
				'selected'         => $thank_you_page_after_registration_url,
				'echo'             => 1,
				'show_option_none' => '--',
				'id' 			   => 'thank_you_page_after_registration_url',
				'name'             => 'thank_you_page_after_registration_url'
				);
				wp_dropdown_pages( $args ); 
			?><br />
			<i>If selected user will be redirected to this page after successfull registration</i>
			</td>
	  </tr>
			</table>
		</td>
	  </tr>
      
      <tr>
		<td colspan="2">
       		 <table width="100%" border="0" style="border:1px dotted #999999;" class="field_form_table">
             <tr style="background-color:#FFFFFF;">
				<td colspan="4" align="center"><h3>Registration and Profile Form Fields</h3></td>
			  </tr>
             
			  <tr style="background-color:#FFFFFF;">
				<td width="10%"><strong>Field</strong></td>
				<td width="10%"><strong>Required</strong></td>
				<td width="40%"><strong>Show In Registration</strong></td>
				<td width="40%"><strong>Show In Profile</strong></td>
			  </tr>
			  <tr>
				<td><strong>User Name</strong></td>
				<td align="center"><input type="checkbox" checked="checked" disabled="disabled" /></td>
				<td><input type="checkbox" name="username_in_registration" value="Yes" <?php echo $username_in_registration == 'Yes'?'checked="checked"':'';?>/><span>If unchecked then <strong>User Email</strong> will be used as <strong>User Name</strong>.</span></td>
				<td><span>This field cannot be updated.</span></td>
			  </tr>
			 <tr style="background-color:#FFFFFF;">
				<td><strong>User Email</strong></td>
				<td align="center"><input type="checkbox" checked="checked" disabled="disabled" /></td>
				<td><span>This field is required and cannot be removed.</span></td>
				<td><span>This field cannot be updated.</span></td>
			  </tr>
			  <tr>
				<td><strong>Password Field </strong></td>
				<td align="center"><input type="checkbox" checked="checked" disabled="disabled" /></td>
				<td><input type="checkbox" name="password_in_registration" value="Yes" <?php echo $password_in_registration == 'Yes'?'checked="checked"':'';?>/><span>Check this to enable password field in registration form. Otherwise the password will be auto generated and Emailed to user.</span></td>
				<td><span>Password can be updated from update password page. Use this shortcode <strong>[rp_update_password]</strong></span></td>
			  </tr>
			 <tr style="background-color:#FFFFFF;">
				<td><strong>First Name </strong></td>
				<td align="center"><input type="checkbox" name="is_firstname_required" value="Yes" <?php echo $is_firstname_required == 'Yes'?'checked="checked"':'';?>/></td>
				<td><input type="checkbox" name="firstname_in_registration" value="Yes" <?php echo $firstname_in_registration == 'Yes'?'checked="checked"':'';?>/><span>Check this to enable first name in registration form.</span></td>
			  <td><input type="checkbox" name="firstname_in_profile" value="Yes" <?php echo $firstname_in_profile == 'Yes'?'checked="checked"':'';?>/><span>Check this to enable first name in profile form.</span></td>
			  </tr>
			   <tr>
				<td><strong>Last Name </strong></td>
				<td align="center"><input type="checkbox" name="is_lastname_required" value="Yes" <?php echo $is_lastname_required == 'Yes'?'checked="checked"':'';?>/></td>
				<td><input type="checkbox" name="lastname_in_registration" value="Yes" <?php echo $lastname_in_registration == 'Yes'?'checked="checked"':'';?>/><span>Check this to enable last name in registration form.</span></td>
				<td><input type="checkbox" name="lastname_in_profile" value="Yes" <?php echo $lastname_in_profile == 'Yes'?'checked="checked"':'';?>/><span>Check this to enable last name in profile form.</span></td>
			  </tr>
			  <tr style="background-color:#FFFFFF;">
				<td><strong>Display Name </strong></td>
				<td align="center"><input type="checkbox" name="is_displayname_required" value="Yes" <?php echo $is_displayname_required == 'Yes'?'checked="checked"':'';?>/></td>
				<td><input type="checkbox" name="displayname_in_registration" value="Yes" <?php echo $displayname_in_registration == 'Yes'?'checked="checked"':'';?>/><span>Check this to enable display name in registration form.</span></td>
			  	<td><input type="checkbox" name="displayname_in_profile" value="Yes" <?php echo $displayname_in_profile == 'Yes'?'checked="checked"':'';?>/><span>Check this to enable display name in profile form.</span></td>
			  </tr>
			  <tr>
				<td><strong>About User </strong></td>
				<td align="center"><input type="checkbox" name="is_userdescription_required" value="Yes" <?php echo $is_userdescription_required == 'Yes'?'checked="checked"':'';?>/></td>
                <td><input type="checkbox" name="userdescription_in_registration" value="Yes" <?php echo $userdescription_in_registration == 'Yes'?'checked="checked"':'';?>/><span>Check this to enable about user in registration form.</span></td>
				<td><input type="checkbox" name="userdescription_in_profile" value="Yes" <?php echo $userdescription_in_profile == 'Yes'?'checked="checked"':'';?>/><span>Check this to enable about user in profile form.</span></td>
			  </tr>
			 <tr style="background-color:#FFFFFF;">
				<td><strong>User Url </strong></td>
				<td align="center"><input type="checkbox" name="is_userurl_required" value="Yes" <?php echo $is_userurl_required == 'Yes'?'checked="checked"':'';?>/></td>
				<td><input type="checkbox" name="userurl_in_registration" value="Yes" <?php echo $userurl_in_registration == 'Yes'?'checked="checked"':'';?>/><span>Check this to enable user url in registration form.</span></td>
				<td><input type="checkbox" name="userurl_in_profile" value="Yes" <?php echo $userurl_in_profile == 'Yes'?'checked="checked"':'';?>/><span>Check this to enable user url in profile form.</span></td>
			  </tr>
              <tr style="background-color:#FFFFFF;">
				<td colspan="4">Use <a href="http://www.aviplugins.com/wp-register-profile-pro/" target="_blank">PRO</a> version to create additional custom fields with Sort option using Drag & Drop</td>
			  </tr>
			</table>
		</td>
	  </tr>
      <tr>
		<td colspan="2">
       	 <table width="100%" border="0" style="background-color:#FFFFFF; padding:10px; border:1px dotted #999999;">
         	  <tr>
				<td><strong>Admin Email</strong> </td>
                <td><input type="text" name="wprw_admin_email" value="<?php echo $wprw_admin_email;?>" placeholder="admin@example.com"><i>Email notification will be sent to this email address when new user do registration in the site</i></td>
			  </tr>
              <tr>
				<td><strong>From Email</strong></td>
                <td> <input type="text" name="wprw_from_email" value="<?php echo $wprw_from_email;?>" placeholder="no-reply@example.com"><i>This will make sure the emails are not treated as SPAM</i></td>
			  </tr>
               <tr>
				<td valign="top"><strong>New User Registration Email Subject</strong> </td>
                <td><input type="text" name="new_user_register_mail_subject" value="<?php echo $new_user_register_mail_subject;?>" /></td>
			  </tr>
               <tr>
				<td valign="top"><strong>New User Registration Email Body</strong>
				<p><i>This mail will be sent to the user who make registration in the site.</i></p>
				</td>
                <td><textarea name="new_user_register_mail_body" style="height:200px; width:100%;"><?php echo $new_user_register_mail_body;?></textarea>
				<p>Shortcodes: #site_name#, #user_name#, #user_password#, #site_url#</p>
				</td>
			  </tr>
              <tr>
				<td colspan="2">&nbsp;</td>
			  </tr>
              <tr>
				<td colspan="2"><strong>Note**</strong> When new user make registration in the site, Admin and User both will get a notification email.</td>
			  </tr>
			</table>
		</td>
	  </tr>
      <tr>
		<td colspan="2">
       	 <table width="100%" border="0" style="background-color:#FFFFFF; padding:10px; border:1px dotted #999999;">
         	  <tr>
				<td>Make User Logged-In after successful registration <input type="checkbox" name="force_login_after_registration" value="Yes" <?php echo $force_login_after_registration == 'Yes'?'checked="checked"':'';?>/></td>
			  </tr>
			</table>
		</td>
	  </tr>
      <tr>
		<td colspan="2">
       	 <table width="100%" border="0" style="background-color:#FFFFFF; padding:10px; border:1px dotted #999999;">
         	  <tr>
				<td>Use CAPTCHA in Registration Form <input type="checkbox" name="captcha_in_registration" value="Yes" <?php echo $captcha_in_registration == 'Yes'?'checked="checked"':'';?>/></td>
			  </tr>
			</table>
		</td>
	  </tr>
      <tr>
		<td colspan="2">
		<table width="100%" border="0" style="background-color:#FFFFFF; padding:10px; border:1px dotted #999999;">
		   <tr>
				<td><strong>Enable default WordPress registration form hooks</strong>
				<input type="checkbox" name="default_registration_form_hooks" value="Yes" <?php echo $default_registration_form_hooks == 'Yes'?'checked="checked"':'';?> /><p>Check to <strong>Enable</strong> default WordPress registration form hooks. This will make the registration form compatible with other plugins. For example <strong>Enable</strong> this if you want to use CAPTCHA on registration, from another plugin. <strong>Disable</strong> this so that no other plugins can interfere with your registration process.</p></td>
			  </tr>
		</table>
		</td>
	  </tr>
      
      
      <tr>
		<td colspan="2">
		<table width="100%" border="0" style="background-color:#FFFFFF; padding:10px; border:1px dotted #999999;">
		   <tr>
				<td><strong>Enable Newsletter Subscription</strong>
				<input type="checkbox" name="enable_cfws_newsletter_subscription" value="Yes" <?php echo $enable_cfws_newsletter_subscription == 'Yes'?'checked="checked"':'';?> /><p>Check to <strong>Enable</strong> Newsletter subscription at the time of Registration. To enable this feature you must Install <a href="https://wordpress.org/plugins/contact-form-with-shortcode/" target="_blank">Contact Form With Shortcode</a> plugin.</p></td>
			  </tr>
		</table>
		</td>
	  </tr>
      
      <tr>
		<td colspan="2">
       	 <table width="100%" border="0" style="background-color:#FFFFFF; padding:10px; border:1px dotted #999999;">
			  <tr>
				<td><input type="submit" name="submit" value="Save" class="button button-primary button-large" /></td>
			  </tr>
			</table>
		</td>
	  </tr>
	   <tr>
		<td colspan="2">
			<table width="100%" border="0" style="background-color:#FFFFFF; padding:10px; border:1px dotted #999999;">
			  <tr>
				<td><h2>Shortcodes</h2></td>
			  </tr>
			  <tr>
				<td>1. Use this <span style="color:#000066;">[rp_register_widget]</span> shortcode to display registration form in post or page.<br />
		 Example: <span style="color:#000066;">[rp_register_widget title="User Registration"]</span>
		 <br />
		 <br />
		 2. Use This shortcode to retrieve user data <span style="color:#000066;">[rp_user_data field="first_name" user_id="2"]</span>. user_id can be blank. if blank then the data is retrieve from currently loged in user. Or else you can use this function in your template file.
		 <span style="color:#000066;">&lt;?php rp_user_data_func("first_name","2"); ?&gt;</span>
		 <br />
		 <br />
		  3. Use this shortcode for user profile page <span style="color:#000066;">[rp_profile_edit]</span>. Logged in usres can edit profile data from this page.
		 <br />
		 <br />
		 4. Use this shortcode to display Update Password form <span style="color:#000066;">[rp_update_password]</span>.
		 <br />
		 </td>
			  </tr>
			</table>
		</td>
	  </tr>
	 
	</table>
	</form>
	<?php
	$this->wp_register_pro_add();
	$this->wp_user_subscription_add();
	$this->donate();
	}
	
	public function wp_register_profile_text_domain(){
		load_plugin_textdomain('wp-register-profile-with-shortcode', FALSE, basename( dirname( __FILE__ ) ) .'/languages');
	}
	
	public function register_widget_afo_menu () {
		add_options_page( 'Register Widget', 'WP Register Settings', 'activate_plugins', 'register_widget_afo', array( $this,'register_widget_afo_options' ));
	}
	
	public function load_settings(){
		add_action( 'admin_menu' , array( $this, 'register_widget_afo_menu' ) );
		add_action( 'admin_init', array( $this, 'register_widget_afo_save_settings' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
		add_action( 'plugins_loaded',  array( $this, 'wp_register_profile_text_domain' ) );
	}
	
	public function register_plugin_styles() {
		wp_enqueue_style( 'style_register_widget', plugins_url( 'wp-register-profile-with-shortcode/style_register_widget.css' ) );
	}
	
	public function help_support(){ ?>
	<table width="98%" border="0" style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px; margin:2px 0px;">
	  <tr>
		<td align="right"><a href="http://www.aviplugins.com/support.php" target="_blank">Help and Support</a> <a href="http://www.aviplugins.com/rss/news.xml" target="_blank"><img src="<?php echo  plugin_dir_url( __FILE__ ) . '/images/rss.png';?>" style="vertical-align: middle;" alt="RSS"></a></td>
	  </tr>
	</table>
	<?php
	}

	public function login_sidebar_widget_add(){ ?>
	<table width="98%" border="0" style="background-color:#FFFFFF; border:1px solid #CCCCCC; padding:0px 0px 0px 10px; margin:2px 0px;">
  <tr>
    <td><p><strong>WP Register Profile With Shortcode</strong> recommends you to download and activate <a href="https://wordpress.org/plugins/login-sidebar-widget/" target="_blank">Login Widget With Shortcode</a> from <a href="https://wordpress.org/" target="_blank">wordpress.org</a> or <a href="http://www.aviplugins.com/fb-login-widget-pro/" target="_blank">Facebook Login Widget (PRO)</a>, so that users can login after successful registration. This will enable user login widget for your site. <a href="http://www.aviplugins.com/fb-login-widget-pro/" target="_blank">Facebook Login Widget (PRO)</a> has social login features, users will be able to login using <strong>Facebook, Google, Twitter, LinkedIn, Microsoft and Yahoo</strong> accounts.</p></td>
  </tr>
</table>
	<?php }
	
	public function wp_register_pro_add(){ ?>
	<table width="98%" border="0" style="background-color:#FFFFD2; border:1px solid #E6DB55; padding:0px 0px 0px 10px; margin:2px 0px;">
  <tr>
    <td><strong>WP Register Profile PRO</strong> 
    <p>The PRO version of this plugin supports <strong>Custom Fields</strong> in user <strong>Registration/ Profile</strong> form. Let user upload their own <strong>Profile Image</strong>. This image will be used as User <strong>Avatar</strong>. User can upload additional <strong>Files</strong> when they register. Admin can check uploaded files from WordPress admin panel. <a href="http://aviplugins.com/wp-register-profile-pro/" target="_blank">Upgrade to PRO version</a> with <strong>USD 2.00</strong> </p></td>
  </tr>
</table>
	<?php }
	
	public function wp_user_subscription_add(){ ?>
	<table width="98%" border="0" style="background-color:#FFFFD2; border:1px solid #E6DB55; padding:0px 0px 0px 10px; margin:2px 0px;">
  <tr>
    <td><strong>WP User Subscription</strong> 
	<p>Get <strong>Paid</strong> when new users make registration in your site. Create <strong>Subscription</strong> packages. Restrict page/ post contents from general members of the site. Configure payment options. <strong>PayPal Standard, PayPal Advanced (Credit/ Debit Card)</strong> payment methods are available by default. Get <a href="http://aviplugins.com/wp-user-subscription/" target="_blank">WP User Subscription</a> with <strong>USD 2.50</strong></p></td>
  </tr>
</table>
	<?php }
	
	public function donate(){	?>
	<table width="98%" border="0" style="background-color:#FFF; border:1px solid #ccc; margin:2px 0px; padding-right:10px;">
	 <tr>
	 <td align="right"><a href="http://www.aviplugins.com/donate/" target="_blank">Donate</a> <img src="<?php echo  plugin_dir_url( __FILE__ ) . '/images/paypal.png';?>" style="vertical-align: middle;" alt="PayPal"></td>
	  </tr>
	</table>
	<?php
	}
}

new register_settings;