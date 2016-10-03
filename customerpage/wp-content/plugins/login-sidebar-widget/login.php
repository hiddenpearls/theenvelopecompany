<?php
/*
Plugin Name: Login Widget With Shortcode
Plugin URI: http://avifoujdar.wordpress.com/category/my-wp-plugins/
Description: This is a simple login form in the widget. just install the plugin and add the login widget in the sidebar. Thats it. :)
Version: 5.5.3
Text Domain: login-sidebar-widget
Domain Path: /languages
Author: avimegladon
Author URI: http://avifoujdar.wordpress.com/
*/

/**
	  |||||   
	<(`0_0`)> 	
	()(afo)()
	  ()-()
**/

include_once dirname( __FILE__ ) . '/settings.php';
include_once dirname( __FILE__ ) . '/login_afo_widget.php';
include_once dirname( __FILE__ ) . '/forgot_pass_class.php';
include_once dirname( __FILE__ ) . '/login_afo_widget_shortcode.php';
include_once dirname( __FILE__ ) . '/message_class.php';
include_once dirname( __FILE__ ) . '/security.php';
include_once dirname( __FILE__ ) . '/login_log.php';
include_once dirname( __FILE__ ) . '/paginate_class.php';

new login_settings;
new afo_login_log;

function afo_login_setup_init() {
	
	// log tables //
	global $wpdb;
	$wpdb->query("CREATE TABLE IF NOT EXISTS `".$wpdb->base_prefix."login_log` (
	  `id` int(11) NOT NULL AUTO_INCREMENT,
	  `ip` varchar(50) NOT NULL,
	  `msg` varchar(255) NOT NULL,
	  `l_added` datetime NOT NULL,
	  `l_status` enum('success','failed','blocked') NOT NULL,
  	  `l_type` enum('new','old') NOT NULL,
	  PRIMARY KEY (`id`)
	)");
	// log tables //
	
	
	$forgot_password_link_mail_subject = "Password Reset Request";
 	$forgot_password_link_mail_body = "Someone requested that the password be reset for the following account:
<br>
#site_url#
<br>
Username: #user_name#
<br>
If this was a mistake, just ignore this email and nothing will happen.
<br>
To reset your password, visit the following address:
<br>
#resetlink#";

	$new_password_mail_subject = "Password Reset Request";
	$new_password_mail_body = "Your new password for the account at:
<br>
#site_url#
<br>
Username: #user_name#
<br>
Password: #user_password#
<br>
You can now login with your new password at:
#site_url#";
	
	update_option( 'forgot_password_link_mail_subject', $forgot_password_link_mail_subject );
    update_option( 'forgot_password_link_mail_body', $forgot_password_link_mail_body );
	update_option( 'new_password_mail_subject', $new_password_mail_subject );
    update_option( 'new_password_mail_body', $new_password_mail_body );
	
	$ls = new login_settings;
	update_option( 'custom_style_afo', $ls->default_style );
}

register_activation_hook( __FILE__, 'afo_login_setup_init' );