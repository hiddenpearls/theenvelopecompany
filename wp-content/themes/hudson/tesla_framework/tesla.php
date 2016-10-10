<?php
//define TeslaThemesFramework directory name
if(!defined('TTF'))
	define('TTF', get_template_directory() . '/tesla_framework');
//Load framework constants
require_once TTF . '/config/constants.php';

//Load theme details
require_once TT_THEME_DIR . '/theme_config/theme-details.php';

if(!defined('THEME_OPTIONS'))
	define('THEME_OPTIONS', THEME_NAME . '_options');

//Load main framework classes
require_once TTF . '/core/teslaframework.php';
require_once TTF . '/core/tesla_admin.php';
require_once TTF . '/core/tt_load.php';
if(file_exists(TTF . '/core/tt_security.php'))
	require_once TTF . '/core/tt_security.php';
else
	exit();
//TT ENQUEUE
require_once TTF . '/core/tt_enqueue.php';

//Contact Form Builder
if(file_exists(TT_THEME_DIR . '/theme_config/contact-form-config.php')){
	require_once TTF . '/core/tt_contact_form.php';
}
//Admin load
do_action( 'tt_admin_load' ); // hooked in TTF . '/core/tesla_admin.php' : $TTA = new Tesla_admin;

//Slider - do not load if plugin TFW detected
if( ( !defined('TT_USES_PLUGIN') || ( defined('TT_USES_PLUGIN') && !TT_USES_PLUGIN ) || $_SERVER['HTTP_HOST'] === 'test.teslathemes.com' || $_SERVER['HTTP_HOST'] === 'demo.teslathemes.com' ) && !class_exists('Tesla_slider') ) {
	require_once TTF . '/core/tesla_slider.php';
}

//Subscription
if(file_exists(TT_THEME_DIR . '/theme_config/subscription.php')){
	require_once TTF . '/core/tt_subscription.php';
}

//action for theme to safelly hook
add_action( 'init' , 'tt_fw_inited' );
function tt_fw_inited(){
	do_action( 'tt_fw_init' );
}